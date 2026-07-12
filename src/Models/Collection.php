<?php

namespace Whilesmart\Library\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Whilesmart\Library\Database\Factories\CollectionFactory;

/**
 * A bucket of library assets. Owned polymorphically (a workspace, an
 * organization, a user), holds organizational folders, and groups the
 * flattened set of assets used as context.
 */
class Collection extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
        'sort_order' => 'integer',
    ];

    public function getTable(): string
    {
        return config('library.collections_table', 'library_collections');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function folders(): HasMany
    {
        return $this->hasMany(config('library.models.folder', Folder::class), 'library_collection_id');
    }

    /** All assets in the bucket, folder-agnostic. */
    public function assets(): HasMany
    {
        return $this->hasMany(config('library.models.asset', Asset::class), 'library_collection_id');
    }

    public function scopeForOwnerKey(Builder $query, string $ownerType, int|string $ownerId): Builder
    {
        return $query->where('owner_type', $ownerType)->where('owner_id', $ownerId);
    }

    protected static function newFactory(): CollectionFactory
    {
        return CollectionFactory::new();
    }
}
