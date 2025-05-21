@extends('layouts.auth')

@section('title', 'Admin Login')

@section('content')
<div class="auth-form animate__animated animate__fadeIn">
    <h2 class="auth-form-title">Admin Control Panel</h2>
    <p class="auth-form-subtitle">Login untuk mengelola sistem tiket BCSXPSS</p>

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email Admin</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                autocomplete="email" autofocus class="form-control" placeholder="Masukkan email admin">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-icon-wrapper">
                <input id="password" type="password" name="password" required 
                    autocomplete="current-password" class="form-control" placeholder="Masukkan password admin">
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
                Login Admin
            </button>
        </div>

        <div class="auth-links mt-3 text-center">
            <a href="{{ route('login') }}" class="user-login">
                <i class="fas fa-user me-2"></i>Kembali ke Login User
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
