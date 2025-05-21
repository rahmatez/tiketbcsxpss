@extends('layouts.admin')

@section('title', 'Kelola Pesanan - BCSXPSS')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Manajemen Pesanan</h1>
            <p class="text-muted">
                Kelola semua pesanan tiket dari pelanggan BCSXPSS.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filter Pesanan
            </h5>
        </div>
        <div class="card-body">            <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status Pesanan</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="redeemed" {{ request('status') == 'redeemed' ? 'selected' : '' }}>Redeemed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="game_id" class="form-label">Pertandingan</label>
                    <select name="game_id" id="game_id" class="form-select">
                        <option value="">-- Semua Pertandingan --</option>
                        @foreach($games ?? [] as $gameOption)
                            <option value="{{ $gameOption->id }}" {{ request('game_id') == $gameOption->id ? 'selected' : '' }}>
                                {{ $gameOption->home_team }} vs {{ $gameOption->away_team }} ({{ \Carbon\Carbon::parse($gameOption->match_time)->format('d-m-Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Tanggal Dari</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Tanggal Sampai</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
      <div class="card shadow mb-4">                <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-shopping-cart me-2"></i>Daftar Pesanan
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">                <table class="table table-striped table-hover mb-0" id="ordersTable">                    <thead class="table-dark">
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Pengguna</th>
                            <th>Pertandingan</th>
                            <th>Tiket</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            @if($order->midtrans_order_id)
                                <span class="small text-muted">#{{ $order->id }}</span><br>
                                {{ $order->midtrans_order_id }}
                            @else
                                #{{ $order->id }}
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.show', $order->user_id) }}">
                                {{ $order->user->name }}
                            </a>
                        </td>
                        <td>{{ $order->game->home_team }} vs {{ $order->game->away_team }}</td>
                        <td>{{ $order->ticket->category }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>Rp{{ number_format($order->ticket->price * $order->quantity, 0, ',', '.') }}</td>                        <td>
                            @if($order->status == 'pending')
                                <span class="badge bg-warning text-dark status-badge" data-status="pending">Pending</span>
                            @elseif($order->status == 'paid')
                                <span class="badge bg-success status-badge" data-status="paid">Paid</span>
                            @elseif($order->status == 'redeemed')
                                <span class="badge bg-primary status-badge" data-status="redeemed">Redeemed</span>
                            @elseif($order->status == 'cancelled')
                                <span class="badge bg-danger status-badge" data-status="cancelled">Cancelled</span>
                            @else
                                <span class="badge bg-secondary status-badge" data-status="{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>                        <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($order->status == 'paid')
                                <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="redeemed">
                                    <button type="submit" class="btn btn-sm btn-success" title="Tandai Tiket Digunakan" onclick="return confirm('Apakah Anda yakin ingin menandai tiket ini sebagai digunakan?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada pesanan yang ditemukan</td>
                    </tr>
                @endforelse                    </tbody>
                </table>
            </div>
          </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() ?? 0 }} entries
            </div>
            <div>
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'top'
            });
        });
        
        // Fungsi untuk filter cepat berdasarkan status
        document.querySelectorAll('.status-badge').forEach(function(badge) {
            badge.addEventListener('mouseover', function() {
                this.style.cursor = 'pointer';
            });
            
            badge.addEventListener('click', function() {
                const status = this.dataset.status;
                document.getElementById('status').value = status;
                document.querySelector('form').submit();
            });
        });
    });
</script>
@endsection