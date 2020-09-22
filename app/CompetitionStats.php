<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompetitionStats extends Model
{
    protected $guarded = [];
    protected $appends = ['points', 'played'];

    protected $with = ['team'];

    public function team() {
        return $this->belongsTo(Team::class, 'team');
    }

    public function competition() {
        return $this->belongsTo(Competition::class);
    }

    public function getPointsAttribute() {
        return $this->wins * 3 + $this->draws;
    }

    public function getPlayedAttribute() {
        return $this->wins + $this->draws + $this->loses;
    }
}
