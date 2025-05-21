@extends('layouts.admin')

@section('title', 'Edit Tiket - BCSXPSS')

@section('page-title', 'Edit Tiket')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Edit Tiket</h1>
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
    
    @if($errors->any())
        <div class="alert alert-danger">
            <div class="d-flex">
                <div class="me-2">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Match Info Card -->
    <div class="admin-card mb-4">
        <div class="admin-card-header">
            <h5 class="admin-card-title mb-0">
                <i class="fas fa-football-ball me-2"></i>Informasi Pertandingan
            </h5>
        </div>
        <div class="admin-card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4>{{ $ticket->game->home_team }} vs {{ $ticket->game->away_team }}</h4>
                    <p class="text-muted mb-0">{{ $ticket->game->tournament_name ?? 'Liga Indonesia' }}</p>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-md-end">
                        @if(\Carbon\Carbon::parse($ticket->game->match_time)->isPast())
                            <span class="status-badge status-inactive">Selesai</span>
                        @elseif(!$ticket->game->isTicketSalesOpen())
                            <span class="status-badge status-rejected">Penjualan Ditutup</span>
                        @else
                            <span class="status-badge status-active">Aktif</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-calendar-day fa-2x text-primary"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Tanggal</div>
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($ticket->game->match_time)->format('d F Y') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-clock fa-2x text-primary"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Waktu</div>
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($ticket->game->match_time)->format('H:i') }} WIB</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Stadion</div>
                            <div class="fw-bold">{{ $ticket->game->stadium_name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Ticket Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h5 class="admin-card-title mb-0">
                <i class="fas fa-edit me-2"></i>Edit Tiket
            </h5>
        </div>
        <div class="admin-card-body">
            <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" class="admin-form">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="admin-card h-100">
                            <div class="admin-card-body">
                                <h6>Tiket Kategori {{ $ticket->category }}</h6>
                                <p class="text-muted small">Informasi kategori tiket tidak dapat diubah</p>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $soldCount = App\Models\Order::where('ticket_id', $ticket->id)->sum('quantity');
                    @endphp
                    
                    <div class="col-md-4">
                        <div class="admin-card h-100">
                            <div class="admin-card-body">
                                <h6>Tiket Terjual</h6>
                                <div class="display-6 text-primary">{{ $soldCount }}</div>
                                <p class="text-muted small mb-0">dari total {{ $ticket->quantity }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="admin-card h-100">
                            <div class="admin-card-body">
                                <h6>Tiket Tersedia</h6>
                                <div class="display-6 text-success">{{ $ticket->quantity - $soldCount }}</div>
                                <p class="text-muted small mb-0">siap untuk dijual</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Harga Tiket (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="price" name="price" value="{{ $ticket->price }}" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="quantity" class="form-label">Jumlah Total Tiket</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $ticket->quantity }}" required>
                        
                        @if($soldCount > 0)
                            <small class="text-danger">
                                <i class="fas fa-info-circle me-1"></i>Sudah terjual {{ $soldCount }} tiket. Nilai minimal: {{ $soldCount }}.
                            </small>
                        @endif
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">Deskripsi Tiket (Opsional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ $ticket->description ?? '' }}</textarea>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="alert-heading mb-1">Informasi Penting</h6>
                            <p class="mb-0">Perubahan harga tidak akan memengaruhi tiket yang sudah terjual. Jumlah total tiket tidak boleh kurang dari jumlah yang sudah terjual ({{ $soldCount }}).</p>
                        </div>
                    </div>
                </div>
                
                <div class="text-end mt-4">
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update available tickets when quantity changes
        const quantityInput = document.getElementById('quantity');
        const soldCount = parseInt("{{ $soldCount }}");
        
        if (quantityInput) {
            quantityInput.addEventListener('change', function() {
                const quantity = parseInt(this.value) || 0;
                
                // Validate minimum quantity
                if (quantity < soldCount) {
                    showToast('Jumlah tiket tidak boleh kurang dari jumlah yang sudah terjual (' + soldCount + ')', 'error');
                    this.value = soldCount;
                }
            });        }
    });
</script>
@endsection
