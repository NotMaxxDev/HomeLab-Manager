<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'type', 'base_url', 'api_key', 'models', 'default_model', 'is_default', 'enabled', 'config'])]
class LlmProvider extends Model
{
    protected function casts(): array
    {
        return [
            'models' => 'array',
            'config' => 'array',
            'is_default' => 'boolean',
            'enabled' => 'boolean',
            'api_key' => 'encrypted',
        ];
    }

    protected $hidden = ['api_key'];

    public function conversations(): HasMany
    {
        return $this->hasMany(ChatConversation::class, 'provider_id');
    }
}
