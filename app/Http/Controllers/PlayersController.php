<?php

namespace App\Http\Controllers;

use App\Events\PlayerCreated;
use App\Player;
use Illuminate\Http\Request;

class PlayersController extends Controller
{
    public function index() {
        return Player::all();
    }

    public function show(Player $player){
        return $player;
    }

    public function store(Request $request){
        $player = Player::create([
            'sofifa' => $request->sofifa,
            'transfermarkt' => $request->transfermarkt,
            'team_id' => $request->team_id
        ]);
        event(new PlayerCreated($player));
        return $player;
    }
    
    public function update(Request $request, Player $player){
        return $player->update([
            'name' => $request->name
        ]);
    }

    public function destroy(Player $player) {
        try {
            return $player->delete();
        } catch (\Exception $e) {
            return abort(404);
        }
    }
}
