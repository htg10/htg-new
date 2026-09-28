<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceReminderLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id', 'entry_id', 'channel', 'status', 'error', 'sent_by', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function entry()
    {
        return $this->belongsTo(Entry::class, 'entry_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
