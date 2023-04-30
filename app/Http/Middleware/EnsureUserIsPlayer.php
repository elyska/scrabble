<?php

namespace App\Http\Middleware;

use App\Models\Game;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsPlayer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // check if the user is one of the players of the game
        $id = $request->route('gameId');
        $game = Game::where("id", $id)->where(function (Builder $query) {
            $user = Auth::user()->name;
            $query->where("player1", $user)->orWhere("player2", $user);
        })->get();
        if (count($game) == 0) return redirect()->route("home");

        return $next($request);
    }
}
