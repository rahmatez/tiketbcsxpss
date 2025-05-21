@extends('layouts.app')

@section('title', 'Detail Status Pembayaran')

@section('content')
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchase.history') }}">Riwayat Pembelian</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Detail Pembayaran 
                        @if($order->midtrans_order_id)
                            {{ $order->midtrans_order_id }}
                        @else
                            #{{ $order->id }}
                        @endif
                    </li>
                </ol>
            </nav>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i> Detail Status Pembayaran
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Status pembayaran header -->                    <div class="text-center mb-4">
                        @if ($order->status === 'paid')
                            <div class="d-inline-block bg-success text-white rounded-circle p-3 mb-3">
                                <i class="fas fa-check-circle fa-3x"></i>
                            </div>
                            <h2 class="mb-2">Pembayaran Berhasil</h2>
                            <p class="text-muted">Pembayaran Anda telah diterima dan tiket sudah aktif.</p>
                        @elseif ($order->status === 'pending')
                            <div class="d-inline-block bg-warning rounded-circle p-3 mb-3">
                                <i class="fas fa-clock fa-3x text-white"></i>
                            </div>
                            <h2 class="mb-2">Menunggu Pembayaran</h2>
                            <p class="text-muted">Pembayaran Anda belum selesai. Silakan selesaikan pembayaran.</p>
                        @elseif ($order->status === 'cancelled')
                            <div class="d-inline-block bg-danger text-white rounded-circle p-3 mb-3">
                                <i class="fas fa-times-circle fa-3x"></i>
                            </div>
                            <h2 class="mb-2">Pembayaran Dibatalkan</h2>
                            <p class="text-muted">Pembayaran Anda telah dibatalkan atau kedaluwarsa.</p>
                        @elseif ($order->status === 'redeemed')
                            <div class="d-inline-block bg-info text-white rounded-circle p-3 mb-3">
                                <i class="fas fa-ticket-alt fa-3x"></i>
                            </div>
                            <h2 class="mb-2">Tiket Telah Digunakan</h2>
                            <p class="text-muted">Tiket Anda telah berhasil digunakan untuk memasuki venue.</p>
                        @else
                            <div class="d-inline-block bg-secondary text-white rounded-circle p-3 mb-3">
                                <i class="fas fa-question-circle fa-3x"></i>
                            </div>
                            <h2 class="mb-2">Status: {{ ucfirst($order->status) }}</h2>
                            <p class="text-muted">Status pembayaran sedang diproses.</p>
                        @endif
                    </div>

                    <!-- Detail Pertandingan -->
                    <div class="card mb-4 border-light">                        <div class="card-header bg-dark d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-futbol me-2"></i> Detail Pertandingan</h5>
                            @if($order->status === 'paid' || $order->status === 'redeemed')
                                <a href="{{ route('ticket.detail', $order->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-ticket-alt me-1"></i> Lihat Tiket
                                </a>
                            @elseif($order->status === 'pending')
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-credit-card me-1"></i> Selesaikan Pembayaran
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h5 class="text-center">{{ $order->game->home_team }} vs {{ $order->game->away_team }}</h5>
                                    <p class="text-center mb-0">
                                        <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($order->game->match_time)->format('l, d F Y') }}<br>
                                        <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($order->game->match_time)->format('H:i') }} WIB<br>
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ $order->game->stadium_name }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td>Kategori Tiket</td>
                                                <td>:</td>
                                                <td><strong>{{ $order->ticket->category }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Jumlah</td>
                                                <td>:</td>
                                                <td>{{ $order->quantity }} tiket</td>
                                            </tr>
                                            <tr>
                                                <td>Harga</td>
                                                <td>:</td>
                                                <td>Rp {{ number_format($order->ticket->price, 0, ',', '.') }} / tiket</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total</strong></td>
                                                <td>:</td>
                                                <td><strong>Rp {{ number_format($order->quantity * $order->ticket->price, 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Pembayaran -->
                    <div class="row">
                        <!-- Informasi Pembayaran -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-dark">
                                <div class="card-header bg-dark">
                                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i> Informasi Pembayaran</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">                                        <table class="table">
                                            <tr>
                                                <td>ID Order</td>
                                                <td>:</td>
                                                <td>
                                                    @if($order->midtrans_order_id)
                                                        {{ $order->midtrans_order_id }}
                                                    @else
                                                        #{{ $order->id }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Order</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i:s') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Status</td>
                                                <td>:</td>                                                <td>
                                                    @if ($order->status == 'pending')
                                                        <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                                                    @elseif ($order->status == 'paid')
                                                        <span class="badge bg-success">Pembayaran Berhasil</span>
                                                    @elseif ($order->status == 'cancelled')
                                                        <span class="badge bg-danger">Dibatalkan</span>
                                                    @elseif ($order->status == 'expired')
                                                        <span class="badge bg-secondary">Kedaluwarsa</span>
                                                    @elseif ($order->status == 'redeemed')
                                                        <span class="badge bg-success">Pembayaran Berhasil</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Metode Pembayaran</td>
                                                <td>:</td>
                                                <td>
                                                    @if ($order->payment_method == 'pending')
                                                        Belum dipilih
                                                    @else
                                                        {{ ucwords(str_replace('_', ' ', $order->payment_method)) }}
                                                    @endif
                                                </td>
                                            </tr>                                            @if ($order->status === 'paid' || $order->status === 'redeemed')
                                            <tr>
                                                <td>Tanggal Pembayaran</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($order->updated_at)->format('d M Y H:i:s') }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pembeli -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-dark">
                                <div class="card-header bg-dark">
                                    <h5 class="mb-0"><i class="fas fa-user me-2"></i> Informasi Pembeli</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tr>
                                                <td>Nama</td>
                                                <td>:</td>
                                                <td>{{ $order->user->name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>:</td>
                                                <td>{{ $order->user->email }}</td>
                                            </tr>
                                            @if ($order->user->phone_number)
                                            <tr>
                                                <td>Telepon</td>
                                                <td>:</td>
                                                <td>{{ $order->user->phone_number }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td colspan="3">
                                                    <strong class="text-muted small">
                                                        * Informasi ini akan digunakan sebagai identifikasi pemegang tiket
                                                    </strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Detail Status Pembayaran -->
                    @if (!empty($paymentSteps) || !empty($paymentHistory))
                    <div class="row">
                        <!-- Langkah Pembayaran -->
                        @if (!empty($paymentSteps))
                        <div class="col-md-6 mb-4">
                            <div class="card border-light h-100">
                                <div class="card-header bg-dark">
                                    <h5 class="mb-0"><i class="fas fa-list-ol me-2"></i> Langkah Pembayaran</h5>
                                </div>                                <div class="card-body">
                                    @if ($order->status === 'paid' || $order->status === 'redeemed')
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i> Pembayaran Anda telah selesai dan berhasil!
                                        </div>
                                    @else
                                        <div class="timeline">
                                            @foreach($paymentSteps as $step)
                                                <div class="timeline-item">
                                                    <div class="timeline-badge">{{ $step['step'] }}</div>
                                                    <div class="timeline-content">
                                                        <h6>{{ $step['title'] }}</h6>
                                                        <p>{!! $step['description'] !!}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Riwayat Status -->
                        @if (!empty($paymentHistory))
                        <div class="col-md-6 mb-4">
                            <div class="card border-light h-100">
                                <div class="card-header bg-dark">
                                    <h5 class="mb-0"><i class="fas fa-history me-2"></i> Riwayat Status</h5>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        @foreach($paymentHistory as $history)
                                            <div class="timeline-item">
                                                <div class="timeline-badge"><i class="fas fa-circle"></i></div>
                                                <div class="timeline-content">
                                                    <h6>{{ $history['status'] }}</h6>
                                                    <p>{{ $history['description'] }}</p>
                                                    <span class="timeline-date">{{ \Carbon\Carbon::parse($history['date'])->format('d M Y H:i:s') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif                    <!-- Tombol Aksi -->
                    <div class="text-center mt-4">
                        @if ($order->status === 'pending')
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-credit-card me-2"></i> Lanjutkan Pembayaran
                            </a>
                            <a href="{{ route('payment.status', $order->id) }}" 
                               class="btn btn-outline-secondary btn-lg ms-2 check-status-btn">
                                <i class="fas fa-sync-alt me-2"></i> Cek Status
                            </a>
                        @elseif ($order->status === 'paid' || $order->status === 'redeemed')
                            <a href="{{ route('ticket.detail', $order->id) }}" class="btn btn-success btn-lg">
                                <i class="fas fa-ticket-alt me-2"></i> Lihat Tiket
                            </a>
                        @endif
                        
                        <a href="{{ route('purchase.history') }}" class="btn btn-outline-primary btn-lg ms-2">
                            <i class="fas fa-history me-2"></i> Riwayat Pembelian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Timeline Style */
.timeline {
    position: relative;
    padding: 20px 0;
}
.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #e0e0e0;
    left: 15px;
    margin-left: -1.5px;
}
.timeline-item {
    position: relative;
    margin-bottom: 30px;
    margin-left: 30px;
}
.timeline-badge {
    width: 30px;
    height: 30px;
    line-height: 30px;
    font-size: 16px;
    text-align: center;
    position: absolute;
    top: 0;
    left: -45px;
    margin-left: 0;
    background-color: #4e73df;
    color: #fff;
    border-radius: 50%;
    z-index: 1;
}
.timeline-badge i {
    font-size: 12px;
}
.timeline-content {
    padding: 0 15px 0 15px;
    position: relative;
}
.timeline-content h6 {
    margin: 0 0 5px;
    color: #333;
    font-weight: 600;
}
.timeline-date {
    display: block;
    margin-top: 8px;
    font-size: 13px;
    color: #999;
}
/* Card hover effect */
.card {
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkStatusBtn = document.querySelector('.check-status-btn');
    
    if (checkStatusBtn) {
        checkStatusBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('href');
            const button = this;
            const originalText = button.innerHTML;
            
            // Disable button and show loading
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memeriksa...';
            
            // Make AJAX request to check payment status
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Show success notification
                    alert('Status pembayaran: ' + data.message);
                    
                    // Reload page if payment status has changed
                    if (data.order_status !== 'pending') {
                        location.reload();
                    }
                } else {
                    // Show error message
                    alert('Error: ' + data.message);
                }
                
                // Re-enable button
                button.disabled = false;
                button.innerHTML = originalText;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memeriksa status pembayaran');
                
                // Re-enable button
                button.disabled = false;
                button.innerHTML = originalText;
            });
        });
    }
});
</script>
@endsection
