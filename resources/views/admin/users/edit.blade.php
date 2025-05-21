@extends('layouts.admin')

@section('title', 'Edit Pengguna - BCSXPSS FC')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Edit Pengguna</h1>
            <p class="text-muted">
                Edit informasi pengguna dengan ID: {{ $user->id }}
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-user-edit me-2"></i>Edit Pengguna: {{ $user->name }}
            </h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <label for="name" class="col-md-3 col-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="email" class="col-md-3 col-form-label">Email <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Email ini akan digunakan untuk login.</small>
                    </div>
                </div>                <div class="row mb-3">
                    <label for="phone_number" class="col-md-3 col-form-label">Nomor Telepon</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="province_id" class="col-md-3 col-form-label">Provinsi</label>
                    <div class="col-md-9">
                        <select class="form-select @error('province_id') is-invalid @enderror" id="province_id" name="province_id">
                            <option value="">Pilih Provinsi</option>
                            @foreach(App\Models\Province::orderBy('name')->get() as $province)
                                <option value="{{ $province->id }}" {{ old('province_id', $user->province_id) == $province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('province_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="city_id" class="col-md-3 col-form-label">Kota/Kabupaten</label>
                    <div class="col-md-9">
                        <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id">
                            <option value="">Pilih Kota/Kabupaten</option>
                            @if($user->province_id)
                                @foreach(App\Models\City::where('province_id', $user->province_id)->orderBy('name')->get() as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id', $user->city_id) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="address" class="col-md-3 col-form-label">Alamat</label>
                    <div class="col-md-9">
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="city_id" class="col-md-3 col-form-label">Kota/Kabupaten</label>
                    <div class="col-md-9">
                        <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id">
                            <option value="">Pilih Kota/Kabupaten</option>
                            @if($user->province_id)
                                @foreach(App\Models\City::where('province_id', $user->province_id)->orderBy('name')->get() as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id', $user->city_id) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="birth_date" class="col-md-3 col-form-label">Tanggal Lahir</label>
                    <div class="col-md-9">
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="gender" class="col-md-3 col-form-label">Jenis Kelamin</label>
                    <div class="col-md-9">
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="password" class="col-md-3 col-form-label">Password</label>
                    <div class="col-md-9">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah password</small>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="password_confirmation" class="col-md-3 col-form-label">Konfirmasi Password</label>
                    <div class="col-md-9">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-9 offset-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ (old('is_active', $user->is_active) == 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Pengguna Aktif
                            </label>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row mb-0">
                    <div class="col-md-9 offset-md-3">
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-2"></i>Perbarui Pengguna
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-danger text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>Zona Berbahaya
            </h5>
        </div>
        <div class="card-body">
            <h5 class="card-title">Hapus Pengguna</h5>
            <p class="card-text">
                Penghapusan akun pengguna bersifat permanen dan tidak dapat dikembalikan. 
                Pengguna tidak dapat dihapus jika memiliki pesanan yang terkait.
            </p>
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-2"></i>Hapus Pengguna
                </button>
            </form>        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil elemen select
        const provinceSelect = document.getElementById('province_id');
        const citySelect = document.getElementById('city_id');
        
        // Tambahkan event listener pada provinsi
        provinceSelect.addEventListener('change', function() {
            // Reset select kota
            citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            
            // Jika provinsi dipilih, load kota-kotanya
            if (this.value) {
                fetch(`/api/provinces/${this.value}/cities`)
                    .then(response => response.json())
                    .then(cities => {
                        // Tambahkan kota-kota ke dropdown
                        cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name;
                            citySelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading cities:', error);
                    });
            }
        });
    });
</script>
@endpush
