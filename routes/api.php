<?php

use App\Models\Payee;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Permissions\Role;
use App\Models\RecurringTransaction;
use Illuminate\Support\Facades\Route;
use App\Models\Permissions\Permission;
use App\Http\Controllers\PayeeController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IntervalController;
use App\Http\Controllers\Permissions\RoleController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\Permissions\PermissionController;

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
            Route::put('/permissions/create', [PermissionController::class, 'store'])->can('create', Permission::class)->name('create');
            Route::patch('/permissions/update/{permission}', [PermissionController::class, 'update'])->can('update', 'permission')->name('update');
            Route::delete('/permissions/delete/{permission}', [PermissionController::class, 'destroy'])->can('delete', 'permission')->name('delete');
            Route::get('/permissions/show/{permission}', [PermissionController::class, 'show'])->can('view', 'permission')->name('show');
        });

        // Roles
        Route::name('roles.')->group(function () {
            Route::put('/roles/create', [RoleController::class, 'store'])->can('create', Role::class)->name('create');
            Route::patch('/roles/update/{role}', [RoleController::class, 'update'])->can('update', 'role')->name('update');
            Route::delete('/roles/delete/{role}', [RoleController::class, 'destroy'])->can('delete', 'role')->name('delete');
            Route::get('/roles/show/{role}', [RoleController::class, 'show'])->can('view', 'role')->name('show');
        });
    });

    // Budgets
    Route::name('budgets.')->group(function () {
        Route::put('/budgets/create', [BudgetController::class, 'store'])->can('create', Budget::class)->name('create');
        Route::patch('/budgets/update/{budget}', [BudgetController::class, 'update'])->can('update', 'budget')->name('update');
        Route::delete('/budgets/delete/{budget}', [BudgetController::class, 'destroy'])->can('delete', 'budget')->name('delete');
        Route::get('/budgets/show/{budget}', [BudgetController::class, 'show'])->can('view', 'budget')->name('show');
    });

    // Budgets
    Route::name('intervals.')->group(function () {
        Route::get('/budgets/show/{budget}/intervals', [IntervalController::class, 'index'])->can('view', 'budget')->name('index');
    });

    // Recurring Transactions
    Route::name('recurring-transactions.')->group(function () {
        Route::put('/recurring-transactions/create', [RecurringTransactionController::class, 'store'])->can('create', RecurringTransaction::class)->name('create');
        Route::patch('/recurring-transactions/update/{recurringTransaction}', [RecurringTransactionController::class, 'update'])->can('update', 'recurringTransaction')->name('update');
        Route::delete('/recurring-transactions/delete/{recurringTransaction}', [RecurringTransactionController::class, 'destroy'])->can('delete', 'recurringTransaction')->name('delete');
        Route::get('/recurring-transactions/show/{recurringTransaction}', [RecurringTransactionController::class, 'show'])->can('view', 'recurringTransaction')->name('show');
    });

    // Categories
    Route::name('categories.')->group(function () {
        Route::put('/categories/create', [CategoryController::class, 'store'])->can('create', Category::class)->name('create');
        Route::patch('/categories/update/{category}', [CategoryController::class, 'update'])->can('update', 'category')->name('update');
        Route::delete('/categories/delete/{category}', [CategoryController::class, 'destroy'])->can('delete', 'category')->name('delete');
        Route::get('/categories/show/{category}', [CategoryController::class, 'show'])->can('view', 'category')->name('show');
    });

    // Payees
    Route::name('payees.')->group(function () {
        Route::put('/payees/create', [PayeeController::class, 'store'])->can('create', Payee::class)->name('create');
        Route::patch('/payees/update/{payee}', [PayeeController::class, 'update'])->can('update', 'payee')->name('update');
        Route::delete('/payees/delete/{payee}', [PayeeController::class, 'destroy'])->can('delete', 'payee')->name('delete');
        Route::get('/payees/show/{payee}', [PayeeController::class, 'show'])->can('view', 'payee')->name('show');
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
