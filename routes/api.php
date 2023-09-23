<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\TransactionPinController;
use App\Http\Controllers\Api\Auth\MoneyTransferController;
use App\Http\Controllers\Api\UserController;
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

Route::get("/test",function(){
    // echo generateAccountNumber();
});
*/

// Login
Route::post('login', [LoginController::class, 'login'])->name("login");
Route::post('password/reset', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name("password-reset");
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name("password.reset");


#TODO GUEST
Route::name("guest")->group(function () {
    #TODO REGISTRATION
    Route::post("register", [RegisterController::class, "register"])->name("register");
    #TODO ACCOUNT VERIFICATION
    Route::post("verify-account", [RegisterController::class, "verify_account"])->name("verify_account");
    #TODO RESEND CODE
    Route::post("resend-verification-code", [RegisterController::class, "resend_verification_code"])->name("resend_verification_code");
});

Route::get("fetch-user", [UserController::class, "index"])->name("getAuthUser");

#TODO AUTHORIZE
Route::middleware('auth:api')->group(function () {
    #TODO GET USER
});


// Create Pin
Route::post("create-transaction-pin", [TransactionPinController::class, "create"]);
// Reset Transaction Pin
Route::post("reset-transaction-pin", [TransactionPinController::class, "reset"]);

// Fetch user information

// Transfer Money
<<<<<<< HEAD
Route::post("money-transfer",[MoneyTransferController::class,"transfer"]);

=======
Route::post("money-transfer", [MoneyTransferController::class, "transfer"]);
>>>>>>> 2dc49209262c1e458b6fcef34f4a553dd6fa3030
