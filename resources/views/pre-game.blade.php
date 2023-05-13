@extends('layouts.app')

@section('content')
    <draw player="{{ \Illuminate\Support\Facades\Auth::user()->name }}" opponent="{{ $opponent }}" tooltip="{{ __("Click to draw a letter") }}. {{ __("Player with the higher score starts the game") }}." starts="{{ __("starts") }}" play="{{ __("Play") }}"></draw>
@endsection
