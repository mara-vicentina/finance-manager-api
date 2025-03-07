<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Services\AppService;

class TransactionController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'type' => 'required|boolean',
            'value' => 'required|decimal:2',
            'category' => 'required|integer',
            'description' => 'nullable|string',
            'payment_method' => 'required|integer',
            'payment_status' => 'required|integer',
        ];
    
        if ($validation = AppService::validateRequest($request->all(), $rules)) {
            return response()->json($validation, $validation['status_code']);
        }

        $transaction = Transaction::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Transação registrada com sucesso!',
            'id' => $transaction->id,
        ], 201);
    }
}
