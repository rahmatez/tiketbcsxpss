@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white p-3 d-flex align-items-center">
                    <i class="fas fa-user-edit me-2"></i>
                    <h3 class="mb-0 fw-bold">Edit Profile</h3>
                </div>
                
                <div class="card-body p-4">
                    <!-- Display Validation Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger fade show">
                            <div class="d-flex">
                                <i class="fas fa-exclamation-circle fs-5 me-2"></i>
                                <div>
                                    <h5 class="alert-heading">Please fix the following errors:</h5>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="text-center mb-4 mt-2">
                        <div class="avatar-circle mx-auto mb-3">
                            <span class="avatar-initials">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    </div>
                    
                    <form action="{{ route('profile.update', $user->id) }}" method="POST" class="profile-edit-form">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user text-primary me-2"></i>Nama Lengkap
                                    </label>
                                    <input type="text" id="name" name="name" value="{{ $user->name }}" required 
                                        class="form-control form-control-lg" placeholder="Masukkan nama lengkap">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope text-primary me-2"></i>Email
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ $user->email }}" required 
                                        class="form-control form-control-lg" placeholder="Masukkan alamat email">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone_number" class="form-label">
                                <i class="fas fa-phone text-primary me-2"></i>Nomor Telepon
                            </label>
                            <input type="text" id="phone_number" name="phone_number" value="{{ $user->phone_number }}" 
                                class="form-control form-control-lg" placeholder="Masukkan nomor telepon">
                        </div>

                        <div class="form-group mb-3">
                            <label for="address" class="form-label">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>Alamat
                            </label>
                            <textarea id="address" name="address" class="form-control form-control-lg" 
                                placeholder="Masukkan alamat lengkap" rows="2">{{ $user->address }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="province_id" class="form-label">
                                        <i class="fas fa-map text-primary me-2"></i>Provinsi
                                    </label>
                                    <select id="province_id" name="province_id" class="form-select form-select-lg">
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="city_id" class="form-label">
                                        <i class="fas fa-city text-primary me-2"></i>Kota/Kabupaten
                                    </label>
                                    <select id="city_id" name="city_id" class="form-select form-select-lg">
                                        <option value="">Pilih Kota/Kabupaten</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-save me-2"></i>Simpan
                            </button>
                            <a href="{{ route('profile.show', $user->id) }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(13, 106, 55, 0.2);
    }
    
    .avatar-initials {
        font-size: 40px;
        font-weight: bold;
        color: white;
        text-transform: uppercase;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #4a5568;
    }
    
    .form-control, .form-select {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(13, 106, 55, 0.1);
        background-color: #fff;
    }
    
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card-header {
        border-bottom: none;
        background-color: var(--primary-color) !important;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background-color: #095830;
        border-color: #095830;
        transform: translateY(-2px);
    }
    
    .btn-outline-secondary:hover {
        transform: translateY(-2px);
    }
    
    .text-primary {
        color: var(--primary-color) !important;
    }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get saved province and city IDs
        const savedProvinceId = '{{ $user->province_id }}';
        const savedCityId = '{{ $user->city_id }}';
        
        // Load provinces on page load
        fetch('{{ route("api.provinces") }}')
            .then(response => response.json())
            .then(provinces => {
                const provinceSelect = document.getElementById('province_id');
                
                // Add provinces to dropdown
                provinces.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.id;
                    option.textContent = province.name;
                    
                    // Set selected province
                    if (province.id == savedProvinceId) {
                        option.selected = true;
                    }
                    
                    provinceSelect.appendChild(option);
                });
                
                // If there's a selected province, load its cities
                if (provinceSelect.value) {
                    loadCities(provinceSelect.value, savedCityId);
                }
            });
        
        // Handle province selection change
        document.getElementById('province_id').addEventListener('change', function() {
            loadCities(this.value);
        });
        
        // Function to load cities for a province
        function loadCities(provinceId, selectedCityId = null) {
            if (!provinceId) {
                // If no province selected, clear cities dropdown
                const citySelect = document.getElementById('city_id');
                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                return;
            }
            
            fetch(`{{ url('api/provinces') }}/${provinceId}/cities`)
                .then(response => response.json())
                .then(cities => {
                    const citySelect = document.getElementById('city_id');
                    citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                    
                    // Add cities to dropdown
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        
                        // Set selected city
                        if (selectedCityId && city.id == selectedCityId) {
                            option.selected = true;
                        }
                        
                        citySelect.appendChild(option);
                    });
                });
        }
    });
</script>
@endpush
