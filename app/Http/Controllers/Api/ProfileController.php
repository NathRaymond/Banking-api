<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        // Validation rules for the new fields
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|alpha',
            'last_name' => 'required|alpha',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'username' => 'nullable|alpha_dash', 
            'd_o_b' => 'nullable|date', 
            'address' => 'nullable|string', 
            'next_of_kin' => 'nullable|string',
            'gender' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return API_Response(500, [
                'message' => $validator->messages()->first(),
            ], $validator->errors());
        }

        // Update the user's profile fields
        $user->first_name = ucfirst($request->input('first_name'));
        $user->last_name = ucfirst($request->input('last_name'));
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');

        // Update the new profile fields if provided
        $user->username = $request->input('username');
        $user->d_o_b = $request->input('d_o_b');
        $user->address = $request->input('address');
        $user->next_of_kin = $request->input('next_of_kin');
        $user->gender = $request->input('gender');
        $user->marital_status = $request->input('marital_status');
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture');
            $path = $profilePicture->store('profile_picture_folder', 'public');
            $user->profile_picture = $path;
        }

        if ($user->save()) {
            return API_Response(200, ['message' => 'Profile updated successfully']);
        } else {
            return API_Response(500, ['message' => 'Failed to update profile']);
        }
    }
}
