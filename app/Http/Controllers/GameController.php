<?php

namespace App\Http\Controllers;

use App\Events\BoardDelete;
use App\Events\BoardUpdate;
use App\Helpers\RackHelper;
use App\Models\Alphabet;
use App\Models\Bag;
use App\Models\Board;
use App\Models\Game;
use App\Models\Rack;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
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

    public function createGame(Request $request) {
        // check if opponent exists
        $request->validate([
            'opponent' => 'required|exists:users,name|max:255',
        ]);

        // create game
        $user = Auth::user()->name;
        $opponent = $request->input('opponent');
        $game = Game::create([
            'player1' => $user,
            'player2' => $opponent,
        ]);
        $gameId = $game->id;

        // create bag
        $language = $request->input('language');
        $alphabet = Alphabet::where('language', $language)->get();
        $bag = [];
        foreach ($alphabet as $letter) {
            for ($i = 0; $i < $letter->count; $i++) {
                array_push($bag, [ "letter" => $letter->letter, "value" => $letter->value, "gameId" => $gameId ]);
            }
        }
        Bag::insert($bag);

        // create racks
        RackHelper::createNew($user, $gameId);
        RackHelper::createNew($opponent, $gameId);

        return redirect()->route('game', ['gameId' => $gameId]);;
    }

    public function getGame($gameId) {
        setcookie("gameId", $gameId, time() + (86400 * 30)); // 86400 = 1 day
        return view('game');
    }

    public function getRack($gameId) {
        $user = Auth::user()->name;
        $rack = Rack::where("gameId", $gameId)->where("user", $user)->get();
        return $rack;
    }

    public function updateRack(Request $request, $gameId) {
        $user = Auth::user()->name;

        $x = $request->input("x");
        $letter = $request->input("letter");
        $value = $request->input("value");

        Rack::where("gameId", $gameId)->where("user", $user)->where("x", $x)->where("y", 15)->update(['letter' => $letter, "value" => $value]);
    }

    public function updateBoard(Request $request, $gameId) {
        $user = Auth::user();

        $x = $request->input("x");
        $y = $request->input("y");
        $letter = $request->input("letter");
        $value = $request->input("value");

        if ($letter === null) {
            Board::where("gameId", $gameId)->where("x", $x)->where("y", $y)->delete();
            broadcast(new BoardDelete($user, $x, $y));
        }
        else {
            // do not add record if there is another tile at this location (not handled on frontend)
            $board = Board::where("gameId", $gameId)->where("x", $x)->where("y", $y)->get();
            if (count($board) > 0)  return response("Space is taken", 400);

            $board = Board::create([
                "gameId" => $gameId, "x" => $x, "y" => $y, "letter" => $letter, "value" => $value
            ]);
            broadcast(new BoardUpdate($user, $board));
        }
    }
    public function getBoard($gameId) {
        $board = Board::where("gameId", $gameId)->get();
        return $board;
    }

}
