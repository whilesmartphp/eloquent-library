<?php

namespace Whilesmart\Library\Presenters;

use Whilesmart\Library\Models\Asset;

/**
 * Turns one kind of asset into the shapes a consumer needs. Adding a new asset
 * kind means adding a presenter and registering it in config/library.php; the
 * registry never branches on kind itself.
 */
interface LibraryAssetPresenter
{
    public function supports(string $kind): bool;

    /**
     * The flat entry a consumer sees when listing a collection (id, kind, and
     * whatever is needed to use the asset: a URL to embed, a caption, fields).
     *
     * @return array<string, mixed>
     */
    public function present(Asset $asset): array;

    /** The full text payload for reading the asset, or null if it has none. */
    public function read(Asset $asset): ?string;
}
