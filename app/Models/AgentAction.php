<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['conversation_id', 'message_id', 'action', 'target_type', 'target_id', 'status', 'arguments', 'result'])]
class AgentAction extends Model
{
    protected function casts(): array
    {
        return [
            'arguments' => 'array',
            'result' => 'array',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class);
    }

    public function target(): MorphTo
    {
        return $this->morphTo();
    }
}
