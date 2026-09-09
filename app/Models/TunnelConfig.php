<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['enabled', 'token', 'hostname', 'container_id', 'connected', 'last_status'])]
class TunnelConfig extends Model
{
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'connected' => 'boolean',
            'last_status' => 'array',
            'token' => 'encrypted',
        ];
    }

    protected $hidden = ['token'];
}
