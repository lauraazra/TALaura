<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionDetailsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LoginController::class, 'index'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

// Route grup role admin
Route::middleware(['admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index', [
            'title' => 'Dashboard',
        ]);
    });
    Route::resource('/dashboard/product', ProductController::class);
    Route::resource('/dashboard/record', RecordController::class);
    Route::get('/dashboard/product/show', [ProductController::class, 'show'])->name('product.show');
    Route::patch('/product/{product}/restore', [ProductController::class, 'restore'])->name('product.restore');
    Route::resource('/dashboard/users', UserController::class);
    Route::get('/login', function () {
        return redirect()->route('dashboard');
    });
    Route::get('/dashboard/transactiondetails', [TransactionDetailsController::class, 'index'])->name('transactiondetails.index');
    Route::get('/dashboard/transactiondetails/void', [TransactionDetailsController::class, 'void'])->name('transactiondetails.void');
    Route::get('/dashboard/transaction', [TransactionController::class, 'index'])->name('transaction.index');
    Route::post('/dashboard/transaction/store', [TransactionController::class, 'store'])->name('transaction.store');
    Route::get('/dashboard/transaction/success', [TransactionController::class, 'success'])->name('transaction.success');
    Route::get('/dashboard/transaction/{transaction}/detail', [TransactionController::class, 'show'])->name('transaction.show');
    Route::post('/dashboard/transaction/{transaction}/update', [TransactionController::class, 'update'])->name('transaction.update');
    Route::get('/dashboard/transaction/{transaction}/get-detail', [TransactionController::class, 'getDetail'])->name('transaction.get_detail');
    Route::get('/dashboard/transaction/{transaction}/invoice', [TransactionController::class, 'showPrint'])->name('transaction.show_print');
    Route::get('/dashboard/transaction/{transaction}/invoice/print', [TransactionController::class, 'getPrintUrl'])->name('transaction.print_url');
    Route::get('/dashboard/transaction/{transaction}/invoice/printV2', [TransactionController::class, 'printAsHtml'])->name('transaction.print_v2');
    Route::get('/dashboard/transaction/{transaction}/invoice/download', [TransactionController::class, 'downloadPrint'])->name('transaction.download_print');
    Route::get('/dashboard/transactiondetails/void', [TransactionDetailsController::class, 'void'])->name('transactiondetails.void');
    Route::post('/dashboard/transaction/{transaction}/void', [TransactionController::class, 'destroy'])->name('transaction.void');
    Route::post('/dashboard/transaction/{transaction}/unvoid', [TransactionController::class, 'restore'])->name('transaction.unvoid');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/login', function () {
        return redirect('/dashboard');
    });
    Route::put('/dashboard/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword');
    Route::patch('/dashboard/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
});