<?php

use App\Events\PublicMessageEvent;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/', function () {
    return view('welcome');
});

Auth::routes();




Route::middleware(['corsMiddleware','auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/chat', [HomeController::class, 'chat'])->name('chat');
    Route::get('/getUsers', [HomeController::class, 'getUsers'])->name('getUsers');

    // Route::get('/chat/{user_id}', [HomeController::class, 'chatUser'])->name('chatUser');
    // Route::get('/messages', [HomeController::class, 'messages'])->name('messages');
    // Route::post('/message/store', [HomeController::class, 'messageStore'])->name('messageStore');

    Route::get('/get-all-messages/{chat_room_id}', [HomeController::class, 'getAllMessages'])->name('getAllMessages');



    //Create Chat room
    Route::get("/chat-room/create/{user_id}",[HomeController::class,'createChatRoom']);

    //send message
    Route::post("/chat-room/send-message",[HomeController::class,'sendMessage']);

    //Mark as Seen
    Route::post('chat-room/mark-as-seen/{room_id}', [HomeController::class, 'markAsSeen']);

    //show room
    Route::get("/chat-room/{user_id}/{room_id}",[HomeController::class,'show_room']);

    //read_messages
    Route::post("/read_all",[HomeController::class,'read_all_messages']);


});


