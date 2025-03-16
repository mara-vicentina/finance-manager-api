<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use App\Services\AppService;

class ReportController extends Controller
{
    public function exportTransactions(Request $request)
    {
        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];
    
        if ($validation = AppService::validateRequest($request->all(), $rules)) {
            return response()->json($validation, $validation['status_code']);
        }

        $fileName = 'relatorio_transacoes_' . now()->format('YmdHis') . '.xlsx';
        return Excel::download(
            new TransactionsExport($request->start_date, $request->end_date),
            $fileName
        );
    }
}
