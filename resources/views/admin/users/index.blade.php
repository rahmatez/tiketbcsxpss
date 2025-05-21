@extends('layouts.admin')

@section('title', 'Kelola Pengguna - BCSXPSS')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Manajemen Pengguna</h1>
            <p class="text-muted">
                Kelola akun pengguna dan status aktivasi untuk PBCSXPSS.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.users.create') }}" class="btn btn-admin-primary">
                <i class="fas fa-user-plus me-2"></i>Tambah Pengguna Baru
            </a>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filter Pengguna
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-end">                <div class="col-md-5">
                    <label for="search" class="form-label">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Nama pengguna atau email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-5">
                    <label for="is_active" class="form-label">Status Akun</label>
                    <select name="is_active" id="is_active" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-admin-primary w-100">
                        <i class="fas fa-filter me-2"></i>Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>Daftar Pengguna
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Provinsi</th>
                            <th>Kota/Kabupaten</th>
                            <th>Tanggal Registrasi</th>
                            <th>Tiket Dibeli</th>
                            <th>Status</th>
                            <th>Aksi</th>
                </tr>
            </thead>
            <tbody>                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs me-2">
                                    <i class="fas fa-user-circle text-secondary"></i>
                                </div>
                                <strong>{{ $user->name }}</strong>
                            </div>
                        </td>
                        <td>                            <i class="fas fa-envelope me-1 text-muted"></i>
                            {{ $user->email }}
                        </td>
                        <td>
                            @if($user->province)
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-map me-1"></i>
                                    {{ $user->province->name }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->city)
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-city me-1"></i>
                                    {{ $user->city->name }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <i class="far fa-calendar-alt me-1 text-muted"></i>
                            {{ $user->created_at->format('d-m-Y') }}
                        </td>
                        <td>
                            @php
                                $ticketCount = App\Models\Order::where('user_id', $user->id)->sum('quantity');
                            @endphp
                            <span class="badge bg-info rounded-pill">
                                <i class="fas fa-ticket-alt me-1"></i>
                                {{ $ticketCount }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                </span>
                            @else
                                <span class="badge bg-danger rounded-pill">
                                    <i class="fas fa-ban me-1"></i>Tidak Aktif
                                </span>
                            @endif                        </td>
                        <td>                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-title="Edit Pengguna">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.update_status', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    @if($user->is_active)
                                        <input type="hidden" name="is_active" value="0">
                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-title="Nonaktifkan Pengguna" onclick="return confirm('Apakah Anda yakin ingin menonaktifkan pengguna ini?')">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    @else
                                        <input type="hidden" name="is_active" value="1">
                                        <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-title="Aktifkan Pengguna" onclick="return confirm('Apakah Anda yakin ingin mengaktifkan pengguna ini?')">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                    @endif
                                </form>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-title="Hapus Pengguna" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Pengguna hanya dapat dihapus jika tidak memiliki pesanan terkait.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">Tidak ada pengguna yang ditemukan</p>
                            <p class="text-muted">Ubah filter untuk menampilkan hasil yang berbeda</p>
                        </td>
                    </tr>
                @endforelse                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'top'
            });
        });
    });
</script>
@endsection
