@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white p-3">
                    <h3 class="mb-0">User Profile</h3>
                </div>
                
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto mb-3">
                            <span class="avatar-initials">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <h4 class="fw-bold">{{ $user->name }}</h4>
                        <p class="text-muted">Member since {{ $user->created_at->format('F Y') }}</p>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="profile-info-card">
                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <span>Email:</span>
                                    </div>
                                    <div class="info-value">{{ $user->email }}</div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <span>Address:</span>
                                    </div>
                                    <div class="info-value">{{ $user->full_address ?? 'Not provided' }}</div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <span>Phone Number:</span>
                                    </div>
                                    <div class="info-value">{{ $user->phone_number ?? 'Not provided' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (Auth::check() && Auth::id() == $user->id)
                        <div class="d-flex justify-content-center mt-4 gap-3">
                            <a href="{{ route('profile.edit', $user->id) }}" class="btn btn-primary px-4">
                                <i class="fas fa-user-edit me-2"></i> Edit Profile
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger px-4">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Recent Activity Section -->
            <div class="card shadow border-0 mt-4">
                <div class="card-header bg-light p-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Activities</h5>
                    <a href="#" class="text-decoration-none">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @if(isset($recentOrders) && count($recentOrders) > 0)
                            @foreach($recentOrders as $order)
                                <div class="list-group-item py-3 px-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Purchased Ticket</h6>
                                            <p class="text-muted small mb-0">{{ $order->game->home_team }} vs {{ $order->game->away_team }}</p>
                                        </div>
                                        <span class="badge bg-success">{{ $order->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="list-group-item py-4 px-4 text-center">
                                <p class="text-muted mb-0">No recent activities found</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(13, 106, 55, 0.2);
    }
    
    .avatar-initials {
        font-size: 40px;
        font-weight: bold;
        color: white;
        text-transform: uppercase;
    }
    
    .profile-info-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
    }
    
    .info-row {
        display: flex;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .info-row:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .info-label {
        flex: 0 0 130px;
        font-weight: 600;
        color: #495057;
        display: flex;
        align-items: center;
    }
    
    .info-value {
        flex: 1;
        color: #6c757d;
    }
    
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card-header {
        background-color: var(--primary-color) !important;
        border-bottom: none;
    }
    
    .bg-primary {
        background-color: var(--primary-color) !important;
    }
    
    .text-primary {
        color: var(--primary-color) !important;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
        background-color: #095830;
        border-color: #095830;
    }
    
    .btn-outline-danger:hover {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
    }
</style>
@endsection
