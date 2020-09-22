<?php

namespace App\Http\Controllers;

use App\Competition;
use App\Season;
use App\CompetitionStats;
use App\CompetitionMatches;
use App\MatchStats;
use App\CalendarLeague;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompetitionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Competition::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $teams =  $request->teams;
        
        $season =  Season::latest()->first();

        $competition = Competition::create([
            'name' => $request->name,
            'season' => $season->number,
            'division' => $request->division,
        ]);

        foreach($teams as $team) {
            $teamStats = CompetitionStats::create([
                'competition' => $competition->id,
                'team' => $team,
            ]);
        }

        $calendar = new CalendarLeague();
        $leagueCalendar = $calendar->generateCalendar($teams);
        
        $round = 1;
        foreach($leagueCalendar as $journey) {
            foreach($journey as $match) {
                $competitionMatch = CompetitionMatches::create([
                    'competition' => $competition->id,
                    'local_team' => $match->first(),
                    'away_team' => $match->last(),
                    'round' => $round
                ]);
            }
            $round++;
        }

        return $competition;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Competition  $competition
     * @return \Illuminate\Http\Response
     */
    public function show(Competition $competition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Competition  $competition
     * @return \Illuminate\Http\Response
     */
    public function edit(Competition $competition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Competition  $competition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Competition $competition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Competition  $competition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Competition $competition)
    {
        //
    }


    public function openRound(Request $request, Competition $competition) {
        $numRound = $request->round;
        $season =  Season::latest()->first();

        $matches = CompetitionMatches::where('competition', $competition->id)->where('round', $numRound)
            ->update([
                'status' => \App\CompetitionMatches::MATCH_OPEN,
            ]);

        return $matches; 
    }
    
    public function postResult(Request $request) {
        $season =  Season::latest()->first();
        $match = CompetitionMatches::find($request->match);

        $local_goals = $request->home_goals;
        $away_goals = $request->away_goals;
        
        $competition_stats_local = CompetitionStats::where('team', $match->local_team)->get()->first();
        $competition_stats_away = CompetitionStats::where('team', $match->away_team)->get()->first();

        //competition stats
        $wins_local = $competition_stats_local->wins;
        $wins_away = $competition_stats_away->wins;
        $draws_local = $competition_stats_local->draws;
        $draws_away = $competition_stats_away->draws;
        $defeats_local = $competition_stats_local->defeats;
        $defeats_away = $competition_stats_away->defeats;

        if ($local_goals > $away_goals) {
            $wins_local++;
            $defeats_away++;
        } else if ($away_goals > $local_goals) {
            $wins_away++;
            $defeats_local++;
        } else {
            $draws_local++;
            $draws_away++;
        }

        $competition_stats_local->update([
            'wins' => $wins_local,
            'draws' => $draws_local,
            'defeats' => $defeats_local,
            'goals' => $competition_stats_local->goals + $local_goals,
            'goals_conceded' => $competition_stats_local->goals_conceded + $away_goals
        ]);

        $competition_stats_away->update([
            'wins' => $wins_away,
            'draws' => $draws_away,
            'defeats' => $defeats_away,
            'goals' => $competition_stats_away->goals + $away_goals,
            'goals_conceded' => $competition_stats_away->goals_conceded + $local_goals
        ]);

        $match->update([
            'local_goals' =>  $request->home_goals,
            'away_goals' =>  $request->away_goals,
            'status' => \App\CompetitionMatches::MATCH_POSTED
        ]);

        //match stats
        $stats = $request->stats;
        
        foreach($stats as $stat) {
            $goals = !empty($stat["goals"]) ? $stat["goals"] : 0;
            $assists = !empty($stat["assists"]) ? $stat["assists"] : 0;
            $mvp = !empty($stat["mvps"]) ? $stat["mvps"] : 0;
            $injured = !empty($stat["injuries"]) ? $stat["injuries"] : 0;
            $yellow_card = !empty($stat["yellows"]) ? $stat["yellows"] : 0;
            $red_card = !empty($stat["reds"]) ? $stat["reds"] : 0;

            MatchStats::create([
                'match' => $match->id,
                'player' => $stat["id"],
                'goals' => $goals,
                'assists' => $assists,
                'mvp' => $mvp,
                'injured' => $injured,
                'yellow_card' => $yellow_card,
                'red_card' => $red_card,
            ]);
        }

        return $match;
    }

    /* public function confirmResult(Request $request) {

    } */

    public function clasificationTable(Competition $competition) {
        return $competition->competitionStats->sortByDesc('points')->flatten();
    }

    public function showRounds(Competition $competition) {
        return $competition->load('competitionMatches');
    }

    public function showStats(Competition $competition) {
        $goal_list = DB::select("SELECT p.id, p.name, p.picture, SUM(ms.goals) as goals FROM fifafriends.competitions c
                        INNER JOIN fifafriends.competition_matches cm ON c.id = cm.competition
                        INNER JOIN fifafriends.match_stats ms ON ms.match = cm.id
                        INNER JOIN fifafriends.players p ON p.id = ms.player
                        WHERE c.id=$competition->id
                        GROUP BY p.id
                        HAVING SUM(ms.goals) > 0
                        ORDER BY goals desc");

        $assist_list = DB::select("SELECT p.id, p.name, p.picture, SUM(ms.assists) as assists FROM fifafriends.competitions c
                        INNER JOIN fifafriends.competition_matches cm ON c.id = cm.competition
                        INNER JOIN fifafriends.match_stats ms ON ms.match = cm.id
                        INNER JOIN fifafriends.players p ON p.id = ms.player
                        WHERE c.id=$competition->id
                        GROUP BY p.id
                        HAVING SUM(ms.assists) > 0
                        ORDER BY assists desc");

        $mvp_list = DB::select("SELECT p.id, p.name, p.picture, SUM(ms.mvp) as mvps FROM fifafriends.competitions c
                        INNER JOIN fifafriends.competition_matches cm ON c.id = cm.competition
                        INNER JOIN fifafriends.match_stats ms ON ms.match = cm.id
                        INNER JOIN fifafriends.players p ON p.id = ms.player
                        WHERE c.id=$competition->id
                        GROUP BY p.id
                        HAVING SUM(ms.mvp) > 0
                        ORDER BY mvps desc");  
                        
        $injury_list = DB::select("SELECT p.id, p.name, p.picture, SUM(ms.injured) as injuries FROM fifafriends.competitions c
                        INNER JOIN fifafriends.competition_matches cm ON c.id = cm.competition
                        INNER JOIN fifafriends.match_stats ms ON ms.match = cm.id
                        INNER JOIN fifafriends.players p ON p.id = ms.player
                        WHERE c.id=$competition->id
                        GROUP BY p.id
                        HAVING SUM(ms.injured) > 0
                        ORDER BY injuries desc");                 
                        
        $yellow_list = DB::select("SELECT p.id, p.name, p.picture, SUM(ms.yellow_card) as yellows FROM fifafriends.competitions c
                        INNER JOIN fifafriends.competition_matches cm ON c.id = cm.competition
                        INNER JOIN fifafriends.match_stats ms ON ms.match = cm.id
                        INNER JOIN fifafriends.players p ON p.id = ms.player
                        WHERE c.id=$competition->id
                        GROUP BY p.id
                        HAVING SUM(ms.yellow_card) > 0
                        ORDER BY yellows desc"); 
                       
        $red_list = DB::select("SELECT p.id, p.name, p.picture, SUM(ms.red_card) as reds FROM fifafriends.competitions c
                        INNER JOIN fifafriends.competition_matches cm ON c.id = cm.competition
                        INNER JOIN fifafriends.match_stats ms ON ms.match = cm.id
                        INNER JOIN fifafriends.players p ON p.id = ms.player
                        WHERE c.id=$competition->id
                        GROUP BY p.id
                        HAVING SUM(ms.red_card) > 0
                        ORDER BY reds desc");   
                        
        $stats = collect(["goals" => $goal_list, "assists" => $assist_list, "mvps" => $mvp_list, "injuries" => $injury_list, "yellows" => $yellow_list, "reds" => $red_list]);

        return $stats;
    }
}
