<?php

namespace App\Http\Controllers;

use App\Events\BoardDelete;
use App\Events\BoardUpdate;
use App\Events\Draw;
use App\Events\EndGameRequest;
use App\Events\EndGameResult;
use App\Events\RemainingUpdate;
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
use Illuminate\Validation\ValidationException;

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
            'language' => 'required'
        ]);

        $opponent = $request->input('opponent');

        // check opponent is different from the player
        $user = Auth::user()->name;
        if ($user === $opponent) throw ValidationException::withMessages(['opponent' => 'The opponent cannot be you.']);

        // check case sensitive uniqueness
        $dbUser = User::where("name", $opponent)->first();
        if ($dbUser && $dbUser->name != $opponent) throw ValidationException::withMessages(['opponent' => 'The selected opponent is invalid.']);

        // create game
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
        setcookie("gameId", $gameId, time() + (86400 * 30), "/"); // 86400 = 1 day

        return redirect()->route('pre-game-draw', ['gameId' => $gameId]);
    }

    public function preGameDraw($gameId) {
        // save game id to cookies
        setcookie("gameId", $gameId, time() + (86400 * 30), "/"); // 86400 = 1 day

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
        // save game id to cookies
        setcookie("gameId", $gameId, time() + (86400 * 30), "/"); // 86400 = 1 day

        $game = Game::where("id", $gameId)->first();

        // redirect if turn not set
        if ($game->turn == null) return redirect()->route('draw', ['gameId' => $gameId]);

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

        // get counter
        $bagCount = Bag::where("gameId", $gameId)->get()->count();
        return [
            "board" => $board,
            "remaining" => $bagCount,
        ];
    }

    public function refillRack($gameId) {
        $user = Auth::user()->name;
        $game = Game::where("id", $gameId)->first();

        if ($game->turn != $user) {
            // language
            if (isset($_COOKIE["language"]) && $_COOKIE["language"] === "cs") $message = 'Nejsi na řadě';
            else $message = 'Not your turn';
            return response()->json([
                'message' => $message
            ]);
        }
        else {
            $emptySpaces = Rack::where("gameId", $gameId)->where("user", $user)->where("letter", null)->get();

            for ($i = 0; $i < count($emptySpaces) - 1; $i++) {
                // get from bag
                $tile = Bag::where("gameId", $gameId)->inRandomOrder()->first();
                // if not empty, delete from bag
                if (!$tile) {
                    // language
                    if (isset($_COOKIE["language"]) && $_COOKIE["language"] === "cs") $message = 'Žádná další písmena';
                    else $message = 'No more letters';
                    return response()->json([
                        'message' => $message
                    ]);
                }
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
            // update counter
            $bagCount = Bag::where("gameId", $gameId)->get()->count();
            broadcast(new RemainingUpdate($bagCount, $gameId));
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

    public function endGameRequest($gameId) {
        $user = Auth::user();
        broadcast(new EndGameRequest($user, $gameId))->toOthers();
    }

    public function endGameConfirm($gameId) {
        $user = Auth::user();
        $userScore = Scoreboard::where("gameId", $gameId)->where("player", $user->name)->sum("score");
        $opponentScore = Scoreboard::where("gameId", $gameId)->where("player", "!=", $user->name)->sum("score");

        // set quit
        $game = Game::where("id", $gameId)->first();
        $game->finished = true;
        $game->save();

        broadcast(new EndGameResult($user, $gameId, true, $opponentScore, $userScore))->toOthers();

        return response()->json([
            'userScore' => $userScore,
            'opponentScore' => $opponentScore,
        ]);
    }
    public function endGameReject($gameId) {
        $user = Auth::user();
        broadcast(new EndGameResult($user, $gameId, false, 0, 0))->toOthers();
    }

    public function skipTurn($gameId) {
        // set turn
        $game = Game::where("id", $gameId)->first();
        $user = Auth::user()->name;
        if ($game->player1 ===  $user) { // user is player1
            $game->turn = $game->player2;
        }
        else if ($game->player2 ===  $user) { // user is player2
            $game->turn = $game->player1;
        }
        $game->save();
        // language
        if (isset($_COOKIE["language"]) && $_COOKIE["language"] === "cs") $message = 'Na řadě je protihráč';
        else $message = "Is is your opponent's turn";
        return response()->json([
            'message' => $message
        ]);
    }

    public function swapTiles(Request $request, $gameId) {
        $user = Auth::user()->name;

        // do not allow if not your turn
        $game = Game::where("id", $gameId)->first();

        if ($game->turn != $user) {
            // language
            if (isset($_COOKIE["language"]) && $_COOKIE["language"] === "cs") $message = 'Nejsi na řadě';
            else $message = 'Not your turn';
            return response()->json([
                'message' => $message
            ]);
        }

        $tilesToSwap = $request->get("tilesToSwap");
        $tiles = $request->get("tiles");

        //dd($tilesToSwap);

        // get number of tiles in the bag
        $bag = Bag::where("gameId", $gameId)->get();
        // if not enough letters, return with message "Select up to :limit letters" (cs, en)
        if (count($tilesToSwap) > count($bag)) {
            // language
            if (isset($_COOKIE["language"]) && $_COOKIE["language"] === "cs") $message = 'Nedostatek písmen';
            else $message = 'Not enough letters';
            return response()->json([
                'message' => $message
            ]);
        }

        // get new letters from the bag
        $newTiles = Bag::where("gameId", $gameId)->inRandomOrder()->limit(count($tilesToSwap))->get();
        $newTilesCopy = unserialize(serialize($newTiles));
        // return letters to the bag
        for ($i = 0; $i < count($newTiles); $i++) {
            if ($tilesToSwap[$i]["letter"] === null) $tilesToSwap[$i]["letter"] = "";
            $newTiles[$i]->letter = $tilesToSwap[$i]["letter"];
            $newTiles[$i]->value = $tilesToSwap[$i]["value"];
            $newTiles[$i]->save();
        }

        // update rack
        $rack = Rack::where("gameId", $gameId)->where("user", $user)->get();
        $newRack = array_merge($tiles, $newTilesCopy->toArray());
        for ($i = 0; $i < count($rack) - 1; $i++) {
            if($rack[$i]->letter !== null) {
                // add to rack
                $rack[$i]->letter = $newRack[$i]["letter"];
                $rack[$i]->value = $newRack[$i]["value"];
                $rack[$i]->save();
            }
        }
        // set turn to opponent
        if ($game->player1 ===  $user) $game->turn = $game->player2;
        else $game->turn = $game->player1;
        $game->save();
    }
    public function getTilesToSwap($gameId) {
        $user = Auth::user()->name;

        $rack = Rack::where("gameId", $gameId)->where("user", $user)->where("letter", "!=", null)->get();

        return $rack;
    }

    public function swapTilesView($gameId) {
        $user = Auth::user()->name;

        // save game id to cookies
        setcookie("gameId", $gameId, time() + (86400 * 30), "/"); // 86400 = 1 day

        $rack = Rack::where("gameId", $gameId)->where("user", $user)->where("letter", "!=", null)->get();

        // get number of tiles in the bag
        $bag = Bag::where("gameId", $gameId)->get();


        return view("swap-tiles", [
            "rack" => $rack,
            "gameId" => $gameId,
            "remainingTiles" => count($bag)
        ]);
    }
}
