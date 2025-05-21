@extends('layouts.app')

@section('title', 'Pencarian Tiket')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Cari Pertandingan & Tiket</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tickets.search') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="date_range" class="form-label">Tanggal</label>
                                <select name="date_range" id="date_range" class="form-select">
                                    <option value="all" {{ request('date_range') == 'all' ? 'selected' : '' }}>Semua Tanggal</option>
                                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                                    <option value="tomorrow" {{ request('date_range') == 'tomorrow' ? 'selected' : '' }}>Besok</option>
                                    <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                                    <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="team" class="form-label">Tim</label>
                                <select name="team" id="team" class="form-select">
                                    <option value="" {{ !request('team') ? 'selected' : '' }}>Semua Tim</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team }}" {{ request('team') == $team ? 'selected' : '' }}>{{ $team }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tournament" class="form-label">Turnamen</label>
                                <select name="tournament" id="tournament" class="form-select">
                                    <option value="" {{ !request('tournament') ? 'selected' : '' }}>Semua Turnamen</option>
                                    @foreach($tournaments as $tournament)
                                        <option value="{{ $tournament }}" {{ request('tournament') == $tournament ? 'selected' : '' }}>{{ $tournament }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="location" class="form-label">Lokasi</label>
                                <select name="location" id="location" class="form-select">
                                    <option value="" {{ !request('location') ? 'selected' : '' }}>Semua Lokasi</option>
                                    <option value="home" {{ request('location') == 'home' ? 'selected' : '' }}>Kandang</option>
                                    <option value="away" {{ request('location') == 'away' ? 'selected' : '' }}>Tandang</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="query" class="form-label">Kata Kunci</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="query" name="query" placeholder="Cari pertandingan..." value="{{ request('query') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search me-1"></i> Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Hasil Pencarian -->
            <div class="mb-3">
                <h3>Hasil Pencarian</h3>
                @if(request()->has('query') || request()->has('date_range') || request()->has('team') || request()->has('tournament') || request()->has('location'))
                    <p>Menampilkan {{ $games->count() }} hasil dari {{ $games->total() }} pertandingan</p>
                @endif
            </div>
            
            @if($games->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <img src="{{ asset('images/not-found.svg') }}" alt="Tidak ditemukan" class="img-fluid mb-3" style="max-height: 150px;">
                        <h4>Tidak Ada Pertandingan yang Ditemukan</h4>
                        <p class="text-muted">Mohon coba kata kunci atau filter pencarian lain</p>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($games as $game)
                        <div class="col-md-6 mb-4">                            <div class="card h-100">
                                <div class="card-header bg-dark">
                                    <span class="badge {{ $game->is_home_game ? 'bg-success' : 'bg-secondary' }} float-end">
                                        {{ $game->is_home_game ? 'Kandang' : 'Tandang' }}
                                    </span>
                                    <h6 class="mb-0">{{ $game->tournament_name }}</h6>
                                </div>
                                
                                @if($game->image_path)
                                <div class="game-image-container">
                                    <img src="{{ Storage::url($game->image_path) }}" class="card-img-top game-image" alt="{{ $game->home_team }} vs {{ $game->away_team }}">
                                </div>
                                @endif
                                
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">                                        <div class="text-center">
                                            @php
                                                $defaultImageUrl = asset('images/team-default.png');
                                                $homeTeamLogo = $game->home_team_logo ? Storage::url($game->home_team_logo) : $defaultImageUrl;
                                            @endphp
                                            <img src="{{ $homeTeamLogo }}" 
                                                 alt="{{ $game->home_team }}" class="img-fluid team-logo mb-2"
                                                 onerror="this.src='{{ $defaultImageUrl }}'">
                                            <h6>{{ $game->home_team }}</h6>
                                        </div>
                                        <div class="text-center mx-2">
                                            <h4>VS</h4>
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($game->match_time)->format('d M Y') }}
                                            </div>
                                            <div class="badge bg-primary">
                                                {{ \Carbon\Carbon::parse($game->match_time)->format('H:i') }} WIB
                                            </div>
                                        </div>                                        <div class="text-center">
                                            @php
                                                $awayTeamLogo = $game->away_team_logo ? Storage::url($game->away_team_logo) : $defaultImageUrl;
                                            @endphp
                                            <img src="{{ $awayTeamLogo }}" 
                                                 alt="{{ $game->away_team }}" class="img-fluid team-logo mb-2"
                                                 onerror="this.src='{{ $defaultImageUrl }}'">
                                            <h6>{{ $game->away_team }}</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mb-3">
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ $game->stadium_name }}
                                    </div>
                                    
                                    @if($game->is_home_game)
                                        <div class="ticket-info mb-3">
                                            <h6>Tiket Tersedia:</h6>
                                            <div class="row">
                                                @php
                                                    $tickets = $game->tickets;
                                                    $availableTickets = 0;
                                                @endphp
                                                
                                                @foreach($tickets as $ticket)
                                                    @php
                                                        $purchased = $purchasedQuantities[$ticket->id] ?? 0;
                                                        $remaining = $ticket->quantity - $purchased;
                                                        if ($remaining > 0) {
                                                            $availableTickets++;
                                                        }
                                                    @endphp

                                                    <div class="col-6 mb-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>{{ $ticket->category }}</span>
                                                            <span class="badge {{ $remaining < 5 ? 'bg-warning text-dark' : 'bg-success' }}">
                                                                {{ $remaining > 0 ? 'Tersedia' : 'Habis' }}
                                                            </span>
                                                        </div>
                                                        <div class="small">
                                                            Rp{{ number_format($ticket->price, 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                                
                                                @if($availableTickets == 0)
                                                    <div class="col-12 text-center">
                                                        <span class="badge bg-danger">Tiket Habis</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-secondary small">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Pertandingan tandang, tiket tidak dijual di sistem ini
                                        </div>
                                    @endif
                                    
                                    <div class="d-grid">
                                        <a href="{{ route('games.show', $game->id) }}" class="btn btn-primary">
                                            <i class="fas fa-ticket-alt me-1"></i>
                                            {{ $game->is_home_game ? 'Lihat Detail & Beli Tiket' : 'Lihat Detail Pertandingan' }}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    <div class="d-flex justify-content-between">
                                        <small>
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ \Carbon\Carbon::parse($game->match_time)->diffForHumans() }}
                                        </small>
                                        <small>
                                            Status: 
                                            <span class="fw-bold {{ $game->status == 'upcoming' ? 'text-primary' : 
                                                                     ($game->status == 'ongoing' ? 'text-success' : 
                                                                     ($game->status == 'completed' ? 'text-info' : 'text-danger')) }}">
                                                {{ ucfirst($game->status) }}
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    {{ $games->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .team-logo {
        width: 60px;
        height: 60px;
        object-fit: contain;
    }
</style>
@endsection
