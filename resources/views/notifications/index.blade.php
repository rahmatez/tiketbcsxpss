@extends('layouts.app')

@section('title', 'Notifikasi - BCSXPSS')

@section('styles')
<style>
    .notification-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    
    .notification-card:hover {
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .notification-header {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .notification-body {
        padding: 20px;
    }
    
    .notification-footer {
        padding: 10px 20px;
        background-color: rgba(0, 0, 0, 0.02);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .notification-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .notification-actions {
        display: flex;
        gap: 10px;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }
    
    .notification-icon.success {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    
    .notification-icon.info {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
    }
    
    .notification-icon.warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }
    
    .notification-unread {
        background-color: rgba(13, 110, 253, 0.05);
        border-left: 3px solid #0d6efd;
    }
    
    .page-header {
        background: linear-gradient(to right, var(--primary-color), #0d6efd);
        color: white;
        padding: 40px 0;
        margin-top: -1.5rem;
        margin-bottom: 30px;
        text-align: center;
        border-radius: 0 0 20px 20px;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="container">
        <h1 class="mb-2"><i class="fas fa-bell me-2"></i>Notifikasi</h1>
        <p class="mb-0">Informasi penting tentang tiket dan pertandingan Anda</p>
    </div>
</div>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            @php
                $unreadCount = $notifications->where('is_read', false)->count();
            @endphp
            
            @if($unreadCount > 0)
                <h5>Anda memiliki {{ $unreadCount }} notifikasi belum dibaca</h5>
            @else
                <h5>Semua notifikasi telah dibaca</h5>
            @endif
        </div>
        
        @if($unreadCount > 0)
            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-check-double me-2"></i>Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>
    
    @if($notifications->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-bell-slash fa-5x text-muted"></i>
            </div>
            <h4>Tidak Ada Notifikasi</h4>
            <p class="text-muted">Anda akan menerima notifikasi ketika ada informasi penting tentang tiket dan pertandingan.</p>
        </div>
    @else
        @foreach($notifications as $notification)
            <div class="card notification-card {{ !$notification->is_read ? 'notification-unread' : '' }}">
                <div class="notification-header">
                    <div class="d-flex align-items-center">
                        @php
                            $iconClass = 'info';
                            $icon = 'bell';
                            
                            if($notification->type == 'match_reminder') {
                                $iconClass = 'success';
                                $icon = 'futbol';
                            } elseif($notification->type == 'payment_reminder') {
                                $iconClass = 'warning';
                                $icon = 'credit-card';
                            }
                        @endphp
                        
                        <div class="notification-icon {{ $iconClass }}">
                            <i class="fas fa-{{ $icon }}"></i>
                        </div>
                        <h5 class="mb-0">{{ $notification->title }}</h5>
                    </div>
                    <span class="badge {{ !$notification->is_read ? 'bg-primary' : 'bg-secondary' }}">
                        {{ !$notification->is_read ? 'Belum Dibaca' : 'Dibaca' }}
                    </span>
                </div>
                
                <div class="notification-body">
                    <p class="mb-0">{{ $notification->message }}</p>
                    
                    @if($notification->type == 'match_reminder' && $notification->reference_id)
                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Detail Pertandingan</h6>
                                    <p class="mb-0 small">
                                        @if($notification->reference && $notification->reference->match_time)
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ \Carbon\Carbon::parse($notification->reference->match_time)->format('d F Y, H:i') }} WIB
                                            <br>
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $notification->reference->stadium_name }}
                                        @elseif(isset($notification->data['match_time']))
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ \Carbon\Carbon::parse($notification->data['match_time'])->format('d F Y, H:i') }} WIB
                                            <br>
                                            @if(isset($notification->data['stadium']))
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $notification->data['stadium'] }}
                                            @endif
                                        @endif
                                    </p>
                                </div>
                                
                                @if($notification->reference_id && $notification->reference_type == 'App\\Models\\Game')
                                    <a href="{{ route('games.show', $notification->reference_id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-info-circle me-1"></i>Lihat Detail
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if($notification->type == 'payment_reminder' && $notification->reference_id)
                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Detail Pembayaran</h6>
                                    <p class="mb-0 small">
                                        <i class="fas fa-receipt me-1"></i> Order ID: {{ $notification->reference_id }}
                                    </p>
                                </div>
                                
                                <a href="{{ route('payment.detail', $notification->reference_id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-credit-card me-1"></i>Bayar Sekarang
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="notification-footer">
                    <div class="notification-time">
                        <i class="far fa-clock me-1"></i>
                        {{ $notification->created_at->diffForHumans() }}
                    </div>
                    
                    <div class="notification-actions">
                        @if(!$notification->is_read)
                            <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-check me-1"></i>Tandai Dibaca
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Anda yakin ingin menghapus notifikasi ini?')">
                                <i class="fas fa-trash-alt me-1"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
        
        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
