<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Negotiation extends Model
{
    protected $guarded = [];
    protected $with = ['negotiationPlayers.players', 'originTeam', 'destinyTeam'];

    const NEGOTIATION_PENDING = 'pending';
    const NEGOTIATION_ACCEPTED = 'accepted';
    const NEGOTIATION_REJECTED = 'rejected';

    public function originTeam() {
        return $this->belongsTo(Team::class, 'origin_team', 'id');
    }

    public function destinyTeam() {
        return $this->belongsTo(Team::class, 'destiny_team', 'id');
    }

    public function negotiationPlayers() {
        return $this->hasMany(NegotiationPlayers::class, 'negotiation', 'id');
    }

    
}
