<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_id',
        'product_id',
        'product_name',
        'amount',
        'payment_bank',
        'payment_date',
    ];

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
