<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionsExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Transaction::select(
            'id',
            'type',
            'transaction_date',
            'value',
            'category',
            'description',
            'payment_method',
            'payment_status'
        )
        ->where('user_id', Auth::id())
        ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
        ->orderBy('transaction_date', 'asc')
        ->orderBy('id', 'asc')
        ->get()
        ->map(function($transaction) {
            $transaction->type = Transaction::getTypeName($transaction->type);
            $transaction->payment_method = Transaction::getPaymentMethodName($transaction->payment_method);
            $transaction->payment_status = Transaction::getPaymentStatusName($transaction->payment_status);
            $transaction->value = 'R$ ' . number_format($transaction->value, 2, ',', '');
            return $transaction;
        });
    }

    public function headings(): array
    {
        return [
            "ID",
            "Tipo",
            "Data da Transação",
            "Valor",
            "Categoria",
            "Descrição",
            "Método de Pagamento",
            "Status do Pagamento"
        ];
    }
}
