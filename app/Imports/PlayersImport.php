<?php

namespace App\Imports;

use App\Events\PlayerCreated;
use App\Player;
use App\Team;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Row;

class PlayersImport implements WithHeadingRow, OnEachRow, WithProgressBar
{
    use Importable;

    public function onRow(Row $row)
    {
        $row = $row->toArray();

        if(!$row['jugador'] || !$row['equipo'] || !$row['transfermarkt']|| !$row['sofifa']) {
            return null;
        }

        $team = Team::where('name', $row['equipo'])->firstOrCreate([
            'name' => $row['equipo']
        ]);

        $player = Player::create([
            'sofifa' => $row['sofifa'],
            'transfermarkt' => $row['transfermarkt'],
            'team_id' => $team->id
        ]);

        if($player) {
            event(new PlayerCreated($player));
        }

    }
}
