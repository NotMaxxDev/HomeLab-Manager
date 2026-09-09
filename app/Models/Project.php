<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable(['name', 'slug', 'description', 'color', 'icon', 'links', 'notes', 'is_favorite', 'created_by'])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'links' => 'array',
            'is_favorite' => 'boolean',
        ];
    }

    public function containers(): BelongsToMany
    {
        return $this->belongsToMany(Container::class, 'project_container');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
