<?php

namespace App\Services\Llm;

use App\Services\Llm\Contracts\LlmProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

/**
 * Anthropic (Claude) Provider über die Messages-API.
 */
class AnthropicProvider implements LlmProviderInterface
{
    private const VERSION = '2023-06-01';

    public function __construct(
        private string $baseUrl,
        private ?string $apiKey,
        private string $model,
    ) {
    }

    public function chat(array $messages, array $options = []): array
    {
        $data = $this->post('/v1/messages', $this->buildBody($messages, $options));

        $text = collect($data['content'] ?? [])
            ->where('type', 'text')
            ->pluck('text')
            ->implode('');

        return [
            'content' => $text !== '' ? $text : null,
            'tool_calls' => [],
            'usage' => $data['usage'] ?? [],
        ];
    }

    public function streamChat(array $messages, array $options = []): \Generator
    {
        $body = $this->buildBody($messages, $options, stream: true);

        $client = $this->client();
        $response = $client->request('POST', $this->endpoint('/v1/messages'), [
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

                $json = json_decode(trim(substr($line, 5)), true);

                if (($json['type'] ?? null) === 'content_block_delta') {
                    $delta = $json['delta']['text'] ?? null;
                    if (is_string($delta) && $delta !== '') {
                        yield $delta;
                    }
                }
            }
        }
    }

    public function listModels(): array
    {
        try {
            $data = $this->get('/v1/models');

            return collect($data['data'] ?? [])->pluck('id')->values()->all();
        } catch (GuzzleException) {
            return [];
        }
    }

    public function testConnection(): bool
    {
        try {
            $this->get('/v1/models');

            return true;
        } catch (GuzzleException) {
            return false;
        }
    }

    protected function buildBody(array $messages, array $options, bool $stream = false): array
    {
        $system = '';
        $conversation = [];

        foreach ($messages as $message) {
            if (($message['role'] ?? '') === 'system') {
                $system .= ($message['content'] ?? '')."\n";
            } else {
                $conversation[] = [
                    'role' => $message['role'] === 'assistant' ? 'assistant' : 'user',
                    'content' => $message['content'] ?? '',
                ];
            }
        }

        $body = [
            'model' => $options['model'] ?? $this->model,
            'max_tokens' => $options['max_tokens'] ?? 4096,
            'messages' => $conversation,
            'stream' => $stream,
        ];

        if (trim($system) !== '') {
            $body['system'] = trim($system);
        }

        if (isset($options['temperature'])) {
            $body['temperature'] = $options['temperature'];
        }

        return $body;
    }

    protected function endpoint(string $path): string
    {
        return rtrim($this->baseUrl, '/').$path;
    }

    protected function client(): Client
    {
        return new Client([
            'http_errors' => false,
            'timeout' => 120,
            'headers' => [
                'x-api-key' => $this->apiKey ?? '',
                'anthropic-version' => self::VERSION,
            ],
        ]);
    }

    protected function post(string $path, array $body): array
    {
        $response = $this->client()->post($this->endpoint($path), ['json' => $body]);
        $data = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() >= 400 || ! is_array($data)) {
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
