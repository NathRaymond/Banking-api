<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Bank;
use Illuminate\Support\Facades\Validator;

class MoneyTransferController extends Controller
{
        public function transfer(Request $request)
    {
        $user = $request->user();

        $rules = [
            'account_number' => 'required',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable',
        ];

        $messages = [
            'amount.min' => 'The amount must be at least 0.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $recipientAccountNumber = $request->input('account_number');
        $amount = $request->input('amount');
        $note = $request->input('note');

        // Check if the user has enough balance in their wallet
        $userWallet = Wallet::where('user_id', $user->id)->first();

        if (!$userWallet || $userWallet->balance < $amount) {
            return response()->json(['error' => 'Insufficient balance'], 400);
        }

        // Verify the recipient's bank and account number
        $recipient = User::where('account_number', $recipientAccountNumber)->first();

        if (!$recipient) {
            return response()->json(['error' => 'Recipient not found'], 404);
        }

        $recipientBank = Bank::where('account_number', $recipientAccountNumber)->first();

        if (!$recipientBank || $recipientBank->bank_name !== 'Expected Bank Name') {
            return response()->json(['error' => 'Recipient bank not verified'], 400);
        }

        // Deduct money from the sender's wallet
        $userWallet->decrement('balance', $amount);

        // Update the recipient's wallet
        $recipientWallet = Wallet::where('user_id', $recipient->id)->first();
        $recipientWallet->increment('balance', $amount);

        return response()->json(['message' => 'Money transfer successful']);
    }

}
