<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required",
            "password" => "required",
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->verified == 1) {
                return response()->json(['message' => 'Login successful']);
            } else {
                Auth::logout();
                return response()->json(['error' => 'User has not been verified']);
            }
        } else {
            return response()->json(['error' => 'Invalid credentials']);
        }
    }
}
