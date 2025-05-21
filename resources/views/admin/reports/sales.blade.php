@extends('layouts.admin')

@section('title', 'Laporan Penjualan - BCSXPSS')

@section('page-title', 'Laporan Penjualan')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan Penjualan</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid animate-fade-in">
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filter Laporan
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.sales') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                           value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Terapkan Filter
                    </button>
                    <a href="{{ route('admin.reports.export', ['type' => 'sales']) }}" class="btn btn-success">
                        <i class="fas fa-file-export me-2"></i>Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <!-- Sales Summary -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Ringkasan Penjualan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <h6 class="text-muted">Total Penjualan</h6>
                            <h2 class="mb-0">{{ $salesByDate->sum('orders_count') }}</h2>
                            <p class="text-muted">Transaksi</p>
                        </div>
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <h6 class="text-muted">Total Tiket Terjual</h6>
                            <h2 class="mb-0">{{ $salesByDate->sum('tickets_sold') }}</h2>
                            <p class="text-muted">Tiket</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6 class="text-muted">Total Pendapatan</h6>
                            <h2 class="mb-0">Rp {{ number_format($salesByCategory->sum('revenue'), 0, ',', '.') }}</h2>
                            <p class="text-muted">IDR</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>    </div>
    
    <!-- Sales By Game Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-futbol me-2"></i>Penjualan per Pertandingan
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Pertandingan</th>
                            <th>Tanggal</th>
                            <th>Tiket Terjual</th>
                            <th>Pembeli Unik</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByGame as $game)
                        <tr>
                            <td>{{ $game->home_team }} vs {{ $game->away_team }}</td>
                            <td>{{ Carbon\Carbon::parse($game->match_time)->format('d M Y, H:i') }}</td>
                            <td>{{ $game->tickets_sold }}</td>
                            <td>{{ $game->unique_customers }}</td>
                            <td>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- No charts to display -->
@endsection
