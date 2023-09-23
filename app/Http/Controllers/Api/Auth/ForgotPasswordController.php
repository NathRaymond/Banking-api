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
                return response()->json([
                    'message' => $validator->messages()->first(),
                ], 500);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Email not found'
                ], 500);
            }

            $user->generatePasswordResetCode();

            try {
                Mail::to($user->email)->send(new PasswordResetMail($user->password_reset_code));
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed to send the email. Please try again later.'
                ], 500);
            }

            return response()->json([
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
            return response()->json([
                'message' => $validator->messages()->first(),
            ], 500);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email not found',
            ], 500);
        }

        if ($user->password_reset_code != $request->reset_code) {
            return response()->json([
                'message' => 'Invalid code',
            ], 500);
        }

        if ($user->password_reset_expires_at <= now()) {
            return response()->json([
                'message' => 'Reset code has expired',
            ], 500);
        }

        $user->update([
            'password' => bcrypt($request->password),
            'password_reset_code' => null,
            'password_reset_expires_at' => null,
        ]);

        return response()->json([
            'message' => 'Password reset successfully',
        ], 200);
    }


    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return API_Response(200, ['message' => 'We have emailed your password reset code!']);
        } else {
            return API_Response(500, ['message' => trans($response)]);
        }
    }


    protected function sendResetLinkResponse($response)
    {
        return API_Response(200, ['message' => 'We have emailed your password reset code!']);
    }


    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return API_Response(500, ['message' => trans($response)]);
    }


}
