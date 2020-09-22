<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NegotiationPlayers extends Model
{
    protected $guarded = [];

    public function negotiation() {
        return $this->belongsTo(Negotiation::class, 'negotiation', 'id');
    }

    public function players() {
        return $this->hasOne(Player::class, 'id', 'player');
    }
}
