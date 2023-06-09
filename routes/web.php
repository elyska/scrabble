<?php

use App\Http\Middleware\EnsureUserIsPlayer;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/changeLanguage', [App\Http\Controllers\GeneralController::class, 'changeLanguage'])->name('change-language');

Route::get('/', function () {
    return redirect()->route("home");
});

Auth::routes();

Route::get('/my-games', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/new-game', [App\Http\Controllers\HomeController::class, 'newGameForm'])->name('new-game');

// Game
Route::post('/create-new-game', [App\Http\Controllers\GameController::class, 'createGame'])->name('create-new-game');

Route::middleware([EnsureUserIsPlayer::class])->group(function () {

    Route::post('/rack-update/{gameId}', [App\Http\Controllers\GameController::class, 'updateRack'])->name('rack-update');
    Route::post('/board-update/{gameId}', [App\Http\Controllers\GameController::class, 'updateBoard'])->name('board-update');
    Route::post('/refill/{gameId}', [App\Http\Controllers\GameController::class, 'refillRack'])->name('refill');
    Route::post('/draw/{gameId}', [App\Http\Controllers\GameController::class, 'draw'])->name('draw');
    Route::post('/score/{gameId}', [App\Http\Controllers\GameController::class, 'writeScore'])->name('write-score');
    Route::post('/skip-turn/{gameId}', [App\Http\Controllers\GameController::class, 'skipTurn'])->name('skip-turn');
    Route::post('/swap-tiles/{gameId}', [App\Http\Controllers\GameController::class, 'swapTiles'])->name('swap-tiles');
    Route::post('/end-game-request/{gameId}', [App\Http\Controllers\GameController::class, 'endGameRequest'])->name('end-game-request');
    Route::post('/end-game-confirm/{gameId}', [App\Http\Controllers\GameController::class, 'endGameConfirm'])->name('end-game-confirm');
    Route::post('/end-game-reject/{gameId}', [App\Http\Controllers\GameController::class, 'endGameReject'])->name('end-game-reject');

    Route::get('/draw/{gameId}', [App\Http\Controllers\GameController::class, 'preGameDraw'])->name('pre-game-draw');
    Route::get('/load-drawn-letters/{gameId}', [App\Http\Controllers\GameController::class, 'loadDrawnLetters'])->name('load-drawn-letters');
    Route::get('/game/{gameId}', [App\Http\Controllers\GameController::class, 'getGame'])->name('game');
    Route::get('/rack/{gameId}', [App\Http\Controllers\GameController::class, 'getRack'])->name('rack');
    Route::get('/board/{gameId}', [App\Http\Controllers\GameController::class, 'getBoard'])->name('board');
    Route::get('/scoreboard/{gameId}', [App\Http\Controllers\GameController::class, 'getScoreboard'])->name('scoreboard');
    Route::get('/swap-tiles/{gameId}', [App\Http\Controllers\GameController::class, 'swapTilesView'])->name('swap-tiles-view');
    Route::get('/tiles-to-swap/{gameId}', [App\Http\Controllers\GameController::class, 'getTilesToSwap'])->name('tiles-to-swap');
});
