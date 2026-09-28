<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappSetting extends Model
{
    protected $fillable = [
        'phone_number_id',
        'business_account_id',
        'access_token',
        'api_version',
        'display_phone',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    protected $hidden = ['access_token'];

    public static function active()
    {
        return static::where('is_active', true)->first();
    }
}
