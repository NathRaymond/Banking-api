<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\PasswordResetMail;



class ForgotPasswordController extends Controller
{

    public function sendResetCodeEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return API_Response([
                'message' => $validator->messages()->first(),
            ], 500);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return API_Response([
                'message' => 'Email not found'
            ], 500);
        }

        $user->generatePasswordResetCode();

        try {
            Mail::to($user->email)->send(new PasswordResetMail($user->password_reset_code));
        } catch (\Exception $e) {
            return API_Response([
                'message' => 'Failed to send the email. Please try again later.'
            ], 500);
        }

        return API_Response([
            'message' => 'Reset code sent to your email'
        ], 200);
    }

    public function submitResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'reset_code' => 'required|digits:4',
            "password" => "required|min:8",
            "confirm_password" => "required|same:password",
        ]);

        if ($validator->fails()) {
            return API_Response([
                'message' => $validator->messages()->first(),
            ], 500);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return API_Response([
                'message' => 'Email not found',
            ], 500);
        }

        if ($user->password_reset_code != $request->reset_code) {
            return API_Response([
                'message' => 'Invalid code',
            ], 500);
        }

        if ($user->password_reset_expires_at <= now()) {
            return API_Response([
                'message' => 'Reset code has expired',
            ], 500);
        }

        $user->update([
            'password' => bcrypt($request->password),
            'password_reset_code' => null,
            'password_reset_expires_at' => null,
        ]);

        return API_Response([
            'message' => 'Password reset successfully',
        ], 200);
    }

    public function resendResetCode(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return API_Response([
            'status' => 500,
            'data' => [
                'message' => $validator->messages()->first(),
            ],
        ]);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return API_Response([
            'status' => 500,
            'data' => ['message' => 'Email not found'],
        ]);
    }

    $code = $user->generatePasswordResentCode();

    Mail::to($user->email)->send(new PasswordResetMail($code));

    return API_Response([
        'status' => 200,
        'data' => ['message' => 'Reset code sent to your email'],
    ]);
}


}
