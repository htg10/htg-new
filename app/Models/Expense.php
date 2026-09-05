<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'purpose',
        'amount',
        'payment_mode',
        'date',
        'remark',
        'attachment'
    ];

    public function purposes()
    {
        return $this->belongsTo(Purpose::class);
    }
}
