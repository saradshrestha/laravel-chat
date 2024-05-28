<?php

namespace App\Http\Controllers;

use App\Events\MessageSeenEvent;
use App\Events\PrivateMessageEvent;
use App\Events\SendMessageEvent;
use App\Models\Chat;
use App\Models\ChatRoomUser;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

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
                    ->with(['chatRooms'=>function($q){
                        $q->withCount('unreadMessages');
                    }])
                    ->get();
        return $users;
    }

    //show room by user Important
    public function show_room($user_id,$room_id){
        $authUser = User::where('id',Auth::id())
                        ->with('chatRooms')
                        ->first();

        $user = User::where('id',$user_id)
                    ->with('chatRooms')
                    ->first();

        // if($authUser->chatRooms->count() > 0){
        //     foreach($authUser->chatRooms as $chatRoom){
        //         $chatRoom = $this->checkUserInChatRoom($chatRoom,$user);
        //     }
        // }elseif($user->chatRooms->count() > 0){
        //     foreach($user->chatRooms as $chatRoom){
        //         $chatRoom = $this->checkUserInChatRoom($chatRoom,$authUser);
        //     }
        // }

        $chatRoom = Chat::where('id',$room_id)->with('messages')->first();
        if(!$chatRoom){
            $chatRoom = Chat::create(['title' => $user->name]);
            $chatRoom->users()->attach([$user->id,$authUser->id]);
        }

        return view('chatRoom',[
            'user'=> $user,
            'room_id'=>$chatRoom->id,
            'messages'=>$chatRoom->messages?? null,
            'authUser' =>$authUser->id
        ]);


    }

    public function createChatRoom($user_id){
        $authUser = User::where('id',Auth::id())
                ->with('chatRooms')
                ->first();

        $user = User::where('id',$user_id)
                ->with('chatRooms')
                ->first();

        $usersInSameRoom = ChatRoomUser::whereIn('user_id',[$authUser,$user_id])
                            ->pluck('chat_room_id')
                            ->unique();
        // dd($usersInSameRoom);
        if($usersInSameRoom->isEmpty()){
            // dd($usersInSameRoom);
            $chatRoom = Chat::create(['title' => $user->name]);
            $chatRoom->users()->attach([$user->id,$authUser->id]);
        }else{
            // dd($usersInSameRoom,"else");
            $chatRoom = Chat::where('id',$usersInSameRoom)->first();
        }

        return view('chatRoom',[
            'user'=> $user,
            'room_id'=>$chatRoom->id,
            'messages'=>$chatRoom->messages?? null,
            'authUser' =>$authUser->id
        ]);

    }


    public function sendMessage(Request $request){
        $fromId = auth()->user();
        $msg = $request->message;
        $room_id =$request->room_id;
        if($request->message){
            $save_message = Message::with('from')->create([
                'message'=>$request->message,
                'from_id' => Auth::id(),
                'to_id'=>$request->to_user_id,
                'chat_id'=>$request->room_id,
            ]);

            event(new PrivateMessageEvent($msg,$room_id,$fromId,$save_message->id,$save_message->read_at));
            return true;
        }
        return false;

    }


    public function getAllMessages($room_id){
        $messages = Message::where('chat_id',$room_id)
                        ->with('from')
                        ->oldest()
                        ->get();
        if($messages){
            $this->markMessagesAsSeen($messages);
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



public function markAsSeen(Request $request, $chatRoom_id)
{

    $request->validate(['message_id' => 'required|integer']);
    $message = Message::where('chat_id', $chatRoom_id)
                      ->where('id', $request->message_id)
                      ->first();

    $message->read_at = now();
    $message->save();

    event(new MessageSeenEvent($message));

    return response()->json(['message' => 'Message marked as seen.']);
}




    protected function markMessagesAsSeen($messages)
    {
        $userId = auth()->user()->id;
        foreach ($messages as $message) {
            if (!$message->read_at && $message->from_id !== $userId) {
                $message->read_at = Carbon::now();
                $message->save();

                event(new MessageSeenEvent($message));
            }
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
