<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    protected $fillable = [
        'name',
        'template_name',
        'language',
        'category',
        'components',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'components' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
