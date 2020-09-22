<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\CompetitionMatches;

class CreateCompetitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->integer('division')->nullable();
            $table->foreignId('season')->constrained('seasons');
        });

        Schema::create('competition_stats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('competition')->constrained('competitions');
            $table->foreignId('team')->constrained('teams');
            $table->integer('wins')->default(0);
            $table->integer('draws')->default(0);
            $table->integer('defeats')->default(0);
            $table->integer('goals')->default(0);
            $table->integer('goals_conceded')->default(0);
        });

        Schema::create('competition_matches', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('competition')->constrained('competitions');
            $table->foreignId('local_team')->constrained('teams');
            $table->foreignId('away_team')->constrained('teams');
            $table->integer('round');
            $table->integer('local_goals')->nullable();
            $table->integer('away_goals')->nullable();
            $table->enum('status', [\App\CompetitionMatches::MATCH_CLOSED, \App\CompetitionMatches::MATCH_OPEN, \App\CompetitionMatches::MATCH_POSTED,
                \App\CompetitionMatches::MATCH_CONFIRMED])->default(\App\CompetitionMatches::MATCH_CLOSED);
        });

        Schema::create('match_stats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('match')->constrained('competition_matches');
            $table->foreignId('player')->constrained('players');
            $table->integer('goals')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('mvp')->default(0);
            $table->integer('injured')->default(0);
            $table->integer('yellow_card')->default(0);
            $table->integer('red_card')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competition');
    }
}
