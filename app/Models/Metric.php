<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['container_id', 'type', 'value', 'recorded_at'])]
class Metric extends Model
{
    protected function casts(): array
    {
        return [
            'value' => 'array',
            'recorded_at' => 'datetime',
        ];
    }
}
