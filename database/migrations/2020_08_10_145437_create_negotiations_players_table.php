<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNegotiationsPlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('negotiation_players', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('negotiation')->constrained('negotiations');
            $table->foreignId('player')->constrained('players');
            $table->foreignId('team')->constrained('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('negotiation_players');
    }
}
