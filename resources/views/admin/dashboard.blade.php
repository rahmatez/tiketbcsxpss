@extends('layouts.admin')

@section('title', 'Dashboard Admin - BCSXPSS')

@section('page-title', 'Dashboard')

@section('breadcrumb', '')

@section('content')
<div class="container-fluid animate-fade-in">
    <!-- Welcome Message -->
    <div class="dashboard-welcome mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="welcome-title">Selamat Datang, {{ Auth::guard('admin')->user()->name }}!</h1>
                <p class="welcome-subtitle">Berikut adalah statistik dan ringkasan untuk sistem tiket BCSXPSS.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-light">
                    <i class="fas fa-calendar me-2"></i>{{ now()->format('d M Y') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row">
        <!-- Total Tickets Sold -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="stat-card">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">Total Tiket Terjual</h4>
                    <div class="stat-card-icon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
                <div class="stat-card-value">{{ $soldTickets ?? 0 }}</div>
                <div class="stat-card-footer">
                    <span class="stat-card-trend {{ $ticketGrowthPercentage >= 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $ticketGrowthPercentage >= 0 ? 'up' : 'down' }} me-1"></i>{{ number_format(abs($ticketGrowthPercentage), 1) }}%
                    </span>
                    <span class="ms-1">dari bulan lalu</span>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="stat-card blue">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">Total Pendapatan</h4>
                    <div class="stat-card-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="stat-card-value">Rp {{ number_format($ticketSalesAmount ?? 0, 0, ',', '.') }}</div>
                <div class="stat-card-footer">
                    <span class="stat-card-trend {{ $revenueGrowthPercentage >= 0 ? 'up' : 'down' }}">
                        <i class="fas fa-arrow-{{ $revenueGrowthPercentage >= 0 ? 'up' : 'down' }} me-1"></i>{{ number_format(abs($revenueGrowthPercentage), 1) }}%
                    </span>
                    <span class="ms-1">dari bulan lalu</span>
                </div>
            </div>
        </div>

        <!-- Tiket Digunakan -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="stat-card purple">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">Tiket Digunakan</h4>
                    <div class="stat-card-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                </div>
                <div class="stat-card-value">{{ $redeemedTickets ?? 0 }}</div>
                <div class="stat-card-footer">
                    <span class="stat-card-trend">
                        @if(isset($soldTickets) && $soldTickets > 0)
                            @php
                                $progressPercentage = ($redeemedTickets / $soldTickets) * 100;
                            @endphp
                            {{number_format($progressPercentage, 1)}}% dari total tiket
                        @else
                            0% dari total tiket
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Upcoming Games -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="stat-card orange">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">Pertandingan</h4>
                    <div class="stat-card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="stat-card-value">{{ $totalGames ?? 0 }}</div>
                <div class="stat-card-footer">
                    <span class="ms-1">{{ $upcomingGames ?? 0 }} pertandingan akan datang</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sales Chart -->
        <div class="col-lg-8 mb-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">Tren Penjualan Tiket</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="salesChartDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            7 Hari Terakhir
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="salesChartDropdown">
                            <li><a class="dropdown-item" href="#">7 Hari Terakhir</a></li>
                            <li><a class="dropdown-item" href="#">30 Hari Terakhir</a></li>
                            <li><a class="dropdown-item" href="#">3 Bulan Terakhir</a></li>
                        </ul>
                    </div>
                </div>
                <div class="admin-card-body">
                    <div style="height: 300px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pertandingan Paling Populer -->
        <div class="col-lg-4 mb-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">Pertandingan Paling Populer</h5>
                    <a href="{{ route('admin.games.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list"></i>
                    </a>
                </div>
                <div class="admin-card-body">
                    @if(isset($popularGame) && $popularGame)
                        <div class="text-center mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle p-4 mb-3">
                                <i class="fas fa-trophy text-warning fa-3x"></i>
                            </div>
                            <h4>{{ $popularGame->home_team }} vs {{ $popularGame->away_team }}</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="small text-muted mb-1">Tanggal</div>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($popularGame->match_time)->format('d-m-Y') }}</div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="small text-muted mb-1">Waktu</div>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($popularGame->match_time)->format('H:i') }}</div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="small text-muted mb-1">Stadion</div>
                                <div class="fw-bold">{{ $popularGame->stadium_name }}</div>
                            </div>
                            <div class="col-12 text-center mt-3">
                                <div class="small text-muted mb-1">Turnamen</div>
                                <div class="fw-bold">{{ $popularGame->tournament_name }}</div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada data penjualan tiket</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Pemesanan Tiket Terbaru
                    </h5>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Pelanggan</th>
                                    <th>Pertandingan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($recentOrders) && count($recentOrders) > 0)
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>{{ $order->game->home_team }} vs {{ $order->game->away_team }}</td>
                                        <td>
                                            @if($order->status == 'pending')
                                                <span class="badge bg-warning text-dark status-badge" data-status="pending">Pending</span>
                                            @elseif($order->status == 'paid')
                                                <span class="badge bg-success status-badge" data-status="paid">Paid</span>
                                            @elseif($order->status == 'redeemed')
                                                <span class="badge bg-primary status-badge" data-status="redeemed">Redeemed</span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge bg-danger status-badge" data-status="cancelled">Cancelled</span>
                                            @else
                                                <span class="badge bg-secondary status-badge">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center py-4">Belum ada pesanan.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Manajemen -->
        <div class="col-lg-4 mb-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">Menu Manajemen</h5>
                </div>
                <div class="admin-card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.games.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <span class="bg-primary text-white p-2 rounded me-3">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <div>
                                <h6 class="mb-0">Kelola Pertandingan</h6>
                                <small class="text-muted">Tambah, edit, dan hapus pertandingan</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.tickets.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <span class="bg-success text-white p-2 rounded me-3">
                                <i class="fas fa-ticket-alt"></i>
                            </span>
                            <div>
                                <h6 class="mb-0">Kelola Tiket</h6>
                                <small class="text-muted">Update ketersediaan dan harga tiket</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.scan') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <span class="bg-info text-white p-2 rounded me-3">
                                <i class="fas fa-qrcode"></i>
                            </span>
                            <div>
                                <h6 class="mb-0">Pindai Tiket</h6>
                                <small class="text-muted">Validasi tiket via QR Code</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <span class="bg-danger text-white p-2 rounded me-3">
                                <i class="fas fa-users"></i>
                            </span>
                            <div>
                                <h6 class="mb-0">Manajemen Pengguna</h6>
                                <small class="text-muted">Lihat dan kelola pengguna</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
