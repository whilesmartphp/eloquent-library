<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Whilesmart\Library\LibraryServiceProvider;
use Whilesmart\OwnerAccess\OwnerAccessServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            OwnerAccessServiceProvider::class,
            LibraryServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('library.route_middleware', ['api']);
    }
}
