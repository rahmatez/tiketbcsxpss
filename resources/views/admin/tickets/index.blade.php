@extends('layouts.admin')

@section('title', 'Kelola Tiket - BCSXPSS')

@section('styles')
<style>
    .game-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #007bff;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    .game-header:hover {
        background-color: #e9ecef;
    }
    .game-header.collapsed {
        border-left-color: #6c757d;
    }
    .match-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    .match-details {
        font-size: 0.85rem;
    }
    .ticket-management .table th {
        background-color: #f8f9fa;
    }
    .ticket-management {
        margin-bottom: 2rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid animate-fade-in">
    <!-- Header with stats and actions -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Manajemen Tiket</h1>
            <p class="text-muted">
                Kelola harga dan ketersediaan tiket untuk semua pertandingan BCSXPSS.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.games.create') }}" class="btn btn-admin-primary">
                <i class="fas fa-plus me-2"></i>Tambah Pertandingan & Tiket
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
    @endif    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filter Tiket
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.tickets.index') }}" method="GET" class="row g-3 admin-form">                <div class="col-md-4">
                    <label for="game_id" class="form-label">Pertandingan</label>
                    <select name="game_id" id="game_id" class="form-select">
                        <option value="">-- Semua Pertandingan --</option>
                        @foreach($dropdownGames ?? [] as $gameOption)
                            <option value="{{ $gameOption->id }}" {{ request('game_id') == $gameOption->id ? 'selected' : '' }}>
                                {{ $gameOption->home_team }} vs {{ $gameOption->away_team }} ({{ \Carbon\Carbon::parse($gameOption->match_time)->format('d-m-Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">Kategori</label>                    <select name="category" id="category" class="form-select">
                        <option value="">-- Semua Kategori --</option>
                        <option value="Tribun Selatan" {{ request('category') == 'Tribun Selatan' ? 'selected' : '' }}>Tribun Selatan</option>
                        <option value="Tribun Utara" {{ request('category') == 'Tribun Utara' ? 'selected' : '' }}>Tribun Utara</option>
                        <option value="Tribun Timur" {{ request('category') == 'Tribun Timur' ? 'selected' : '' }}>Tribun Timur</option>
                        <option value="Tribun Barat" {{ request('category') == 'Tribun Barat' ? 'selected' : '' }}>Tribun Barat</option>
                        <option value="Away" {{ request('category') == 'Away' ? 'selected' : '' }}>Away</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="price" class="form-label">Harga Minimal</label>
                    <input type="number" class="form-control" id="price" name="price_min" 
                           placeholder="Minimal" value="{{ request('price_min') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-admin-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>    <!-- Tickets Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-ticket-alt me-2"></i>Daftar Tiket
            </h5>
            <div>
                <button id="refreshTicketsBtn" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
            </div>
        </div>        <div class="card-body">            <div class="ticket-management">                @if(count($games) > 0)
                    @forelse($games as $game)
                        @if(count($game->tickets) > 0 && $game->is_home_game)<!-- Game Section Header -->
                        <div class="game-header mb-3">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <h5 class="match-title">
                                        <i class="fas fa-futbol me-2"></i>
                                        {{ $game->home_team }} vs {{ $game->away_team }}
                                    </h5>
                                    <div class="match-details text-muted">
                                        <span class="me-3"><i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($game->match_time)->format('d-m-Y H:i') }}</span>
                                        <span><i class="fas fa-map-marker-alt me-1"></i> {{ $game->stadium_name ?? 'Stadium' }}</span>
                                        
                                        @php
                                            $totalTickets = $game->tickets->sum('quantity');
                                            $totalSold = 0;
                                            foreach($game->tickets as $t) {
                                                $totalSold += App\Models\Order::where('ticket_id', $t->id)->sum('quantity');
                                            }
                                            $soldPercent = $totalTickets > 0 ? ($totalSold / $totalTickets) * 100 : 0;
                                        @endphp
                                        
                                        <div class="mt-2">
                                            <div class="d-flex align-items-center">
                                                <div class="small me-2">Terjual: {{ $totalSold }}/{{ $totalTickets }} ({{ number_format($soldPercent, 0) }}%)</div>
                                                <div class="flex-grow-1" style="max-width: 200px;">                                                    <div class="progress" style="height: 4px;">
                                                        <div class="progress-bar {{ $soldPercent > 80 ? 'bg-danger' : ($soldPercent > 50 ? 'bg-warning' : 'bg-success') }}" 
                                                            data-width="{{ $soldPercent }}"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 text-lg-end mt-2 mt-lg-0">
                                    <a href="{{ route('admin.games.edit', $game->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit Pertandingan
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tickets for this game -->
                        <div class="table-responsive mb-5">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Harga (Rp)</th>
                                        <th width="15%">Terjual</th>
                                        <th width="15%">Status</th>
                                        <th width="10%">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($game->tickets as $ticket)                                        @php
                                            $soldCount = App\Models\Order::where('ticket_id', $ticket->id)->sum('quantity');
                                            $remainingCount = $ticket->quantity - $soldCount;
                                            $soldPercent = $ticket->quantity > 0 ? ($soldCount / $ticket->quantity) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td><span class="fw-semibold">{{ $ticket->category }}</span></td>
                                            <td>{{ number_format($ticket->price, 0, ',', '.') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">{{ $soldCount }}</div>
                                                    <div class="flex-grow-1">                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar progress-bar-striped {{ $soldPercent > 80 ? 'bg-danger' : ($soldPercent > 50 ? 'bg-warning' : 'bg-success') }}" 
                                                                role="progressbar" 
                                                                data-width="{{ $soldPercent }}"
                                                                aria-valuenow="{{ $soldPercent }}" 
                                                                aria-valuemin="0" 
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <div class="small text-muted mt-1">{{ $remainingCount }} tersisa</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(\Carbon\Carbon::parse($ticket->game->match_time)->isPast())
                                                    <span class="badge bg-secondary">Selesai</span>
                                                @elseif(!$ticket->game->isTicketSalesOpen())
                                                    <span class="badge bg-danger">Ditutup</span>
                                                @else
                                                    <span class="badge bg-success">Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>                        @endif
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                            <p>Tidak ada tiket yang ditemukan</p>
                            <a href="{{ route('admin.games.create') }}" class="btn btn-sm btn-admin-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Pertandingan & Tiket
                            </a>
                        </div>
                    @endforelse
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                        <p>Tidak ada tiket yang ditemukan</p>
                        <a href="{{ route('admin.games.create') }}" class="btn btn-sm btn-admin-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Pertandingan & Tiket
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">Menampilkan {{ $tickets->count() }} dari {{ $tickets->total() }} tiket</span>
                </div>
                <div>
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle refresh button
        const refreshBtn = document.getElementById('refreshTicketsBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                location.reload();
            });
        }
        
        // Expand/collapse game sections
        const gameHeaders = document.querySelectorAll('.game-header');
        gameHeaders.forEach(header => {
            header.style.cursor = 'pointer';
            const ticketTable = header.nextElementSibling;
            
            header.addEventListener('click', function(e) {
                // Don't collapse if clicking on a button within the header
                if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || 
                    e.target.closest('button') || e.target.closest('a')) {
                    return;                }
                
                // Toggle visibility of ticket table
                if (ticketTable.style.display === 'none') {
                    ticketTable.style.display = 'block';
                    header.classList.remove('collapsed');
                } else {
                    ticketTable.style.display = 'none';
                    header.classList.add('collapsed');
                }
            });
        });
        
        // Set progress bar widths from data-width attribute
        document.querySelectorAll('.progress-bar[data-width]').forEach(bar => {
            const width = bar.getAttribute('data-width');
            bar.style.width = width + '%';
        });
    });
</script>
@endsection
@endsection
