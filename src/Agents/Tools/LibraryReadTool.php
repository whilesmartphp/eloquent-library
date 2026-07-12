<?php

namespace Whilesmart\Library\Agents\Tools;

use Whilesmart\Agents\Enums\ToolPermission;
use Whilesmart\Agents\Tools\AbstractTool;
use Whilesmart\Agents\ValueObjects\ParameterSpec;
use Whilesmart\Agents\ValueObjects\ToolContext;
use Whilesmart\Library\Models\Asset;
use Whilesmart\Library\Presenters\PresenterRegistry;

/**
 * Reads the full text payload of a library asset by id (from library.list) via
 * the package's PresenterRegistry: a note's body, an offering's summary, any
 * text-bearing kind. Deterministic, owner-scoped so a model-supplied id can
 * never read another owner's content.
 */
class LibraryReadTool extends AbstractTool
{
    public function __construct(private readonly PresenterRegistry $presenters) {}

    public function name(): string
    {
        return 'library.read';
    }

    public function description(): string
    {
        return 'Read the full text of a library asset by its id (from library.list) to ground the '
            .'response in the owner\'s own material.';
    }

    public function permission(): ToolPermission
    {
        return ToolPermission::READ;
    }

    public function parameters(): array
    {
        return [
            ParameterSpec::number('id', 'The asset id returned by library.list.'),
        ];
    }

    public function handle(array $arguments, ToolContext $context): string|array
    {
        $ownerType = (string) $context->get('library_owner_type', '');
        $ownerId = (int) $context->get('library_owner_id', 0);
        $assetId = (int) ($arguments['id'] ?? 0);
        if ($ownerType === '' || $ownerId <= 0 || $assetId <= 0) {
            return 'Provide a valid "id" from library.list.';
        }

        /** @var class-string<Asset> $model */
        $model = config('library.models.asset', Asset::class);

        $asset = $model::query()->forOwnerKey($ownerType, $ownerId)->find($assetId);
        if (! $asset) {
            return "No library asset with id {$assetId}.";
        }

        $text = $this->presenters->read($asset);
        if ($text === null || $text === '') {
            return "Asset {$assetId} (\"{$asset->title}\") has no readable text.";
        }

        return ['id' => $asset->id, 'title' => $asset->title, 'body' => $text];
    }
}
