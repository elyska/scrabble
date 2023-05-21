<?php

namespace App\Http\Controllers;

use App\Events\BoardDelete;
use App\Events\BoardUpdate;
use App\Events\Draw;
use App\Events\ScoreWrite;
use App\Helpers\RackHelper;
use App\Models\Alphabet;
use App\Models\Bag;
use App\Models\Board;
use App\Models\Game;
use App\Models\Rack;
use App\Models\Scoreboard;
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

        // save game id to cookies
        setcookie("gameId", $gameId, time() + (86400 * 30)); // 86400 = 1 day

        return redirect()->route('pre-game-draw', ['gameId' => $gameId]);
    }

    public function preGameDraw($gameId) {
        // save game id to cookies
        setcookie("gameId", $gameId, time() + (86400 * 30)); // 86400 = 1 day

        $game = Game::where("id", $gameId)->first();

        // redirect to the game if turn is already set
        if ($game->turn != null) return redirect()->route('game', ['gameId' => $gameId]);

        // get opponent name
        $user = Auth::user()->name;
        if ($game->player1 ===  $user) $opponent = $game->player2;
        else $opponent = $game->player1;

        return view("pre-game", [
            "opponent" => $opponent,
        ]);
    }
    public function draw($gameId) {
        $tile = Bag::where("gameId", $gameId)->inRandomOrder()->first();

        // determine if user is player1 or player2 and
        // save drawn letter or return letter drawn previously
        $game = Game::where("id", $gameId)->first();
        $user = Auth::user()->name;
        if ($game->player1 ===  $user) { // user is player1
            if (!$game->player1Draw) {// user has not drawn a letter
                $game->player1Draw = $tile->id;
                broadcast(new Draw($gameId))->toOthers();
            }
            else {
                $letter = $game->player1Draw;
                $tile = Bag::where("id", $letter)->first(); // get previously drawn letter
            }
        }
        else if ($game->player2 ===  $user) { // user is player2
            if (!$game->player2Draw) {// user has not drawn a letter
                $game->player2Draw = $tile->id;
                broadcast(new Draw($gameId))->toOthers();
            }
            else {
                $letter = $game->player2Draw;
                $tile = Bag::where("id", $letter)->first(); // get previously drawn letter
            }
        }
        // set turn
        if ($game->player1Draw != null && $game->player2Draw != null && $game->turn == null) {
            $player1Draw = Bag::where("id", $game->player1Draw)->first();
            $player2Draw = Bag::where("id", $game->player2Draw)->first();
            if ($player1Draw->value > $player2Draw->value) {
                $game->turn = $game->player1;
            }
            else if ($player1Draw->value < $player2Draw->value) {
                $game->turn = $game->player2;
            }
        }
        $game->save();

        return $tile;
    }

    public function loadDrawnLetters($gameId) {
        // get drawn letters
        $game = Game::where("id", $gameId)->first();
        $player1Draw = Bag::where("id", $game->player1Draw)->first();
        $player2Draw = Bag::where("id", $game->player2Draw)->first();

        // if drawn letters have same value -> delete them, players need to draw letters again
        if ($player1Draw && $player2Draw && $player1Draw->value === $player2Draw->value) {
            $game->player1Draw = null;
            $game->player2Draw = null;
            $game->save();
            return;
        }

        // determine if user is player1 or player2
        $user = Auth::user()->name;
        if ($game->player1 ===  $user) { // user is player1
            return [
                "userTile" => $player1Draw,
                "opponentTile" => $player2Draw
            ];
        }
        else if ($game->player2 ===  $user) { // user is player2
            return [
                "userTile" => $player2Draw,
                "opponentTile" => $player1Draw
            ];
        }
    }

    public function getGame($gameId) {
        $game = Game::where("id", $gameId)->first();

        // redirect if turn not set
        if ($game->turn == null) return redirect()->route('draw', ['gameId' => $gameId]);

        // save game id to cookies
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

        Rack::where("gameId", $gameId)->where("user", $user)->where("x", $x)->where("y", 15)->update([
            'letter' => $letter,
            "value" => $value
        ]);
    }

    public function updateBoard(Request $request, $gameId) {
        $user = Auth::user();

        $x = $request->input("x");
        $y = $request->input("y");
        $letter = $request->input("letter");
        $value = $request->input("value");

        if ($letter === null) {
            Board::where("gameId", $gameId)->where("x", $x)->where("y", $y)->delete();
            broadcast(new BoardDelete($user, $x, $y, $gameId))->toOthers();
        }
        else {
            // do not add record if there is another tile at this location (not handled on frontend)
            $board = Board::where("gameId", $gameId)->where("x", $x)->where("y", $y)->get();
            if (count($board) > 0)  return response("Space is taken", 400);

            $board = Board::create([
                "gameId" => $gameId, "x" => $x, "y" => $y, "letter" => $letter, "value" => $value
            ]);
            broadcast(new BoardUpdate($user, $board, $gameId))->toOthers();
        }
    }
    public function getBoard($gameId) {
        $board = Board::where("gameId", $gameId)->get();
        return $board;
    }

    public function refillRack($gameId) {
        $user = Auth::user()->name;
        $game = Game::where("id", $gameId)->first();

        if ($game->turn != $user) {
            return response()->json([
                'message' => 'Not your turn'
            ]);
        }

        $emptySpaces = Rack::where("gameId", $gameId)->where("user", $user)->where("letter", null)->get();

        for ($i = 0; $i < count($emptySpaces) - 1; $i++) {
            // get from bag
            $tile = Bag::where("gameId", $gameId)->inRandomOrder()->first();
            // if not empty, delete from bag
            if (!$tile) return response()->json([
                'message' => 'No more letters'
            ]);
            $tile->delete($tile);
            // add to rack
            $emptySpaces[$i]->letter = $tile->letter;
            $emptySpaces[$i]->value = $tile->value;
            $emptySpaces[$i]->save();
        }
        // set turn after refill
        if (count($emptySpaces) != 0) {

            if ($game->player1 == $user) $game->turn = $game->player2;
            else $game->turn = $game->player1;
            $game->save();
        }
    }

    public function getScoreboard($gameId) {
        $user = Auth::user()->name;
        $game = Game::where("id", $gameId)->first();
        // get opponent name
        if ($game->player1 == $user) $opponent = $game->player2;
        else $opponent = $game->player1;

        $playerScores = Scoreboard::where("gameId", $gameId)->where("player", $user)->get();
        $opponentScores = Scoreboard::where("gameId", $gameId)->where("player", $opponent)->get();

        return [
            "playerName" => $user,
            "opponentName" => $opponent,
            "playerScores" => $playerScores,
            "opponentScores" => $opponentScores
        ];
    }
    public function writeScore($gameId, Request $request) {
        $request->validate([
            'score' => 'required|integer',
        ]);

        $score = $request->get("score");

        $user = Auth::user()->name;

        $record = Scoreboard::create([
           "gameId" => $gameId,
           "player" => $user,
           "score" => $score
        ]);

        broadcast(new ScoreWrite(Auth::user(), $record->score, $gameId))->toOthers();

        return $record;
    }

}
