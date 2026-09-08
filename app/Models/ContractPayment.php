<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractPayment extends Model
{
    protected $table = 'contract_payments';

    protected $fillable = [
        'entry_id',
        'bank_name',
        'amount',
        'payment_date',
        'remark',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function entry()
    {
        return $this->belongsTo(Entry::class, 'entry_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
