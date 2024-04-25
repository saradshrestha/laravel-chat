@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @php
            $logged = Auth::user();
        @endphp
       
        <Chatroom :room_id="{{ $room_id }}" :messages="{{ $messages }}" :loggeduser="{{ $logged }}"  :user="{{ $user }}"    />
           
    </div>
</div>
@endsection
