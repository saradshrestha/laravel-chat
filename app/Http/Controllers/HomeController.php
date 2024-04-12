<?php

namespace App\Http\Controllers;

use App\Events\SendMessageEvent;
use App\Models\Message;
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
        $this->middleware('auth');
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

    public function messages()
    {
        return Message::with('user')->get();
    }

    public function messageStore(Request $request)
    {
        $user = Auth::user();

        $messages = $user->messages()->create([
            'message' => $request->message
        ]);
       
        broadcast(new SendMessageEvent($user,$message))->toOthers();
        return "message sent";
    }
    
}
