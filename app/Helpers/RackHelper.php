<?php

namespace App\Helpers;

use App\Models\Bag;
use App\Models\Rack;

class RackHelper {
    public function createNew($user, $gameId) {
        // get random letters from bag
        $rack = Bag::where("gameId", $gameId)->inRandomOrder()->limit(7)->get();
        for ($i = 0; $i < count($rack); $i++) {
            // delete from bag
            $rack[$i]->delete($rack[$i]);
            // add to rack
            $rack[$i]->user = $user;
            $rack[$i]->x = $i;
            unset($rack[$i]->id);
            Rack::create($rack[$i]->toArray());
        }
        // add empty space
        Rack::create([
            "user" => $user,
            "gameId" => $gameId,
            "letter" => null,
            "value" => null,
            "x" => 7,
        ]);
    }
}
