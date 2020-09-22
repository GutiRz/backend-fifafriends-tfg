<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalendarLeague
{
    public function generateCalendar($teamsInRound)
    {
        if (!is_array($teamsInRound)) {
            throw new \InvalidArgumentException('BergerAlgorithm expects an array of teams');
        }

        shuffle($teamsInRound);
        $teamsCount = count($teamsInRound);

        if ($teamsCount === 0) {
            throw new \InvalidArgumentException('BergerAlgorithm expects an array of at least 1 team');
        }

        // if odd, add a dummy team
        if ($teamsCount % 2 == 1) {
            $teamsInRound[] = 'REST';
            $teamsCount++;
        }

        //if just 2 teams, skip the whole process
        if (!($teamsCount > 2)) {
            return [
                [$teamsInRound[0], $teamsInRound[1]],
            ];
        }

        $gamesCount = $teamsCount - 1;

        $home = [];
        $away = [];

        for ($i = 0; $i < $teamsCount / 2; $i++) {
            $home[$i] = $teamsInRound[$i];
            $away[$i] = $teamsInRound[$teamsCount - 1 - $i];
        }

        $calendar = [];
        for ($i = 0; $i < $gamesCount; $i++) {
            if (($i % 2) == 0) {
                for ($j = 0; $j < $teamsCount / 2; $j++) {
                    $calendar[$i][] = [$away[$j], $home[$j]];
                }
            } else {
                for ($j = 0; $j < $teamsCount / 2; $j++) {
                    $calendar[$i][] = [$home[$j], $away[$j]];
                }
            }

            $pivot = $home[0];
            array_unshift($away, $home[1]);
            $carryover = array_pop($away);
            array_shift($home);
            array_push($home, $carryover);
            $home[0] = $pivot;
        }//endfor

        // convert array into collection
        $calendar = collect($calendar)->recursive();


        // generate second round
        foreach ($calendar as $journey) {
            $secondRoundJourney = collect();
            foreach ($journey as $match) {
                $secondRoundJourney->push($match->reverse()->values());
            }
            $calendar->push($secondRoundJourney);
        }
        
        return $calendar;
    }
}
