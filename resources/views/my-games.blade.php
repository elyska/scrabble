@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
                <h1 class="mb-4">{{ __('My Games') }}</h1>

                @forelse ($games as $game)
                    <div class="col-3">
                        <div class="card mb-2 px-2">
                            <div class="card-body">
                                <h5 class="card-title">{{ $game->player1 }} vs. {{ $game->player2 }}</h5>
                                <p class="card-text">{{ date_format($game->created_at, 'd/m/Y H:i') }}</p>
                                <a href="/draw/{{ $game->id }}" class="btn filled-button-link">{{ __("Continue Game") }}</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning">{{ __("No games") }}. <a href="/new-game">{{ __("Create new game") }}.</a></div>
                @endforelse
        </div>
    </div>
@endsection
