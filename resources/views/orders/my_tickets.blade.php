@extends('layouts.app')

@section('title', 'Tiket Saya - BCSXPSS')

@php
    $backgroundImage = asset('images/' . rand(1, 3) . '.jpg');
@endphp

@section('styles')
<style>
    /* Fix for upcoming-tickets alignment */
    #upcoming-tickets, #past-tickets {
        padding: 0;
        margin: 0 auto;
        max-width: 100%;
    }
    
    /* Fix for countdown display */
    .ticket-info-item[data-countdown] {
        min-height: 80px;
    }
    
    .countdown-container {
        margin: 0;
        padding: 0;
    }
    
    .countdown-value {
        font-size: 1.2rem !important;
        height: 40px !important;
        width: 40px !important;
    }
    
    .countdown-label {
        font-size: 0.7rem !important;
    }
    
    .ticket-info-value .countdown-container {
        display: flex;
        gap: 5px;
        justify-content: flex-start;
        margin-top: 5px;
    }
    
    .ticket-info-value .countdown-item {
        width: auto;
        min-width: 40px;
        margin: 0;
    }
    
    .page-header {
         background: linear-gradient(
        to right,
        var(--secondary-color),
        #075028,
        var(--primary-color) 150%
    );
        padding: 50px 0;
        text-align: center;
        margin-bottom: 40px;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        color: white;
        position: relative;
        overflow: hidden;
        margin-top: -1.5rem;
    }
    
    .page-header::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('{{ $backgroundImage }}') center center;
        background-size: cover;
        opacity: 0.2;
        z-index: 0;
    }
    
    .page-header h1 {
        position: relative;
        z-index: 1;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .page-header p {
        position: relative;
        z-index: 1;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .tab-navigation {
        display: flex;
        margin-bottom: 30px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .tab-link {
        flex: 1;
        text-align: center;
        padding: 15px;
        background: white;
        color: var(--gray-color);
        font-weight: 600;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .tab-link.active {
        color: var(--primary-color);
        border-bottom: 3px solid var(--primary-color);
    }
    
    .ticket-tab {
        display: none;
    }
    
    .ticket-tab.active {
        display: block;
    }
    
    .empty-state {
        text-align: center;
        padding: 50px 0;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .empty-icon {
        font-size: 4rem;
        color: var(--light-gray);
        margin-bottom: 20px;
    }
    
    .ticket-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .ticket-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .ticket-header {
        padding: 20px;
        position: relative;
    }
    
    .ticket-header.home {
         background: linear-gradient(
        to right,
        var(--secondary-color),
        #075028,
        var(--primary-color) 150%
    );
        color: white;
    }
    
    .ticket-header.away {
        background: linear-gradient(135deg, var(--gray-color), #4d545a);
        color: white;
    }
    
    .match-teams {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .match-team {
        text-align: center;
        flex: 1;
    }
    
    .team-name {
        font-weight: 700;
        font-size: 1.2rem;
    }
    
    .team-info {
        font-size: 0.8rem;
        opacity: 0.8;
    }
    
    .match-vs {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.9);
        color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        margin: 0 15px;
    }
    
    .ticket-body {
        padding: 20px;
    }
    
    .ticket-info {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }
    
    .ticket-info-item {
        flex: 1;
        min-width: 150px;
        padding: 10px;
    }
    
    .ticket-info-label {
        font-size: 0.8rem;
        color: var(--gray-color);
        margin-bottom: 5px;
    }
    
    .ticket-info-value {
        font-weight: 600;
    }
    
    .ticket-footer {
        padding: 15px 20px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px dashed var(--light-gray);
    }
    
    .ticket-status {
        padding: 8px 15px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
    }
    
    .ticket-status i {
        margin-right: 5px;
    }
    
    .ticket-status.paid {
        background-color: rgba(26, 115, 232, 0.1);
        color: var(--primary-color);
    }
    
    .ticket-status.redeemed {
        background-color: rgba(52, 168, 83, 0.1);
        color: var(--secondary-color);
    }
    
    .ticket-status.pending {
        background-color: rgba(251, 188, 4, 0.1);
        color: var(--accent-color);
    }
    
    .ticket-status.cancelled {
        background-color: rgba(234, 67, 53, 0.1);
        color: var(--danger-color);
    }
    
    .ticket-actions .btn {
        padding: 8px 15px;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="animate__animated animate__fadeInDown">Tiket Saya</h1>
            <p class="animate__animated animate__fadeInUp">
                Kelola dan akses tiket pertandingan yang telah Anda beli di sini
            </p>
        </div>
    </div>

    <div class="container">
        <!-- Tab Navigation -->
        <div class="tab-navigation animate-fade-in">
            <div class="tab-link active" data-tab="upcoming">Akan Datang</div>
            <div class="tab-link" data-tab="past">Selesai</div>
        </div>
        
        @php
            $upcomingTickets = $orders->filter(function($order) {
                return Carbon\Carbon::parse($order->game->match_time)->isFuture();
            });
            
            $pastTickets = $orders->filter(function($order) {
                return Carbon\Carbon::parse($order->game->match_time)->isPast();
            });
        @endphp
        
        <!-- Upcoming Tickets -->
        <div id="upcoming-tickets" class="ticket-tab active container-fluid">
            @forelse($upcomingTickets as $order)
                <div class="ticket-card animate-fade-in">
                    <div class="ticket-header {{ $order->game->is_home_game ? 'home' : 'away' }}">
                        <div class="match-teams">
                            <div class="match-team">
                                <div class="team-name">{{ $order->game->home_team }}</div>
                                <div class="team-info">Home</div>
                            </div>
                            
                            <div class="match-vs">VS</div>
                            
                            <div class="match-team">
                                <div class="team-name">{{ $order->game->away_team }}</div>
                                <div class="team-info">Away</div>
                            </div>
                        </div>
                        
                        <div class="match-info text-center mt-3">
                            <i class="fas fa-calendar-alt me-2"></i>
                            {{ Carbon\Carbon::parse($order->game->match_time)->format('d M Y - H:i') }} WIB
                        </div>
                    </div>
                    
                    <div class="ticket-body">
                        <div class="ticket-info">
                            <div class="ticket-info-item">
                                <div class="ticket-info-label">Kategori</div>
                                <div class="ticket-info-value">{{ $order->ticket->category }}</div>
                            </div>
                            
                            <div class="ticket-info-item">
                                <div class="ticket-info-label">Jumlah</div>
                                <div class="ticket-info-value">{{ $order->quantity }} tiket</div>
                            </div>
                            
                            <div class="ticket-info-item">
                                <div class="ticket-info-label">Stadium</div>
                                <div class="ticket-info-value">{{ $order->game->stadium_name }}</div>
                            </div>
                            
                            <div class="ticket-info-item">
                                <div class="ticket-info-label">Hitung Mundur</div>
                                <div class="ticket-info-value" data-countdown="{{ $order->game->match_time }}" data-expired-text="Pertandingan Sedang Berlangsung"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ticket-footer">
                        <div class="ticket-status {{ strtolower($order->status) }}">
                            @if($order->status == 'paid')
                                <i class="fas fa-check-circle"></i> Lunas
                            @elseif($order->status == 'redeemed')
                                <i class="fas fa-ticket-alt"></i> Digunakan
                            @elseif($order->status == 'pending')
                                <i class="fas fa-clock"></i> Menunggu Pembayaran
                            @else
                                <i class="fas fa-info-circle"></i> {{ ucfirst($order->status) }}
                            @endif
                        </div>
                        
                        <div class="ticket-actions">
                            @if($order->status == 'paid')
                                <a href="{{ route('ticket.detail', $order->id) }}" class="btn btn-primary">
                                    <i class="fas fa-qrcode me-2"></i>Lihat Tiket
                                </a>
                                
                                <button type="button" class="btn btn-outline-primary btn-share ms-2"
                                    data-title="Tiket Pertandingan {{ $order->game->home_team }} vs {{ $order->game->away_team }}"
                                    data-text="Saya akan menonton pertandingan {{ $order->game->home_team }} vs {{ $order->game->away_team }} pada {{ Carbon\Carbon::parse($order->game->match_time)->format('d M Y - H:i') }} WIB di {{ $order->game->stadium_name }}">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                            @elseif($order->status == 'redeemed')
                                <button class="btn btn-secondary" disabled title="Tiket telah digunakan untuk masuk venue">
                                    <i class="fas fa-check-circle me-2"></i>Tiket Telah Digunakan
                                </button>
                                
                                <button type="button" class="btn btn-outline-primary btn-share ms-2"
                                    data-title="Tiket Pertandingan {{ $order->game->home_team }} vs {{ $order->game->away_team }}"
                                    data-text="Saya telah menonton pertandingan {{ $order->game->home_team }} vs {{ $order->game->away_team }} pada {{ Carbon\Carbon::parse($order->game->match_time)->format('d M Y - H:i') }} WIB di {{ $order->game->stadium_name }}">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                            @elseif($order->status == 'pending')
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-warning">
                                    <i class="fas fa-credit-card me-2"></i>Selesaikan Pembayaran
                                </a>
                            @else
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-ban me-2"></i>Tiket Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state animate-fade-in">
                    <i class="fas fa-ticket-alt empty-icon"></i>
                    <h3>Tiket Akan Datang Kosong</h3>
                    <p>Anda belum memiliki tiket untuk pertandingan yang akan datang</p>
                    <a href="{{ url('/') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-search me-2"></i>Cari Pertandingan
                    </a>
                </div>
            @endforelse
        </div>
        
        <!-- Past Tickets -->
        <div id="past-tickets" class="ticket-tab container-fluid">
            @forelse($pastTickets as $order)
                <div class="ticket-card animate-fade-in">
                    <div class="ticket-header {{ $order->game->is_home_game ? 'home' : 'away' }}">
                        <div class="match-teams">
                            <div class="match-team">
                                <div class="team-name">{{ $order->game->home_team }}</div>
                                <div class="team-info">Home</div>
                            </div>
                            
                            <div class="match-vs">VS</div>
                            
                            <div class="match-team">
                                <div class="team-name">{{ $order->game->away_team }}</div>
                                <div class="team-info">Away</div>
                            </div>
                        </div>
                        
                        <div class="match-info text-center mt-3">
                            <i class="fas fa-calendar-alt me-2"></i>
                            {{ Carbon\Carbon::parse($order->game->match_time)->format('d M Y - H:i') }} WIB
                        </div>
                    </div>
                    
                    <div class="ticket-body">
                        <div class="ticket-info">
                            <div class="ticket-info-item">
                                <div class="ticket-info-label">Kategori</div>
                                <div class="ticket-info-value">{{ $order->ticket->category }}</div>
                            </div>
                            
                            <div class="ticket-info-item">
                                <div class="ticket-info-label">Jumlah</div>
                                <div class="ticket-info-value">{{ $order->quantity }} tiket</div>
                            </div>
                            
                            <div class="ticket-info-item">
                                <div class="ticket-info-label">Stadium</div>
                                <div class="ticket-info-value">{{ $order->game->stadium_name }}</div>
                            </div>
                            
                            <div class="ticket-info-item">
                                <div class="ticket-info-label">Status</div>
                                <div class="ticket-info-value">
                                    <span class="badge bg-secondary">Selesai</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ticket-footer">
                        <div class="ticket-status {{ strtolower($order->status) }}">
                            @if($order->status == 'redeemed')
                                <i class="fas fa-check-circle"></i> Digunakan
                            @else
                                <i class="fas fa-times-circle"></i> Tidak Digunakan
                            @endif
                        </div>
                        
                        <div class="ticket-actions">
                            @if($order->status == 'paid')
                                <a href="{{ route('ticket.detail', $order->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-ticket-alt me-2"></i>Lihat Riwayat
                                </a>
                            @elseif($order->status == 'redeemed')
                                <a href="{{ route('payment.detail', $order->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-receipt me-2"></i>Detail Pembelian
                                </a>
                            @else
                                <button class="btn btn-outline-secondary" disabled>
                                    <i class="fas fa-ban me-2"></i>Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state animate-fade-in">
                    <i class="fas fa-history empty-icon"></i>
                    <h3>Riwayat Tiket Kosong</h3>
                    <p>Anda belum memiliki riwayat tiket untuk pertandingan yang telah selesai</p>
                    <a href="{{ url('/') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-search me-2"></i>Cari Pertandingan
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Navigation
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.ticket-tab');
        
        tabLinks.forEach(link => {
            link.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                tabLinks.forEach(tab => tab.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to current tab and content
                this.classList.add('active');
                document.getElementById(tabId + '-tickets').classList.add('active');
            });
        });
    });
</script>
@endsection
