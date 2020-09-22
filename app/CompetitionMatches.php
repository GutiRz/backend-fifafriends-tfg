<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompetitionMatches extends Model
{
    protected $guarded = [];

    protected $with = ['localTeam', 'awayTeam'];
    
    const MATCH_CLOSED = 'closed';
    const MATCH_OPEN = 'open';
    const MATCH_POSTED = 'posted';
    const MATCH_CONFIRMED = 'confirmed';

    public function localTeam() {
        return $this->belongsTo(Team::class, 'local_team', 'id');
    }

    public function awayTeam() {
        return $this->belongsTo(Team::class, 'away_team');
    }
}
