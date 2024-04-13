<?php

use App\Modules\Admin\Controllers\AuthController;
use App\Modules\Admin\Controllers\PrivilegeController;
use App\Modules\Admin\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//auth
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')
    ->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
//
Route::middleware(['auth:sanctum'])->group(function () {
//admin
    Route::middleware(['can:admin'])
        ->prefix('admin')
        ->group(function () {
// auth
            Route::post('register', [AuthController::class, 'register']);

            Route::prefix('users')
                ->group(function () {
                    Route::get('', [UserController::class, 'index']);
                    Route::post('', [UserController::class, 'store']);
                    Route::patch('/{id}', [UserController::class, 'update']);
                    Route::delete('/{id}', [UserController::class, 'delete']);
                });

            Route::prefix('privileges')
                ->group(function () {

                    Route::post('assign-role-to-user/{role_id}/{user_id}', [PrivilegeController::class, 'assignRoleToUser']);
                    Route::post('assign-permission-to-user/{permission_id}/{user_id}', [PrivilegeController::class, 'assignPermissionToUser']);
                    Route::post('assign-permission-to-role/{permission_id}/{role_id}', [PrivilegeController::class, 'assignPermissionToRole']);

                    //
                    Route::prefix('roles')
                        ->group(function () {
                            Route::get('', [PrivilegeController::class, 'getRoles']);
                            Route::post('', [PrivilegeController::class, 'storeRole']);
                            Route::put('', [PrivilegeController::class, 'updateRole']);
                            Route::delete('', [PrivilegeController::class, 'destroyRole']);
                        });
                    //
                    Route::prefix('permissions')
                        ->group(function () {
                            Route::get('', [PrivilegeController::class, 'getPermission']);
                            Route::post('', [PrivilegeController::class, 'storePermission']);
                            Route::put('', [PrivilegeController::class, 'updatePermission']);
                            Route::delete('', [PrivilegeController::class, 'destroyPermission']);
                        });
                });
        });
});
