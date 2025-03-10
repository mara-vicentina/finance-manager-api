<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'value',
        'category',
        'description',
        'payment_method',
        'payment_status',
    ];
}
