<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\VerifyAccountMail;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->messages()->first()
            ], $validator->errors());
        } else {
            $user = User::where("email", $request->email)->first();
            if ($user) {
                if (Hash::check($user->password, $request->password)) {
                    if ($user->verified != 1) {
                        $user->verification_code = rand(10000, 99999);
                        $user->save();
                        Mail::to($request->email)->send(new VerifyAccountMail($user));
                        return API_Response(200, [
                            'message' =>  "Successfully login! please verify your account",
                            'redirect' => "verify_account"
                        ]);
                    } else {
                        $auth_token = $user->createToken("MyApp")->accessToken;
                        return API_Response(200, [
                            'message' => 'Login successful',
                            'redirect' => 'dashboard',
                            "token" => $auth_token
                        ]);
                    }
                } else {
                    return API_Response(500, ['message' => 'Invalid email or password']);
                }
            } else {
                return API_Response(500, ['message' => 'Invalid email or password']);
            }
        }
    }
}
