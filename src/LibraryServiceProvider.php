<?php

namespace Whilesmart\Library;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Whilesmart\Agents\Facades\Agents;
use Whilesmart\Agents\Tools\AbstractTool;
use Whilesmart\Library\Agents\Tools\LibraryListTool;
use Whilesmart\Library\Agents\Tools\LibraryReadTool;
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

        $this->registerAgentTools();
    }

    /**
     * Expose the library to eloquent-agents as grounding tools, but only when
     * that (optional) package is installed. Guarding on the base tool class
     * keeps the tool classes from ever autoloading when the framework is absent.
     */
    protected function registerAgentTools(): void
    {
        if (! class_exists(AbstractTool::class)) {
            return;
        }

        Agents::registerTool(LibraryListTool::class);
        Agents::registerTool(LibraryReadTool::class);
    }
}
