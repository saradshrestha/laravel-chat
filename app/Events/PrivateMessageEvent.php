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

    public $message, $roomId, $from, $message_id,$read_at;

    public function __construct($message,$roomId,$from,$message_id,$read_at)
    {
        $this->message = $message;
        $this->roomId = $roomId;
        $this->from = $from;
        $this->message_id = $message_id;
        $this->read_at = $read_at;

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
            'from'=>$this->from,
            'message'=>$this->message,
            'id'=>$this->message_id,
            'read_at'=>$this->read_at,
        ];
    }
}
