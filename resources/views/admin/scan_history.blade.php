@extends('layouts.admin')

@section('title', 'Riwayat Pemindaian Tiket - Admin')

@section('page-title', 'Riwayat Pemindaian Tiket')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Riwayat Pemindaian</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">Riwayat Pemindaian Tiket</h1>
            <p class="text-muted">Lihat riwayat pemindaian QR Code tiket.</p>
        </div>
        <a href="{{ route('admin.scan') }}" class="btn btn-primary">
            <i class="fas fa-qrcode me-2"></i> Pindai QR Baru
        </a>
    </div>
      <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filter Riwayat
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.scan.history') }}" method="GET" class="row g-3 align-items-end">                <div class="col-md-4">
                    <label for="status" class="form-label">Hasil</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">-- Semua Hasil --</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Berhasil</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="date" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
      <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Riwayat Scan
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Pengguna</th>
                            <th>Pertandingan</th>
                            <th>Admin</th>
                            <th>Hasil</th>
                            <th>Catatan</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scans as $scan)
                            <tr>
                                <td>{{ $scan->id }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $scan->order->user_id) }}">
                                        {{ $scan->order->user->name }}
                                    </a>
                                </td>
                                <td>{{ $scan->order->game->home_team }} vs {{ $scan->order->game->away_team }}</td>
                                <td>{{ $scan->admin->name }}</td>                                <td>
                                    @if($scan->status == 'success')
                                        <span class="badge bg-success">Berhasil</span>
                                    @else
                                        <span class="badge bg-danger">Gagal</span>
                                    @endif
                                </td>
                                <td>{{ $scan->notes }}</td>
                                <td>{{ $scan->created_at->format('d-m-Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Tidak ada riwayat pemindaian</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $scans->links() }}
    </div>
</div>
@endsection
