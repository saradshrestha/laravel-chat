<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
    ];

    public function users(){
        return $this->belongsToMany(User::class,'chat_room_users','chat_room_id','user_id');
    }
   
    public function messages(){
        return $this->hasMany(Message::class,'chat_id')->orderBy('created_at');
    }
}
