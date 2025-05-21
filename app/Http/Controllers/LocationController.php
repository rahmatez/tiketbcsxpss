<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\City;

class LocationController extends Controller
{
    /**
     * Get all provinces.
     */
    public function getProvinces()
    {
        $provinces = Province::orderBy('name')->get();
        return response()->json($provinces);
    }
    
    /**
     * Get cities by province ID.
     */
    public function getCitiesByProvince($provinceId)
    {
        $cities = City::where('province_id', $provinceId)
                      ->orderBy('name')
                      ->get();
                      
        return response()->json($cities);
    }
}
