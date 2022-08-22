<?php

use App\Http\Controllers\HealthCheckController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route('web.dashboard'));
});

Route::get('/healthcheck', [HealthCheckController::class, 'index']);

Route::name('web.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');
});

Route::get('/testing/{name}', function ($name) {
    $perm = App\Models\Permissions\Permission::with('audits')->find(28);

    $perm->name = strrev($perm->name);
    $perm->save();
    dd('here');
});

require __DIR__.'/auth.php';
