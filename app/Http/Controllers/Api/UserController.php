<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\TransactionPin;
use App\Models\Wallet;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function fetchAuthUser(Request $request)
    {
        $user = $request->user();

        if ($user) {
            return API_Response(200, [
                "message" => $user
            ]);
        } else {
            return API_Response(500, [
                'message' => 'User unauthorize'
            ]);
        }

    }

    public function getAuthWallet(Request $request){
        $user = $request->user();
        $wallet = Wallet::where("user_id", $user->id)->first();
        if ($wallet) {
            return API_Response(200, [
                "message" => $wallet
            ]);
        } else {
            return API_Response(500, [
                'message' => "Something went wrong fetching user wallet"
            ]);
        }
    }

    public function checkIfUserHaveTransactionPin(Request $request){
        $transactionPin = TransactionPin::where("user_id", $request->user()->id)->first();
        if ($transactionPin) {
            return API_Response(200, [
                "message" => "User have transaction pin",
                "status" => true
            ]);
        } else {
            return API_Response(500, [
                'message' => "User do not have transaction pin",
                "status" => false
            ]);
        }
    }
}
