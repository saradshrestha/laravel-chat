@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
       
        <chat :user="{{ Auth::user() }}" />
           
    </div>
</div>
@endsection
