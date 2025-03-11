<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Services\AppService;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function list(Request $request)
    {
        $rules = [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    
        if ($validation = AppService::validateRequest($request->all(), $rules)) {
            return response()->json($validation, $validation['status_code']);
        }

        $transactions = Transaction::when(!empty($request->start_date), function ($query) use ($request) {
                return $query->where('transaction_date', '>=', $request->start_date . ' 00:00:00');
            })
            ->when(!empty($request->end_date), function ($query) use ($request) {
                return $query->where('transaction_date', '<=', $request->end_date . ' 23:59:59');
            })
            ->orderBy('transaction_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ], 200);
    }
    
    public function create(Request $request)
    {
        $rules = [
            'type' => 'required|boolean',
            'transaction_date' => 'required|date',
            'value' => 'required|decimal:2',
            'category' => 'required|integer',
            'description' => 'nullable|string',
            'payment_method' => 'required|integer',
            'payment_status' => 'required|integer',
        ];
    
        if ($validation = AppService::validateRequest($request->all(), $rules)) {
            return response()->json($validation, $validation['status_code']);
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $transaction = Transaction::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Transação registrada com sucesso!',
            'id' => $transaction->id,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'type' => 'required|boolean',
            'transaction_date' => 'required|date',
            'value' => 'required|decimal:2',
            'category' => 'required|integer',
            'description' => 'nullable|string',
            'payment_method' => 'required|integer',
            'payment_status' => 'required|integer',
        ];
    
        if ($validation = AppService::validateRequest($request->all(), $rules)) {
            return response()->json($validation, $validation['status_code']);
        }

        $transaction = Transaction::whereId($id)->first();
        if (!$this->validateTransaction($transaction)) {
            return response()->json([
                'success' => false,
                'message' => 'A transação não foi encontrada.',
            ], 404);
        }

        $transaction->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'A transação foi atualizada com sucesso!',
        ], 200);
    }

    public function delete($id)
    {
        $transaction = Transaction::whereId($id)->first();
        if (!$this->validateTransaction($transaction)) {
            return response()->json([
                'success' => false,
                'message' => 'A transação não foi encontrada.',
            ], 404);
        }

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'A transação foi removida com sucesso!',
        ], 200);
    }

    private function validateTransaction($transaction): bool
    {
        if (!$transaction || $transaction->user_id !== Auth::id()) {
            return false;
        }
    
        return true;
    }
}
