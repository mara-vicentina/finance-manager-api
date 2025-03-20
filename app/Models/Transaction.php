<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    private const TYPES = [
        0 => 'Entrada',
        1 => 'Saída',
    ];

    private const CATEGORIES = [
        1 => 'Receitas',
        2 => 'Despesas',
        3 => 'Investimentos',
        4 => 'Adicionais',
        5 => 'Outros',
    ];

    private const PAYMENT_METHODS = [
        1 => 'Dinheiro',
        2 => 'Cartão de Crédito',
        3 => 'Cartão de Débito',
        4 => 'PIX',
        5 => 'Outros',
    ];

    private const PAYMENT_STATUS = [
        1 => 'Pago',
        2 => 'Pendente',
        3 => 'Parcelado',
        4 => 'Outros',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'transaction_date',
        'value',
        'category',
        'description',
        'payment_method',
        'payment_status',
    ];

    public static function getTypeName($type)
    {
        return self::TYPES[$type] ?? '';
    }

    public static function getCategoryName($category)
    {
        return self::CATEGORIES[$category] ?? '';
    }

    public static function getPaymentMethodName($paymentMethod)
    {
        return self::PAYMENT_METHODS[$paymentMethod] ?? '';
    }

    public static function getPaymentStatusName($paymentStatus)
    {
        return self::PAYMENT_STATUS[$paymentStatus] ?? '';
    }
}
