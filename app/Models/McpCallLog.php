<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['token_id', 'tool', 'arguments', 'result', 'status', 'duration_ms'])]
class McpCallLog extends Model
{
    protected function casts(): array
    {
        return [
            'arguments' => 'array',
            'result' => 'array',
        ];
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(McpToken::class, 'token_id');
    }
}
