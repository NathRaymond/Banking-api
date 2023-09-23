<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TransactionPin;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;

class TransactionPinController extends Controller
{
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_pin' => 'required|min:4',
            'confirm_pin' => 'required|same:new_pin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $transacPin = TransactionPin::where('user_id', $user->id)->first();

        if (!$transacPin) {
            return response()->json(['error' => 'Transaction PIN record not found'], 404);
        }

        $transacPin->transaction_pin = Hash::make($request->input('new_pin'));
        $transacPin->save();

        return response()->json(['message' => 'Transaction PIN reset successfully'], 200);
    }


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_pin' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if the user already has a transaction PIN
        $existingTransactionPin = TransactionPin::where('user_id', $user->id)->first();

        if ($existingTransactionPin) {
            return response()->json(['error' => 'Transaction PIN already exists'], 400);
        }

        // Create a new transaction PIN record
        $newTransactionPin = new TransactionPin();
        $newTransactionPin->user_id = $user->id;
        $newTransactionPin->transaction_pin = Hash::make($request->input('transaction_pin'));
        $newTransactionPin->save();

        return response()->json(['message' => 'Transaction PIN created successfully'], 201);
    }

}
