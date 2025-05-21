@extends('layouts.admin')

@section('title', 'Profil Admin - BCSXPSS')

@section('page-title', 'Profil Admin')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Profil</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid animate-fade-in">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <!-- Profil Admin -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Informasi Profil
                    </h5>
                </div>
                <div class="card-body">
                    <div class="profile-info mb-4">
                        <div class="text-center mb-4">
                            <div class="profile-avatar mb-3">
                                <i class="fas fa-user-circle fa-6x"></i>
                            </div>
                            <h4>{{ $admin->username }}</h4>
                            <p class="text-muted">Administrator</p>
                        </div>
                        
                        <div class="profile-details">
                            <div class="detail-item">
                                <div class="detail-label">Username</div>
                                <div class="detail-value">{{ $admin->username }}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Dibuat pada</div>
                                <div class="detail-value">{{ $admin->created_at->format('d M Y, H:i') }}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Login terakhir</div>
                                <div class="detail-value">{{ now()->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <!-- Update Profil -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Update Profil
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                id="username" name="username" value="{{ old('username', $admin->username) }}">
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                id="current_password" name="current_password">
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                id="password" name="password">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah password</small>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" 
                                id="password_confirmation" name="password_confirmation">
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt me-2"></i>Keamanan Akun
                    </h5>
                </div>
                <div class="card-body">
                    <p>Penting untuk menjaga keamanan akun admin Anda:</p>
                    
                    <ul class="security-tips">
                        <li><i class="fas fa-check-circle text-success me-2"></i> Gunakan password yang kuat dan unik</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Jangan bagikan informasi akun Anda</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Selalu logout setelah selesai menggunakan sistem</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Perbarui password Anda secara berkala</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .profile-avatar {
        color: var(--admin-primary);
        margin-bottom: 1rem;
    }
    
    .profile-details {
        margin-top: 2rem;
    }
    
    .detail-item {
        display: flex;
        border-bottom: 1px solid var(--admin-border-light);
        padding: 0.75rem 0;
    }
    
    .detail-item:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 600;
        width: 40%;
    }
    
    .detail-value {
        width: 60%;
    }
    
    .security-tips {
        list-style: none;
        padding-left: 0;
    }
    
    .security-tips li {
        padding: 0.5rem 0;
    }
</style>
@endsection
