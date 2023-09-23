<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\UserController;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\MoneyTransferController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\TransactionPinController;
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

Route::get("/test", function () {
    return Flutterwave::generateReference();
});


#TODO GET LIST OF BANKS IN NIGERIA
Route::get("/list-of-banks", [BankController::class, "fetchAllBank"])->name('list-of-banks');;

#TODO GUEST
Route::name("guest")->group(function () {
    #TODO LOGIN
    Route::post('login', [LoginController::class, 'login'])->name("login");
    #TODO REGISTRATION
    Route::post("register", [RegisterController::class, "register"])->name("register");
    #TODO ACCOUNT VERIFICATION
    Route::post("verify-account", [RegisterController::class, "verify_account"])->name("verify_account");
    #TODO RESEND CODE
    Route::post("resend-verification-code", [RegisterController::class, "resend_verification_code"])->name("resend_verification_code");
    #TODO FORGET PASSWORD
    Route::post("password/email", [ForgotPasswordController::class, "sendResetCodeEmail"]);
    #TODO RESET PASSWORD
    Route::post("reset-password", [ForgotPasswordController::class, "submitResetCode"]);
    #TODO RESEND CODE
    Route::post("password/resend-code", [ForgotPasswordController::class, "resendResetCode"])->name('password.resend-code');
});



#TODO AUTHORIZE
Route::middleware('auth:api')->group(function () {
    #TODO GET AUTH USER
    Route::get("fetch-auth-user", [UserController::class, "fetchAuthUser"])->name("getAuthUser");
    #TODO TRANSFER
    Route::post("/verify-bank-account", [MoneyTransferController::class, "verify_account"])->name("verify_account");
});


// Create Pin
Route::post("create-transaction-pin", [TransactionPinController::class, "create"]);
// Reset Transaction Pin
Route::post("reset-transaction-pin", [TransactionPinController::class, "reset"]);

// Fetch user information

// Transfer Money
