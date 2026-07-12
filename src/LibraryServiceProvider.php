<?php

namespace Whilesmart\Library;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Whilesmart\Library\Presenters\PresenterRegistry;

class LibraryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/library.php', 'library');

        $this->app->singleton(PresenterRegistry::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/library.php' => config_path('library.php'),
        ], 'library-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'library-migrations');

        if (config('library.register_routes', true)) {
            Route::middleware(config('library.route_middleware', ['api', 'auth:sanctum']))
                ->prefix(config('library.route_prefix', 'api'))
                ->group(__DIR__.'/../routes/api.php');
        }
    }
}
