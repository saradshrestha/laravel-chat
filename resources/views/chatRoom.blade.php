@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
       
        <Chatroom :user="{{ $user }}" :room_id = "{{$room_id}}" :messages ="{{ $messages}}" />
           
    </div>
</div>
@endsection
