<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable(['docker_id', 'name', 'image', 'status', 'health', 'compose_project', 'compose_service', 'ports', 'networks', 'volumes', 'labels', 'started_at', 'last_seen_at'])]
class Container extends Model
{
    protected function casts(): array
    {
        return [
            'ports' => 'array',
            'networks' => 'array',
            'volumes' => 'array',
            'labels' => 'array',
            'started_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_container');
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
