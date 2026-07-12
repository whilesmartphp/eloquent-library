<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Whilesmart\Agents\Contracts\Tool;
use Whilesmart\Agents\Registries\ToolRegistry;
use Whilesmart\Agents\ValueObjects\ToolContext;
use Whilesmart\Library\Models\Asset;
use Whilesmart\Library\Models\Collection;

class LibraryToolsTest extends TestCase
{
    private const OWNER = 'App\\Models\\Workspace';

    private function tool(string $name): Tool
    {
        return $this->app->make(ToolRegistry::class)->resolve($name);
    }

    private function context(int $ownerId = 1): ToolContext
    {
        return new ToolContext(scope: [
            'library_owner_type' => self::OWNER,
            'library_owner_id' => $ownerId,
        ]);
    }

    private function note(int $ownerId, ?int $collectionId, string $title, string $body): Asset
    {
        return Asset::create([
            'owner_type' => self::OWNER,
            'owner_id' => $ownerId,
            'library_collection_id' => $collectionId,
            'kind' => 'note',
            'title' => $title,
            'body' => $body,
        ]);
    }

    #[Test]
    public function the_library_tools_are_registered_with_the_agent_registry(): void
    {
        $registry = $this->app->make(ToolRegistry::class);

        $this->assertTrue($registry->has('library.list'));
        $this->assertTrue($registry->has('library.read'));
    }

    #[Test]
    public function list_returns_the_owners_assets(): void
    {
        $this->note(1, null, 'Tone', 'Friendly and concise.');
        $this->note(1, null, 'Values', 'Honest, direct.');

        $result = $this->tool('library.list')->handle([], $this->context());

        $this->assertIsArray($result);
        $titles = array_column($result['assets'], 'title');
        $this->assertEqualsCanonicalizing(['Tone', 'Values'], $titles);
    }

    #[Test]
    public function list_scopes_to_a_collection_when_given(): void
    {
        $bucket = Collection::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'name' => 'Bucket']);
        $inBucket = $this->note(1, $bucket->id, 'In bucket', 'x');
        $this->note(1, null, 'Loose', 'y');

        $result = $this->tool('library.list')->handle(['collection_id' => $bucket->id], $this->context());

        $ids = array_column($result['assets'], 'id');
        $this->assertSame([$inBucket->id], $ids);
    }

    #[Test]
    public function list_scopes_to_explicit_asset_ids(): void
    {
        $a = $this->note(1, null, 'Keep', 'x');
        $this->note(1, null, 'Drop', 'y');

        $result = $this->tool('library.list')->handle(['asset_ids' => [$a->id]], $this->context());

        $this->assertSame([$a->id], array_column($result['assets'], 'id'));
    }

    #[Test]
    public function list_filters_by_kind(): void
    {
        $this->note(1, null, 'A note', 'x');
        Asset::create([
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'kind' => 'offering',
            'title' => 'A plan',
            'metadata' => ['price' => 10, 'currency' => 'USD'],
        ]);

        $result = $this->tool('library.list')->handle(['kind' => 'offering'], $this->context());

        $this->assertSame(['A plan'], array_column($result['assets'], 'title'));
    }

    #[Test]
    public function read_returns_a_notes_body(): void
    {
        $note = $this->note(1, null, 'Tone', 'Friendly and concise.');

        $result = $this->tool('library.read')->handle(['id' => $note->id], $this->context());

        $this->assertSame([
            'id' => $note->id,
            'title' => 'Tone',
            'body' => 'Friendly and concise.',
        ], $result);
    }

    #[Test]
    public function another_owners_asset_is_not_listable(): void
    {
        $mine = $this->note(1, null, 'Mine', 'x');
        $this->note(2, null, 'Theirs', 'secret');

        $result = $this->tool('library.list')->handle([], $this->context(1));

        $this->assertSame([$mine->id], array_column($result['assets'], 'id'));
    }

    #[Test]
    public function another_owners_asset_is_not_readable_even_by_id(): void
    {
        $theirs = $this->note(2, null, 'Theirs', 'secret');

        $result = $this->tool('library.read')->handle(['id' => $theirs->id], $this->context(1));

        $this->assertSame("No library asset with id {$theirs->id}.", $result);
    }
}
