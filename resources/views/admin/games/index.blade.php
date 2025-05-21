@extends('layouts.admin')

@section('title', 'Kelola Pertandingan - BCSXPSS')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Manajemen Pertandingan</h1>
            <p class="text-muted">
                Kelola jadwal pertandingan home dan away untuk tim BCSXPSS.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.games.create') }}" class="btn btn-admin-primary">
                <i class="fas fa-plus me-2"></i>Tambah Pertandingan Baru
            </a>
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
      <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filter Pertandingan
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.games.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="all">Semua Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="postponed" {{ request('status') == 'postponed' ? 'selected' : '' }}>Postponed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">Tipe Pertandingan</label>
                    <select name="type" id="type" class="form-select">
                        <option value="all">Semua</option>
                        <option value="home" {{ request('type') == 'home' ? 'selected' : '' }}>Home Game</option>
                        <option value="away" {{ request('type') == 'away' ? 'selected' : '' }}>Away Game</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Tim, turnamen, stadion..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-admin-primary w-100">
                        <i class="fas fa-filter me-2"></i>Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
      <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-calendar-alt me-2"></i>Daftar Pertandingan
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Pertandingan</th>
                            <th>Waktu</th>
                            <th>Stadion</th>
                            <th>Kompetisi</th>
                            <th>Status</th>
                            @if(request('type') != 'away')
                            <th>Tiket (Terjual/Total)</th>
                            @endif
                            <th>Aksi</th>
                        </tr>
                    </thead>
            <tbody>                @forelse($games as $game)
                    <tr class="{{ \Carbon\Carbon::parse($game->match_time)->isPast() ? 'table-secondary' : '' }}">
                        <td>{{ $game->id }}</td>
                        <td>
                            <strong>{{ $game->home_team }} vs {{ $game->away_team }}</strong>
                            <br>
                            <span class="badge {{ $game->is_home_game ? 'bg-success' : 'bg-dark' }} rounded-pill">
                                <i class="fas fa-{{ $game->is_home_game ? 'home' : 'plane' }} me-1"></i>
                                {{ $game->is_home_game ? 'Home' : 'Away' }}
                            </span>
                        </td>
                        <td>
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ \Carbon\Carbon::parse($game->match_time)->format('d-m-Y H:i') }}
                            <br>
                            <small class="text-muted">
                                @if(\Carbon\Carbon::parse($game->match_time)->isPast())
                                    <span class="text-danger">
                                        <i class="fas fa-flag-checkered me-1"></i>Pertandingan selesai
                                    </span>
                                @else
                                    <i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($game->match_time)->diffForHumans() }}
                                @endif
                            </small>
                        </td>                        <td>
                            <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                            {{ $game->stadium_name }}
                        </td>
                        <td>
                            <i class="fas fa-trophy me-1 text-warning"></i>
                            {{ $game->tournament_name }}
                        </td>
                        <td>
                            @if($game->status == 'scheduled')
                                <span class="badge bg-success rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i>Scheduled
                                </span>
                            @elseif($game->status == 'postponed')
                                <span class="badge bg-warning text-dark rounded-pill">
                                    <i class="fas fa-clock me-1"></i>Postponed
                                </span>
                            @elseif($game->status == 'cancelled')
                                <span class="badge bg-danger rounded-pill">
                                    <i class="fas fa-times-circle me-1"></i>Cancelled
                                </span>
                            @endif
                        </td>                        @if(request('type') != 'away')
                        <td>
                            @if($game->is_home_game)
                                @php
                                    $soldTickets = $ticketSalesData[$game->id] ?? 0;
                                    $totalTickets = App\Models\Ticket::where('game_id', $game->id)->sum('quantity');
                                    $soldPercent = $totalTickets > 0 ? ($soldTickets / $totalTickets) * 100 : 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <strong>{{ $soldTickets }}</strong>/{{ $totalTickets }}
                                    </div>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div id="soldTicketsBar-{{ $game->id }}" 
                                             class="progress-bar progress-bar-striped {{ $soldPercent > 80 ? 'bg-danger' : ($soldPercent > 50 ? 'bg-warning' : 'bg-success') }}" 
                                             role="progressbar" 
                                             aria-valuenow="{{ $soldPercent }}"
                                             aria-valuemin="0" 
                                             aria-valuemax="100"
                                             data-width="{{ $soldPercent }}"></div>
                                    </div>
                                </div>
                                <small class="text-muted">{{ number_format($soldPercent, 0) }}% terjual</small>
                            @else
                                <span class="text-muted"><i class="fas fa-ban me-1"></i>Tidak ada tiket</span>
                            @endif                        </td>
                        @endif                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.games.edit', $game->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-title="Edit Pertandingan">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($game->is_home_game)
                                <a href="{{ route('admin.tickets.index', ['game_id' => $game->id]) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-title="Kelola Tiket">
                                    <i class="fas fa-ticket-alt"></i>
                                </a>
                                @endif
                                <a href="{{ route('admin.games.delete', $game->id) }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-title="Hapus Pertandingan">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">Tidak ada pertandingan yang ditemukan</p>
                            <p class="text-muted">Ubah filter atau tambahkan pertandingan baru</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $games->firstItem() ?? 0 }} to {{ $games->lastItem() ?? 0 }} of {{ $games->total() }} entries
            </div>
            <div>
                {{ $games->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Apply width to progress bars
        document.querySelectorAll('[id^="soldTicketsBar-"]').forEach(function(bar) {
            let width = bar.getAttribute('data-width');
            bar.style.width = width + '%';
        });
        
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