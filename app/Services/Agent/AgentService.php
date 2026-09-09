<?php

namespace App\Services\Agent;

use App\Models\AgentAction;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\AuditService;
use App\Services\Llm\LlmService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Log;

/**
 * Orchestriert den Agenten-Chat mit Tool-Calling und verpflichtendem
 * Human-in-the-loop für verändernde Aktionen.
 */
class AgentService
{
    public function __construct(
        private LlmService $llm,
        private ToolRegistry $tools,
        private SettingsService $settings,
        private AuditService $audit,
    ) {
    }

    /**
     * Verarbeitet eine Runde des Agenten-Dialogs.
     *
     * @return array{type: 'text'|'confirmations', content?: string, actions?: array, message_id?: int}
     */
    public function run(ChatConversation $conversation): array
    {
        $provider = $conversation->provider ?? $this->llm->defaultProvider();

        if (! $provider) {
            return ['type' => 'text', 'content' => __('chat.no_provider')];
        }

        $llm = $this->llm->resolve($provider);
        $model = $provider->default_model;
        $allowRead = $this->settings->bool('agent_allow_read_without_confirmation', false);
        $tools = $this->tools->definitions();

        for ($i = 0; $i < 6; $i++) {
            $messages = $this->buildMessages($conversation);

            try {
                $response = $llm->chat($messages, ['model' => $model, 'tools' => $tools]);
            } catch (\Throwable $e) {
                Log::error('LLM call failed', ['error' => $e->getMessage()]);

                return ['type' => 'text', 'content' => __('chat.provider_error', ['message' => $e->getMessage()])];
            }

            $toolCalls = $response['tool_calls'] ?? [];

            if (empty($toolCalls)) {
                $content = $response['content'] ?? '';

                ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $content,
                    'model' => $model,
                    'tokens_prompt' => $response['usage']['prompt_tokens'] ?? 0,
                    'tokens_completion' => $response['usage']['completion_tokens'] ?? 0,
                ]);

                return ['type' => 'text', 'content' => $content];
            }

            // Assistant-Tool-Call-Nachricht persistieren (für Fortsetzung nach Bestätigung)
            $assistantMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => null,
                'model' => $model,
                'meta' => ['tool_calls' => $toolCalls],
            ]);

            $needsConfirmation = collect($toolCalls)->contains(
                fn (array $call) => $this->needsConfirmation($call['function']['name'] ?? '', $allowRead)
            );

            if ($needsConfirmation) {
                $actions = [];

                foreach ($toolCalls as $call) {
                    $name = $call['function']['name'] ?? '';
                    $args = json_decode($call['function']['arguments'] ?? '{}', true) ?? [];

                    $actions[] = AgentAction::create([
                        'conversation_id' => $conversation->id,
                        'message_id' => $assistantMessage->id,
                        'action' => $name,
                        'status' => 'pending',
                        'arguments' => [
                            'tool_call_id' => $call['id'] ?? null,
                            'args' => $args,
                            'level' => $this->tools->level($name),
                        ],
                    ]);
                }

                return [
                    'type' => 'confirmations',
                    'actions' => $actions,
                    'message_id' => $assistantMessage->id,
                ];
            }

            // Alle Aktionen sind lesend und dürfen ohne Bestätigung ausgeführt werden
            foreach ($toolCalls as $call) {
                $this->executeCall($call, $conversation, $assistantMessage);
            }

            // Schleife fortsetzen: LLM mit Tool-Ergebnissen erneut aufrufen
        }

        return ['type' => 'text', 'content' => __('chat.too_many_tool_calls')];
    }

    /**
     * Bestätigt/lehnt eine ausstehende Agentenaktion ab und setzt den
     * Dialog fort, sobald der komplette Batch abgearbeitet ist.
     *
     * @return array{type: 'text'|'confirmations'|'waiting', ...}
     */
    public function resolveAction(AgentAction $action, bool $approved): array
    {
        $action->status = $approved ? 'approved' : 'denied';
        $action->save();

        $messageId = $action->message_id;
        $conversation = $action->conversation;

        $pending = AgentAction::where('message_id', $messageId)
            ->where('status', 'pending')
            ->count();

        if ($pending > 0) {
            return ['type' => 'waiting'];
        }

        $assistantMessage = ChatMessage::find($messageId);
        $toolCalls = $assistantMessage?->meta['tool_calls'] ?? [];

        foreach ($toolCalls as $call) {
            $toolCallId = $call['id'] ?? null;
            $matching = AgentAction::where('message_id', $messageId)
                ->get()
                ->first(fn (AgentAction $a) => ($a->arguments['tool_call_id'] ?? null) === $toolCallId);

            if ($matching && $matching->status === 'approved') {
                $this->executeCall($call, $conversation, $assistantMessage, $matching);
            } else {
                $this->storeDeniedResult($call, $conversation);
            }
        }

        return $this->run($conversation);
    }

    protected function executeCall(array $call, ChatConversation $conversation, ChatMessage $assistantMessage, ?AgentAction $action = null): void
    {
        $name = $call['function']['name'] ?? '';
        $args = json_decode($call['function']['arguments'] ?? '{}', true) ?? [];

        try {
            $result = $this->tools->execute($name, $args);
            $status = 'executed';
            $resultPayload = $result;
        } catch (\Throwable $e) {
            $result = ['error' => $e->getMessage()];
            $status = 'failed';
            $resultPayload = $result;
        }

        if ($action) {
            $action->status = $status;
            $action->result = $resultPayload;
            $action->save();
        }

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'tool',
            'content' => json_encode($resultPayload),
            'meta' => ['tool_call_id' => $call['id'] ?? null, 'tool' => $name],
        ]);

        $this->audit->log(
            action: "agent.{$name}",
            targetType: $name,
            targetId: $args['container'] ?? $args['project'] ?? $args['note'] ?? null,
            result: $status === 'executed' ? 'success' : 'failure',
            meta: ['conversation_id' => $conversation->id, 'arguments' => $args],
            actorType: 'chatbot',
            actorName: 'AI-Assistent',
        );
    }

    protected function storeDeniedResult(array $call, ChatConversation $conversation): void
    {
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'tool',
            'content' => json_encode(['denied' => true]),
            'meta' => ['tool_call_id' => $call['id'] ?? null, 'tool' => $call['function']['name'] ?? ''],
        ]);
    }

    protected function needsConfirmation(string $name, bool $allowRead): bool
    {
        return $this->tools->level($name) === 'write' || ! $allowRead;
    }

    protected function buildMessages(ChatConversation $conversation): array
    {
        $system = $this->settings->string('agent_system_prompt');

        if ($context = $conversation->context) {
            $system .= "\n\nKontext:\n".json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $messages = [['role' => 'system', 'content' => $system]];

        foreach ($conversation->messages as $message) {
            $messages[] = match (true) {
                $message->role === 'assistant' && ! empty($message->meta['tool_calls']) => [
                    'role' => 'assistant',
                    'content' => $message->content,
                    'tool_calls' => $message->meta['tool_calls'],
                ],
                $message->role === 'tool' => [
                    'role' => 'tool',
                    'tool_call_id' => $message->meta['tool_call_id'] ?? null,
                    'content' => $message->content,
                ],
                default => [
                    'role' => $message->role,
                    'content' => $message->content,
                ],
            };
        }

        return $messages;
    }
}
