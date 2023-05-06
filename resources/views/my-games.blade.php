@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
                <h1 class="mb-4">{{ __('My Games') }}</h1>

                @foreach($games as $game)
                    <div class="col">
                        <div class="card mb-2 px-2">
                            <div class="card-body">
                                <h5 class="card-title">{{ $game->player1 }} vs. {{ $game->player2 }}</h5>
                                <p class="card-text">{{ date_format($game->created_at, 'd/m/Y H:i') }}</p>
                                <a href="/game/{{ $game->id }}" class="btn filled-button-link">{{ __("Continue Game") }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
        </div>
    </div>
@endsection
