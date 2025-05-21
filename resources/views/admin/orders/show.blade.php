@extends('layouts.admin')

@section('title', 'Detail Pesanan {{ $order->midtrans_order_id ? $order->midtrans_order_id : "#".$order->id }} - BCSXPSS')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-2">Detail Pesanan 
                @if($order->midtrans_order_id)
                    {{ $order->midtrans_order_id }}
                    <small class="text-muted">(ID Internal: #{{ $order->id }})</small>
                @else
                    #{{ $order->id }}
                @endif
            </h1>
            <p class="text-muted">
                Lihat detail pesanan dan informasi pembeli.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Pesanan
            </a>
        </div>
    </div>
      @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif
      <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi Pesanan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Informasi Pertandingan</h6>
                            <p class="mb-1"><strong>Tim:</strong> {{ $order->game->home_team }} vs {{ $order->game->away_team }}</p>
                            <p class="mb-1"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($order->game->match_time)->format('d-m-Y H:i') }} WIB</p>
                            <p class="mb-0"><strong>Stadion:</strong> {{ $order->game->stadium_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Informasi Tiket</h6>
                            <p class="mb-1"><strong>Kategori:</strong> {{ $order->ticket->category }}</p>
                            <p class="mb-1"><strong>Harga:</strong> Rp{{ number_format($order->ticket->price, 0, ',', '.') }}</p>
                            <p class="mb-1"><strong>Jumlah:</strong> {{ $order->quantity }}</p>
                            <p class="mb-0"><strong>Total Harga:</strong> Rp{{ number_format($order->ticket->price * $order->quantity, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Detail Pembeli</h6>
                            <p class="mb-1">
                                <strong>Nama:</strong> 
                                <a href="{{ route('admin.users.show', $order->user_id) }}">{{ $order->user->name }}</a>
                            </p>
                            <p class="mb-1"><strong>Email:</strong> {{ $order->user->email }}</p>
                            <p class="mb-0"><strong>No. Telp:</strong> {{ $order->user->phone ?? 'Tidak tersedia' }}</p>
                        </div>                        <div class="col-md-6">
                            <h6 class="fw-bold">Detail Pembayaran</h6>
                            @if($order->midtrans_order_id)
                            <p class="mb-1"><strong>ID Midtrans:</strong> {{ $order->midtrans_order_id }}</p>
                            @endif
                            <p class="mb-1"><strong>Status:</strong> 
                                @if($order->status == 'paid')
                                    <span class="badge bg-warning text-dark">Paid</span>
                                @elseif($order->status == 'redeemed')
                                    <span class="badge bg-success">Redeemed</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                @endif
                            </p>
                            <p class="mb-1"><strong>Tanggal Pesanan:</strong> {{ $order->created_at->format('d-m-Y H:i:s') }}</p>
                            <p class="mb-0"><strong>Tanggal Pembayaran:</strong> {{ $order->updated_at->format('d-m-Y H:i:s') }}</p>
                        </div>
                    </div>
                    
                    @if($order->status == 'paid')
                    <div class="text-center">
                        <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="redeemed">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin mengubah status menjadi REDEEMED?')">
                                <i class="fas fa-check-circle"></i> Tandai Tiket Sudah Digunakan
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">QR Code Tiket</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($order->qr_code)
                            <img src="{{ asset('storage/qrcodes/' . $order->qr_code) }}" alt="QR Code Tiket" class="img-fluid" style="max-width: 200px;">
                        @else
                            <div class="alert alert-warning">
                                QR Code tidak tersedia
                            </div>
                        @endif
                    </div>
                    <p class="text-muted">Kode Tiket: {{ $order->id . '-' . substr(md5($order->user->email . $order->id), 0, 8) }}</p>
                </div>
            </div>
        </div>
          <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Riwayat Pemindaian
                    </h5>
                </div>
                <div class="card-body">
                    @if($scans->count() > 0)
                        <div class="timeline">
                            @foreach($scans as $scan)
                                <div class="timeline-item">
                                    <div class="timeline-marker {{ $scan->scan_result == 'success' ? 'bg-success' : 'bg-danger' }}"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">
                                            @if($scan->scan_result == 'success')
                                                <i class="fas fa-check-circle text-success"></i> Berhasil
                                            @else
                                                <i class="fas fa-times-circle text-danger"></i> Gagal
                                            @endif
                                        </h6>
                                        <p class="text-muted mb-1">{{ $scan->created_at->format('d-m-Y H:i:s') }}</p>
                                        <p class="mb-0">
                                            <small>
                                                <strong>Admin:</strong> {{ $scan->admin->username }}<br>
                                                @if($scan->notes)
                                                    <strong>Catatan:</strong> {{ $scan->notes }}
                                                @endif
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                            <p>Tiket belum pernah dipindai</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 20px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 9px;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    
    .timeline-marker {
        position: absolute;
        left: -20px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }
    
    .timeline-content {
        padding-left: 15px;
    }
</style>
@endsection