<?php

namespace Whilesmart\Library\Presenters;

use Whilesmart\Library\Models\Asset;

/**
 * An "offering" is a product or service: structured commercial fields live in
 * metadata (price, currency, features), so listing exposes them and reading
 * renders a plain-text summary.
 */
class OfferingAssetPresenter implements LibraryAssetPresenter
{
    public function supports(string $kind): bool
    {
        return $kind === 'offering';
    }

    public function present(Asset $asset): array
    {
        $meta = $asset->metadata ?? [];

        return [
            'id' => $asset->id,
            'kind' => 'offering',
            'title' => $asset->title,
            'description' => $asset->description,
            'price' => $meta['price'] ?? null,
            'currency' => $meta['currency'] ?? null,
            'features' => $meta['features'] ?? [],
            'folder' => $asset->folder?->name,
        ];
    }

    public function read(Asset $asset): ?string
    {
        $meta = $asset->metadata ?? [];
        $lines = [];

        if ($asset->title) {
            $lines[] = $asset->title;
        }

        if ($asset->description) {
            $lines[] = $asset->description;
        }

        if (isset($meta['price'])) {
            $currency = $meta['currency'] ?? '';
            $lines[] = trim('Price: '.trim($currency.' '.$meta['price']));
        }

        if (! empty($meta['features']) && is_array($meta['features'])) {
            $lines[] = 'Features: '.implode(', ', $meta['features']);
        }

        if ($lines === []) {
            return $asset->body ?: null;
        }

        return implode("\n", $lines);
    }
}
