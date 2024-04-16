<?php

use App\Events\PublicMessageEvent;
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

Route::get('/public-chat', function() {
        broadcast(new PublicMessageEvent());
        return true;
    })
    ->name('home'); 




Route::middleware(['corsMiddleware','auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');   
    Route::get('/chat', [App\Http\Controllers\HomeController::class, 'chat'])->name('chat');
    Route::get('/getUsers', [App\Http\Controllers\HomeController::class, 'getUsers'])->name('getUsers');
    
    Route::get('/chat/{user_id}', [App\Http\Controllers\HomeController::class, 'chatUser'])->name('chatUser');
    Route::get('/messages', [App\Http\Controllers\HomeController::class, 'messages'])->name('messages');
    Route::post('/message/store', [App\Http\Controllers\HomeController::class, 'messageStore'])->name('messageStore'); 
});


