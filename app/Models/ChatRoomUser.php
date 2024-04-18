<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoomUser extends Model
{
    use HasFactory;

    protected $table = 'chat_room_users';
    
    protected $fillable = [
        'user_id',
        'chat_room_id',
    ];
}
