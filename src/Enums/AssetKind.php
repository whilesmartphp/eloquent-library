<?php

namespace Whilesmart\Library\Enums;

/**
 * Suggested asset kinds used for validation and the UI catalog. The model
 * stores `kind` as a plain string, so host apps can introduce new kinds
 * without a schema or enum change.
 */
enum AssetKind: string
{
    case Note = 'note';
    case Image = 'image';
    case Offering = 'offering';
    case Profile = 'profile';
    case Snippet = 'snippet';
    case Link = 'link';

    public static function values(): array
    {
        return array_map(fn (self $k) => $k->value, self::cases());
    }
}
