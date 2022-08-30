<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\PayeeController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\TransactionController;
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

Route::middleware(['auth', 'verified'])->name('web.')->group(function () {
    Route::get('/dashboard', function () { return Inertia::render('Dashboard'); })->name('dashboard');

    // Budgets
    Route::name('budgets.')->prefix('/budgets')->group(function () {
        Route::get('/', [BudgetController::class, 'index'])->name('index');
        Route::get('/create', [BudgetController::class, 'create'])->name('create');
        Route::get('/edit/{budget}', [BudgetController::class, 'edit'])->name('edit');
        Route::get('/view/{budget}', [BudgetController::class, 'show'])->name('view');
    });

    // Categories
    Route::name('categories.')->prefix('/categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::get('/edit/{category}', [CategoryController::class, 'edit'])->name('edit');
        Route::get('/view/{category}', [CategoryController::class, 'show'])->name('view');
    });

    // Payees
    Route::name('payees.')->prefix('/payees')->group(function () {
        Route::get('/', [PayeeController::class, 'index'])->name('index');
        Route::get('/create', [PayeeController::class, 'create'])->name('create');
        Route::get('/edit/{payee}', [PayeeController::class, 'edit'])->name('edit');
        Route::get('/view/{payee}', [PayeeController::class, 'show'])->name('view');
    });

    // Recurring Transactions
    Route::name('recurring-transactions.')->prefix('/recurring-transactions')->group(function () {
        Route::get('/', [RecurringTransactionController::class, 'index'])->name('index');
        Route::get('/create', [RecurringTransactionController::class, 'create'])->name('create');
        Route::get('/edit/{recurringTransaction}', [RecurringTransactionController::class, 'edit'])->name('edit');
        Route::get('/view/{recurringTransaction}', [RecurringTransactionController::class, 'show'])->name('view');
    });

    // Transactionscategories
    Route::name('transactions.')->prefix('/transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::get('/edit/{transaction}', [TransactionController::class, 'edit'])->name('edit');
        Route::get('/view/{transaction}', [TransactionController::class, 'show'])->name('view');
    });
});

require __DIR__.'/auth.php';
