<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
        public function login(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'login' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return API_Response(500, [], $validator->errors());
            }

            $credentials = [
                'password' => $request->input('password'),
            ];

            $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
            $credentials[$loginField] = $request->input('login');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                if ($user->verified == 1) {
                    return API_Response(200, ['message' => 'Login successful']);
                } else {
                    Auth::logout();
                    return API_Response(500, ['message' => 'User has not been verified']);
                }
            } else {
                return API_Response(500, ['message' => 'Invalid credentials']);
            }
        }
    }
