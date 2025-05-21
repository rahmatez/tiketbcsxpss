<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Game;
use Carbon\Carbon;

class ConvertPurchaseDurationToPurchaseDeadline extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pertama tambahkan kolom purchase_deadline bila belum ada
        if (!Schema::hasColumn('games', 'purchase_deadline')) {
            Schema::table('games', function (Blueprint $table) {
                $table->timestamp('purchase_deadline')->nullable()->after('match_time');
            });
        }
        
        // Konversi data: Set purchase_deadline berdasarkan match_time dikurangi purchase_duration jam
        $games = Game::all();
        foreach ($games as $game) {
            if ($game->match_time && isset($game->purchase_duration) && $game->purchase_duration) {
                // Hitung purchase_deadline sebagai match_time dikurangi purchase_duration jam
                try {
                    $deadline = Carbon::parse($game->match_time)->subHours($game->purchase_duration);
                    $game->purchase_deadline = $deadline;
                    $game->save();
                } catch (\Exception $e) {
                    // Log error jika terjadi masalah saat konversi
                    Log::error("Gagal konversi game ID {$game->id}: " . $e->getMessage());
                }
            } else if ($game->match_time && !$game->purchase_deadline) {
                // Jika tidak ada purchase_duration, set default 24 jam sebelum match_time
                $game->purchase_deadline = Carbon::parse($game->match_time)->subHours(24);
                $game->save();
            }
        }
        
        // Hapus kolom purchase_duration jika diperlukan dan jika kolom tersebut ada
        if (Schema::hasColumn('games', 'purchase_duration')) {
            Schema::table('games', function (Blueprint $table) {
                $table->dropColumn('purchase_duration');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tambahkan kembali kolom purchase_duration jika tidak ada
        if (!Schema::hasColumn('games', 'purchase_duration')) {
            Schema::table('games', function (Blueprint $table) {
                $table->integer('purchase_duration')->default(24)->after('match_time');
            });
            
            // Konversi balik data jika purchase_deadline ada
            if (Schema::hasColumn('games', 'purchase_deadline')) {
                $games = Game::all();
                foreach ($games as $game) {
                    if ($game->match_time && $game->purchase_deadline) {
                        try {
                            // Hitung selisih waktu dalam jam
                            $duration = Carbon::parse($game->match_time)->diffInHours(Carbon::parse($game->purchase_deadline));
                            $game->purchase_duration = $duration;
                            $game->save();
                        } catch (\Exception $e) {
                            // Log error
                            Log::error("Gagal konversi balik game ID {$game->id}: " . $e->getMessage());
                            // Default 24 jam
                            $game->purchase_duration = 24;
                            $game->save();
                        }
                    } else {
                        // Default 24 jam
                        $game->purchase_duration = 24;
                        $game->save();
                    }
                }
            }
        }

        // Hapus kolom purchase_deadline jika ada
        if (Schema::hasColumn('games', 'purchase_deadline')) {
            Schema::table('games', function (Blueprint $table) {
                $table->dropColumn('purchase_deadline');
            });
        }
    }
}