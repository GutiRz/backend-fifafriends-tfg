<?php

namespace App\Http\Controllers;

use App\Clause;
use App\Player;
use App\Season;
use App\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\CalendarLeague;

class ClausesController extends Controller
{
    public function index() {
        $season =  Season::latest()->first();
        $clauses = Clause::where('season', $season->id)->get()->each(function ($c) {$c->now = now()->toDateTime();});

        foreach($clauses as $c)
        { 
            $clausesGrouped[$c->player_id][] = $c;
        }

        return $clausesGrouped;
    }

    public function showIncoming(Team $team) {
        return $team->clausesIncoming;
    }

    public function showOutgoing(Team $team) {
        return $team->clausesOutgoing;
    }

    public function store() {
        $season =  Season::latest()->first();
        $otherClausesPlayer = Clause::where('player_id', \request('player'))->where('season', $season->id)->get();
        $originalClause = $otherClausesPlayer->first();
        $player = Player::find(\request('player'));
        $team = Team::find(\request('destiny_team'));
        $hasMoney = $team->budget - ($player->salary + $player->clause + $team->totalSalaries) >= 0;

        $isOpen =  empty($originalClause) ? true : Carbon::parse($originalClause->launchable_at)->diffInMilliseconds(now()) > 0;
        $is_launchable = $otherClausesPlayer->count() > 0 ? false : true;

        if( !$hasMoney ||  !$isOpen) {
            return abort(403);
        };

        if($player->salary < 1500000) {
            $deposit = $player->clause * 0.3;
            $team->budget -= $deposit;
            $team->save();
        }

        return Clause::create([
            'origin_team' => $player->team->id,
            'destiny_team' => \request('destiny_team'),
            'player_id' => \request('player'),
            'season' => $season->number,
            'launchable_at' => $originalClause ? $originalClause->launchable_at : now()->addHours(24),
            'is_launchable' => $is_launchable,
            'order' => $otherClausesPlayer->count()
        ]);
    }

    public function launch(Clause $clause) {
        $otherClausesPlayer = Clause::where('player_id', $clause->player_id)->where('season',$clause->season)->where('order' ,'<', $clause->order)->where('state', '!=', Clause::CLAUSE_REJECTED)->count();

        $launchable_at = Carbon::parse($clause->launchable_at);
        $now = Carbon::parse(now());

        if( $otherClausesPlayer || !$now->greaterThan($launchable_at) || $clause->state != Clause::CLAUSE_OPEN) {
            return abort(403);
        }

        if($clause->destinyTeam->budget - ($clause->player->salary + $clause->player->clause + $clause->destinyTeam->totalSalaries) < 0) {
            $clause->state = Clause::CLAUSE_REJECTED;
            $clause->save();
            return abort(403);
        };
        $clause->rollDice();
        $clause->state = Clause::CLAUSE_PENDING;
    
        
        $clause->save();
        $clause->now = now()->toDateTime();

        return $clause;
    }

    public function accept(Clause $clause) {
        if($clause->state !== Clause::CLAUSE_PENDING) {
            return abort(403);
        }

        if($clause->destinyTeam->budget - ($clause->player->salary + $clause->player->clause + $clause->dice * 1000000 + $clause->destinyTeam->totalSalaries) < 0) {
            $clause->state = Clause::CLAUSE_REJECTED;
            $clause->save();
            return abort(403);
        };

        $clause->state = Clause::CLAUSE_ACCEPTED;

        $otherClausesPlayer = Clause::where('player_id', $clause->player->id)->where('season', $clause->season)->where('order','>',$clause->order)->get();

        if (!empty($otherClausesPlayer)) {
            foreach ($otherClausesPlayer as $otherClause) {
                $otherClause->state = Clause::CLAUSE_REJECTED;
                $otherClause->save();
            }
        }

        $clause->destinyTeam->update([
            'budget' =>  $clause->destinyTeam->budget -  ($clause->player->salary >= 1500000 ?  $clause->player->clause : $clause->player->clause * 0.7)
        ]);
        $clause->originTeam->update([
            'budget' =>  $clause->originTeam->budget + $clause->player->clause + ($clause->dice * 1000000)
        ]);
        $clause->player->team_id = $clause->destinyTeam->id;
        $clause->player->save();
        $clause->save();

        $clause->now = now()->toDateTime();

        return $clause;
    }

    public function reject(Clause $clause) {
        $clause->state = Clause::CLAUSE_REJECTED;
        $clause->is_launchable = false;
        $clause->save();
        
        $nextClausePlayer = Clause::where('player_id', $clause->player->id)->where('season', $clause->season)->where('order', '>', $clause->order)->first();

        if (!empty($nextClausePlayer)) {
            $nextClausePlayer->is_launchable = true;
            $nextClausePlayer->state = Clause::CLAUSE_PENDING;
            $nextClausePlayer->save();
        }

        $clause->now = now()->toDateTime();

        return $clause;
    }
}
