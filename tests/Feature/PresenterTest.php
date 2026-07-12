<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Whilesmart\Library\Models\Asset;
use Whilesmart\Library\Presenters\PresenterRegistry;

class PresenterTest extends TestCase
{
    private const OWNER = 'App\\Models\\Workspace';

    private function registry(): PresenterRegistry
    {
        return $this->app->make(PresenterRegistry::class);
    }

    #[Test]
    public function a_note_presents_its_envelope_and_reads_its_body(): void
    {
        $asset = Asset::create([
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'kind' => 'note',
            'title' => 'Tone',
            'body' => 'Friendly and concise.',
        ]);

        $registry = $this->registry();

        $this->assertSame([
            'id' => $asset->id,
            'kind' => 'note',
            'title' => 'Tone',
            'description' => null,
            'folder' => null,
        ], $registry->present($asset));

        $this->assertSame('Friendly and concise.', $registry->read($asset));
    }

    #[Test]
    public function an_offering_presents_structured_fields_and_reads_a_summary(): void
    {
        $asset = Asset::create([
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'kind' => 'offering',
            'title' => 'Pro plan',
            'description' => 'For growing teams',
            'metadata' => [
                'price' => 49,
                'currency' => 'USD',
                'features' => ['priority support', 'unlimited seats'],
            ],
        ]);

        $registry = $this->registry();

        $presented = $registry->present($asset);
        $this->assertSame('offering', $presented['kind']);
        $this->assertSame(49, $presented['price']);
        $this->assertSame('USD', $presented['currency']);
        $this->assertSame(['priority support', 'unlimited seats'], $presented['features']);

        $read = $registry->read($asset);
        $this->assertStringContainsString('Pro plan', $read);
        $this->assertStringContainsString('Price: USD 49', $read);
        $this->assertStringContainsString('Features: priority support, unlimited seats', $read);
    }

    #[Test]
    public function an_unknown_kind_falls_through_to_the_default_presenter(): void
    {
        $asset = Asset::create([
            'owner_type' => self::OWNER,
            'owner_id' => 1,
            'kind' => 'whatever',
            'title' => 'Mystery',
            'body' => 'raw text',
            'metadata' => ['url' => 'https://example.test/file.pdf'],
        ]);

        $registry = $this->registry();

        $presented = $registry->present($asset);
        $this->assertSame('whatever', $presented['kind']);
        $this->assertSame('https://example.test/file.pdf', $presented['url']);
        $this->assertSame('raw text', $registry->read($asset));
    }
}
