<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $fillable = [
        'to_phone',
        'to_name',
        'template_name',
        'message_type',
        'content',
        'wa_message_id',
        'status',
        'error',
        'context_type',
        'context_id',
        'sent_by',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
