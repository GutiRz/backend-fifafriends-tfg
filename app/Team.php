<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Team extends Model
{
    use HasSlug;

    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function players() {
        return $this->hasMany(Player::class);
    }

    public function getNegotationsAttribute() {
        return $this->negotiationsIncoming->merge($this->negotiationsOutgoing);
    }

    public function negotiationsIncoming() {
        return $this->hasMany(Negotiation::class, 'destiny_team', 'id');
    }

    public function negotiationsOutgoing() {
        return $this->hasMany(Negotiation::class, 'origin_team', 'id');
    }

    public function clausesIncoming() {
        return $this->hasMany(Clause::class, 'origin_team', 'id');
    }

    public function clausesOutgoing() {
        return $this->hasMany(Clause::class, 'destiny_team', 'id');
    }

    public function getTotalSalariesAttribute() {
        return $this->players()->sum('salary');
    }

    public function competitionStats() {
        return $this->hasMany(CompetitionStats::class, 'team', 'id');
    }

    public function getCompetitionMatches() {
        return $this->getLocalCompetitionMatches->merge($this->getAwayCompetitionMatches);
    }

    public function getLocalCompetitionMatches() {
        return $this->hasMany(CompetitionMatches::class, 'local_team', 'id');
    }
    
    public function getAwayCompetitionMatches() {
        return $this->hasMany(CompetitionMatches::class, 'away_team', 'id');
    }
}
