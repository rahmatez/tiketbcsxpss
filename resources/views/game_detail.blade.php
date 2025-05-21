@php use Carbon\Carbon; @endphp

@extends('layouts.app')

@section('title', 'Detail Pertandingan - BCSXPSS')

@section('styles')
<style>
    /* Match Countdown Custom Styles */
    .match-countdown .countdown-container {
        display: flex;
        gap: 15px;
        justify-content: center;
        align-items: center;
    }
    
    .match-countdown .countdown-item {
        text-align: center;
        width: 70px;
        margin: 0 5px;
    }
    
    .match-countdown .countdown-value {
        background-color: var(--primary-color);
        color: white;
        font-size: 1.8rem;
        font-weight: 700;
        height: 65px;
        width: 65px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .match-countdown .countdown-label {
        margin-top: 8px;
        font-size: 0.9rem;
        color: var(--dark-color);
        font-weight: 500;
    }
    
    /* Stadium Map */
    .stadium-map {
        position: relative;
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        border: 2px solid var(--primary-color);
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .stadium-field {
        background-color: #2E7D32;
        height: 300px;
        border-radius: 10px;
        position: relative;
        margin: 20px;
    }
    
    .stadium-field::after {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: white;
        transform: translateX(-50%);
    }
    
    .stadium-field::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        width: 60px;
        height: 60px;
        border: 2px solid white;
        border-radius: 50%;
        transform: translate(-50%, -50%);
    }
    
    .stadium-seats {
        display: flex;
        flex-wrap: wrap;
        padding: 10px;
    }
    
    .seat-category {
        text-align: center;
        padding: 10px;
        margin: 5px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        flex: 1;
        min-width: 100px;
    }
    
    .seat-category.category-A {
        background-color: rgba(26, 115, 232, 0.2);
        border: 2px solid var(--primary-color);
    }
    
    .seat-category.category-B {
        background-color: rgba(52, 168, 83, 0.2);
        border: 2px solid var(--secondary-color);
    }
    
    .seat-category.category-C {
        background-color: rgba(251, 188, 4, 0.2);
        border: 2px solid var(--accent-color);
    }
    
    .seat-category.selected {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .seat-category.category-A.selected {
        background-color: rgba(26, 115, 232, 0.5);
    }
    
    .seat-category.category-B.selected {
        background-color: rgba(52, 168, 83, 0.5);
    }
    
    .seat-category.category-C.selected {
        background-color: rgba(251, 188, 4, 0.5);
    }
    
    .seat-category h5 {
        margin-bottom: 5px;
    }
    
    .seat-category .price {
        font-weight: 600;
    }
    
    .seat-category .availability {
        font-size: 0.85rem;
        margin-top: 5px;
    }
    
    .seat-category .availability.low {
        color: var(--danger-color);
    }
    
    /* Game Details */
    .match-countdown {
        background-color: white;
        color: var(--dark-color);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .game-info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .game-info-list li {
        padding: 15px;
        border-bottom: 1px solid var(--light-gray);
        display: flex;
        align-items: center;
    }
    
    .game-info-list li:last-child {
        border-bottom: none;
    }
    
    .game-info-list li i {
        min-width: 30px;
        font-size: 1.2rem;
        color: var(--primary-color);
    }
    
    .quantity-selector {
        display: flex;
        align-items: center;
    }
    
    .quantity-selector .btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: bold;
    }
    
    .quantity-selector input {
        width: 60px;
        text-align: center;
        border: none;
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    /* Teams display */
    .teams-vs-display {
        display: flex;
        align-items: center;
        justify-content: space-around;
        margin: 30px 0;
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .team-box {
        text-align: center;
        padding: 10px;
    }
    
    .team-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 10px 0 0;
    }
    
    .team-logo {
        width: 80px;
        height: 80px;
        background: #f5f5f5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        overflow: hidden;
    }
    
    .team-logo i {
        font-size: 2.5rem;
        color: var(--primary-color);
    }
    
    .team-logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    
    .vs-badge {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--gray-color);
    }
</style>
@endsection

@section('content')
    <!-- Hero Section -->
    @php
        $backgroundImage = $game->image_path ? Storage::url($game->image_path) : asset('images/' . rand(1, 3) . '.jpg');
        $defaultImageUrl = asset('images/team-default.png');
        $homeTeamLogo = $game->home_team_logo ? Storage::url($game->home_team_logo) : $defaultImageUrl;
        $awayTeamLogo = $game->away_team_logo ? Storage::url($game->away_team_logo) : $defaultImageUrl;
    @endphp
    <div class="game-hero" style="background-image: url('{{ $backgroundImage }}');">
        <div class="container game-hero-content">
            <div class="teams-display">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="text-center me-3">
                        <span class="team-name d-block">{{ $game->home_team }}</span>
                    </div>
                    <span class="vs-badge mx-3">VS</span>
                    <div class="text-center ms-3">
                        <span class="team-name d-block">{{ $game->away_team }}</span>
                    </div>
                </div>
            </div>
            
            <div class="match-details">
                <div class="match-detail-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ Carbon::parse($game->match_time)->format('d M Y') }}</span>
                </div>
                <div class="match-detail-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ Carbon::parse($game->match_time)->format('H:i') }} WIB</span>
                </div>
                <div class="match-detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $game->stadium_name }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <!-- Left Column - Game Details -->
            <div class="col-lg-5 mb-4">
                <!-- Teams VS Display -->
                <div class="teams-vs-display animate-fade-in">
                    <div class="team-box">
                        <div class="team-logo">
                            <img src="{{ $homeTeamLogo }}" alt="{{ $game->home_team }}" class="img-fluid" style="max-width: 80px; max-height: 80px;" onerror="this.src='{{ $defaultImageUrl }}'">
                        </div>
                        <h4 class="team-name">{{ $game->home_team }}</h4>
                        <span class="badge bg-primary">HOME</span>
                    </div>
                    
                    <div class="vs-badge">VS</div>
                    
                    <div class="team-box">
                        <div class="team-logo">
                            <img src="{{ $awayTeamLogo }}" alt="{{ $game->away_team }}" class="img-fluid" style="max-width: 80px; max-height: 80px;" onerror="this.src='{{ $defaultImageUrl }}'">
                        </div>
                        <h4 class="team-name">{{ $game->away_team }}</h4>
                        <span class="badge bg-secondary">AWAY</span>
                    </div>
                </div>
                
                <!-- Match Countdown -->
                <div class="card match-countdown animate-fade-in">
                    <div class="card-body text-center">
                        <h5 class="card-title text-center mb-3">Pertandingan Dimulai Dalam</h5>
                        <div id="match-countdown" class="d-flex justify-content-center align-items-center" data-countdown="{{ $game->match_time }}" data-expired-text="Pertandingan Telah Dimulai"></div>
                    </div>
                </div>
                
                <!-- Game Information -->
                <div class="card mb-4 animate-fade-in">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Pertandingan</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="game-info-list">
                            <li>
                                <i class="fas fa-trophy"></i>
                                <div>
                                    <strong>Turnamen</strong><br>
                                    <span>{{ $game->tournament_name }}</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <strong>Tanggal & Waktu</strong><br>
                                    <span>{{ Carbon::parse($game->match_time)->format('d M Y - H:i') }} WIB</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Lokasi</strong><br>
                                    <span>{{ $game->stadium_name }}</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-shopping-cart"></i>
                                <div>
                                    <strong>Batas Pembelian Tiket</strong><br>
                                    <span>{{ Carbon::parse($game->purchase_deadline)->format('d M Y - H:i') }} WIB</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <strong>Status</strong><br>
                                    <span class="badge bg-{{ $game->status == 'active' ? 'success' : 'warning' }}">
                                        {{ ucfirst($game->status) }}
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Add to Calendar -->
                <div class="d-grid gap-2 animate-fade-in">
                    <button type="button" class="btn btn-outline-primary" onclick="addToCalendar(
                        '{{ $game->tournament_name }}: {{ $game->home_team }} vs {{ $game->away_team }}',
                        '{{ $game->match_time }}',
                        '',
                        '{{ $game->stadium_name }}',
                        'Pertandingan {{ $game->tournament_name }} antara {{ $game->home_team }} vs {{ $game->away_team }}'
                    )">
                        <i class="far fa-calendar-plus me-2"></i>Tambahkan ke Kalender
                    </button>
                    
                    <button type="button" class="btn btn-outline-secondary btn-share"
                        data-title="Pertandingan {{ $game->home_team }} vs {{ $game->away_team }}"
                        data-text="Pertandingan {{ $game->tournament_name }}: {{ $game->home_team }} vs {{ $game->away_team }} pada {{ Carbon::parse($game->match_time)->format('d M Y - H:i') }} WIB di {{ $game->stadium_name }}">
                        <i class="fas fa-share-alt me-2"></i>Bagikan Pertandingan
                    </button>
                </div>
            </div>
            
            <!-- Right Column - Ticket Selection -->
            <div class="col-lg-7">
                <!-- Ticket Selection -->
                <div class="card ticket-selection-card animate-fade-in">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Pilih Tiket</h5>
                    </div>
                    <div class="card-body">
                        @if(!$game->is_home_game)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Perhatian!</strong> Ini adalah pertandingan away. Tiket tidak tersedia untuk pembelian.
                            </div>
                        @else
                            <!-- Stadium Map -->
                            <h5 class="mb-3">Denah Stadion</h5>
                            <div class="stadium-map mb-4">
                                <div class="stadium-field"></div>
                                <div class="stadium-seats">
                                    @foreach($tickets as $ticket)
                                        @php
                                            $purchased = $purchasedQuantities[$ticket->category] ?? 0;
                                            $remaining = $ticket->quantity - $purchased;
                                            $availabilityClass = $remaining < 5 ? 'low' : '';
                                        @endphp
                                        <div class="seat-category category-{{ $ticket->category }} {{ $ticket->category == 'A' ? 'selected' : '' }}" 
                                             data-category="{{ $ticket->category }}" 
                                             onclick="selectCategory(this)">
                                            <h5>Kategori {{ $ticket->category }}</h5>
                                            <div class="price">Rp{{ number_format($ticket->price, 0, ',', '.') }}</div>
                                            <div class="availability {{ $availabilityClass }}">
                                                @if($remaining <= 0)
                                                    <span class="badge bg-danger">Habis</span>
                                                @else
                                                    Tersisa {{ $remaining }} tiket
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <form id="checkout_form" method="POST" action="{{ route('checkout') }}" data-loading="true" class="needs-validation" novalidate>
                                @csrf
                                <input type="hidden" name="game_id" value="{{ $game->id }}">
                                
                                <!-- Ticket Category -->
                                <div class="mb-4">
                                    <label for="ticket_category" class="form-label">Kategori Tiket</label>
                                    <select id="ticket_category" name="ticket_category" class="form-select form-select-lg mb-3" required onchange="updateStockInfo()">
                                        @foreach($tickets as $ticket)
                                            @php
                                                $purchased = $purchasedQuantities[$ticket->category] ?? 0;
                                                $remaining = $ticket->quantity - $purchased;
                                            @endphp
                                            <option value="{{ $ticket->category }}" 
                                                    data-price="{{ $ticket->price }}"
                                                    data-purchased="{{ $purchased }}"
                                                    data-total="{{ $ticket->quantity }}" 
                                                    data-remaining="{{ $remaining }}"
                                                    {{ $remaining <= 0 ? 'disabled' : '' }}
                                                    {{ $ticket->category == 'A' ? 'selected' : '' }}>
                                                Kategori {{ $ticket->category }} - Rp{{ number_format($ticket->price, 0, ',', '.') }}
                                                {{ $remaining <= 0 ? ' (Habis)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="stock_info" class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="stock_quantity">
                                        @php
                                            $defaultTicket = $tickets->firstWhere('category', $defaultCategory);
                                            $purchasedDefault = $purchasedQuantities[$defaultCategory] ?? 0;
                                        @endphp
                                        @if($defaultTicket)
                                            {{ $purchasedDefault }}/{{ $defaultTicket->quantity }}. 
                                            Tersisa {{ $defaultTicket->quantity - $purchasedDefault }} tiket
                                        @else
                                            0/0. Tersisa 0 tiket
                                        @endif
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Quantity Selection -->
                                <div class="mb-4">
                                    <label class="form-label">Jumlah Tiket</label>
                                    <div class="quantity-selector">
                                        <button type="button" class="btn btn-outline-secondary btn-minus me-3">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" id="purchase_quantity" name="purchase_quantity" value="1"
                                               min="1" max="2" readonly required>
                                        <button type="button" class="btn btn-outline-secondary btn-plus ms-3">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-user-tag me-1"></i>
                                        Maksimal 2 tiket per akun
                                    </div>
                                </div>
                                
                                <!-- Price Summary -->
                                <div class="card mb-4">
                                    <div class="card-header bg-dark">
                                        <h6 class="mb-0">Ringkasan Pembelian</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Kategori</span>
                                            <span id="summary_category">Kategori A</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Harga Per Tiket</span>
                                            <span id="summary_price">
                                            @php
                                                $defaultTicket = $tickets->firstWhere('category', $defaultCategory);
                                            @endphp
                                            Rp{{ number_format($defaultTicket ? $defaultTicket->price : 0, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Jumlah</span>
                                            <span id="summary_quantity">1</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total</span>
                                            <span id="ticket_price" class="text-primary">
                                            @php
                                                $defaultTicket = $tickets->firstWhere('category', $defaultCategory);
                                            @endphp
                                            Rp{{ number_format($defaultTicket ? $defaultTicket->price : 0, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Status Information -->
                                <div id="purchase_info" class="alert alert-info mb-4" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Maksimal pembelian 2 tiket per akun!
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" id="buy_button" class="btn btn-primary btn-lg">
                                        <i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function updatePrice() {
        var ticketCategory = document.getElementById('ticket_category');
        if (!ticketCategory || ticketCategory.options.length === 0) {
            console.error("Tidak ada kategori tiket yang tersedia");
            return;
        }
        var selectedCategory = ticketCategory.options[ticketCategory.selectedIndex];
        var price = parseFloat(selectedCategory.getAttribute('data-price') || 0);
        var quantity = parseInt(document.getElementById('purchase_quantity').value);
        var totalPrice = price * quantity;
        
        // Update price display
        document.getElementById('ticket_price').innerText = 'Rp' + totalPrice.toLocaleString('id-ID');
        
        // Update summary
        document.getElementById('summary_category').innerText = 'Kategori ' + selectedCategory.value;
        document.getElementById('summary_price').innerText = 'Rp' + price.toLocaleString('id-ID');
        document.getElementById('summary_quantity').innerText = quantity;
    }

    function updateStockInfo() {
        var ticketCategory = document.getElementById('ticket_category');
        var selectedCategory = ticketCategory.options[ticketCategory.selectedIndex];
        var purchased = selectedCategory.getAttribute('data-purchased') || '0';
        var total = selectedCategory.getAttribute('data-total') || '0';
        var remaining = selectedCategory.getAttribute('data-remaining') || '0';
        
        // Update stock info
        document.getElementById('stock_quantity').innerText = purchased + '/' + total + '. Tersisa ' + remaining + ' tiket';
        
        // Update stadium map selection
        const seatCategories = document.querySelectorAll('.seat-category');
        seatCategories.forEach(category => {
            category.classList.remove('selected');
            if (category.getAttribute('data-category') === selectedCategory.value) {
                category.classList.add('selected');
            }
        });
        
        // Update purchase info and button state
        const purchaseInfo = document.getElementById('purchase_info');
        const buyButton = document.getElementById('buy_button');
        
        if (parseInt(remaining) <= 0) {
            buyButton.disabled = true;
            purchaseInfo.className = 'alert alert-danger mb-4';
            purchaseInfo.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Tiket Kategori ' + 
                selectedCategory.value + ' telah habis terjual!';
        } else if (parseInt(remaining) < 5) {
            buyButton.disabled = false;
            purchaseInfo.className = 'alert alert-warning mb-4';
            purchaseInfo.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Segera Habis! Tersisa ' + 
                remaining + ' tiket untuk Kategori ' + selectedCategory.value;
        } else {
            buyButton.disabled = false;
            purchaseInfo.className = 'alert alert-info mb-4';
            purchaseInfo.innerHTML = '<i class="fas fa-info-circle me-2"></i>Maksimal pembelian 2 tiket per akun!';
        }
        
        updatePrice();
    }

    function checkPurchaseDuration() {
        var purchaseDeadline = new Date("{{ $game->purchase_deadline }}");
        var now = new Date();
        if (now > purchaseDeadline) {
            document.getElementById('buy_button').disabled = true;
            const purchaseInfo = document.getElementById('purchase_info');
            purchaseInfo.className = 'alert alert-danger mb-4';
            purchaseInfo.innerHTML = '<i class="fas fa-clock me-2"></i>Penjualan ditutup karena batas waktu pembelian tiket telah berakhir.';
        }
    }
    
    function selectCategory(element) {
        // Get category
        const category = element.getAttribute('data-category');
        
        // Update select element
        const selectElement = document.getElementById('ticket_category');
        for (let i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === category) {
                selectElement.selectedIndex = i;
                break;
            }
        }
        
        // Update UI
        updateStockInfo();
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateStockInfo();
        checkPurchaseDuration();
        
        // Handle quantity buttons
        const minusBtn = document.querySelector('.btn-minus');
        const plusBtn = document.querySelector('.btn-plus');
        const quantityInput = document.getElementById('purchase_quantity');
        
        if (minusBtn && plusBtn && quantityInput) {
            minusBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > parseInt(quantityInput.min)) {
                    quantityInput.value = currentValue - 1;
                    updatePrice();
                }
            });
            
            plusBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue < parseInt(quantityInput.max)) {
                    quantityInput.value = currentValue + 1;
                    updatePrice();
                }
            });
        }
    });
</script>
@endsection
