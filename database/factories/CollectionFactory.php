<?php

namespace Whilesmart\Library\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Whilesmart\Library\Models\Collection;

class CollectionFactory extends Factory
{
    protected $model = Collection::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'sort_order' => 0,
        ];
    }
}
