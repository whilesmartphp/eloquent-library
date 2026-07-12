<?php

namespace Whilesmart\Library\Presenters;

use Whilesmart\Library\Models\Asset;

/**
 * A "profile" is a person or brand: identity fields live in metadata (role,
 * bio, links), so listing exposes them and reading renders a plain-text bio.
 */
class ProfileAssetPresenter implements LibraryAssetPresenter
{
    public function supports(string $kind): bool
    {
        return $kind === 'profile';
    }

    public function present(Asset $asset): array
    {
        $meta = $asset->metadata ?? [];

        return [
            'id' => $asset->id,
            'kind' => 'profile',
            'title' => $asset->title,
            'description' => $asset->description,
            'role' => $meta['role'] ?? null,
            'links' => $meta['links'] ?? [],
            'folder' => $asset->folder?->name,
        ];
    }

    public function read(Asset $asset): ?string
    {
        $meta = $asset->metadata ?? [];
        $lines = [];

        if ($asset->title) {
            $header = $asset->title;
            if (! empty($meta['role'])) {
                $header .= ' ('.$meta['role'].')';
            }
            $lines[] = $header;
        }

        if (! empty($meta['bio'])) {
            $lines[] = $meta['bio'];
        } elseif ($asset->description) {
            $lines[] = $asset->description;
        }

        if ($lines === []) {
            return $asset->body ?: null;
        }

        return implode("\n", $lines);
    }
}
