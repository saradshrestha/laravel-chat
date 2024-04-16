@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
       
        <chatuser :user="{{ Auth::user() }}" />
           
    </div>
</div>
@endsection
