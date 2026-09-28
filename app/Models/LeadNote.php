<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadNote extends Model
{
    protected $fillable = [
        'telecaller_id',
        'user_id',
        'type',
        'content',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(Telecaller::class, 'telecaller_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
