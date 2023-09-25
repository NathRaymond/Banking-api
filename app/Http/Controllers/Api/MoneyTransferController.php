<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Wallet;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\TransactionPin;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Unicodeveloper\Paystack\Facades\Paystack;
// use KingFlamez\Rave\Facades\Rave as Flutterwave;


class MoneyTransferController extends Controller
{
    public function verify_account(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "account_number" => "required|min:10|max:10",
            "bank_code" => "required",
        ]);

        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->messages()->first()
            ], $validator->errors());
        } else {
            try {
                $client = new Client();
                $response = $client->get('https://api.paystack.co/bank/resolve?account_number=' . $request->account_number . '&bank_code=' . $request->bank_code, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . config('paystack.secretKey'),
                    ]
                ]);
                return API_Response(
                    200,
                    json_decode($response->getBody())
                );
            } catch (\Exception $e) {
                return API_Response(500, [
                    "message" => "Could not resolve account name. Check parameters or try again"
                ]);
            }
        }
    }

    public function verify_account_internal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "account_number" => "required|min:10|max:10",
        ]);

        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->messages()->first()
            ], $validator->errors());
        } else {
            $user =  User::where("account_no", $request->account_number)->first();
            if ($user) {
                return  API_Response(200, [
                    "message" => "Account number resolved",
                    "data" => [
                        "account_name" => $user->first_name . " " . $user->last_name,
                        "account_number" => $user->account_no,
                    ]
                ]);
            } else {
                return API_Response(500, [
                    "message" => "Invalid account number"
                ]);
            }
        }
    }

    public function createRecipient(Request $request)
    {
        $wallet = Wallet::where("user_id", auth()->user()->id)->first();
        $validator = Validator::make($request->all(), [
            "recipient_name" => "required",
            "account_number" => "required|min:10|max:10",
            "bank_code" => "required",
            "amount" => "required|numeric",
        ]);
        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->messages()->first()
            ], $validator->errors());
        } else {
            if (($request->amount +  get_settings("external_transfer_charges")) > $wallet->balance) {
                $status = "Insufficient balance";
            } else {
                $status = "success";
            }

            $client = new Client();
            $url = 'https://api.paystack.co/transferrecipient';
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('paystack.secretKey'),
                ],
                'json' => [
                    'type' => "nuban",
                    'name' => "Transfer Fund",
                    'description' => "Transfer Fund",
                    'account_number' => $request->account_number,
                    'account_name' => $request->recipient_name,
                    'bank_code' => $request->bank_code,
                    'currency' => 'NGN'
                ],
            ]);

            return API_Response(200, [
                ...json_decode($response->getBody(), true),
                "status" => $status,
                "amount" => $request->amount,
                "charges" => get_settings("external_transfer_charges") == 0 ? "free" : get_settings("external_transfer_charges"),
                "total_amount" => ($request->amount + get_settings("external_transfer_charges")),
            ]);
        }
    }

    public function initiateTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "recipient_code" => "required",
            "amount" => "required|numeric",
            "reason" => "required|max:100",
            "transfer_pin" => "required",
        ]);
        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->messages()->first()
            ], $validator->errors());
        } else {
            $transaction_pin = TransactionPin::where("user_id", $request->user()->id)->first();

            if (Hash::check($request->transfer_pin, $transaction_pin->pin)) {
                $reference = generateReferenceId();
                try {
                    $client = new Client();
                    $url = 'https://api.paystack.co/transfer';
                    $response = $client->post($url, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . config('paystack.secretKey'),
                        ],
                        'json' => [
                            'source' => "balance",
                            'amount' => $request->amount,
                            'reference' => $reference,
                            'recipient' => $request->recipient_code,
                            'reason' => $request->reason,
                        ],
                    ]);
                    return API_Response(200, [
                        ...json_decode($response->getBody(), true)
                    ]);
                } catch (Exception $err) {
                    return API_Response(500, [
                        "message" => $err->getMessage(),
                    ]);
                }
            } else {
                return API_Response(500, [
                    "message" => "Incorrect transaction pin"
                ]);
            }
        }
    }

    public function initiateTransferInternal(Request $request)
    {
        $wallet = Wallet::where("user_id", auth()->user()->id)->first();
        $validator = Validator::make($request->all(), [
            "account_no" => "required",
            "amount" => "required|numeric",
            "reason" => "required|max:100",
        ]);
        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->messages()->first()
            ], $validator->errors());
        } else {
            if (($request->amount +  get_settings("internal_transfer_charges")) > $wallet->balance) {
                return API_Response(500, [
                    "message" => "Insufficient balance",
                ]);
            } else {
                $amount = $request->amount + get_settings("internal_transfer_charges");
                $recipient = User::where("account_no", $request->account_no)->first();
                if ($recipient->id != $request->user()->id) {
                    return API_Response(500, [
                        "message" => "You cannot make transfer to yourself",
                    ]);
                } else {
                    ///Wallet Account
                    $recipient_wallet = Wallet::where("user_id", $recipient->id)->first();
                    $recipient_wallet->balance += $request->amount;
                    $recipient_wallet->save();
                    $user_wallet = Wallet::where("user_id", $request->user()->id)->first();
                    $user_wallet->balance -= $amount;
                    $user_wallet->save();
                    //TRANSACTION RECEIPT 
                    $transaction = new Transaction();
                    $transaction->transaction_id = generateNumericTransactionId();
                    $transaction->reference_id = generateReferenceId();
                    $transaction->user_id = $request->user()->id;
                    $transaction->amount = $amount;
                    $transaction->charges = get_settings("internal_transfer_charges");
                    $transaction->currency = "NGN";
                    $transaction->reason = $request->reason;
                    $transaction->type = "internal_transfer";
                    $transaction->status = "success";
                    $transaction->details = json_encode([
                        "account_number" => $recipient->account_no,
                        "account_name" => $recipient->first_name . " " . $recipient->last_name,
                        "bank_name" => get_settings("app_name"),
                    ]);
                    $transaction->save();
                    return API_Response(200, [
                        "message" => "Transfer has been queued",
                        "transaction_code" => $transaction->transaction_id
                    ]);
                }
            }
        }
    }

    public function verifyTransfer(Request $request, $transfer_code)
    {

        try {
            $client = new Client();
            $url = 'https://api.paystack.co/transfer/' . $transfer_code;
            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('paystack.secretKey'),
                ]
            ]);

            $response_data = json_decode($response->getBody(), true);
            $transaction = new Transaction();

            return API_Response(200, [
                ...$response_data,
            ]);
        } catch (Exception $err) {
            return API_Response(500, [
                "message" => $err->getMessage(),
            ]);
        }
    }

    public function verifyTransferInternal(Request $request, $transfer_code)
    {
        $transaction = Transaction::where("transaction_id", $transfer_code)->first();
        if ($transaction) {
            return API_Response(200, [
                "message" => $transaction,
            ]);
        } else {
            return API_Response(500, [
                "message" => "Invalid transaction code"
            ]);
        }
    }
}
