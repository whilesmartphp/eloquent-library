<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Whilesmart\Library\Models\Asset;
use Whilesmart\Library\Models\Collection;

class AssetApiTest extends TestCase
{
    private const OWNER = 'App\\Models\\Workspace';

    private function collection(int $ownerId = 1): Collection
    {
        return Collection::create(['owner_type' => self::OWNER, 'owner_id' => $ownerId, 'name' => 'Bucket']);
    }

    #[Test]
    public function it_creates_a_note_asset(): void
    {
        $collection = $this->collection();

        $this->postJson('/api/library/assets', [
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'library_collection_id' => $collection->id,
            'kind' => 'note',
            'title' => 'Tone of voice',
            'body' => 'Friendly and concise.',
        ])->assertCreated()
            ->assertJsonPath('data.kind', 'note')
            ->assertJsonPath('data.title', 'Tone of voice')
            ->assertJsonPath('data.body', 'Friendly and concise.');

        $this->assertDatabaseHas('library_assets', [
            'library_collection_id' => $collection->id,
            'kind' => 'note',
            'title' => 'Tone of voice',
        ]);
    }

    #[Test]
    public function it_defaults_kind_to_note_when_omitted(): void
    {
        $this->postJson('/api/library/assets', [
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'title' => 'Untyped',
        ])->assertCreated()
            ->assertJsonPath('data.kind', 'note');
    }

    #[Test]
    public function it_filters_assets_by_collection_and_kind(): void
    {
        $collection = $this->collection();

        Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'library_collection_id' => $collection->id, 'kind' => 'note', 'title' => 'A note']);
        Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'library_collection_id' => $collection->id, 'kind' => 'offering', 'title' => 'A plan']);

        $this->getJson('/api/library/assets?library_collection_id='.$collection->id.'&kind=offering')
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.title', 'A plan');
    }

    #[Test]
    public function it_updates_and_deletes_an_asset(): void
    {
        $asset = Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'kind' => 'note', 'title' => 'Draft', 'body' => 'old']);

        $this->putJson("/api/library/assets/{$asset->id}", ['body' => 'new'])
            ->assertOk()
            ->assertJsonPath('data.body', 'new');

        $this->deleteJson("/api/library/assets/{$asset->id}")->assertOk();

        $this->assertSoftDeleted('library_assets', ['id' => $asset->id]);
    }
}
