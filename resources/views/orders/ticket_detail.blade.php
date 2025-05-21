@extends('layouts.app')

@section('title', 'Detail Tiket - BCSXPSS')

@section('styles')
<style>
    /* Custom styles for countdown on ticket detail page */
    .timer-container .countdown-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: nowrap;
        gap: 15px;
    }
    
    .timer-container .countdown-item {
        margin: 0 5px;
        text-align: center;
        flex-basis: 0;
        flex-grow: 1;
        max-width: 90px;
    }
    
    .timer-container .countdown-value {
        background-color: #00451f;
        border: 2px solid rgba(255, 255, 255, 0.1);
        padding: 10px;
        border-radius: 10px;
        font-size: 2rem;
        font-weight: bold;
        line-height: 1;
        height: 60px;
        width: 60px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .timer-container .countdown-label {
        margin-top: 8px;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .ticket-detail-page {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .ticket-container {
        position: relative;
        margin-bottom: 30px;
    }
    
    .ticket-card {
        background-color: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .ticket-header {
        padding: 20px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .ticket-header.home {
         background: linear-gradient(
        to right,
        var(--secondary-color),
        #075028,
        var(--primary-color) 150%
    );
    }
    
    .ticket-header.away {
        background: linear-gradient(135deg, var(--gray-color), #4d545a);
    }
    
    .ticket-header::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: -10px;
        height: 20px;
        background-color: white;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    
    .ticket-match {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .ticket-team {
        text-align: center;
        flex: 1;
    }
    
    .team-logo {
        width: 60px;
        height: 60px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .team-logo i {
        font-size: 30px;
        color: white;
    }
    
    .team-name {
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 5px;
    }
    
    .team-tag {
        display: inline-block;
        padding: 3px 8px;
        background-color: rgba(255, 255, 255, 0.3);
        border-radius: 15px;
        font-size: 0.7rem;
        text-transform: uppercase;
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
    
    .ticket-meta {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
    }
    
    .ticket-meta-item {
        text-align: center;
    }
    
    .meta-label {
        font-size: 0.8rem;
        opacity: 0.8;
        margin-bottom: 5px;
    }
    
    .meta-value {
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .ticket-body {
        padding: 20px;
        position: relative;
    }
    
    .ticket-divider {
        position: relative;
        height: 30px;
        margin: 0 -20px;
    }
    
    .ticket-divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        border-top: 2px dashed #e0e0e0;
    }
    
    .ticket-divider::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        display: flex;
        justify-content: space-between;
    }
    
    .ticket-divider-circle-left {
        position: absolute;
        left: -10px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        background-color: var(--body-bg);
        border-radius: 50%;
    }
    
    .ticket-divider-circle-right {
        position: absolute;
        right: -10px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        background-color: var(--body-bg);
        border-radius: 50%;
    }
    
    .ticket-info {
        display: flex;
        flex-wrap: wrap;
    }
    
    .ticket-info-item {
        width: 50%;
        padding: 10px 0;
    }
    
    .info-label {
        font-size: 0.8rem;
        color: var(--gray-color);
        margin-bottom: 5px;
    }
    
    .info-value {
        font-weight: 600;
    }
    
    .qr-section {
        margin-top: 20px;
        text-align: center;
        padding: 20px 0;
    }
    
    .qr-title {
        margin-bottom: 15px;
        position: relative;
        padding-bottom: 10px;
    }
    
    .qr-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 3px;
        background-color: var(--primary-color);
    }
    
    .qr-container {
        display: inline-block;
        padding: 15px;
        background-color: white;
        border-radius: 10px;
        border: 2px solid var(--primary-color);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    
    .qr-container svg {
        display: block;
        max-width: 200px;
        height: auto;
    }
    
    .qr-instructions {
        max-width: 300px;
        margin: 0 auto;
        font-size: 0.9rem;
        color: var(--gray-color);
    }
    
    .ticket-footer {
        border-top: 1px solid #f0f0f0;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .ticket-status-badge {
        padding: 8px 15px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
    }
    
    .ticket-status-badge i {
        margin-right: 5px;
    }
    
    .ticket-status-badge.paid {
        background-color: rgba(26, 115, 232, 0.1);
        color: var(--primary-color);
    }
    
    .ticket-status-badge.redeemed {
        background-color: rgba(52, 168, 83, 0.1);
        color: var(--secondary-color);
    }
    
    .ticket-status-badge.pending {
        background-color: rgba(251, 188, 4, 0.1);
        color: var(--accent-color);
    }
    
    .ticket-status-badge.cancelled {
        background-color: rgba(234, 67, 53, 0.1);
        color: var(--danger-color);
    }
    
    .ticket-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
    }
    
    .btn-ticket {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background-color: #f8f9fa;
        color: var(--gray-color);
        transition: all 0.3s ease;
    }
    
    .btn-ticket:hover {
        background-color: var(--primary-color);
        color: white;
    }
    
    .timer-container {
        text-align: center;
        margin: 20px 0;
        padding: 20px;
        border-radius: 10px;
        background: #002914;
        color: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .timer-container h5 {
        margin-bottom: 15px;
        text-align: center;
        font-weight: bold;
    }
    
    @media (max-width: 768px) {
        .ticket-match {
            flex-direction: column;
        }
        
        .team-logo {
            margin-bottom: 15px;
        }
        
        .match-vs {
            margin: 15px 0;
        }
        
        .ticket-meta {
            flex-wrap: wrap;
        }
        
        .ticket-meta-item {
            width: 50%;
            margin-bottom: 15px;
        }
        
        .ticket-info-item {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
    <div class="container ticket-detail-page">
        <div class="d-flex align-items-center mb-4 animate-fade-in">
            <a href="{{ url()->previous() }}" class="btn btn-outline-primary me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="mb-0">Detail Tiket</h1>
        </div>
        
        <div class="ticket-container animate-fade-in">
            <!-- E-Ticket Card -->
            <div class="ticket-card">
                <!-- Ticket Header -->
                <div class="ticket-header {{ $order->game->is_home_game ? 'home' : 'away' }}">
                    <div class="ticket-match">
                        <!-- Home Team -->
                        <div class="ticket-team">
                            <div class="team-logo">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="team-name">{{ $order->game->home_team }}</div>
                            <div class="team-tag">Home</div>
                        </div>
                        
                        <!-- VS Badge -->
                        <div class="match-vs">VS</div>
                        
                        <!-- Away Team -->
                        <div class="ticket-team">
                            <div class="team-logo">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="team-name">{{ $order->game->away_team }}</div>
                            <div class="team-tag">Away</div>
                        </div>
                    </div>
                    
                    <!-- Match Meta Info -->
                    <div class="ticket-meta">
                        <div class="ticket-meta-item">
                            <div class="meta-label">Tanggal</div>
                            <div class="meta-value">{{ Carbon\Carbon::parse($order->game->match_time)->format('d M Y') }}</div>
                        </div>
                        
                        <div class="ticket-meta-item">
                            <div class="meta-label">Waktu</div>
                            <div class="meta-value">{{ Carbon\Carbon::parse($order->game->match_time)->format('H:i') }} WIB</div>
                        </div>
                        
                        <div class="ticket-meta-item">
                            <div class="meta-label">Kategori</div>
                            <div class="meta-value">{{ $order->ticket->category }}</div>
                        </div>
                        
                        <div class="ticket-meta-item">
                            <div class="meta-label">Gate</div>
                            <div class="meta-value">{{ chr(ord('A') + (int)$order->ticket->category - 1) }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Ticket Body -->
                <div class="ticket-body">
                    <!-- Ticket Divider -->
                    <div class="ticket-divider">
                        <div class="ticket-divider-circle-left"></div>
                        <div class="ticket-divider-circle-right"></div>
                    </div>
                    
                    <!-- Ticket Information -->
                    <div class="ticket-info">
                        <div class="ticket-info-item">
                            <div class="info-label">ID Pemesanan</div>
                            <div class="info-value">#{{ $order->id }}</div>
                        </div>
                        
                        <div class="ticket-info-item">
                            <div class="info-label">Jumlah Tiket</div>
                            <div class="info-value">{{ $order->quantity }} Tiket</div>
                        </div>
                        
                        <div class="ticket-info-item">
                            <div class="info-label">Stadium</div>
                            <div class="info-value">{{ $order->game->stadium_name }}</div>
                        </div>
                        
                        <div class="ticket-info-item">
                            <div class="info-label">Tanggal Pembelian</div>
                            <div class="info-value">{{ Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</div>
                        </div>
                    </div>
                    
                    <!-- QR Code Section -->
                    <div class="qr-section">
                        <h5 class="qr-title">Ticket QR Code</h5>
                        
                        <div class="qr-container" id="qr-code-container">
                            {!! $qrCode !!}
                        </div>
                        
                        <p class="qr-instructions">
                            <i class="fas fa-info-circle me-2"></i>
                            Tunjukkan QR Code ini kepada petugas di pintu masuk stadium untuk melakukan validasi tiket
                        </p>
                    </div>
                </div>
                
                <!-- Ticket Footer -->
                <div class="ticket-footer">
                    <div class="ticket-status">
                        <div class="ticket-status-badge {{ strtolower($order->status) }}">
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
                    </div>
                    
                    <div class="action-buttons">
                        <button class="btn-ticket btn-save-qr" data-qr-container="#qr-code-container" data-bs-toggle="tooltip" title="Simpan QR Code">
                            <i class="fas fa-download"></i>
                        </button>
                        
                        <a href="{{ route('orders.ticket.pdf', $order->id) }}" class="btn-ticket btn-download-pdf" data-bs-toggle="tooltip" title="Unduh Tiket PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        
                        <button class="btn-ticket btn-share" 
                            data-title="Tiket {{ $order->game->home_team }} vs {{ $order->game->away_team }}"
                            data-text="Saya akan menonton pertandingan {{ $order->game->home_team }} vs {{ $order->game->away_team }} pada {{ Carbon\Carbon::parse($order->game->match_time)->format('d M Y - H:i') }} WIB di {{ $order->game->stadium_name }}"
                            data-bs-toggle="tooltip" title="Bagikan">
                            <i class="fas fa-share-alt"></i>
                        </button>
                        
                        <button class="btn-ticket" onclick="addToCalendar(
                            '{{ $order->game->tournament_name }}: {{ $order->game->home_team }} vs {{ $order->game->away_team }}',
                            '{{ $order->game->match_time }}',
                            '',
                            '{{ $order->game->stadium_name }}',
                            'Pertandingan {{ $order->game->tournament_name }} antara {{ $order->game->home_team }} vs {{ $order->game->away_team }}'
                        )" data-bs-toggle="tooltip" title="Tambahkan ke Kalender">
                            <i class="fas fa-calendar-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Countdown Timer (if match is in the future) -->
        @if(Carbon\Carbon::parse($order->game->match_time)->isFuture())
            <div class="timer-container animate-fade-in">
                <h5>Pertandingan Dimulai Dalam</h5>
                <div class="countdown-wrapper">
                    <div id="ticket-countdown" data-countdown="{{ $order->game->match_time }}" data-expired-text="Pertandingan Sedang Berlangsung"></div>
                </div>
            </div>
        @endif
        
        <!-- Additional Information -->
        <div class="card mb-4 animate-fade-in">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Penting</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Silakan datang minimal 1 jam sebelum pertandingan dimulai untuk menghindari antrean
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        QR Code hanya dapat digunakan satu kali untuk masuk ke stadion
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Pastikan baterai ponsel Anda cukup untuk menunjukkan tiket digital ini
                    </li>
                    <li>
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Simpan tiket digital ini dengan baik dan jangan bagikan QR Code dengan orang lain
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Back Button -->
        <div class="d-grid gap-2 col-md-6 mx-auto mb-5 animate-fade-in">
            <a href="{{ route('my.tickets') }}" class="btn btn-primary">
                <i class="fas fa-ticket-alt me-2"></i>Kembali ke Daftar Tiket
            </a>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Custom countdown handler for ticket detail page
    const ticketCountdown = document.getElementById('ticket-countdown');
    if (ticketCountdown) {
        const targetDate = new Date(ticketCountdown.getAttribute('data-countdown')).getTime();
        
        const updateCountdown = () => {
            const now = new Date().getTime();
            const distance = targetDate - now;
            
            if (distance < 0) {
                ticketCountdown.innerHTML = ticketCountdown.getAttribute('data-expired-text') || "Pertandingan Sedang Berlangsung";
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Set the countdown HTML with better layout
            ticketCountdown.innerHTML = `
                <div class="countdown-container">
                    <div class="countdown-item">
                        <div class="countdown-value">${days}</div>
                        <div class="countdown-label">Hari</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-value">${hours}</div>
                        <div class="countdown-label">Jam</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-value">${minutes}</div>
                        <div class="countdown-label">Menit</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-value">${seconds}</div>
                        <div class="countdown-label">Detik</div>
                    </div>
                </div>
            `;
        };
        
        // Initial update
        updateCountdown();
        
        // Update every second
        setInterval(updateCountdown, 1000);
    }
    
    // Add event listeners for share functionality
    const shareButton = document.getElementById('share-ticket');
    if (shareButton) {
        shareButton.addEventListener('click', function() {
            if (navigator.share) {
                navigator.share({
                    title: shareButton.getAttribute('data-share-title'),
                    text: shareButton.getAttribute('data-share-text'),
                    url: window.location.href,
                })
                .catch((error) => console.log('Error sharing:', error));
            } else {
                // Fallback for browsers that don't support Web Share API
                alert('Copy this link to share: ' + window.location.href);
            }
        });
    }
});
</script>
@endsection
