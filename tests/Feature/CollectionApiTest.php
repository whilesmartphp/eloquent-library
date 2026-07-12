<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Whilesmart\Library\Models\Collection;

class CollectionApiTest extends TestCase
{
    private const OWNER = 'App\\Models\\Workspace';

    #[Test]
    public function it_creates_a_collection(): void
    {
        $this->postJson('/api/library/collections', [
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'name' => 'Brand assets',
            'description' => 'Everything on-brand',
            'metadata' => ['color' => '#fff'],
        ])->assertCreated()
            ->assertJsonPath('data.name', 'Brand assets')
            ->assertJsonPath('data.metadata.color', '#fff');

        $this->assertDatabaseHas('library_collections', [
            'owner_id' => 1,
            'name' => 'Brand assets',
        ]);
    }

    #[Test]
    public function it_filters_collections_by_owner(): void
    {
        Collection::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'name' => 'Mine']);
        Collection::create(['owner_type' => self::OWNER, 'owner_id' => 2, 'name' => 'Theirs']);

        $this->getJson('/api/library/collections?owner_type='.urlencode(self::OWNER).'&owner_id=1')
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.name', 'Mine');
    }

    #[Test]
    public function it_updates_and_deletes_a_collection(): void
    {
        $collection = Collection::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'name' => 'Draft']);

        $this->putJson("/api/library/collections/{$collection->id}", ['name' => 'Final'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Final');

        $this->deleteJson("/api/library/collections/{$collection->id}")->assertOk();

        $this->assertSoftDeleted('library_collections', ['id' => $collection->id]);
    }
}
