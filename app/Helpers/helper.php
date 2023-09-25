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
    $branchCode = '4422';

    $accountIdentifier = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

    $accountNumber = $bankCode . $branchCode . $accountIdentifier;

    return $accountNumber;
}

