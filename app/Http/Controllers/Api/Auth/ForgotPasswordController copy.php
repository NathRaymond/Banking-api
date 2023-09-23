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
            return API_Response(500, [
                "message" => $validator->messages()->first(),
            ], $validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return API_Response(404, ['message' => 'Email not found']);
        }

        $code = $user->generatePasswordResetCode();

        Mail::to($user->email)->send(new PasswordResetMail($code));

        return API_Response(200, ['message' => 'Reset code sent to your email']);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // "email" => "required|email",
            'code' => 'required|digits:4',
            "password" => "required|min:8",
            "confirm_password" => "required|same:password",
        ]);

        if ($validator->fails()) {
            return API_Response(500, [
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->password_reset_code !== $request->code) {
            return API_Response(500, ['message' => 'Invalid reset code']);
        }

        $user->update([
            'password' => bcrypt($request->password),
            'password_reset_code' => null,
        ]);

        return API_Response(200, ['message' => 'Password reset successfully']);
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
