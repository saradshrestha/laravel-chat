<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSeenEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('message-seen.' . $this->message->chat_id);
    }

    public function broadcastAs()
    {
        return 'chatroom-message-seen';
    }

    public function broadcastWith()
    {
        return [
            'message_id'=>$this->message->id,
            'read_at'=>$this->message->read_at,
        ];
    }
}
