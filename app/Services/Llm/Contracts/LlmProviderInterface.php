<?php

namespace App\Services\Llm\Contracts;

/**
 * Abstraktion über LLM-Provider (OpenAI, Anthropic, Ollama, OpenRouter,
 * beliebige OpenAI-kompatible Endpoints).
 */
interface LlmProviderInterface
{
    /**
     * Nicht-streamender Chat-Aufruf.
     *
     * @param  array<int, array{role:string, content:string}>  $messages
     * @param  array  $options  (model, tools, temperature, ...)
     * @return array{content: string|null, tool_calls: array, usage: array}
     */
    public function chat(array $messages, array $options = []): array;

    /**
     * Streamender Chat-Aufruf. Liefert Text-Deltas als Generator.
     *
     * @param  array<int, array{role:string, content:string}>  $messages
     * @return \Generator<string>
     */
    public function streamChat(array $messages, array $options = []): \Generator;

    /** Verfügbare Modelle des Providers abrufen. */
    public function listModels(): array;

    /** Verbindungstest. */
    public function testConnection(): bool;
}
