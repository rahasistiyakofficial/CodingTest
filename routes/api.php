<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BankingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/users', [UserController::class, 'createUser']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/transactions', [BankingController::class, 'showTransactionsAndBalance']);
    Route::get('/deposit/transactions', [BankingController::class, 'showDepositedTransactions']);
    Route::post('/deposit', [BankingController::class, 'deposit']);
    Route::get('/withdrawal/transactions', [BankingController::class, 'showWithdrawalTransactions']);
    Route::post('/withdrawal', [BankingController::class, 'withdrawal']);
});