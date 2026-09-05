<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryHistory extends Model
{
    protected $table = 'entry_histories';
    protected $fillable = ['original_entry_id', 'data'];
}
