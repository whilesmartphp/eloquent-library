<?php

namespace Whilesmart\Library\Presenters;

use Whilesmart\Library\Models\Asset;

/**
 * Resolves the right presenter for an asset's kind from the config-bound list.
 * Container-resolvable (no constructor args); consumers depend on this rather
 * than on any concrete presenter, so new kinds are purely additive.
 */
class PresenterRegistry
{
    /** @var array<int, LibraryAssetPresenter> */
    private array $presenters;

    public function __construct()
    {
        $this->presenters = array_map(
            fn (string $class) => app($class),
            config('library.presenters', []),
        );
    }

    public function forKind(string $kind): ?LibraryAssetPresenter
    {
        foreach ($this->presenters as $presenter) {
            if ($presenter->supports($kind)) {
                return $presenter;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function present(Asset $asset): ?array
    {
        return $this->forKind($asset->kind)?->present($asset);
    }

    public function read(Asset $asset): ?string
    {
        return $this->forKind($asset->kind)?->read($asset);
    }
}
