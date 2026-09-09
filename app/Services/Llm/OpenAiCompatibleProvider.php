<?php

namespace App\Services\Llm;

use App\Services\Llm\Contracts\LlmProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

/**
 * OpenAI-kompatibler Provider.
 *
 * Deckt OpenAI, OpenRouter, Ollama (/v1) und beliebige OpenAI-kompatible
 * Endpoints ab (Chat-Completions-API).
 */
class OpenAiCompatibleProvider implements LlmProviderInterface
{
    public function __construct(
        private string $baseUrl,
        private ?string $apiKey,
        private string $model,
    ) {
    }

    public function chat(array $messages, array $options = []): array
    {
        $body = $this->buildBody($messages, $options, stream: false);

        $data = $this->post('/chat/completions', $body);

        $message = $data['choices'][0]['message'] ?? [];

        return [
            'content' => $message['content'] ?? null,
            'tool_calls' => $message['tool_calls'] ?? [],
            'usage' => $data['usage'] ?? [],
        ];
    }

    public function streamChat(array $messages, array $options = []): \Generator
    {
        $body = $this->buildBody($messages, $options, stream: true);

        $client = $this->client();
        $response = $client->request('POST', $this->endpoint('/chat/completions'), [
            'json' => $body,
            'stream' => true,
            'timeout' => 300,
        ]);

        $stream = $response->getBody();
        $buffer = '';

        while (! $stream->eof()) {
            $buffer .= $stream->read(8192);

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if (! str_starts_with($line, 'data:')) {
                    continue;
                }

                $payload = trim(substr($line, 5));
                if ($payload === '[DONE]') {
                    return;
                }

                $json = json_decode($payload, true);
                $delta = $json['choices'][0]['delta']['content'] ?? null;

                if (is_string($delta) && $delta !== '') {
                    yield $delta;
                }
            }
        }
    }

    public function listModels(): array
    {
        try {
            $data = $this->get('/models');

            return collect($data['data'] ?? [])
                ->pluck('id')
                ->values()
                ->all();
        } catch (GuzzleException) {
            return [];
        }
    }

    public function testConnection(): bool
    {
        try {
            $this->get('/models');

            return true;
        } catch (GuzzleException) {
            return false;
        }
    }

    protected function buildBody(array $messages, array $options, bool $stream): array
    {
        $body = [
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'stream' => $stream,
        ];

        if (isset($options['temperature'])) {
            $body['temperature'] = $options['temperature'];
        }

        if (! empty($options['tools'])) {
            $body['tools'] = $options['tools'];
            $body['tool_choice'] = $options['tool_choice'] ?? 'auto';
        }

        return $body;
    }

    protected function endpoint(string $path): string
    {
        $base = rtrim($this->baseUrl, '/');

        // Normalisieren: sicherstellen, dass /v1 nur einmal vorkommt
        if (str_ends_with($base, '/v1')) {
            return $base.$path;
        }

        return $base.'/v1'.$path;
    }

    protected function client(): Client
    {
        $config = ['http_errors' => false, 'timeout' => 120];

        if ($this->apiKey) {
            $config['headers'] = ['Authorization' => "Bearer {$this->apiKey}"];
        }

        return new Client($config);
    }

    protected function post(string $path, array $body): array
    {
        $response = $this->client()->post($this->endpoint($path), ['json' => $body]);
        $data = json_decode((string) $response->getBody(), true);

        if (($response->getStatusCode() >= 400) || ! is_array($data)) {
            $error = $data['error']['message'] ?? ('HTTP '.$response->getStatusCode());

            throw new RuntimeException($error);
        }

        return $data;
    }

    protected function get(string $path): array
    {
        $response = $this->client()->get($this->endpoint($path));

        if ($response->getStatusCode() >= 400) {
            throw new RuntimeException('HTTP '.$response->getStatusCode());
        }

        $data = json_decode((string) $response->getBody(), true);

        return is_array($data) ? $data : [];
    }
}
