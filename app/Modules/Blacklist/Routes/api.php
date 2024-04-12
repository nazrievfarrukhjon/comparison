<?php

use App\Modules\Blacklist\Controllers\WhitelistController;
use Illuminate\Support\Facades\Route;

// frauds
Route::namespace('Blacklist\Controllers')
    ->prefix('blacklists')
    ->group(function () {
        Route::get('/', [WhitelistController::class, 'index']);
        Route::post('/', [WhitelistController::class, 'store']);
        Route::delete('/{id}', [WhitelistController::class, 'delete']);
        Route::put('/{id}', [WhitelistController::class, 'update']);

        Route::put('/find', [WhitelistController::class, 'find']);
    });
