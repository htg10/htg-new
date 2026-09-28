<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'bank',
        'opening_balance',
        'attachment',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
    ];
}
