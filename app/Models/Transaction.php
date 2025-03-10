<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
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
}
