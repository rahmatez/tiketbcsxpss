@extends('layouts.admin')

@section('title', 'Detail Pengguna - BCSXPSS')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Detail Pengguna</h1>
            <p class="text-muted">
                Informasi lengkap dan riwayat pemesanan tiket untuk pengguna {{ $user->name }}.
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
      <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-circle me-2"></i>Informasi Pengguna
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4 text-center">
                        <div class="avatar-placeholder mb-3">
                            <i class="fas fa-user fa-5x"></i>
                        </div>
                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">
                            <i class="fas fa-envelope me-2"></i>{{ $user->email }}
                        </p>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <i class="fas fa-check-circle me-2 text-primary"></i>
                        <strong>Status:</strong> 
                        @if($user->is_active)
                            <span class="badge bg-success rounded-pill">Aktif</span>
                        @else
                            <span class="badge bg-danger rounded-pill">Tidak Aktif</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                        <strong>Bergabung Sejak:</strong> {{ $user->created_at->format('d F Y') }}
                    </div>
                      <div class="mb-3">
                        <i class="fas fa-phone me-2 text-primary"></i>
                        <strong>Nomor Telepon:</strong> {{ $user->phone_number ?? 'Tidak diisi' }}
                    </div>
                      <div class="mb-3">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        <strong>Alamat:</strong> {{ $user->address ?? 'Tidak diisi' }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-city me-2 text-primary"></i>
                        <strong>Kota/Kabupaten:</strong> {{ $user->city->name ?? 'Tidak diisi' }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-map me-2 text-primary"></i>
                        <strong>Provinsi:</strong> {{ $user->province->name ?? 'Tidak diisi' }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-birthday-cake me-2 text-primary"></i>
                        <strong>Tanggal Lahir:</strong> {{ $user->birth_date ? $user->birth_date->format('d F Y') : 'Tidak diisi' }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-venus-mars me-2 text-primary"></i>
                        <strong>Jenis Kelamin:</strong> 
                        @if ($user->gender == 'male')
                            Laki-laki
                        @elseif ($user->gender == 'female')
                            Perempuan
                        @elseif ($user->gender == 'other')
                            Lainnya
                        @else
                            Tidak diisi
                        @endif
                    </div>
                      <hr>
                    
                    <div class="mb-3">
                        <i class="fas fa-ticket-alt me-2 text-primary"></i>
                        <strong>Total Tiket Dibeli:</strong>
                        @php
                            $totalTickets = App\Models\Order::where('user_id', $user->id)->sum('quantity');
                        @endphp
                        <span class="badge bg-info rounded-pill">{{ $totalTickets }} tiket</span>
                    </div>

                    <hr>
                    
                    <form action="{{ route('admin.users.update_status', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        @if($user->is_active)
                            <input type="hidden" name="is_active" value="0">
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Apakah Anda yakin ingin menonaktifkan pengguna ini?')">
                                <i class="fas fa-user-slash me-2"></i>Nonaktifkan Pengguna
                            </button>
                        @else
                            <input type="hidden" name="is_active" value="1">
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Apakah Anda yakin ingin mengaktifkan pengguna ini?')">
                                <i class="fas fa-user-check me-2"></i>Aktifkan Pengguna
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Riwayat Tiket
                    </h5>
                </div>                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Pertandingan</th>
                                    <th>Tiket</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>
                                            <strong>{{ $order->game->home_team }} vs {{ $order->game->away_team }}</strong>
                                            <div class="small text-muted">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ \Carbon\Carbon::parse($order->game->match_time)->format('d-m-Y H:i') }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $order->ticket->category }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info rounded-pill">
                                                <i class="fas fa-ticket-alt me-1"></i>{{ $order->quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($order->status == 'paid')
                                                <span class="badge bg-warning text-dark rounded-pill">
                                                    <i class="fas fa-money-bill-wave me-1"></i>Paid
                                                </span>
                                            @elseif($order->status == 'redeemed')
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="fas fa-check-circle me-1"></i>Redeemed
                                                </span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">
                                                    <i class="fas fa-info-circle me-1"></i>{{ ucfirst($order->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="far fa-clock me-1 text-muted"></i>
                                            {{ $order->created_at->format('d-m-Y H:i') }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-ticket-alt text-muted fa-3x mb-3"></i>
                                            <p class="mb-0 text-muted">Pengguna belum memiliki tiket</p>
                                        </td>
                                    </tr>
                                @endforelse                            </tbody>
                        </table>
                    </div>
                </div>                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            Total: {{ $orders->count() }} pesanan
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-placeholder {
        width: 120px;
        height: 120px;
        background-color: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: #6c757d;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>

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
