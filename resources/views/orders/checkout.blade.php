@extends('layouts.app')

@section('title', 'Checkout - BCSXPSS')

@section('styles')
<style>
    .checkout-section {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .checkout-header {
        text-align: center;
        margin-bottom: 30px;
        position: relative;
    }
    
    .checkout-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        position: relative;
    }
    
    .checkout-steps::before {
        content: '';
        position: absolute;
        top: 14px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: var(--light-gray);
        z-index: -1;
    }
    
    .checkout-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 1;
    }
    
    .step-number {
        width: 30px;
        height: 30px;
        background-color: var(--light-gray);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
        font-weight: 600;
        color: white;
        transition: background-color 0.3s ease;
    }
    
    .checkout-step.active .step-number {
        background-color: var(--primary-color);
    }
    
    .checkout-step.completed .step-number {
        background-color: var(--secondary-color);
    }
    
    .step-label {
        font-size: 0.8rem;
        color: var(--gray-color);
        font-weight: 500;
    }
    
    .checkout-step.active .step-label {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .checkout-step.completed .step-label {
        color: var(--secondary-color);
        font-weight: 600;
    }
    
    .ticket-details-card {
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 30px;
        transition: all 0.3s ease;
        animation: fadeIn 0.5s ease;
    }
    
    .match-details {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        background-color: var(--primary-color);
        color: white;
    }
    
    .team-details {
        text-align: center;
        flex: 1;
    }
    
    .team-name {
        font-weight: 700;
        font-size: 1.2rem;
        margin-top: 10px;
    }
    
    .vs-badge {
        width: 40px;
        height: 40px;
        background-color: white;
        color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin: 0 15px;
    }
    
    .match-meta {
        padding: 15px 20px;
        background-color: rgba(26, 115, 232, 0.1);
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid rgba(26, 115, 232, 0.2);
    }
    
    .match-meta-item {
        display: flex;
        align-items: center;
    }
    
    .match-meta-item i {
        margin-right: 8px;
        color: var(--primary-color);
    }
    
    .order-summary {
        padding: 20px;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--light-gray);
    }
    
    .order-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .order-total {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 1.2rem;
    }
    
    .payment-options {
        margin-top: 30px;
    }
    
    .payment-note {
        background-color: rgba(251, 188, 4, 0.1);
        border-left: 3px solid var(--accent-color);
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    
    .checkout-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }
    
    @media (max-width: 768px) {
        .match-details {
            flex-direction: column;
            padding: 15px;
        }
        
        .vs-badge {
            margin: 15px 0;
        }
        
        .match-meta {
            flex-direction: column;
        }
        
        .match-meta-item {
            margin-bottom: 10px;
        }
        
        .checkout-actions {
            flex-direction: column;
            gap: 15px;
        }
        
        .checkout-actions .btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
    <div class="container mt-5 checkout-section">
        <!-- Checkout Steps -->
        <div class="checkout-header">
            <h1 class="mb-4 animate-fade-in">Checkout</h1>
            
            <div class="checkout-steps">
                <div class="checkout-step completed">
                    <div class="step-number">1</div>
                    <div class="step-label">Pilih Tiket</div>
                </div>
                <div class="checkout-step active">
                    <div class="step-number">2</div>
                    <div class="step-label">Checkout</div>
                </div>
                <div class="checkout-step">
                    <div class="step-number">3</div>
                    <div class="step-label">Pembayaran</div>
                </div>
                <div class="checkout-step">
                    <div class="step-number">4</div>
                    <div class="step-label">Selesai</div>
                </div>
            </div>
        </div>
        
        <!-- Ticket Details -->
        <div class="card ticket-details-card animate-fade-in">
            <!-- Match Details -->
            <div class="match-details">
                <div class="team-details">
                    <div class="team-logo">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                    <div class="team-name">{{ $game->home_team }}</div>
                </div>
                
                <div class="vs-badge">VS</div>
                
                <div class="team-details">
                    <div class="team-logo">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                    <div class="team-name">{{ $game->away_team }}</div>
                </div>
            </div>
            
            <!-- Match Meta Info -->
            <div class="match-meta">
                <div class="match-meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ Carbon\Carbon::parse($game->match_time)->format('d M Y') }}</span>
                </div>
                
                <div class="match-meta-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ Carbon\Carbon::parse($game->match_time)->format('H:i') }} WIB</span>
                </div>
                
                <div class="match-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $game->stadium_name }}</span>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <h5 class="mb-4">Ringkasan Pesanan</h5>
                
                <div class="order-item">
                    <div>
                        <strong>Tiket Kategori {{ $ticket->category }}</strong>
                        <div class="text-muted small">{{ $quantity }} x Rp{{ number_format($ticket->price, 0, ',', '.') }}</div>
                    </div>
                    <div>Rp{{ number_format($ticket->price * $quantity, 0, ',', '.') }}</div>
                </div>
                
                <div class="order-item">
                    <div>Biaya Layanan</div>
                    <div>Rp0</div>
                </div>
                
                <div class="order-item order-total">
                    <div>Total</div>
                    <div>Rp{{ number_format($ticket->price * $quantity, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Payment Options -->
        <div class="payment-options animate-fade-in">
            <div class="payment-note">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="fas fa-info-circle text-warning fa-2x"></i>
                    </div>
                    <div>
                        <h5>Informasi Pembayaran</h5>
                        <p>Setelah melanjutkan, Anda akan diarahkan ke halaman pembayaran Midtrans untuk menyelesaikan transaksi Anda dengan aman.</p>
                        <p class="mb-0"><strong>Metode pembayaran yang tersedia:</strong> Transfer Bank, E-Wallet, Kartu Kredit, dan lainnya.</p>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('finalize_checkout') }}" onsubmit="showCheckoutConfirmation(event)" data-loading="true" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="game_id" value="{{ $game->id }}">
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                <input type="hidden" name="quantity" value="{{ $quantity }}">
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="terms-check" required>
                    <label class="form-check-label" for="terms-check">
                        Saya setuju dengan <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">syarat dan ketentuan</a> yang berlaku.
                    </label>
                    <div class="invalid-feedback">
                        Anda harus menyetujui syarat dan ketentuan untuk melanjutkan.
                    </div>
                </div>
                
                <div class="checkout-actions">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-lock me-2"></i>Lanjutkan ke Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Syarat dan Ketentuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Ketentuan Umum</h6>
                    <p>Dengan melakukan pembelian tiket melalui platform Tiket BCS, Anda dianggap telah membaca, memahami, dan menyetujui semua syarat dan ketentuan yang berlaku.</p>
                    
                    <h6>Kebijakan Tiket</h6>
                    <ul>
                        <li>Tiket yang sudah dibeli tidak dapat dibatalkan, dikembalikan, atau ditukar dengan uang.</li>
                        <li>Satu akun hanya dapat membeli maksimal 2 tiket untuk satu pertandingan.</li>
                        <li>Tiket hanya berlaku untuk pertandingan, tanggal, dan waktu yang tertera pada tiket.</li>
                        <li>Tiket tidak dapat dialihkan ke orang lain.</li>
                    </ul>
                    
                    <h6>Kebijakan Pembayaran</h6>
                    <ul>
                        <li>Pembayaran harus dilakukan dalam waktu 30 menit setelah pemesanan dibuat.</li>
                        <li>Jika pembayaran tidak dilakukan dalam jangka waktu tersebut, pemesanan akan otomatis dibatalkan.</li>
                        <li>Semua pembayaran diproses melalui Midtrans dengan berbagai metode pembayaran yang tersedia.</li>
                    </ul>
                    
                    <h6>Penggunaan Tiket</h6>
                    <ul>
                        <li>Pastikan QR code tiket Anda tidak rusak atau terhalang.</li>
                        <li>QR code akan dipindai di pintu masuk stadion.</li>
                        <li>Setiap QR code hanya dapat digunakan satu kali.</li>
                        <li>Harap membawa identitas diri yang sesuai dengan data akun Anda.</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Saya Mengerti</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function showCheckoutConfirmation(event) {
        const form = event.target;
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
        }
        
        // Show loading
        showLoading();
    }
</script>
@endsection
