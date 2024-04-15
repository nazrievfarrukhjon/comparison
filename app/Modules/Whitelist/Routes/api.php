<?php

use App\Modules\Whitelist\Controllers\WhitelistController;
use Illuminate\Support\Facades\Route;

// frauds
Route::namespace('Whitelist\Controllers')
    ->middleware(['auth:sanctum'])
    ->prefix('whitelist')
    ->group(function () {
        Route::get('/', [WhitelistController::class, 'index']);
        Route::post('/', [WhitelistController::class, 'store']);
        Route::delete('/{id}', [WhitelistController::class, 'delete']);
        Route::put('/{id}', [WhitelistController::class, 'update']);

        Route::put('/find', [WhitelistController::class, 'find']);
    });
