<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

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
        ->get();
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
