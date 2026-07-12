<?php

namespace Whilesmart\Library\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Whilesmart\Library\Models\Folder;

class FolderFactory extends Factory
{
    protected $model = Folder::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'sort_order' => 0,
        ];
    }
}
