<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
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
Route::post('login', [LoginController::class, 'login'])->name("login");

Route::post("register",[RegisterController::class,"register"])->name("register");
Route::post("verify-account",[RegisterController::class,"verify_account"])->name("verify_account");
