<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $table = 'buildings';

    protected $fillable = [
        'name',
        'mobile',
        'building',
        'amount',
        'payment_mode',
        'date',
    ];
}
