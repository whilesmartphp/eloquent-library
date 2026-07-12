<?php

namespace Whilesmart\Library\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Whilesmart\Files\Traits\HasFiles;
use Whilesmart\Library\Database\Factories\AssetFactory;

/**
 * A single artifact in the library: a thin, polymorphic envelope. `kind` is an
 * open string. Text kinds (note/snippet/...) use `body`; binary kinds
 * (image/video/...) hang their file off the eloquent-files relation. `metadata`
 * holds any kind-specific extension fields, so new kinds need no schema change.
 * Presenters (config/library.php) turn a kind into what a consumer reads.
 */
class Asset extends Model
{
    use HasFactory, HasFiles, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Asset $asset) {
            if (empty($asset->kind)) {
                $asset->kind = 'note';
            }
        });
    }

    public function getTable(): string
    {
        return config('library.assets_table', 'library_assets');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The URL of the asset's attached file (binary kinds), if any.
     */
    public function getUrlAttribute(): ?string
    {
        return $this->files()->first()?->url;
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(config('library.models.collection', Collection::class), 'library_collection_id');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(config('library.models.folder', Folder::class), 'library_folder_id');
    }

    public function scopeForOwnerKey(Builder $query, string $ownerType, int|string $ownerId): Builder
    {
        return $query->where('owner_type', $ownerType)->where('owner_id', $ownerId);
    }

    public function scopeOfKind(Builder $query, string $kind): Builder
    {
        return $query->where('kind', $kind);
    }

    protected static function newFactory(): AssetFactory
    {
        return AssetFactory::new();
    }
}
