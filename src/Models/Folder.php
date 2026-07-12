<?php

namespace Whilesmart\Library\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Whilesmart\Library\Database\Factories\FolderFactory;

/**
 * An organizational folder inside a collection. Folders may nest and are a
 * grouping only: passing a collection as context flattens assets across all
 * folders.
 */
class Folder extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
        'sort_order' => 'integer',
    ];

    public function getTable(): string
    {
        return config('library.folders_table', 'library_folders');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(config('library.models.collection', Collection::class), 'library_collection_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(config('library.models.folder', Folder::class), 'parent_folder_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(config('library.models.folder', Folder::class), 'parent_folder_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(config('library.models.asset', Asset::class), 'library_folder_id');
    }

    public function scopeForOwnerKey(Builder $query, string $ownerType, int|string $ownerId): Builder
    {
        return $query->where('owner_type', $ownerType)->where('owner_id', $ownerId);
    }

    protected static function newFactory(): FolderFactory
    {
        return FolderFactory::new();
    }
}
