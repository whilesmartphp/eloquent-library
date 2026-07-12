<?php

namespace Whilesmart\Library\Presenters;

use Whilesmart\Library\Models\Asset;

/**
 * Catch-all presenter for any kind without a dedicated one. Exposes the common
 * envelope fields, surfaces a binary reference from metadata when present, and
 * reads back the text body. Keep it last in the config list.
 */
class DefaultAssetPresenter implements LibraryAssetPresenter
{
    public function supports(string $kind): bool
    {
        return true;
    }

    public function present(Asset $asset): array
    {
        $meta = $asset->metadata ?? [];

        return [
            'id' => $asset->id,
            'kind' => $asset->kind,
            'title' => $asset->title,
            'description' => $asset->description,
            'url' => $asset->url ?? ($meta['url'] ?? null),
            'folder' => $asset->folder?->name,
        ];
    }

    public function read(Asset $asset): ?string
    {
        return $asset->body;
    }
}
