<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClausesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->dateTime('start_date');
            $table->string('game');
            $table->timestamps();
        });

        Schema::create('clauses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_team')->constrained('teams');
            $table->foreignId('destiny_team')->constrained('teams');
            $table->enum('state', [\App\Clause::CLAUSE_OPEN, \App\Clause::CLAUSE_PENDING, \App\Clause::CLAUSE_ACCEPTED,
                \App\Clause::CLAUSE_REJECTED])->default(\App\Clause::CLAUSE_OPEN);
            $table->foreignId('player_id')->constrained('players');
            $table->foreignId('season')->constrained('seasons');
            $table->float('dice')->nullable();
            $table->integer('order')->default(0);
            $table->timestamp('launchable_at');
            $table->boolean('is_launchable')->default(true);
            $table->timestamps();
        });
    }
}
