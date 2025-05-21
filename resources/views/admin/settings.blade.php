@extends('layouts.admin')

@section('title', 'Pengaturan Sistem - BCSXPSS')

@section('page-title', 'Pengaturan Sistem')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pengaturan</li>
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
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>Pengaturan Sistem
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <h6 class="settings-group-title">Pengaturan Umum</h6>
                            
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Nama Situs</label>
                                <input type="text" class="form-control @error('site_name') is-invalid @enderror" 
                                    id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name']) }}">
                                <small class="text-muted">Nama yang akan ditampilkan di judul halaman dan header</small>
                                @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="settings-group-title">Pengaturan Tiket</h6>
                            
                            <div class="mb-3">
                                <label for="ticket_expiry" class="form-label">Masa Berlaku Tiket (jam)</label>
                                <input type="number" class="form-control @error('ticket_expiry') is-invalid @enderror" 
                                    id="ticket_expiry" name="ticket_expiry" value="{{ old('ticket_expiry', $settings['ticket_expiry']) }}">
                                <small class="text-muted">Berapa lama tiket yang sudah dibayar namun belum digunakan tetap valid</small>
                                @error('ticket_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="settings-group-title">Pengaturan Notifikasi</h6>
                            
                            <div class="mb-3">
                                <label for="notification_email" class="form-label">Email Notifikasi</label>
                                <input type="email" class="form-control @error('notification_email') is-invalid @enderror" 
                                    id="notification_email" name="notification_email" 
                                    value="{{ old('notification_email', $settings['notification_email']) }}">
                                <small class="text-muted">Email untuk mengirim notifikasi sistem</small>
                                @error('notification_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <!-- Bantuan pengaturan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Bantuan
                    </h5>
                </div>
                <div class="card-body">
                    <p>Halaman ini memungkinkan Anda mengonfigurasi pengaturan sistem Pundit FC.</p>
                    <p>Perubahan pengaturan akan langsung diterapkan ke seluruh sistem.</p>
                    
                    <hr>
                    
                    <h6><i class="fas fa-question-circle me-2"></i>Perlu Bantuan?</h6>
                    <p class="mb-0">Jika Anda membutuhkan bantuan dengan pengaturan sistem, silakan hubungi tim teknis kami di <a href="mailto:support@punditfc.com">support@punditfc.com</a></p>
                </div>
            </div>
            
            <!-- Sistem info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-server me-2"></i>Informasi Sistem
                    </h5>
                </div>
                <div class="card-body">
                    <div class="system-info-item">
                        <div class="system-info-label">Versi Laravel</div>
                        <div class="system-info-value">{{ app()->version() }}</div>
                    </div>
                    <div class="system-info-item">
                        <div class="system-info-label">Versi PHP</div>
                        <div class="system-info-value">{{ phpversion() }}</div>
                    </div>
                    <div class="system-info-item">
                        <div class="system-info-label">Environment</div>
                        <div class="system-info-value">{{ app()->environment() }}</div>
                    </div>
                    <div class="system-info-item">
                        <div class="system-info-label">Status Midtrans</div>
                        <div class="system-info-value">
                            <span class="badge bg-success">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .settings-group-title {
        color: var(--admin-primary);
        border-bottom: 1px solid var(--admin-border-light);
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .system-info-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--admin-border-light);
    }
    
    .system-info-item:last-child {
        border-bottom: none;
    }
    
    .system-info-label {
        font-weight: 500;
    }
    
    .system-info-value {
        text-align: right;
    }
</style>
@endsection
