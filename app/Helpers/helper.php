<?php
function API_Response($statusCode, $response = false, $errorBag = false)
{
    $status = ($statusCode == 200) ? "success" : (($statusCode == 500) ?  "error" : ($statusCode == 199 ? "warning" : ($statusCode == 100 ? "info" : "unknown")));
    $responseAPI = [
        "status" => $status,
        "statusCode" => $statusCode,
    ];
    if ($errorBag) $responseAPI["errors"] = $errorBag;
    if ($response) $responseAPI["responseBody"] = $response;
    return $responseAPI;
}


function generateAccountNumber()
{
    $bankCode = '753';
    $branchCode = '442';

    $accountIdentifier = str_pad(rand(1, 9999), 3, '0', STR_PAD_LEFT);

    $accountNumber = $bankCode . $branchCode . $accountIdentifier;

    return $accountNumber;
}

function get_settings($setting_key)
{
    $setting_data = \App\Models\Setting::where("site_key", $setting_key)->first();
    return @$setting_data->value;
}

function generateReferenceId($length = 8)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $referenceId = '';
    $charLength = strlen($characters);
    for ($i = 0; $i < $length; $i++) {
        $referenceId .= $characters[rand(0, $charLength - 1)];
    }
    return $referenceId;
}

function generateNumericTransactionId($length = 4) {
    $timestamp = time();
    $randomDigits = '0123456789'; 
    $randomLength = strlen($randomDigits);
    $transactionId = '';
    $transactionId .= date('mdHis', $timestamp);
    for ($i = 0; $i < $length - 14; $i++) {
        $transactionId .= $randomDigits[rand(0, $randomLength - 1)];
    }
    return $transactionId;
}
