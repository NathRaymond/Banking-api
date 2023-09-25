<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class BeneficiaryController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string", 
            "account_number" => "required|numeric|digits:10",
            "bank_name" => "required|string", 
        ]);
    
        if ($validator->fails()) {
            return API_Response(500, [
                "message" => $validator->errors()->first() 
            ]);
        }

        $user = auth()->user();
        $beneficiary = new Beneficiary($request->all());
        $user->beneficiaries()->save($beneficiary);

        return API_Response(200, ['message' => 'Beneficiary created successfully']);
    }
    // Get a list of beneficiaries for the authenticated user
    public function index(Request $request)
    {
        $user = auth()->user();
        $beneficiaries = $user->beneficiaries;

        return response()->json(['beneficiaries' => $beneficiaries]);
    }

    // Get a specific beneficiary by ID
    public function show(Request $request, $id)
    {
        $user = auth()->user();
        $beneficiary = Beneficiary::where('user_id', $user->id)->find($id);

        if (!$beneficiary) {
            return API_Response(500, ['message' => 'Beneficiary not found']);
        }

        return response()->json(['beneficiary' => $beneficiary]);
    }

    // Update a beneficiary by ID
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $beneficiary = Beneficiary::where('user_id', $user->id)->find($id);

        if (!$beneficiary) {
            return API_Response(500, ['message' => 'Beneficiary not found']);
        }

        // Update beneficiary fields
        $beneficiary->fill($request->all());
        $beneficiary->save();

        return API_Response(200, ['message' => 'Beneficiary updated successfully']);
    }

    // Delete a beneficiary by ID
    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        $beneficiary = Beneficiary::where('user_id', $user->id)->find($id);

        if (!$beneficiary) {
            return API_Response(500, ['message' => 'Beneficiary not found']);
        }
        
        // Delete the beneficiary
        $beneficiary->delete();
        
        return API_Response(200, ['message' => 'Beneficiary deleted successfully']);
    }
}


