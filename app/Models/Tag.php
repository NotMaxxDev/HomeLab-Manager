<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable(['name', 'slug'])]
class Tag extends Model
{
    public function notes(): MorphToMany
    {
        return $this->morphedByMany(Note::class, 'taggable');
    }

    public function projects(): MorphToMany
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }

    public function containers(): MorphToMany
    {
        return $this->morphedByMany(Container::class, 'taggable');
    }
}
