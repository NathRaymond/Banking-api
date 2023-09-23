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
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'login' => [
                'required',
                Rule::exists('users')->where(function ($query) use ($request) {
                    $query->where('email', $request->input('login'))
                        ->orWhere('phone_number', $request->input('login'));
                }),
            ],
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        $credentials = [
            'password' => $request->input('password'),
        ];
    
        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        $credentials[$loginField] = $request->input('login');
    
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
