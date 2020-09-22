<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class Player extends Model
{
    use HasSlug;

    protected $guarded = [];
    protected $appends = ['clause'];

    const POSITIONS = ['POR', 'DFC', 'LTD', 'LTI', 'MCD', 'MC', 'MCO', 'MD', 'MI', 'DC', 'SD', 'ED', 'EI'];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getClauseAttribute() {
        return $this->salary * 10;
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }

    public function scrapInfo(){
        $html = Http::get($this->sofifa)->body();
        $crawler = new Crawler($html);

        $crawler->filter('.header h1')->each(function (Crawler $elem) {
            $this->name =  $elem->text();
        });

        $this->position = $crawler->filter('.info .meta .pos')->first()->text();

        $crawler->filter('.wrapper .player')->each(function (Crawler $elem)  {
            $urlImage = $elem->children('img')->getNode(0)->attributes->getNamedItem('data-src')->nodeValue;
            $image = Http::get($urlImage)->body();
            $name = "player-{$this->id}.jpg";
            $path = "public/players/{$name}";
            Storage::put($path, $image);
            $this->picture = Storage::url($path);
        });

        $crawler->filter('.wrapper .player section')->each(function (Crawler $elem)  {
            $overall = $elem->filter('span')->text();

            $this->overall = $overall;
        });

        $html = Http::get($this->transfermarkt)->body();
        $crawler = new Crawler($html);
        $crawler->filter('.dataMarktwert')->each(function (Crawler $elem) {
            $node = explode('mil', $elem->text());
            $value = (float)trim(Str::replaceFirst(',', '.',$node[0]));
            $value *= 1000000;
            $this->value = $value;
        });
        $this->save();
    }
}
