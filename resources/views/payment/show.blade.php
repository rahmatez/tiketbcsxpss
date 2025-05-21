@extends('layouts.app')

@section('title', 'Pembayaran Tiket')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Pembayaran Tiket</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h4>{{ $order->game->home_team }} vs {{ $order->game->away_team }}</h4>
                        <p class="text-muted">
                            {{ \Carbon\Carbon::parse($order->game->match_time)->format('l, d F Y - H:i') }} WIB
                            <br>
                            {{ $order->game->stadium_name }}
                        </p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h5 class="mb-3">Detail Pesanan</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td>Kategori Tiket</td>
                                        <td>:</td>
                                        <td><strong>{{ $order->ticket->category }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Harga Tiket</td>
                                        <td>:</td>
                                        <td>Rp {{ number_format($order->ticket->price, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah</td>
                                        <td>:</td>
                                        <td>{{ $order->quantity }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td>:</td>
                                        <td><strong>Rp {{ number_format($order->ticket->price * $order->quantity, 0, ',', '.') }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h5 class="mb-3">Detail Pembeli</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td>Nama</td>
                                        <td>:</td>
                                        <td><strong>{{ $order->user->name }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>:</td>
                                        <td>{{ $order->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>:</td>
                                        <td>
                                            @if ($order->status == 'pending')
                                                <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                                            @elseif ($order->status == 'paid')
                                                <span class="badge bg-success">Pembayaran Berhasil</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    @if ($order->status == 'pending')                        <div class="text-center">
                            <div class="mb-4">
                                <h5>Pilih Metode Pembayaran</h5>
                                <p class="text-muted small">Selesaikan pembayaran Anda untuk mendapatkan tiket</p>
                            </div>
                            
                            <button id="pay-button" class="btn btn-primary btn-lg">
                                <i class="fas fa-credit-card"></i> Bayar Sekarang
                            </button>
                            
                            <p class="mt-3 text-muted small">
                                Pembayaran Anda akan diproses secara aman oleh <strong>Midtrans</strong>
                            </p>
                            
                            @if($order->midtrans_order_id)
                            <div class="mt-3">
                                <button id="check-status" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync-alt"></i> Cek Status Pembayaran
                                </button>
                                <p class="text-muted small mt-1">
                                    Jika Anda sudah membayar tetapi status belum berubah, klik tombol di atas
                                </p>
                            </div>
                            
                            <script>
                                document.getElementById('check-status').addEventListener('click', function() {
                                    // Show loading indicator
                                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memeriksa...';
                                    this.disabled = true;
                                    
                                    // Check payment status via AJAX
                                    fetch('{{ route("payment.status", $order->id) }}')
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status === 'success') {
                                                // If payment successful, refresh the page
                                                if (data.order_status === 'paid') {
                                                    alert('Pembayaran telah berhasil! Halaman akan dimuat ulang.');
                                                    window.location.reload();
                                                } else {
                                                    alert(data.message);
                                                    // Reset button
                                                    this.innerHTML = '<i class="fas fa-sync-alt"></i> Cek Status Pembayaran';
                                                    this.disabled = false;
                                                }
                                            } else {
                                                alert('Gagal memeriksa status: ' + data.message);
                                                // Reset button
                                                this.innerHTML = '<i class="fas fa-sync-alt"></i> Cek Status Pembayaran';
                                                this.disabled = false;
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('Terjadi kesalahan saat memeriksa status pembayaran');
                                            // Reset button
                                            this.innerHTML = '<i class="fas fa-sync-alt"></i> Cek Status Pembayaran';
                                            this.disabled = false;
                                        });
                                });
                            </script>
                            @endif
                        </div>
                        
                        <!-- Midtrans Snap Integration -->
                        <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
                        <script type="text/javascript">
                            document.getElementById('pay-button').onclick = function() {
                                // Show loading
                                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
                                this.disabled = true;
                                
                                // Call Midtrans API
                                fetch('{{ route("payment.create", $order->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        // If redirect URL is available, redirect to it
                                        if (data.redirect_url) {
                                            window.location.href = data.redirect_url;
                                        } else {
                                            // Otherwise open Snap popup
                                            snap.pay(data.snap_token, {
                                                onSuccess: function(result) {
                                                    window.location.href = '{{ route("payment.finish") }}?' + new URLSearchParams(result).toString();
                                                },
                                                onPending: function(result) {
                                                    window.location.href = '{{ route("payment.finish") }}?' + new URLSearchParams(result).toString();
                                                },
                                                onError: function(result) {
                                                    window.location.href = '{{ route("payment.error") }}?' + new URLSearchParams(result).toString();
                                                },
                                                onClose: function() {
                                                    // Reset button
                                                    document.getElementById('pay-button').innerHTML = '<i class="fas fa-credit-card"></i> Bayar Sekarang';
                                                    document.getElementById('pay-button').disabled = false;
                                                    
                                                    alert('Anda menutup popup pembayaran sebelum menyelesaikan transaksi!');
                                                }
                                            });
                                        }
                                    } else {
                                        // Handle error
                                        alert('Terjadi kesalahan: ' + data.message);
                                        
                                        // Reset button
                                        document.getElementById('pay-button').innerHTML = '<i class="fas fa-credit-card"></i> Bayar Sekarang';
                                        document.getElementById('pay-button').disabled = false;
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Terjadi kesalahan saat memproses pembayaran');
                                    
                                    // Reset button
                                    document.getElementById('pay-button').innerHTML = '<i class="fas fa-credit-card"></i> Bayar Sekarang';
                                    document.getElementById('pay-button').disabled = false;
                                });
                            };
                        </script>

                        <!-- Link to Payment Detail Page -->
                        <div class="text-center mt-4">
                            <a href="{{ route('payment.detail', $order->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-info-circle"></i> Lihat Detail Status Pembayaran
                            </a>
                        </div>
                    @elseif ($order->status == 'paid')
                        <div class="text-center">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <h4>Pembayaran Berhasil!</h4>
                                <p>Tiket telah dikirim ke email Anda dan dapat diakses di halaman Tiket Saya.</p>
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('my.tickets') }}" class="btn btn-primary">
                                    <i class="fas fa-ticket-alt"></i> Lihat Tiket Saya
                                </a>
                                <a href="{{ route('payment.detail', $order->id) }}" class="btn btn-outline-primary ms-2">
                                    <i class="fas fa-info-circle"></i> Lihat Detail Status Pembayaran
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                                <h4>Status: {{ ucfirst($order->status) }}</h4>
                                <p>Silakan hubungi admin untuk informasi lebih lanjut.</p>
                            </div>
                            
                            <a href="{{ route('payment.detail', $order->id) }}" class="btn btn-outline-primary mt-3">
                                <i class="fas fa-info-circle"></i> Lihat Detail Status Pembayaran
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
