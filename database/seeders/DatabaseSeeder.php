<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks sementara untuk semua seeder
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Seed data provinsi dan kota
        $this->call([
            ProvinceSeeder::class,
            CitySeeder::class,
        ]);
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Buat user admin jika belum ada
        if (!Admin::where('email', 'admin@gmail.com')->exists()) {
            Admin::create([
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin'
            ]);
        }
        
        // Buat user biasa jika belum ada
        if (!User::where('email', 'user@example.com')->exists()) {
            User::create([
                'name' => 'Pengguna Demo',
                'email' => 'user@gmail.com',
                'password' => Hash::make('password'),
                'phone_number' => '081234567890'
            ]);
        }
        
        // Hapus data game dan ticket yang ada sebelum membuat yang baru
        // Berhati-hati dengan foreign key constraints
        // Nonaktifkan foreign key checks sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncate tables in the right order (dependent tables first)
        if (class_exists('\App\Models\TicketScan')) {
            \App\Models\TicketScan::truncate();
        }
        \App\Models\Order::truncate();
        Ticket::truncate();
        Game::truncate();
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Buat beberapa game sepak bola
        $game1 = Game::create([
            'home_team' => 'PSS Sleman',
            'away_team' => 'Persija',
            'is_home_game' => true,
            'tournament_name' => 'Liga 1 BRI',
            'match_time' => now()->addDays(7),
            'stadium_name' => 'Stadion Maguwoharjo',
            'description' => 'Pertandingan penting untuk menentukan puncak klasemen'
        ]);
        
        $game2 = Game::create([
            'home_team' => 'PSS Sleman',
            'away_team' => 'Persib',
            'is_home_game' => false,
            'tournament_name' => 'Liga 1 BRI',
            'match_time' => now()->addDays(14),
            'stadium_name' => 'Stadion Maguwoharjo',
            'description' => 'Pertandingan tandang melawan Pro FC'
        ]);
        
        $game3 = Game::create([
            'home_team' => 'PSS Sleman',
            'away_team' => 'Persebaya',
            'is_home_game' => true,
            'tournament_name' => 'Liga 1 BRI',
            'match_time' => now()->addDays(21),
            'stadium_name' => 'Stadion Maguwoharjo',
            'description' => 'Derby klasik'
        ]);
        
        // Buat tiket untuk pertandingan dengan kategori tribun baru
        // Game 1 tickets
        Ticket::create([
            'game_id' => $game1->id,
            'category' => 'Tribun Selatan',
            'quantity' => 500,
            'price' => 150000
        ]);
        
        Ticket::create([
            'game_id' => $game1->id,
            'category' => 'Tribun Utara',
            'quantity' => 500,
            'price' => 125000
        ]);
        
        Ticket::create([
            'game_id' => $game1->id,
            'category' => 'Tribun Timur',
            'quantity' => 750,
            'price' => 75000
        ]);
        
        Ticket::create([
            'game_id' => $game1->id,
            'category' => 'Tribun Barat',
            'quantity' => 750,
            'price' => 50000
        ]);
        
        // Game 2 tickets
        Ticket::create([
            'game_id' => $game2->id,
            'category' => 'Tribun Selatan',
            'quantity' => 500,
            'price' => 150000
        ]);
        
        Ticket::create([
            'game_id' => $game2->id,
            'category' => 'Tribun Utara',
            'quantity' => 500,
            'price' => 125000
        ]);
        
        Ticket::create([
            'game_id' => $game2->id,
            'category' => 'Tribun Timur',
            'quantity' => 750,
            'price' => 75000
        ]);
        
        Ticket::create([
            'game_id' => $game2->id,
            'category' => 'Tribun Barat',
            'quantity' => 750,
            'price' => 50000
        ]);
        
        // Game 3 tickets
        Ticket::create([
            'game_id' => $game3->id,
            'category' => 'Tribun Selatan',
            'quantity' => 500,
            'price' => 150000
        ]);
        
        Ticket::create([
            'game_id' => $game3->id,
            'category' => 'Tribun Utara',
            'quantity' => 500,
            'price' => 125000
        ]);
        
        Ticket::create([
            'game_id' => $game3->id,
            'category' => 'Tribun Timur',
            'quantity' => 750,
            'price' => 75000
        ]);
        
        Ticket::create([
            'game_id' => $game3->id,
            'category' => 'Tribun Barat',
            'quantity' => 750,
            'price' => 50000
        ]);
    }
}
