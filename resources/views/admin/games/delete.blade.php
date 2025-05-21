@extends('layouts.admin')

@section('title', 'Konfirmasi Hapus Pertandingan - BCSXPSS')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-trash-alt me-2"></i>Konfirmasi Hapus Pertandingan
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> 
                        <strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan!
                    </div>
                      <h5 class="text-center mb-4">Apakah Anda yakin ingin menghapus pertandingan berikut?</h5>
                    
                    <div class="mt-4 p-4 border rounded bg-light">
                        <div class="text-center mb-3">
                            <span class="badge {{ $game->is_home_game ? 'bg-success' : 'bg-secondary' }} rounded-pill mb-2">
                                <i class="fas fa-{{ $game->is_home_game ? 'home' : 'plane' }} me-1"></i>
                                {{ $game->is_home_game ? 'Home Game' : 'Away Game' }}
                            </span>
                            <h3 class="mb-0">{{ $game->home_team }} <span class="text-muted">vs</span> {{ $game->away_team }}</h3>
                        </div>
                        
                        <hr>
                        
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <p>
                                    <i class="far fa-calendar-alt me-2 text-primary"></i>
                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($game->match_time)->format('d-m-Y H:i') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                    <strong>Stadion:</strong> {{ $game->stadium_name }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <i class="fas fa-trophy me-2 text-warning"></i>
                                    <strong>Kompetisi:</strong> {{ $game->tournament_name }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <i class="fas fa-info-circle me-2 text-info"></i>
                                    <strong>Status:</strong> 
                                    <span class="badge {{ $game->status == 'scheduled' ? 'bg-success' : ($game->status == 'postponed' ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill">
                                        {{ ucfirst($game->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        @if($game->is_home_game)
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-ticket-alt me-2"></i>
                                <strong>Jumlah Tiket:</strong> {{ $ticketCount }} tiket akan dihapus
                            </div>
                        @endif
                    </div>                        @if($orderCount > 0)
                        <div class="alert alert-danger mt-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-ban fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading">Tidak dapat dihapus!</h5>
                                    <p class="mb-0">Pertandingan ini sudah memiliki <strong>{{ $orderCount }}</strong> pesanan tiket yang aktif.</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            <a href="{{ route('admin.games.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Pertandingan
                            </a>
                        </div>
                    @else
                        @if($ticketCount > 0)
                            <div class="alert alert-warning mt-4">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading">Peringatan!</h5>
                                        <p class="mb-0">Menghapus pertandingan ini akan menghapus <strong>{{ $ticketCount }}</strong> tiket yang terkait.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.games.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Batal
                            </a>
                            
                            <form action="{{ route('admin.games.destroy', $game->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-trash-alt me-2"></i>Hapus Pertandingan
                                </button>
                            </form>
                        </div>
                    @endif                </div>
                <div class="card-footer bg-light text-center text-muted">
                    <small>Game ID: {{ $game->id }} | Dibuat: {{ $game->created_at->format('d M Y H:i') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add a confirmation dialog for delete button
        const deleteForm = document.querySelector('form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!confirm('Apakah Anda yakin ingin menghapus pertandingan ini? Tindakan ini tidak dapat dibatalkan.')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
</script>
@endsection
