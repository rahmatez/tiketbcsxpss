@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="auth-form animate__animated animate__fadeIn">
    <h2 class="auth-form-title">Buat Akun Baru</h2>
    <p class="auth-form-subtitle">Daftar untuk membeli tiket pertandingan BCSXPSS</p>

    <form method="POST" action="{{ route('register') }}" class="register-form">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required 
                autocomplete="name" autofocus class="form-control" placeholder="Masukkan nama lengkap Anda">
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                autocomplete="email" class="form-control" placeholder="Masukkan email Anda">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-icon-wrapper">
                <input id="password" type="password" name="password" required 
                    autocomplete="new-password" class="form-control" placeholder="Buat password (min. 6 karakter)">
                <i class="fas fa-eye input-icon" id="togglePassword"></i>
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <div class="input-icon-wrapper">
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password" class="form-control" placeholder="Masukkan password sekali lagi">
                <i class="fas fa-eye input-icon" id="togglePasswordConfirm"></i>
            </div>
        </div>
        
        <div class="form-group">
            <label for="phone_number" class="form-label">Nomor Telepon</label>
            <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}"
                class="form-control" placeholder="Masukkan nomor telepon Anda">
        </div>

        <div class="form-group">
            <label for="address" class="form-label">Alamat</label>
            <textarea id="address" name="address" class="form-control" placeholder="Masukkan alamat lengkap Anda">{{ old('address') }}</textarea>
        </div>

        <div class="form-group">
            <label for="province_id" class="form-label">Provinsi</label>
            <select id="province_id" name="province_id" class="form-control">
                <option value="">Pilih Provinsi</option>
                @foreach(App\Models\Province::orderBy('name')->get() as $province)
                    <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                        {{ $province->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="city_id" class="form-label">Kota/Kabupaten</label>
            <select id="city_id" name="city_id" class="form-control">
                <option value="">Pilih Kota/Kabupaten</option>
                @if(old('province_id'))
                    @foreach(App\Models\City::where('province_id', old('province_id'))->orderBy('name')->get() as $city)
                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="form-group">
            <label for="birth_date" class="form-label">Tanggal Lahir</label>
            <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date') }}"
                class="form-control" placeholder="mm/dd/yyyy">
        </div>

        <div class="form-group">
            <label for="gender" class="form-label">Jenis Kelamin</label>
            <select id="gender" name="gender" class="form-control">
                <option value="">Pilih Jenis Kelamin</option>
                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <button type="submit" class="btn btn-auth">
                Daftar Sekarang
            </button>
        </div>

        <div class="auth-links">
            <span>Sudah punya akun?</span>
            <a href="{{ route('login') }}" class="login-link text-success">Masuk di sini</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
        
        // Toggle confirm password visibility
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        
        if (togglePasswordConfirm && passwordConfirmInput) {
            togglePasswordConfirm.addEventListener('click', function() {
                const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmInput.setAttribute('type', type);
                
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
        
        // Province and city selection
        const provinceSelect = document.getElementById('province_id');
        const citySelect = document.getElementById('city_id');
        
        if (provinceSelect && citySelect) {
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                
                // Clear the cities dropdown
                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                
                if (provinceId) {
                    // Fetch cities for the selected province
                    fetch(`/api/provinces/${provinceId}/cities`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.id;
                                option.textContent = city.name;
                                citySelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching cities:', error);
                        });
                }
            });
        }
    });

    // Center the content vertically
    document.addEventListener('DOMContentLoaded', function() {
        const authContent = document.querySelector('.auth-content');
        if (authContent) {
            // Adjust scroll position for better centering if needed
            const formHeight = document.querySelector('.register-form').offsetHeight;
            const containerHeight = document.querySelector('.auth-content-wrapper').offsetHeight;
            
            if (formHeight > containerHeight) {
                authContent.scrollTop = 0; // For very long forms, start at top
            }
            
            // Handle focus without forcing scroll to top
            const formInputs = document.querySelectorAll('.register-form input, .register-form textarea, .register-form select');
            formInputs.forEach(input => {
                if (input.id === 'name') {
                    // Give focus but don't force scroll
                    setTimeout(() => {
                        input.focus();
                    }, 100);
                }
            });
        }
    });
</script>
@endsection
