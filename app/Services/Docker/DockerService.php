<?php

namespace App\Services\Docker;

use App\Services\Contracts\DockerServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Docker Engine API über Guzzle, primär über den Unix-Socket.
 *
 * Kein Aufruf der Docker-CLI. Der Socket-Pfad kommt aus der .env
 * (DOCKER_SOCKET_PATH). Alternativ kann eine TCP-URL (Socket-Proxy)
 * angegeben werden.
 */
class DockerService implements DockerServiceInterface
{
    private Client $client;

    private bool $available = false;

    public function __construct(?string $socketPath = null)
    {
        $socketPath ??= (string) config('docker.socket_path', env('DOCKER_SOCKET_PATH', '/var/run/docker.sock'));

        $this->client = $this->buildClient($socketPath);
    }

    public function ping(): bool
    {
        try {
            $response = $this->client->get('/_ping');
            $this->available = $response->getStatusCode() === 200;

            return $this->available;
        } catch (GuzzleException $e) {
            $this->available = false;

            return false;
        }
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function listContainers(bool $all = true): array
    {
        return $this->request('GET', '/containers/json', [
            'query' => ['all' => $all ? 'true' : 'false'],
        ]);
    }

    public function inspectContainer(string $id): array
    {
        return $this->request('GET', "/containers/{$id}/json");
    }

    public function containerStats(string $id): array
    {
        return $this->request('GET', "/containers/{$id}/stats", [
            'query' => ['stream' => 'false'],
        ]);
    }

    public function logs(string $id, int $tail = 200, ?string $since = null): array
    {
        $query = [
            'stdout' => 'true',
            'stderr' => 'true',
            'tail' => (string) $tail,
        ];

        if ($since) {
            $query['since'] = $since;
        }

        $raw = $this->requestRaw('GET', "/containers/{$id}/logs", ['query' => $query]);

        return $this->splitMultiplexedStream($raw);
    }

    public function startContainer(string $id): array
    {
        $this->request('POST', "/containers/{$id}/start");

        return $this->inspectContainer($id);
    }

    public function stopContainer(string $id): array
    {
        $this->request('POST', "/containers/{$id}/stop", ['query' => ['t' => 10]]);

        return $this->inspectContainer($id);
    }

    public function restartContainer(string $id): array
    {
        $this->request('POST', "/containers/{$id}/restart", ['query' => ['t' => 10]]);

        return $this->inspectContainer($id);
    }

    public function pauseContainer(string $id): array
    {
        $this->request('POST', "/containers/{$id}/pause");

        return $this->inspectContainer($id);
    }

    public function unpauseContainer(string $id): array
    {
        $this->request('POST', "/containers/{$id}/unpause");

        return $this->inspectContainer($id);
    }

    public function removeContainer(string $id, bool $force = false): array
    {
        return $this->request('DELETE', "/containers/{$id}", [
            'query' => ['force' => $force ? 'true' : 'false'],
        ]);
    }

    public function recreateContainer(string $id): array
    {
        // Recreate = Stop -> Remove -> (Compose) neu erzeugen ist nicht über die
        // Engine-API allein möglich. Für eigenständige Container versuchen wir
        // einen Restart; für Compose-Container signalisieren wir dies dem Aufrufer.
        $inspect = $this->inspectContainer($id);

        $this->stopContainer($id);

        if ($this->isComposeContainer($inspect)) {
            throw new RuntimeException('recreate_requires_compose');
        }

        return $this->startContainer($id);
    }

    public function listImages(bool $all = false): array
    {
        return $this->request('GET', '/images/json', [
            'query' => ['all' => $all ? 'true' : 'false'],
        ]);
    }

    public function imageRemove(string $id, bool $force = false): array
    {
        return $this->request('DELETE', "/images/{$id}", [
            'query' => ['force' => $force ? 'true' : 'false'],
        ]);
    }

    public function imagesPrune(): array
    {
        return $this->request('POST', '/images/prune');
    }

    public function listVolumes(): array
    {
        $data = $this->request('GET', '/volumes');

        return $data['Volumes'] ?? [];
    }

    public function removeVolume(string $name): array
    {
        return $this->request('DELETE', "/volumes/{$name}");
    }

    public function listNetworks(): array
    {
        return $this->request('GET', '/networks');
    }

    public function removeNetwork(string $id): array
    {
        return $this->request('DELETE', "/networks/{$id}");
    }

    public function systemInfo(): array
    {
        return $this->request('GET', '/info');
    }

    protected function isComposeContainer(array $inspect): bool
    {
        $labels = $inspect['Config']['Labels'] ?? [];

        return isset($labels['com.docker.compose.project']);
    }

    protected function request(string $method, string $uri, array $options = []): array
    {
        $body = $this->requestRaw($method, $uri, $options);

        if ($body === '' || $body === null) {
            return [];
        }

        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function requestRaw(string $method, string $uri, array $options = []): string
    {
        try {
            $response = $this->client->request($method, $uri, $options);
            $this->available = true;

            return (string) $response->getBody();
        } catch (GuzzleException $e) {
            $this->available = false;
            Log::error('Docker API request failed', [
                'method' => $method,
                'uri' => $uri,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Docker-API nicht erreichbar oder Anfrage fehlgeschlagen.', 0, $e);
        }
    }

    private function buildClient(string $socketPath): Client
    {
        $config = ['http_errors' => false, 'timeout' => 30];

        if (str_starts_with($socketPath, 'tcp://') || str_starts_with($socketPath, 'http://') || str_starts_with($socketPath, 'https://')) {
            $baseUri = str_starts_with($socketPath, 'tcp://')
                ? 'http://'.substr($socketPath, 6)
                : $socketPath;

            $config['base_uri'] = rtrim($baseUri, '/').'/';
        } else {
            // Unix-Socket über curl
            $config['base_uri'] = 'http://docker/';
            $config['curl'] = [
                \CURLOPT_UNIX_SOCKET_PATH => $socketPath,
            ];
        }

        return new Client($config);
    }

    /**
     * Teilt den multiplexierten Docker-Log-Stream in Zeilen auf.
     * Der Docker-Log-Stream beginnt mit einem 8-Byte-Header pro Frame.
     */
    private function splitMultiplexedStream(string $raw): array
    {
        $lines = [];
        $offset = 0;
        $length = strlen($raw);

        while ($offset < $length) {
            if ($length - $offset < 8) {
                // Rest ohne Header: direkt als Zeilen behandeln
                foreach (preg_split('/\r\n|\n|\r/', substr($raw, $offset)) ?: [] as $line) {
                    if ($line !== '') {
                        $lines[] = $line;
                    }
                }
                break;
            }

            $header = substr($raw, $offset, 8);
            $size = unpack('Nsize', substr($header, 4, 4))['size'] ?? 0;
            $offset += 8;

            $payload = substr($raw, $offset, $size);
            $offset += $size;

            if ($payload !== '') {
                foreach (preg_split('/\r\n|\n|\r/', $payload) ?: [] as $line) {
                    if ($line !== '') {
                        $lines[] = $line;
                    }
                }
            }
        }

        return $lines;
    }
}
