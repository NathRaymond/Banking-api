<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AccountUpgradeController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        // Validation rules for the new fields
        $validator = Validator::make($request->all(), [
            'isued_id_document' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_type' => 'required|string',
            'utility_bill' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'verified_state' => 'required|string',
            'verified_lga' => 'required|string',
            'verified_address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return API_Response(500, [
                'message' => $validator->messages()->first(),
            ], $validator->errors());
        }

        // Update the user's table fields to upgrade account
        $user->id_type = $request->input('id_type');
        $user->verified_state = $request->input('verified_state');
        $user->verified_lga = $request->input('verified_lga');
        $user->verified_address = $request->input('verified_address');

        if ($request->hasFile('isued_id_document')) {
            $idDocuments = $request->file('isued_id_document');
            $path = $idDocuments->store('account_upgrade_document_folder', 'public');
            $user->isued_id_document = $path;
        }
        if ($request->hasFile('utility_bill')) {
            $utilityDocument = $request->file('utility_bill');
            $path = $utilityDocument->store('account_upgrade_document_folder', 'public');
            $user->utility_bill = $path;
        }

        if ($user->save()) {
            return API_Response(200, ['message' => 'Documents uploaded successfully']);
        } else {
            return API_Response(500, ['message' => 'Failed to upload documents']);
        }
    }
}
