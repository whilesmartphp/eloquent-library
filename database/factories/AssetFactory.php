<?php

namespace Whilesmart\Library\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Whilesmart\Library\Enums\AssetKind;
use Whilesmart\Library\Models\Asset;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'kind' => AssetKind::Note->value,
            'title' => $this->faker->words(3, true),
            'body' => $this->faker->paragraph(),
            'sort_order' => 0,
        ];
    }

    public function offering(): static
    {
        return $this->state(fn () => [
            'kind' => AssetKind::Offering->value,
            'body' => null,
            'metadata' => [
                'price' => $this->faker->numberBetween(10, 500),
                'currency' => 'USD',
                'features' => ['support', 'updates'],
            ],
        ]);
    }
}
