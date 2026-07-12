<?php

namespace Whilesmart\Library\Presenters;

use Whilesmart\Library\Models\Asset;

class NoteAssetPresenter implements LibraryAssetPresenter
{
    public function supports(string $kind): bool
    {
        return $kind === 'note';
    }

    public function present(Asset $asset): array
    {
        return [
            'id' => $asset->id,
            'kind' => 'note',
            'title' => $asset->title,
            'description' => $asset->description,
            'folder' => $asset->folder?->name,
        ];
    }

    public function read(Asset $asset): ?string
    {
        return $asset->body;
    }
}
