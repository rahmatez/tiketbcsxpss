<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('home_team');
            $table->string('away_team');
            $table->boolean('is_home_game')->default(true);
            $table->string('tournament_name')->nullable();
            $table->timestamp('match_time');
            $table->timestamp('purchase_deadline')->nullable(); // Batas waktu pembelian tiket
            $table->string('stadium_name')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');    }
}
