<?php

namespace App\Console\Commands;

use App\Imports\PlayersImport;
use Illuminate\Console\Command;

class ImportPlayersFromExcel extends Command
{
    protected $signature = 'players:import-excel {file}';

    protected $description = 'Import all players of Fifafriends from Excel';

    public function handle()
    {
        (new PlayersImport())->withOutput($this->output)->import(storage_path($this->argument('file')));
    }
}
