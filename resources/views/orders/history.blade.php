@extends('layouts.app')

@section('title', 'Riwayat Pembelian')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Riwayat Pembelian Tiket</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('purchase.history', ['filter' => 'all']) }}">Semua</a></li>
                            <li><a class="dropdown-item" href="{{ route('purchase.history', ['filter' => 'upcoming']) }}">Pertandingan Mendatang</a></li>
                            <li><a class="dropdown-item" href="{{ route('purchase.history', ['filter' => 'past']) }}">Pertandingan Selesai</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('purchase.history', ['filter' => 'paid']) }}">Pembayaran Selesai</a></li>
                            <li><a class="dropdown-item" href="{{ route('purchase.history', ['filter' => 'pending']) }}">Menunggu Pembayaran</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($orders) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Tanggal Pembelian</th>
                                        <th>Pertandingan</th>
                                        <th>Tanggal Pertandingan</th>
                                        <th>Jenis Tiket</th>
                                        <th>Jumlah</th>
                                        <th>Total Harga</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                            <td>{{ $order->game->home_team }} vs {{ $order->game->away_team }}</td>
                                            <td>{{ \Carbon\Carbon::parse($order->game->match_time)->format('d M Y, H:i') }}</td>
                                            <td>{{ $order->ticket->category }}</td>
                                            <td>{{ $order->quantity }}</td>
                                            <td>Rp{{ number_format($order->ticket->price * $order->quantity, 0, ',', '.') }}</td>
                                            <td>
                                                @if($order->status == 'paid')
                                                    <span class="badge bg-success">Pembayaran Selesai</span>
                                                @elseif($order->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                                                @elseif($order->status == 'cancelled')
                                                    <span class="badge bg-danger">Dibatalkan</span>
                                                @elseif($order->status == 'used')
                                                    <span class="badge bg-secondary">Tiket Terpakai</span>
                                                @endif
                                            </td>                                            <td>
                                                <div class="d-flex gap-1">
                                                    <!-- Tombol lihat detail pembayaran -->
                                                    <a href="{{ route('payment.detail', $order->id) }}" class="btn btn-sm btn-info" title="Detail Pembayaran">
                                                        <i class="fas fa-info-circle"></i>
                                                    </a>
                                                    
                                                    @if($order->status == 'paid' && \Carbon\Carbon::parse($order->game->match_time)->isFuture())
                                                        <a href="{{ route('ticket.detail', $order->id) }}" class="btn btn-sm btn-primary" title="Lihat Tiket">
                                                            <i class="fas fa-ticket-alt"></i>
                                                        </a>
                                                    @endif
                                                    
                                                    @if($order->status == 'pending')
                                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-success" title="Bayar Sekarang">
                                                            <i class="fas fa-credit-card"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/empty-cart.svg') }}" alt="Tidak ada pembelian" class="img-fluid mb-3" style="max-height: 150px;">
                            <h5>Belum ada riwayat pembelian</h5>
                            <p class="text-muted">Anda belum melakukan pembelian tiket apapun</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-ticket-alt me-2"></i>Beli Tiket Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Statistik Pembelian -->
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50">Total Pembelian</h6>
                                    <h3 class="mb-0">{{ $totalOrders }}</h3>
                                </div>
                                <i class="fas fa-shopping-cart fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50">Total Tiket</h6>
                                    <h3 class="mb-0">{{ $totalTickets }}</h3>
                                </div>
                                <i class="fas fa-ticket-alt fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50">Pertandingan Mendatang</h6>
                                    <h3 class="mb-0">{{ $upcomingMatches }}</h3>
                                </div>
                                <i class="far fa-calendar-alt fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Custom script untuk filter dropdown
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const filter = urlParams.get('filter');
        
        // Set active class pada dropdown item yang dipilih
        if (filter) {
            const dropdownItems = document.querySelectorAll('.dropdown-item');
            dropdownItems.forEach(item => {
                const href = new URL(item.href);
                const itemFilter = href.searchParams.get('filter');
                
                if (itemFilter === filter) {
                    item.classList.add('active');
                }
            });
        }
    });
</script>
@endsection
