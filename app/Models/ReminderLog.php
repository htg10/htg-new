<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'entry_id',
        'rule_id',
        'channel',
        'status',
        'error',
        'created_at',
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
        return $this->belongsTo(Entry::class);
    }

    public function rule()
    {
        return $this->belongsTo(ReminderRule::class, 'rule_id');
    }
}
