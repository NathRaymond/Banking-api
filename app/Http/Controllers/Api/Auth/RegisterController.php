<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyAccountMail;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => "required|alpha",
            "last_name" => "required|alpha",
            "email" => "required|email",
            "password" => "required|min:8",
            "confirm_password" => "required|same:password",
        ]);

        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->messages()->first()
            ], $validator->errors());
        } else {
            $user = new User();
            $user->first_name = ucfirst($request->first_name);
            $user->last_name = ucfirst($request->last_name);
            $user->email = ($request->email);
            $user->account_no = generateAccountNumber();
            $user->email = ($request->email);
            $user->password = Hash::make($request->password);
            if ($user->save()) {
                $wallet = new Wallet();
                $wallet->user_id = $user->id;
                $wallet->save();
                Mail::to($request->email)->send(new VerifyAccountMail($user));
                return API_Response(200, [
                    "message" => "Successfully registered account! please verify your account",
                    "data" => [
                        "user_id" =>  $user->id
                    ],
                    "redirect" => "verify_account"
                ]);
            } else {
                return API_Response(500, [
                    "message" => "Something went wrong! please try again"
                ]);
            }
        }
    }

    public function verify_account(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "code" => "required",
            "user_id" => "required",
        ]);

        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->messages()->first()
            ], $validator->errors());
        } else {
            $user = User::find($request->user_id);
            if ($user) {
                if ($user->verification_code == $request->code) {
                    $user->verified = 1;
                    $user->verification_code = null;
                    $user->save();
                    return API_Response(200, [
                        "message" => "Verification Success",
                        "redirect" => "dashboard"
                    ]);
                } else {
                    return API_Response(500, [
                        "message" => "Incorrect code"
                    ]);
                }
            } else {
                return API_Response(500, [
                    "message" => "Something went wrong. please try again"
                ]);
            }
        }
    }
}
