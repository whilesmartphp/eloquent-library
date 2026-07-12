<?php

namespace Whilesmart\Library\Agents\Tools;

use Whilesmart\Agents\Enums\ParameterType;
use Whilesmart\Agents\Enums\ToolPermission;
use Whilesmart\Agents\Tools\AbstractTool;
use Whilesmart\Agents\ValueObjects\ParameterSpec;
use Whilesmart\Agents\ValueObjects\ToolContext;
use Whilesmart\Library\Models\Asset;
use Whilesmart\Library\Presenters\PresenterRegistry;

/**
 * Lists the library assets owned by the context owner as a single flat list,
 * each rendered through the package's PresenterRegistry so the tool never
 * branches on kind and new kinds appear automatically. Deterministic (plain
 * Eloquent, no LLM/HTTP). Owner-scoped: a model-supplied id can never reach
 * another owner's assets.
 */
class LibraryListTool extends AbstractTool
{
    public function __construct(private readonly PresenterRegistry $presenters) {}

    public function name(): string
    {
        return 'library.list';
    }

    public function description(): string
    {
        return 'List the owner\'s library assets as grounding context: notes, offerings, profiles and '
            .'other kinds, each presented with its id and summary fields. Call this first to discover what '
            .'is available, then use library.read to read an asset\'s full text by id.';
    }

    public function permission(): ToolPermission
    {
        return ToolPermission::READ;
    }

    public function parameters(): array
    {
        return [
            ParameterSpec::number('collection_id', 'Optional: restrict to a single collection (bucket) by id.', false),
            ParameterSpec::arrayOf('asset_ids', 'Optional: restrict to specific asset ids.', ParameterType::NUMBER, false),
            ParameterSpec::string('kind', 'Optional: only assets of this kind (e.g. "note", "offering").', false),
        ];
    }

    public function handle(array $arguments, ToolContext $context): string|array
    {
        $ownerType = (string) $context->get('library_owner_type', '');
        $ownerId = (int) $context->get('library_owner_id', 0);
        if ($ownerType === '' || $ownerId <= 0) {
            return 'No library is available in this context.';
        }

        /** @var class-string<Asset> $model */
        $model = config('library.models.asset', Asset::class);

        $query = $model::query()
            ->forOwnerKey($ownerType, $ownerId)
            ->with(['files', 'folder']);

        $assetIds = array_values(array_filter(array_map('intval', (array) ($arguments['asset_ids'] ?? []))));
        $collectionId = (int) ($arguments['collection_id'] ?? 0);

        // Scope priority: explicit asset ids -> a selected collection -> the whole owner library.
        if ($assetIds !== []) {
            $query->whereIn('id', $assetIds);
        } elseif ($collectionId > 0) {
            $query->where('library_collection_id', $collectionId);
        }

        $kind = trim((string) ($arguments['kind'] ?? ''));
        if ($kind !== '') {
            $query->where('kind', $kind);
        }

        $assets = $query->orderBy('sort_order')->get()
            ->map(fn (Asset $asset) => $this->presenters->present($asset))
            ->filter()
            ->values()
            ->all();

        if ($assets === []) {
            return 'The library has no assets'.($kind !== '' ? " of kind \"{$kind}\"." : '.');
        }

        return ['assets' => $assets];
    }
}
