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
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function chat()
    {
        return view('chat');
    }

    public function getUsers(){
        // $authUser = User::where('id',Auth::id())
        //             ->with('chatRooms')
        //             ->first();

        $users = User::where('id','!=',Auth::id())
                    ->with('chatRooms')
                    ->get();
        
        // foreach($users as $user){
           
        //     foreach($authUser->chatRooms as $chatRoom){
        //         if($chatRoom->user_id != $user->id){
        //             $chatRoom = Chat::create(['title' => $user->name]);
        //             $chat->users()->create([
        //                 'user_id' => $user->id
        //             ]);
        //         }
        //     }
        // }
        // $users = $user->refresh();


        return $users;

    }

    // public function chatUser()
    // {
    //     return view('chatUser');
    // }


    // public function messages()
    // {
    //     return Message::with('user')->get();
    // }

    // public function messageStore(Request $request)
    // {
    //     $user = User::where('id',Auth::id())->with('messages')->first();

    //     $message = new Message();
    //     $message->user_id = $user->id;
    //     $message->message = $request->message;
    //     $message->save();

    //     $event  = event(new SendMessageEvent($message,$user));
    //     return true;
    // }

    public function sendMessage(Request $request){

        $fromId = auth()->user()->id;
        $status = 1;
        $msg = $request->message;
        $room_id =$request->room_id;
        $save_message = Message::create([
            'message'=>$request->message,
            'from_id'=>Auth::id(),
            'to_id'=>$request->to_user_id,
            'chat_id'=>$request->room_id,

        ]);
        
        $user = auth()->user()->name;
        event(new PrivateMessageEvent( $msg ,$user,$room_id,$fromId,$status));
        return true;
    }

    //show room by user
    public function show_room($user_id){
        //update auth user status to be online 
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
            'messages'=>$chatRoom->messages
        ]);
    }
  
    //update message status to => is_readed 
    public function read_all_messages(Request $request){
        // dd('askdjl');
        $to_id = $request->toId;
        $room_id = $request->roomId;
        $update = Message::where([
            ['chat_id',$room_id],
            ['from_id',auth()->user()->id],
            ['to_id',$to_id],
            ['is_readed',0],
        ])->update([
            'is_readed'=>1
        ]);
        return null;
    }


    public function getAllMessages($room_id){
        $room = Message::where('chat_id',$room_id)
                    ->with('from','to')->get();
        if($room){
            return $room;
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


    


    
    
}
