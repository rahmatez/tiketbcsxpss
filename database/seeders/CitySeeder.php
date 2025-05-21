<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data yang ada terlebih dahulu dengan cara yang aman
        // Gunakan DB::statement untuk menonaktifkan foreign key checks sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        City::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Ambil semua provinsi
        $provinces = Province::all();
        
        // Variable untuk menghitung total kota yang ditambahkan
        $totalCities = 0;
        
        // Ambil data kota untuk setiap provinsi dari API
        foreach ($provinces as $province) {
            $this->command->info("Mengambil data kota untuk provinsi {$province->name}...");
            
            try {
                // Ambil data kota berdasarkan province code
                $response = Http::get("https://ibnux.github.io/data-indonesia/kabupaten/{$province->code}.json");
                
                if ($response->successful()) {
                    $cities = $response->json();
                    
                    foreach ($cities as $city) {
                        // Tentukan tipe (Kota atau Kabupaten) berdasarkan nama
                        $type = 'Kabupaten';
                        $name = $city['nama'];
                        
                        // Jika nama dimulai dengan "KOTA", ubah tipe menjadi Kota dan hilangkan "KOTA " dari nama
                        if (strpos(strtoupper($name), 'KOTA ') === 0) {
                            $type = 'Kota';
                            $name = substr($name, 5); // Hilangkan "KOTA " dari nama
                        }
                        
                        // Menggunakan postal_code default (akan kosong di database)
                        $postalCode = '';
                        
                        // Buat record city
                        City::create([
                            'province_id' => $province->id,
                            'name' => $name,
                            'type' => $type,
                            'postal_code' => $postalCode
                        ]);
                        
                        $totalCities++;
                    }
                    
                    $this->command->info("Ditambahkan " . count($cities) . " kota/kabupaten untuk provinsi {$province->name}");
                } else {
                    $this->command->error("Gagal mengambil data kota untuk provinsi {$province->name}: " . $response->status());
                    // Jika gagal, gunakan data fallback untuk provinsi ini
                    $this->seedFallbackData($province);
                }
            } catch (\Exception $e) {
                $this->command->error("Error mengambil data kota untuk provinsi {$province->name}: " . $e->getMessage());
                // Jika gagal, gunakan data fallback untuk provinsi ini
                $this->seedFallbackData($province);
            }
        }
        
        $this->command->info("Total {$totalCities} kota/kabupaten berhasil ditambahkan");
    }
    
    /**
     * Seed fallback data untuk provinsi tertentu jika API tidak tersedia
     */
    private function seedFallbackData($province)
    {
        $this->command->info("Menggunakan data fallback untuk provinsi {$province->name}");
        
        // Fallback data hanya untuk beberapa provinsi utama
        $fallbackCities = [];
        
        switch ($province->name) {
            case 'DKI Jakarta':
                $fallbackCities = [
                    ['name' => 'Jakarta Pusat', 'type' => 'Kota', 'postal_code' => '10110'],
                    ['name' => 'Jakarta Utara', 'type' => 'Kota', 'postal_code' => '14140'],
                    ['name' => 'Jakarta Barat', 'type' => 'Kota', 'postal_code' => '11220'],
                    ['name' => 'Jakarta Selatan', 'type' => 'Kota', 'postal_code' => '12230'],
                    ['name' => 'Jakarta Timur', 'type' => 'Kota', 'postal_code' => '13330'],
                    ['name' => 'Kepulauan Seribu', 'type' => 'Kabupaten', 'postal_code' => '14550'],
                ];
                break;
            case 'Jawa Barat':
                $fallbackCities = [
                    ['name' => 'Bandung', 'type' => 'Kota', 'postal_code' => '40111'],
                    ['name' => 'Bekasi', 'type' => 'Kota', 'postal_code' => '17121'],
                    ['name' => 'Bogor', 'type' => 'Kota', 'postal_code' => '16119'],
                    ['name' => 'Cimahi', 'type' => 'Kota', 'postal_code' => '40512'],
                    ['name' => 'Depok', 'type' => 'Kota', 'postal_code' => '16416'],
                ];
                break;
            case 'Jawa Tengah':
                $fallbackCities = [
                    ['name' => 'Semarang', 'type' => 'Kota', 'postal_code' => '50131'],
                    ['name' => 'Surakarta', 'type' => 'Kota', 'postal_code' => '57113'],
                    ['name' => 'Tegal', 'type' => 'Kota', 'postal_code' => '52114'],
                ];
                break;
            // Tambahkan data fallback untuk provinsi lainnya jika diperlukan
        }
        
        // Masukkan data fallback
        foreach ($fallbackCities as $cityData) {
            City::create([
                'province_id' => $province->id,
                'name' => $cityData['name'],
                'type' => $cityData['type'],
                'postal_code' => $cityData['postal_code']
            ]);
        }
        
        $this->command->info("Ditambahkan " . count($fallbackCities) . " kota/kabupaten fallback untuk provinsi {$province->name}");
    }
}
