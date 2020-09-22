<?php

namespace App\Console\Commands;

use App\Player;
use Illuminate\Console\Command;

class ExtractPlayerInfo extends Command
{
    protected $signature = 'players:crawl';

    protected $description = 'Command to scrap player info from sofifa & transfermarkt';

    public function handle()
    {
        Player::all()->each(function ($player) {
            $player->scrapInfo();
        });
    }
}
