<?php

namespace Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Whilesmart\Library\Models\Asset;
use Whilesmart\Library\Models\Collection;
use Whilesmart\OwnerAccess\Contracts\OwnerAuthorizer;

class AssetAuthorizationTest extends TestCase
{
    private const OWNER = 'App\\Models\\Workspace';

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(OwnerAuthorizer::class, new class implements OwnerAuthorizer
        {
            public function authorize(?Authenticatable $user, string $ownerType, mixed $ownerId): bool
            {
                return false;
            }

            public function scope(Builder $query, ?Authenticatable $user, string $ownerTypeColumn = 'owner_type', string $ownerIdColumn = 'owner_id'): Builder
            {
                return $query->whereRaw('0 = 1');
            }
        });
    }

    #[Test]
    public function store_is_forbidden_when_authorizer_denies(): void
    {
        $this->postJson('/api/library/assets', [
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'kind' => 'note',
            'title' => 'Hijacked',
        ])->assertForbidden();

        $this->assertDatabaseCount('library_assets', 0);
    }

    #[Test]
    public function show_update_and_destroy_are_forbidden_when_authorizer_denies(): void
    {
        $asset = Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'kind' => 'note', 'title' => 'Private']);

        $this->getJson("/api/library/assets/{$asset->id}")->assertForbidden();
        $this->putJson("/api/library/assets/{$asset->id}", ['title' => 'Hijacked'])->assertForbidden();
        $this->deleteJson("/api/library/assets/{$asset->id}")->assertForbidden();

        $this->assertSame('Private', $asset->fresh()->title);
    }

    #[Test]
    public function collection_store_is_forbidden_when_authorizer_denies(): void
    {
        $this->postJson('/api/library/collections', [
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'name' => 'Hijacked',
        ])->assertForbidden();

        $this->assertDatabaseCount('library_collections', 0);
    }

    #[Test]
    public function index_returns_nothing_when_scope_denies(): void
    {
        Collection::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'name' => 'Private']);
        Asset::create(['owner_type' => self::OWNER, 'owner_id' => 1, 'kind' => 'note', 'title' => 'Private']);

        $this->getJson('/api/library/assets')
            ->assertOk()
            ->assertJsonPath('data.meta.total', 0);

        $this->getJson('/api/library/collections')
            ->assertOk()
            ->assertJsonPath('data.meta.total', 0);
    }
}
