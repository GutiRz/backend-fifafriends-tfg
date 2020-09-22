<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('slug');
            $table->double('value')->default(0);
            $table->double('salary')->default(0);
            $table->string('picture')->nullable();
            $table->integer('overall')->nullable();
            $table->boolean('starter')->default(false);
            $table->string('sofifa');
            $table->string('transfermarkt');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->timestamps();
        });
    }
}
