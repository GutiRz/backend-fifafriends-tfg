<?php

namespace App\Http\Controllers;

use App\Negotiation;
use App\Player;
use App\Team;
use App\NegotiationPlayers;
use Illuminate\Http\Request;
use DB;

class NegotiationsController extends Controller
{
    // Returns every negotiation
    public function index() {
        return Negotiation::all();
    }

    // Returns negotiations from $team
    public function show(Team $team){
        return $team->negotations;
    }

    // Returns incoming negotiations from $team
    public function showIncoming(Team $team){
        return $team->negotiationsIncoming;
    }

    // Returns outgoing negotiations from $team
    public function showOutgoing(Team $team){
        return $team->negotiationsOutgoing;
    }

    // Creates negotiation
    public function store(Request $request){
        $players = $request->players;

        $negotiation = Negotiation::create([
            'origin_team' => $request->origin_team,
            'destiny_team' => $request->destiny_team,
            'money_origin' => $request->money_origin,
            'money_destiny' => $request->money_destiny
        ]);

        $players = Player::whereIn('id', $players)->get();

        foreach ($players as $player) {
            NegotiationPlayers::create([
                'negotiation' => $negotiation->id,
                'player' => $player->id,
                'team' => $player->team->id
            ]);
        }

        return $negotiation;
    }

    // Accept $negotiation and process it
    public function accept(Negotiation $negotiation) {
        if($negotiation->status != Negotiation::NEGOTIATION_PENDING) {
            abort(403);
        }

        // budget origin
        $new_budget_origin = $negotiation->originTeam->budget + $negotiation->money_destiny - $negotiation->money_origin;
        $negotiation->originTeam->budget = $new_budget_origin;
        $negotiation->originTeam->save();

        // budget destiny
        $new_budget_destiny = $negotiation->destinyTeam->budget + $negotiation->money_origin - $negotiation->money_destiny;
        $negotiation->destinyTeam->budget = $new_budget_destiny;
        $negotiation->destinyTeam->save();

        // players movement
        $negotiation_players = $negotiation->negotiationPlayers;

        foreach ($negotiation_players as $np) {
            $np = $np->players;

            if($np->team_id == $negotiation->origin_team) {
                $np->update([
                    'team_id' => $negotiation->destiny_team
                ]);
            } else {
                $np->update([
                    'team_id' => $negotiation->origin_team
                ]);
            }
        }

        $negotiation->update([
            'status' => Negotiation::NEGOTIATION_ACCEPTED
        ]);
        return $negotiation;
    }

    // Rejects $negotiation
    public function reject(Negotiation $negotiation) {
        if($negotiation->status != Negotiation::NEGOTIATION_PENDING) {
            abort(403);
        }
        
        $negotiation->update([
            'status' => Negotiation::NEGOTIATION_REJECTED
        ]);

        return $negotiation;
    }


}
