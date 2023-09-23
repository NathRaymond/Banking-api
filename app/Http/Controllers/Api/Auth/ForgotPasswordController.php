<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return API_Response(500, $validator->errors());
    }

    $user = User::where('email', $request->input('email'))->first();

    if (!$user) {
        return API_Response(500, ['error' => 'Email does not exist']); 
    }

    $status = Password::sendResetLink(
        $request->only('email')
    );

    if ($status === Password::RESET_LINK_SENT) {
        return API_Response(200, ['message' => 'Reset link sent to your email']);
    } else {
        return API_Response(500, ['error' => 'Unable to send reset link']);
    }
}

    
}
