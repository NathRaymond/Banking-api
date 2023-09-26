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
use App\Http\Controllers\Api\TransactionPinController;
use App\Http\Controllers\Api\AirtimeController;
use App\Http\Controllers\Api\DataRechargeController;
use App\Http\Controllers\Api\ElectricityRechargeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\Api\AccountUpgradeController;
use App\Http\Controllers\Api\AccontMonitoringController;
use App\Http\Controllers\Api\NotificationController;
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
    // return Flutterwave::generateReference();
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
    #TODO GET USER WALLER
    Route::get("fetch-auth-user-wallet", [UserController::class, "getAuthWallet"])->name("getAuthWallet");
    #TODO CHECK USER HAVE TRANSACTION PIN
    Route::get("check-user-have-transaction-pin", [UserController::class, "checkIfUserHaveTransactionPin"])->name("checkIfUserHaveTransactionPin");
    #TODO GET TRANSACTION HISTORY
    Route::get("fetch-transaction-history", [UserController::class, "fetchTransactionHistory"])->name("fetchTransactionHistory");

    #TODO UPDATE USER PROFILE
    Route::post("update-profile", [ProfileController::class, "update"]);

    #TODO CREATE TRANSACTION PIN
    Route::post("store-transaction-pin", [TransactionPinController::class, "store"]);
    #TODO UPDATE TRANSACTION PIN
    Route::post("update-transaction-pin", [TransactionPinController::class, "update"]);

    #TODO VERIFY ACCOUNT NUMBER
    Route::post("/verify-bank-account", [MoneyTransferController::class, "verify_account"])->name("verify_account");
    Route::post("/verify-account-number-internal", [MoneyTransferController::class, "verify_account_internal"])->name("verify_account_internal");
    #TODO TRANSFER
    Route::post("/create-recipient", [MoneyTransferController::class, "createRecipient"])->name("create_recipient");
    #TODO INITIATE TRANSFER
    Route::post("/initiate-transfer", [MoneyTransferController::class, "initiateTransfer"])->name("initiate_transfer");
    Route::post("/initiate-transfer-internal", [MoneyTransferController::class, "initiateTransferInternal"])->name("initiate_transfer");
    #TODO VERIFY TRANSFER
    Route::get("/verify-transfer/{transfer_code}", [MoneyTransferController::class, "verifyTransfer"])->name("verify_transfer");
    Route::get("/verify-transfer-internal/{transfer_code}", [MoneyTransferController::class, "verifyTransferInternal"])->name("verify_transfer_internal");


    #TODO STORE BENEFICIARY
    Route::post("store-beneficiaries", [BeneficiaryController::class, "store"]);
    #TODO List BENEFICIARY
    Route::get("list-beneficiaries", [BeneficiaryController::class, "index"]);
    #TODO SHOW BENEFICIARY
    Route::get("show-beneficiaries/{id}", [BeneficiaryController::class, "show"]);
    #TODO UPDATE BENEFICIARY
    Route::post("update-beneficiaries/{id}", [BeneficiaryController::class, "update"]);
    #TODO DELETE BENEFICIARY
    Route::delete("delete-beneficiaries/{id}", [BeneficiaryController::class, "destroy"]);

    #TODO ACCOUNT UPGRADE VERIFICATION DOCUMENT
    Route::post("upload-document", [AccountUpgradeController::class, "update"]);

    #TODO RESTRICT ACCOUNT
    Route::post("restrict-account", [AccontMonitoringController::class, "restrict"]);
    #TODO CLOSE ACCOUNT
    Route::post("close-account", [AccontMonitoringController::class, "close"]);

    #TODO GET NOTIFICATION
    Route::get("notifications", [NotificationController::class, "fetchNotification"]);

});

    // #TODO RECHARGE AIRTIME
    // Route::post("recharge-airtime", [AirtimeController::class, "recharge_airtime"]);
    // #TODO RECHARGE DATA
    // Route::post("recharge-data", [DataRechargeController::class, "recharge_data"]);
    // #TODO RECHARGE ELECTRICITY
    // Route::post("recharge-electricity", [ElectricityRechargeController::class, "recharge_electricity"]);
