@extends('layouts.admin')

@section('title', 'Laporan Kehadiran - BCSXPSS')

@section('page-title', 'Laporan Kehadiran')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan Kehadiran</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid animate-fade-in p-0">
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filter Laporan
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.attendance') }}" method="GET" class="row g-3 align-items-end">
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
                    <a href="{{ route('admin.reports.export', ['type' => 'attendance']) }}" class="btn btn-success">
                        <i class="fas fa-file-export me-2"></i>Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- @if(!isset($hideCategories) || !$hideCategories) -->
    <!-- <div class="row mx-0"> -->
        <!-- Attendance by Category -->
        <!-- <div class="col-lg-12 mb-4 px-0">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>Kehadiran per Kategori
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div> -->
    <!-- @endif -->
    
    <!-- Attendance By Game Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-futbol me-2"></i>Kehadiran per Pertandingan
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Pertandingan</th>
                            <th>Tanggal</th>
                            <th>Tiket Terjual</th>
                            <th>Kehadiran</th>
                            <th>Persentase Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($games as $game)
                        <tr>
                            <td>
                                <strong>{{ $game->home_team }} vs {{ $game->away_team }}</strong>
                            </td>
                            <td>{{ Carbon\Carbon::parse($game->match_time)->format('d M Y, H:i') }}</td>                            <td><span class="badge bg-info">{{ number_format($game->tickets_sold) }}</span></td>
                            <td><span class="badge bg-success">{{ number_format($game->ticket_scans_count) }}</span></td>
                            <td>
                                @if($game->tickets_sold > 0)
                                    @php
                                        $attendancePercentage = ($game->ticket_scans_count / $game->tickets_sold) * 100;
                                    @endphp                                    <div class="d-flex align-items-center">
                                        <span class="me-2">{{ number_format($attendancePercentage, 1) }}%</span>
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            @php
                                                $progressClass = $attendancePercentage >= 75 ? 'bg-success' : ($attendancePercentage >= 50 ? 'bg-info' : 'bg-warning');
                                            @endphp
                                            <div class="progress-bar {{ $progressClass }} progress-bar-dynamic" id="progress-{{ $loop->index }}" data-width="{{ $attendancePercentage }}"></div>
                                        </div>
                                    </div>
                                @else
                                    0%
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Tidak ada data pertandingan dalam periode yang dipilih.
                                    <a href="{{ route('admin.reports.attendance', ['sample' => 1]) }}" class="alert-link">
                                        <i class="fas fa-flask ms-2"></i> Lihat contoh data
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Attendance Stats -->
    <div class="row mx-0">
        <div class="col-md-6 mb-4 px-0 pe-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Ringkasan Kehadiran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="attendance-summary">
                        <div class="row">
                            <div class="col-md-6 text-center mb-3">
                                <h6 class="text-muted">Total Kehadiran</h6>
                                <h2 class="display-4 fw-bold text-success">{{ number_format($games->sum('ticket_scans_count')) }}</h2>
                            </div>
                            <div class="col-md-6 text-center mb-3">
                                <h6 class="text-muted">Persentase Kehadiran Rata-rata</h6>
                                <h2 class="display-4 fw-bold text-primary">
                                    @php
                                        $totalSold = $games->sum('tickets_sold') ?? 0;
                                        $totalAttended = $games->sum('ticket_scans_count') ?? 0;
                                        $percentage = $totalSold > 0 ? ($totalAttended / $totalSold) * 100 : 0;
                                    @endphp
                                    {{ number_format($percentage, 1) }}%
                                </h2>
                            </div>
                        </div>
                        
                        <div class="attendance-progress mt-4">
                            <h6 class="text-muted mb-2">Performa Kehadiran</h6>                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated progress-bar-dynamic" 
                                     role="progressbar" 
                                     id="main-progress-bar"
                                     data-width="{{ $percentage }}"
                                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($percentage, 1) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4 px-0 ps-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Wawasan & Rekomendasi
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="insights-list">
                        <li>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Pertandingan dengan kehadiran terbaik: 
                                @php
                                    $bestAttendance = $games->sortByDesc(function($game) {
                                        return $game->tickets_sold > 0 ? ($game->ticket_scans_count / $game->tickets_sold) : 0;
                                    })->first();
                                @endphp
                                @if($bestAttendance)
                                    <strong>{{ $bestAttendance->home_team }} vs {{ $bestAttendance->away_team }}</strong>
                                    ({{ number_format(($bestAttendance->ticket_scans_count / max(1, $bestAttendance->tickets_sold)) * 100, 1) }}%)
                                @else
                                    -
                                @endif
                            </span>
                        </li>
                        <li>
                            <i class="fas fa-ticket-alt text-info me-2"></i>
                            <span>Kategori tiket terpopuler (berdasarkan kehadiran): 
                                @if($attendanceByCategory->count() > 0)
                                    <strong>Kategori {{ $attendanceByCategory->first()->category }}</strong>
                                @else
                                    -
                                @endif
                            </span>
                        </li>
                        <li>
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            <span>
                                Rekomendasi: Pertimbangkan untuk meningkatkan promosi pada kategori tiket dengan kehadiran rendah.
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Chart data stored in hidden elements to avoid issues with JSON parsing -->
<script type="application/json" id="chart-category-data">
    @json($attendanceByCategory ?? [])
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Set progress bar widths
        document.querySelectorAll('.progress-bar-dynamic').forEach(function(bar) {
            const width = bar.getAttribute('data-width');
            if (width) {
                bar.style.width = width + '%';
            }
        });
        
        // Attendance by category chart
        let categoryData = [];
        let categories = [];
        let categoryCounts = [];
        
        try {
            categoryData = JSON.parse(document.getElementById('chart-category-data').textContent);
            categories = categoryData.length > 0 ? categoryData.map(item => 'Kategori ' + item.category) : [];
            categoryCounts = categoryData.length > 0 ? categoryData.map(item => item.scan_count) : [];
        } catch (e) {
            console.error("Error parsing category data", e);
            categories = ['VIP', 'Tribune', 'Regular'];
            categoryCounts = [30, 20, 50];
        }
        
        if (categories.length === 0) {
            categories = ['VIP', 'Tribune', 'Regular'];
            categoryCounts = [30, 20, 50];
        }
        
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            new Chart(categoryCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: categories,
                    datasets: [{
                        data: categoryCounts,
                        backgroundColor: [
                            '#4E9F3D',
                            '#1E5128',
                            '#D8E9A8',
                            '#3498DB',
                            '#9B59B6'
                        ],
                        borderColor: '#FFFFFF',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    } catch (e) {
        console.error("Error initializing charts", e);
    }
});
</script>
@endsection

@section('styles')
<style>
    .container-fluid.p-0 {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
    
    @media (min-width: 768px) {
        .px-0.pe-md-2 {
            padding-right: 0.5rem !important;
        }
        
        .px-0.ps-md-2 {
            padding-left: 0.5rem !important;
        }
    }
    
    .insights-list {
        list-style: none;
        padding: 0;
    }
    
    .insights-list li {
        padding: 10px 0;
        border-bottom: 1px solid var(--admin-border-light);
    }
    
    .insights-list li:last-child {
        border-bottom: none;
    }
    
    .attendance-summary h2 {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--admin-primary);
        margin-bottom: 0;
    }
      /* Progress bar widths - diatur melalui JavaScript untuk menghindari error CSS */
    .progress-bar-dynamic {
        height: 100%;
        transition: width 0.5s ease;
        /* Width akan diatur via JavaScript */
    }
</style>
@endsection
