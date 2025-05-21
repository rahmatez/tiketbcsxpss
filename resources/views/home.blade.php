@php use App\Models\Ticket; use Carbon\Carbon; use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.app')

@section('title', 'Home - BCSXPSS')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endsection

@section('content')
    <!-- Main Content -->
    @php
        $nextMatch = $games->where('match_time', '>', now())->sortBy('match_time')->first();
    @endphp
    
    @if($nextMatch)
    <div class="countdown-section">
        <div class="container">
            <h2 class="mb-4 animate__animated animate__fadeIn">Pertandingan Selanjutnya</h2>
            <div class="d-flex justify-content-center align-items-center animate__animated animate__fadeIn">
                @php
                    $defaultImageUrl = asset('images/team-default.png');
                    $homeTeamLogo = $nextMatch->home_team_logo ? Storage::url($nextMatch->home_team_logo) : $defaultImageUrl;
                    $awayTeamLogo = $nextMatch->away_team_logo ? Storage::url($nextMatch->away_team_logo) : $defaultImageUrl;
                @endphp
                <div class="text-center px-4">
                    <img src="{{ $homeTeamLogo }}" alt="{{ $nextMatch->home_team }}" class="img-fluid mb-2" style="max-width: 80px; max-height: 80px;" onerror="this.src='{{ $defaultImageUrl }}'">
                    <h3>{{ $nextMatch->home_team }}</h3>
                </div>
                <div class="px-3">
                    <span class="h1">VS</span>
                </div>
                <div class="text-center px-4">
                    <img src="{{ $awayTeamLogo }}" alt="{{ $nextMatch->away_team }}" class="img-fluid mb-2" style="max-width: 80px; max-height: 80px;" onerror="this.src='{{ $defaultImageUrl }}'">
                    <h3>{{ $nextMatch->away_team }}</h3>
                </div>
            </div>
            <p class="mt-3 animate__animated animate__fadeIn">
                <i class="fas fa-calendar-alt me-2"></i>{{ Carbon::parse($nextMatch->match_time)->format('d F Y, H:i') }} WIB
                <br>
                <i class="fas fa-map-marker-alt me-2"></i>{{ $nextMatch->stadium_name }}
            </p>
            
            <div class="countdown-container" id="match-countdown" data-match-time="{{ Carbon::parse($nextMatch->match_time)->format('Y-m-d H:i:s') }}">
                <div class="countdown-item">
                    <div class="countdown-number" id="days">--</div>
                    <div class="countdown-label">Hari</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="hours">--</div>
                    <div class="countdown-label">Jam</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="minutes">--</div>
                    <div class="countdown-label">Menit</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="seconds">--</div>
                    <div class="countdown-label">Detik</div>
                </div>
            </div>
            
            @if($nextMatch->is_home_game)
            <div class="mt-4 animate__animated animate__fadeIn">
                <a href="{{ route('games.show', $nextMatch->id) }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-ticket-alt me-2"></i>Beli Tiket Sekarang
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif
    


    <!-- Main Content -->
    <div class="container" id="upcoming-matches">
        <h2 class="section-title animate-fade-in">Jadwal Pertandingan</h2>
        
        <!-- Filter Section -->
        <div class="filter-section animate-fade-in">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" id="search-teams" class="form-control" placeholder="Cari tim...">
                </div>
                <div class="col-md-3">
                    <select id="filter-tournament" class="form-select">
                        <option value="">Semua Turnamen</option>
                        @php
                            $tournaments = $games->pluck('tournament_name')->unique();
                        @endphp
                        @foreach($tournaments as $tournament)
                            <option value="{{ $tournament }}">{{ $tournament }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filter-match-type" class="form-select">
                        <option value="">Semua Tipe Match</option>
                        <option value="1">Home</option>
                        <option value="0">Away</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="reset-filters" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-sync-alt me-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Games Grid -->
        <div class="row" id="games-container">
            @foreach($games->take(3) as $game)
                <div class="col-md-4 mb-4 game-item animate-fade-in" 
                    data-home-team="{{ strtolower($game->home_team) }}"
                    data-away-team="{{ strtolower($game->away_team) }}"
                    data-tournament="{{ strtolower($game->tournament_name) }}"
                    data-match-type="{{ $game->is_home_game ? '1' : '0' }}">
                    <div class="card game-card h-100">
                        <div class="game-image-container">
                            @if($game->image_path)
                                <img src="{{ Storage::url($game->image_path) }}"
                                     class="card-img-top" alt="{{ $game->home_team }} vs {{ $game->away_team }}">
                            @elseif($game->is_home_game)
                                <img src="{{ asset('images/' . rand(1, 3) . '.jpg') }}"
                                     class="card-img-top" alt="Home Stadium">
                            @else
                                <img src="{{ asset('images/away-game.jpg') }}"
                                     class="card-img-top" alt="Away Stadium">
                            @endif
                            <span class="game-date-badge">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ Carbon::parse($game->match_time)->format('d M Y') }}
                            </span>
                        </div>
                        
                        <div class="card-body">
                            @php
                                $defaultImageUrl = asset('images/team-default.png');
                                $homeTeamLogo = $game->home_team_logo ? Storage::url($game->home_team_logo) : $defaultImageUrl;
                                $awayTeamLogo = $game->away_team_logo ? Storage::url($game->away_team_logo) : $defaultImageUrl;
                            @endphp
                            <!-- <div class="teams">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="text-center">
                                        <img src="{{ $homeTeamLogo }}" alt="{{ $game->home_team }}" class="img-fluid mb-2" style="max-width: 40px; max-height: 40px;" onerror="this.src='{{ $defaultImageUrl }}'">
                                        <span class="d-block">{{ $game->home_team }}</span>
                                    </div>
                                    <span class="vs">VS</span>
                                    <div class="text-center">
                                        <img src="{{ $awayTeamLogo }}" alt="{{ $game->away_team }}" class="img-fluid mb-2" style="max-width: 40px; max-height: 40px;" onerror="this.src='{{ $defaultImageUrl }}'">
                                        <span class="d-block">{{ $game->away_team }}</span>
                                    </div>
                                </div>
                            </div> -->
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="far fa-clock text-primary me-2"></i>
                                <span>{{ Carbon::parse($game->match_time)->format('H:i') }} WIB</span>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-trophy text-warning me-2"></i>
                                <span>{{ $game->tournament_name }}</span>
                            </div>
                            
                            @if($game->is_home_game)
                                @php
                                    $tickets = Ticket::where('game_id', $game->id)->get();
                                    $minPrice = $tickets->min('price');
                                    $maxPrice = $tickets->max('price');
                                @endphp
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-tag text-success me-2"></i>
                                    <span>Rp{{ number_format($minPrice, 0, ',', '.') }} - 
                                    Rp{{ number_format($maxPrice, 0, ',', '.') }}</span>
                                </div>
                            @else
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-plane text-info me-2"></i>
                                    <span>Pertandingan Away</span>
                                </div>
                            @endif
                            
                            <div class="mt-auto">
                                @if($game->is_home_game)
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('games.show', $game->id) }}" class="btn btn-primary">
                                            <i class="fas fa-ticket-alt me-2"></i>Beli Tiket
                                        </a>
                                        <button type="button" class="btn btn-outline-primary btn-share"
                                            data-title="Pertandingan {{ $game->home_team }} vs {{ $game->away_team }}"
                                            data-text="Pertandingan {{ $game->tournament_name }}: {{ $game->home_team }} vs {{ $game->away_team }} pada {{ Carbon::parse($game->match_time)->format('d-m-Y H:i') }} WIB">
                                            <i class="fas fa-share-alt me-2"></i>Bagikan
                                        </button>
                                    </div>
                                @else
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-outline-secondary" disabled>
                                            <i class="fas fa-ban me-2"></i>Tidak Tersedia
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-footer text-center text-muted">
                            <small>
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $game->stadium_name }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- No Results Message -->
        <div id="no-results-message" class="text-center py-5 d-none">
            <i class="fas fa-search fa-3x mb-3 text-muted"></i>
            <h4>Tidak ada pertandingan yang sesuai dengan kriteria filter Anda</h4>
            <p>Silakan coba dengan kata kunci atau filter yang berbeda</p>
            <button id="clear-search" class="btn btn-primary mt-2">
                <i class="fas fa-redo me-2"></i>Tampilkan Semua Pertandingan
            </button>
        </div>
        
        <!-- View More Button -->
        @if(count($games) > 3)
        <div class="text-center my-4 animate-fade-in">
            <a href="{{ route('tickets.search') }}" class="btn btn-lg btn-outline-success">
                <i class="fas fa-plus-circle me-2"></i>Lihat Semua Pertandingan
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
<script src="{{ asset('js/custom.js') }}"></script>
@endsection
