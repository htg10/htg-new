<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderRule extends Model
{
    protected $fillable = [
        'name',
        'type',
        'days',
        'channels',
        'wa_template_name',
        'sms_template_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'channels' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function logs()
    {
        return $this->hasMany(ReminderLog::class, 'rule_id');
    }
}
