<?php

namespace App\Events;

use App\Models\Telecaller;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BdmAssignedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $telecaller;

    // Pass telecaller data to the event
    public function __construct(Telecaller $telecaller)
    {
        $this->telecaller = $telecaller;
    }

    public function broadcastOn()
    {
        return new Channel('admin.dashboard');
    }

    public function broadcastWith()
    {
        return [
            'message' => 'BDM ' . $this->telecaller->user->name . ' has been assigned.'
        ];
    }
}
