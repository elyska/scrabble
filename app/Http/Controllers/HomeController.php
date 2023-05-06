<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user()->name;
        $games = Game::where("player1", $user)->orWhere("player2", $user)->orderBy('created_at', 'desc')->get();
        return view('my-games', [
            "games" => $games
        ]);
    }

    public function newGameForm() {
        return view('new-game');
    }
}
