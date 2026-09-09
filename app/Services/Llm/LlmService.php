<?php

namespace App\Services\Llm;

use App\Models\LlmProvider;
use App\Services\Llm\Contracts\LlmProviderInterface;

/**
 * Erzeugt aus einem LlmProvider-Model die passende Provider-Implementierung.
 */
class LlmService
{
    public function resolve(LlmProvider $provider): LlmProviderInterface
    {
        $baseUrl = $this->baseUrl($provider);
        $apiKey = $provider->api_key;
        $model = $provider->default_model ?? '';

        return match ($provider->type) {
            'anthropic' => new AnthropicProvider($baseUrl, $apiKey, $model),
            default => new OpenAiCompatibleProvider($baseUrl, $apiKey, $model),
        };
    }

    public function defaultProvider(): ?LlmProvider
    {
        return LlmProvider::where('enabled', true)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();
    }

    public function baseUrl(LlmProvider $provider): string
    {
        if ($provider->base_url) {
            return $provider->base_url;
        }

        return match ($provider->type) {
            'openai' => 'https://api.openai.com',
            'anthropic' => 'https://api.anthropic.com',
            'openrouter' => 'https://openrouter.ai/api',
            'ollama' => 'http://ollama:11434',
            default => 'http://localhost:11434',
        };
    }
}
