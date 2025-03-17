<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use App\Services\AppService;
use App\Models\Transaction;

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

    public function monthlyTransactions(Request $request)
    {
        $periods = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('m/Y');
            $periods[$month] = [
                'month' => $month,
                'incomes' => number_format(0, 2, '.', ''),
                'expenses' => number_format(0, 2, '.', ''),
                'total' => number_format(0, 2, '.', ''),
            ];
        }

        $transactions = Transaction::select(
                DB::raw("DATE_FORMAT(transaction_date, '%m/%Y') as month"),
                DB::raw('SUM(CASE WHEN type = 0 THEN value ELSE 0 END) as incomes'),
                DB::raw('SUM(CASE WHEN type = 1 THEN value ELSE 0 END) as expenses'),
                DB::raw('SUM(CASE WHEN type = 0 THEN value ELSE -value END) as total')
            )
            ->where('user_id', Auth::id())
            ->whereBetween('transaction_date', [
                now()->subMonths(11)->startOfMonth(),
                now()->endOfMonth()
            ])
            ->groupBy(DB::raw("DATE_FORMAT(transaction_date, '%m/%Y')"))
            ->orderBy('month', 'asc')
            ->get();

        foreach ($transactions as $transaction) {
            $month = $transaction->month;

            if (isset($periods[$month])) {
                $periods[$month]['incomes'] = number_format($transaction->incomes, 2, '.', '');
                $periods[$month]['expenses'] = number_format($transaction->expenses, 2, '.', '');
                $periods[$month]['total'] = number_format($transaction->total, 2, '.', '');
            }
        }

        return response()->json([
            'success' => true,
            'data' => array_values($periods),
        ], 200);
    }
}
