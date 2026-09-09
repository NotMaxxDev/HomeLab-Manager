<?php

namespace App\Livewire\Chat;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\Llm\LlmService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('AI Chat')]
class Chat extends Component
{
    public ?ChatConversation $conversation = null;
    public string $message = '';
    public array $messages = [];

    public function mount(?ChatConversation $conversation = null)
    {
        if ($conversation && $conversation->exists) {
            $this->conversation = $conversation;
            $this->messages = $conversation->messages()->oldest()->get()->toArray();
        }
    }

    public function sendMessage(LlmService $llm)
    {
        if (!trim($this->message)) return;

        if (!$this->conversation) {
            $this->conversation = ChatConversation::create([
                'user_id' => auth()->id(),
                'title' => substr($this->message, 0, 30) . '...'
            ]);
        }

        ChatMessage::create([
            'conversation_id' => $this->conversation->id,
            'role' => 'user',
            'content' => $this->message,
        ]);

        $this->message = '';
        $this->messages = $this->conversation->messages()->oldest()->get()->toArray();
    }

    public function render()
    {
        $conversations = ChatConversation::where('user_id', auth()->id())->latest()->get();

        return view('livewire.chat.chat', [
            'conversations' => $conversations,
        ]);
    }
}
