<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user;
    public $roomId;
    public $fromId;
    public $status;

    public function __construct($message,$roomId,$fromId,$status)
    {
        $this->message = $message;
        // $this->user = $user;
        $this->roomId = $roomId;
        $this->fromId = $fromId;
        $this->status = $status;
    }

   
    public function broadcastOn()
    {
        return new PrivateChannel('message.'.$this->roomId);
    }

    public function broadcastAs()
    {
        return 'chatroom-message';
    }

    public function broadcastWith()
    {
        return [
            'from_id'=>$this->fromId,
            'user'=>$this->user,
            'message'=>$this->message,
            'status'=>$this->status,
        ];
    }
}
