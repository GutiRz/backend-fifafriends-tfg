<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Negotiation;

class CreateNegotiationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('negotiations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('origin_team')->constrained('teams');
            $table->foreignId('destiny_team')->constrained('teams');
            $table->double('money_origin')->default(0);
            $table->double('money_destiny')->default(0);
            $table->enum('status', [Negotiation::NEGOTIATION_PENDING, Negotiation::NEGOTIATION_ACCEPTED, 
                    Negotiation::NEGOTIATION_REJECTED])->default(Negotiation::NEGOTIATION_PENDING);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('negotiations');
    }
}
