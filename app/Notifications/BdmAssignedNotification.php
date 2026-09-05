<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BdmAssignedNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $admin;

    public function __construct($user, $admin)
    {
        $this->user = $user;
        $this->admin = $admin;
    }

    // Define the delivery channels
    public function via($notifiable)
    {
        return ['database'];
    }

    // Create the database notification
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'New Meeting ' . $this->admin->name . ' has been created and assigned to ' . $this->user->name,
            'user_id' => $this->user->id,
            'admin_id' => $this->admin->id,
        ];
    }
}
