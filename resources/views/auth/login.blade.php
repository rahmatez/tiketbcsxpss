@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-form animate__animated animate__fadeIn">
    <h2 class="auth-form-title">Selamat Datang Kembali</h2>
    <p class="auth-form-subtitle">Masuk ke akun Anda untuk membeli tiket pertandingan</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                autocomplete="email" autofocus class="form-control" placeholder="Masukkan email Anda">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-icon-wrapper">
                <input id="password" type="password" name="password" required 
                    autocomplete="current-password" class="form-control" placeholder="Masukkan password Anda">
                <i class="fas fa-eye input-icon" id="togglePassword"></i>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="form-group">
            <button type="submit" class="btn btn-auth">
                Masuk
            </button>
        </div>

        <div class="auth-links">
            <a href="#" class="forgot-password">Lupa Password?</a>
            <a href="{{ route('register') }}" class="create-account">Daftar Akun Baru</a>
        </div>

        <div class="auth-links mt-2 text-center">
            <a href="{{ route('admin.login') }}" class="admin-login">
                <i class="fas fa-user-shield me-1"></i>Login Admin
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endsection
