<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\TransactionPinController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MoneyTransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get("/test",function(){
    // echo generateAccountNumber();
});

// Login
Route::post('login', [LoginController::class, 'login'])->name("login");
Route::post('password/reset', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name("password-reset");
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name("password.reset");

// Registration
Route::post("register",[RegisterController::class,"register"])->name("register");
Route::post("verify-account",[RegisterController::class,"verify_account"])->name("verify_account");

// Create Pin
Route::post("create-transaction-pin",[TransactionPinController::class,"create"]);
// Reset Transaction Pin
Route::post("reset-transaction-pin",[TransactionPinController::class,"reset"]);

// Fetch user information
Route::get("user/{id}",[UserController::class,"show"]);


// Transfer Money
Route::post("money-transfer",[MoneyTransferController::class,"transfer"]);

