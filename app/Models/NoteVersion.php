<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['note_id', 'title', 'content', 'version', 'created_by'])]
class NoteVersion extends Model
{
    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
