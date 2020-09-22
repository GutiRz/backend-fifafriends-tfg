<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ScrapInfoPlayer
{
    public function handle($event)
    {
        $event->player->scrapInfo();
    }
}
