<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Clause extends Model
{
    protected $guarded = [];
    protected $with = ['originTeam', 'destinyTeam', 'player'];

    const CLAUSE_OPEN = 'open';
    const CLAUSE_PENDING = 'pending';
    const CLAUSE_ACCEPTED = 'accepted';
    const CLAUSE_REJECTED = 'rejected';

    public function originTeam() {
        return $this->belongsTo(Team::class, 'origin_team', 'id');
    }

    public function destinyTeam() {
        return $this->belongsTo(Team::class, 'destiny_team', 'id');
    }

    public function player() {
        return $this->belongsTo(Player::class);
    }

    public function season() {
        return $this->belongsTo(Season::class, 'season', 'id');
    }

    public function rollDice() {
        return $this->dice = Arr::random([0, 4, 6, 8]);
    }
}
