<?php

use App\Modules\Blacklist\Controllers\BlacklistController;
use Illuminate\Support\Facades\Route;

// frauds
Route::namespace('Blacklist\Controllers')
    ->middleware(['auth:sanctum'])
    ->prefix('blacklist')
    ->group(function () {
        Route::get('/', [BlacklistController::class, 'index']);
        Route::post('/', [BlacklistController::class, 'store']);
        Route::delete('/{id}', [BlacklistController::class, 'delete']);
        Route::put('/{id}', [BlacklistController::class, 'update']);

        Route::post('/find', [BlacklistController::class, 'find']);
    });
