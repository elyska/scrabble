<?php

use App\Models\Game;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('draw.{gameId}', function ($user, $gameId) {
    $game = Game::where("id", $gameId)->where(function (Builder $query) {
        $user = Auth::user()->name;
        $query->where("player1", $user)->orWhere("player2", $user);
    })->get();
    return count($game) != 0;
});

Broadcast::channel('board.{gameId}', function ($user, $gameId) {
    $game = Game::where("id", $gameId)->where(function (Builder $query) {
        $user = Auth::user()->name;
        $query->where("player1", $user)->orWhere("player2", $user);
    })->get();
    return count($game) != 0;
});

Broadcast::channel('board-delete.{gameId}', function ($user, $gameId) {
    $game = Game::where("id", $gameId)->where(function (Builder $query) {
        $user = Auth::user()->name;
        $query->where("player1", $user)->orWhere("player2", $user);
    })->get();
    return count($game) != 0;
});

Broadcast::channel('score.{gameId}', function ($user, $gameId) {
    $game = Game::where("id", $gameId)->where(function (Builder $query) {
        $user = Auth::user()->name;
        $query->where("player1", $user)->orWhere("player2", $user);
    })->get();
    return count($game) != 0;
});
