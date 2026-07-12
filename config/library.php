<?php

use Whilesmart\Library\Models\Asset;
use Whilesmart\Library\Models\Collection;
use Whilesmart\Library\Models\Folder;
use Whilesmart\Library\Presenters\DefaultAssetPresenter;
use Whilesmart\Library\Presenters\NoteAssetPresenter;
use Whilesmart\Library\Presenters\OfferingAssetPresenter;
use Whilesmart\Library\Presenters\ProfileAssetPresenter;

return [
    'register_routes' => env('LIBRARY_REGISTER_ROUTES', true),
    'route_prefix' => env('LIBRARY_ROUTE_PREFIX', 'api'),
    'route_middleware' => ['api', 'auth:sanctum'],

    'collections_table' => env('LIBRARY_COLLECTIONS_TABLE', 'library_collections'),
    'folders_table' => env('LIBRARY_FOLDERS_TABLE', 'library_folders'),
    'assets_table' => env('LIBRARY_ASSETS_TABLE', 'library_assets'),

    // Swap any model for a host subclass without touching the package.
    'models' => [
        'collection' => Collection::class,
        'folder' => Folder::class,
        'asset' => Asset::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Library asset presenters
    |--------------------------------------------------------------------------
    |
    | Maps an asset's open "kind" to the presenter that turns it into what a
    | consumer reads. Add a kind by adding a presenter class here; the registry
    | resolves them in order and never branches on kind itself. The default
    | presenter matches every kind, so keep it last.
    |
    */

    'presenters' => [
        NoteAssetPresenter::class,
        OfferingAssetPresenter::class,
        ProfileAssetPresenter::class,
        DefaultAssetPresenter::class,
    ],
];
