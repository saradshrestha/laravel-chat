<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageEvent;
use App\Events\SendMessageEvent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function chat()
    {
        return view('chat');
    }

    public function getUsers(){
        $users = User::where('id','!=',Auth::id())
                    ->with('chatRooms')
                    ->get();
        return $users;
    }

   

    //show room by user Important
    public function show_room($user_id){
        $authUser = User::where('id',Auth::id())
                        ->with('chatRooms')
                        ->first();
  
        $user = User::where('id',$user_id)
                    ->with('chatRooms')
                    ->first();
        $chatRoom="";
        if($authUser->chatRooms->count() > 0){
            foreach($authUser->chatRooms as $chatRoom){
                $chatRoom = $this->checkUserInChatRoom($chatRoom,$user);
            }
        }elseif($user->chatRooms->count() > 0){
            foreach($user->chatRooms as $chatRoom){
                $chatRoom = $this->checkUserInChatRoom($chatRoom,$authUser);
            }

        }else{
            $chatRoom = Chat::create(['title' => $user->name]);
            $chatRoom->users()->attach([$user->id,$authUser->id]);
        }

       $chatRoom = Chat::whereHas('users', function($q) use ($user,$authUser){
                $q->whereIn('user_id',[$user->id,$authUser]);
            })->first();


        return view('chatRoom',[
            'user'=> $user, 
            'room_id'=>$chatRoom->id,
            'messages'=>$chatRoom->messages?? null,
            'authUser' =>$authUser->id

        ]);
    }


    public function sendMessage(Request $request){
        $fromId = auth()->user();
        $status = 1;
        $msg = $request->message;
        $room_id =$request->room_id;
        $save_message = Message::with('from')->create([
            'message'=>$request->message,
            'from_id' => Auth::id(),
            'to_id'=>$request->to_user_id,
            'chat_id'=>$request->room_id,
        ]);

        $save_message->from = $fromId;

        // $message = Message::where('id',$save_message->id)->with('from')->first();
            
        event(new PrivateMessageEvent($msg,$room_id,$fromId,$status));
        return true;
    }


   


    public function getAllMessages($room_id){
        $messages = Message::where('chat_id',$room_id)
                        ->with('from')
                        ->get();
        if($messages){
            return $messages;
        }
        return [];
    }




    public function checkUserInChatRoom($chatRoom,$user){

        $userIds = $chatRoom->users->pluck('id')->toArray();
        if (!in_array($user->id, $userIds)) {
            // User is not part of this chat room, attach them
            $chatRoom->users()->attach($user->id);
            return $chatRoom;
        }

    }




     // public function read_all_messages(Request $request){
    //     // dd('askdjl');
    //     $to_id = $request->toId;
    //     $room_id = $request->roomId;
    //     $update = Message::where([
    //         ['chat_id',$room_id],
    //         ['from_id',auth()->user()->id],
    //         ['to_id',$to_id],
    //         ['is_readed',0],
    //     ])->update([
    //         'is_readed'=>1
    //     ]);
    //     return null;
    // }

    


    
    
}
