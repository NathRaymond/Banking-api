<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TransactionPin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TransactionPinController extends Controller
{
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        "pin" => "required|min:4|max:4|confirmed", 
        "pin_confirmation" => "required|min:4|max:4", 
    ]);

    if ($validator->fails()) {
        return API_Response(500, [
            "message" => $validator->errors()->first() 
        ]);
    }

    $user = auth()->user();

    // Store the PIN in the transaction_pin table
    $transactionPin = $user->transactionPin()->create([
        'pin' => Hash::make($request->pin),
    ]);

    if ($transactionPin) {
        return API_Response(200, [
            'message' => "Transaction PIN created successfully",
        ]);
    } else {
        return API_Response(500, ['message' => 'Failed to create Transaction PIN']);
    }
}

public function update(Request $request)
{
    $user = auth()->user();
    $oldPin = $user->transactionPin->pin;

    $validator = Validator::make($request->all(), [
        "current_pin" => "required|min:4",
        "pin" => [
            "required",
            "min:4",
            "max:4",
            "confirmed", // Ensure "pin" matches "pin_confirmation"
            function ($attribute, $value, $fail) use ($oldPin) {
                if (Hash::check($value, $oldPin)) {
                    $fail("New PIN cannot be the same as the old PIN.");
                }
            },
        ],
        "pin_confirmation" => "required|min:4|max:4",
    ]);

    if ($validator->fails()) {
        return API_Response(400, [
            "message" => $validator->errors()->first()
        ]);
    }

        // Check if the user has a transaction PIN
        $transactionPin = $user->transactionPin;

        if (!$transactionPin) {
            return API_Response(500, ['message' => 'Transaction PIN not found']);
        }

        // Verify if the current PIN matches
        if (!Hash::check($request->current_pin, $transactionPin->pin)) {
            return API_Response(500, ['message' => 'Current PIN is incorrect']);
        }

        // Update the PIN
        $transactionPin->pin = Hash::make($request->pin);
        $transactionPin->save();

        return API_Response(200, [
            'message' => "Transaction PIN updated successfully",
        ]);
    }

    // $validator = Validator::make($request->all(), [
    //     "current_pin" => "required|min:4",
    //     "pin" => "required|min:4|max:4|confirmed", 
    //     "pin_confirmation" => "required|min:4|max:4", 
    // ]);

    // if ($validator->fails()) {
    //     return API_Response(500, [
    //         "message" => $validator->errors()->first()
    //     ]);
    // }

    // $validator = Validator::make($request->all(), [
    //     "current_pin" => "required|min:4",
    //     "pin" => [
    //         "required",
    //         "min:4",
    //         "max:4",
    //         "confirmed", // Ensure "pin" matches "pin_confirmation"
    //         "not_same_as:" . auth()->user()->transactionPin->pin, // Check against the old PIN
    //     ],
    //     "pin_confirmation" => "required|min:4|max:4",
    // ]);

    // if ($validator->fails()) {
    //     return API_Response(500, [
    //         "message" => $validator->errors()->first()
    //     ]);
    // }

    // $user = auth()->user();
      



}
