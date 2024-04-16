<?php

namespace App\Http\Controllers;

use App\Events\SendMessageEvent;
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
        return User::with('messages')->get();
    }

    public function chatUser()
    {
        return view('chatUser');
    }


    public function messages()
    {
        return Message::with('user')->get();
    }

    public function messageStore(Request $request)
    {
        $user = User::where('id',Auth::id())->with('messages')->first();

        $message = new Message();
        $message->user_id = $user->id;
        $message->message = $request->message;
        $message->save();

        $event  = event(new SendMessageEvent($message,$user));
        return true;
    }
    
}
