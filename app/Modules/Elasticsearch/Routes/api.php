<?php

use App\Modules\Elasticsearch\Controllers\ElasticsearchController;
use Illuminate\Support\Facades\Route;

Route::namespace('ElasticSearch\Controllers')
    ->middleware(['auth:sanctum'])
    ->prefix('elasticsearch')
    ->group(function () {
        Route::prefix('index')->group(function () {
            Route::get('/', [ElasticsearchController::class, 'indexContent']);
            Route::get('/indices', [ElasticsearchController::class, 'indices']);

            Route::post('/', [ElasticsearchController::class, 'createIndex']);
            Route::delete('/{id}', [ElasticsearchController::class, 'delete']);
            Route::put('/{id}', [ElasticsearchController::class, 'update']);

            Route::post('/find', [ElasticsearchController::class, 'find']);
        });

        Route::prefix('document')->group(function () {
            Route::get('/', [ElasticsearchController::class, 'documents']);
            Route::post('/', [ElasticsearchController::class, 'addDocument']);
            Route::delete('/', [ElasticsearchController::class, 'deleteDocument']);
            Route::put('/', [ElasticsearchController::class, 'updateDocument']);

            Route::post('/exact-match', [ElasticsearchController::class, 'exactMatch']);
            Route::post('/fuzzy-match', [ElasticsearchController::class, 'fuzzyMatch']);
        });
    });
