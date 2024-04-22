@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
       
        <Chatroom :room_id = "{{$room_id}}" 
                :messages ="{{ $messages}}" :authUser="{{ Auth::user() }}"  :user="{{ $user }}" />
           
    </div>
</div>
@endsection
