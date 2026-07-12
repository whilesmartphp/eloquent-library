<?php

namespace Whilesmart\Library\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Whilesmart\Library\Models\Collection;

trait HasLibrary
{
    public function libraryCollections(): MorphMany
    {
        return $this->morphMany(config('library.models.collection', Collection::class), 'owner');
    }
}
