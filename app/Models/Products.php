<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $table ='products';

    protected $fillable =[
        'entry_id',
        'product_name',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'validity',
        'expiry_date',
    ];

    public function entry(){
        return $this->belongsTo(Entry::class);
    }
}
