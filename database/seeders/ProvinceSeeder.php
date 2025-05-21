<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data yang ada terlebih dahulu dengan cara yang aman
        // Gunakan DB::statement untuk menonaktifkan foreign key checks sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Province::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Ambil data provinsi dari API
        try {
            $response = Http::get('https://ibnux.github.io/data-indonesia/provinsi.json');
            
            if ($response->successful()) {
                $provinces = $response->json();
                
                foreach ($provinces as $province) {
                    Province::create([
                        'name' => $province['nama'],
                        'code' => $province['id']
                    ]);
                }
                
                $this->command->info('Berhasil mengimpor ' . count($provinces) . ' provinsi dari API');
            } else {
                // Gunakan data fallback jika API gagal
                $this->seedFallbackData();
            }
        } catch (\Exception $e) {
            $this->command->error('Error mengambil data dari API: ' . $e->getMessage());
            // Gunakan data fallback jika API gagal
            $this->seedFallbackData();
        }
    }
    
    /**
     * Seed fallback data jika API tidak tersedia
     */
    private function seedFallbackData()
    {
        $this->command->info('Menggunakan data provinsi fallback');
        
        // Daftar provinsi di Indonesia sebagai fallback
        $provinces = [
            ['name' => 'Aceh', 'code' => 'AC'],
            ['name' => 'Sumatera Utara', 'code' => 'SU'],
            ['name' => 'Sumatera Barat', 'code' => 'SB'],
            ['name' => 'Riau', 'code' => 'RI'],
            ['name' => 'Jambi', 'code' => 'JA'],
            ['name' => 'Sumatera Selatan', 'code' => 'SS'],
            ['name' => 'Bengkulu', 'code' => 'BE'],
            ['name' => 'Lampung', 'code' => 'LA'],
            ['name' => 'Kepulauan Bangka Belitung', 'code' => 'BB'],
            ['name' => 'Kepulauan Riau', 'code' => 'KR'],
            ['name' => 'DKI Jakarta', 'code' => 'JK'],
            ['name' => 'Jawa Barat', 'code' => 'JB'],
            ['name' => 'Jawa Tengah', 'code' => 'JT'],
            ['name' => 'DI Yogyakarta', 'code' => 'YO'],
            ['name' => 'Jawa Timur', 'code' => 'JI'],
            ['name' => 'Banten', 'code' => 'BT'],
            ['name' => 'Bali', 'code' => 'BA'],
            ['name' => 'Nusa Tenggara Barat', 'code' => 'NB'],
            ['name' => 'Nusa Tenggara Timur', 'code' => 'NT'],
            ['name' => 'Kalimantan Barat', 'code' => 'KB'],
            ['name' => 'Kalimantan Tengah', 'code' => 'KT'],
            ['name' => 'Kalimantan Selatan', 'code' => 'KS'],
            ['name' => 'Kalimantan Timur', 'code' => 'KI'],
            ['name' => 'Kalimantan Utara', 'code' => 'KU'],
            ['name' => 'Sulawesi Utara', 'code' => 'SA'],
            ['name' => 'Sulawesi Tengah', 'code' => 'ST'],
            ['name' => 'Sulawesi Selatan', 'code' => 'SN'],
            ['name' => 'Sulawesi Tenggara', 'code' => 'SG'],
            ['name' => 'Gorontalo', 'code' => 'GO'],
            ['name' => 'Sulawesi Barat', 'code' => 'SR'],
            ['name' => 'Maluku', 'code' => 'MA'],
            ['name' => 'Maluku Utara', 'code' => 'MU'],
            ['name' => 'Papua', 'code' => 'PA'],
            ['name' => 'Papua Barat', 'code' => 'PB'],
        ];
        
        // Masukkan data provinsi fallback
        foreach ($provinces as $province) {
            Province::create($province);
        }
    }
}
