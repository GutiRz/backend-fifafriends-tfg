<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $guarded = [];

    public function competitionStats() {
        return $this->hasMany(CompetitionStats::class, 'competition', 'id');
    }

    public function competitionMatches() {
        return $this->hasMany(CompetitionMatches::class, 'competition', 'id');
    }
}
