<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['note_id', 'filename', 'disk', 'path', 'mime', 'size', 'created_by'])]
class NoteAttachment extends Model
{
    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }
}
