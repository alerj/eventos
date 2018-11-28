<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class InvitationAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $invitationId;

    /**
     * Create a new event instance.
     *
     * @param $invitationId
     */
    public function __construct($invitationId)
    {
        $this->invitationId = $invitationId;
    }
}
