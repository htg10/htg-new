<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telecaller extends Model
{
    protected $table = 'telecallers';

    protected $fillable = [
        'business',
        'name',
        'address',
        'mobile',
        'user_id',
        'meeting_datetime',
        'interest',
        'remark',
        'products',
        'latitude',
        'longitude',
        'location_accuracy',
        'location_url',
        'deal_status',
        'status',
        'created_by'
    ];

    protected $casts = [
        'meeting_datetime' => 'datetime',
        'products' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bdm()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function telecallerUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
