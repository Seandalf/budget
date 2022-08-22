<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Models\Permissions\Permission;
use App\Models\Permissions\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    // Permissions System
    Route::name('permissions.')->group(function () {
        // Permissions
        Route::name('permissions.')->group(function () {
            Route::put('/permissions/permissions/create', [PermissionController::class, 'create'])->can('create', Permission::class)->name('create');
            Route::patch('/permissions/permissions/update/{permission}', [PermissionController::class, 'update'])->can('update', 'permission')->name('update');
            Route::delete('/permissions/permissions/delete/{permission}', [PermissionController::class, 'delete'])->can('delete', 'permission')->name('delete');
            Route::get('/permissions/permissions/show/{permission}', [PermissionController::class, 'show'])->can('show', 'permission')->name('show');
        });

        // Roles
        Route::name('roles.')->group(function () {
            Route::put('/roles/roles/create', [RoleController::class, 'create'])->can('create', Role::class)->name('create');
            Route::patch('/roles/roles/update/{role}', [RoleController::class, 'update'])->can('update', 'role')->name('update');
            Route::delete('/roles/roles/delete/{role}', [RoleController::class, 'delete'])->can('delete', 'role')->name('delete');
            Route::get('/roles/roles/show/{role}', [RoleController::class, 'show'])->can('show', 'role')->name('show');
        });
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
