<?php

use Illuminate\Support\Facades\Route;
use Whilesmart\Library\Http\Controllers\AssetController;
use Whilesmart\Library\Http\Controllers\CollectionController;
use Whilesmart\Library\Http\Controllers\FolderController;

Route::prefix('library')->group(function () {
    Route::apiResource('collections', CollectionController::class);
    Route::apiResource('folders', FolderController::class);
    Route::apiResource('assets', AssetController::class);
});
