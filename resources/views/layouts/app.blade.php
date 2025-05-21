<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BCSXPSSS - Pemesanan Tiket Pertandingan Sepak Bola">
    <meta name="theme-color" content="#1a73e8">
    <title>@yield('title', 'BCSXPSS')</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    
    <style>
        /* Game Image Styles */
        .game-image-container {
            overflow: hidden;
            max-height: 180px;
        }
        
        .game-image {
            width: 100%;
            height: auto;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .card:hover .game-image {
            transform: scale(1.05);
        }
    </style>
    
    <!-- Admin specific styles -->
    @if(Auth::guard('admin')->check())
    <style>
        .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
        .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
        .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
        .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
        .border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
        .text-xs { font-size: .7rem; }
    </style>
    @endif
    
    @yield('styles')
</head>
<body>
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="loading-spinner"></div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <!-- Logo (sudah di posisi kiri) -->
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <i class="fas fa-futbol me-2"></i> 
            <span>BCSXPSS</span>
        </a>
        
        <!-- Form pencarian (pindah ke sebelah kanan logo) -->
        <div class="navbar-search d-none d-lg-block ms-3">
            <form action="{{ route('tickets.search') }}" method="get" class="d-flex">
                <input class="form-control form-control-sm me-2 search-input" type="search" name="query" placeholder="Cari tiket atau pertandingan..." aria-label="Search">
                <button class="btn btn-sm btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @if(Auth::guard('admin')->check())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.games.create') }}">
                            <i class="fas fa-plus me-2"></i>Tambah Pertandingan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.scan') }}">
                            <i class="fas fa-qrcode me-2"></i>Scan Tiket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                @elseif(Auth::check())
                    <!-- Urutan Menu - Pertandingan -> Tiket -> Pengguna -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tickets.search') }}">
                            <i class="fas fa-calendar-alt me-2"></i>Pertandingan
                        </a>
                    </li>
                    <!-- Sembunyikan "Cari Tiket" dari navbar karena sudah pindah ke search form -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="ticketsDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ticket-alt me-2"></i>Tiket
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="ticketsDropdown">
                            <li><a class="dropdown-item" href="{{ route('my.tickets') }}">
                                <i class="fas fa-ticket-alt me-2"></i>Tiket Saya
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('purchase.history') }}">
                                <i class="fas fa-history me-2"></i>Riwayat Pembelian
                            </a></li>
                        </ul>
                    </li>
                <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2"></i>
                            {{ Auth::user()->name }}
                            @php
                                $unreadNotifications = \App\Models\Notification::where('user_id', Auth::id())
                                                        ->where('is_read', false)
                                                        ->count();
                            @endphp
                            @if($unreadNotifications > 0)
                                <span class="position-relative ms-2">
                                    <i class="fas fa-bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $unreadNotifications }}
                                    </span>
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end animate__animated animate__fadeIn animate__faster" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.show', ['id' => Auth::user()->id]) }}">
                                <i class="fas fa-id-card me-2"></i>Edit Profil
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('notifications.index') }}">
                                <i class="fas fa-bell me-2"></i>Notifikasi
                                @if($unreadNotifications > 0)
                                    <span class="badge bg-danger ms-2">{{ $unreadNotifications }}</span>
                                @endif
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- Menu untuk user yang belum login -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">
                            <i class="fas fa-calendar-alt me-2"></i>Pertandingan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-2"></i>Daftar
                        </a>
                    </li>
                @endif
                
                <!-- Form pencarian mobile (hanya muncul di tampilan mobile) -->
                <div class="d-block d-lg-none mt-3">
                    <form action="{{ route('tickets.search') }}" method="get" class="d-flex">
                        <input class="form-control form-control-sm me-2" type="search" name="query" 
                               placeholder="Cari tiket atau pertandingan..." aria-label="Search">
                        <button class="btn btn-sm btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-4">
    @include('components.flash-messages')
    @yield('content')
</div>

<!-- Footer -->
<footer class="text-center text-lg-start text-white">
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4"><i class="fas fa-futbol me-2"></i>BCSXPSS</h5>
                <p>
                    Platform pemesanan tiket untuk pertandingan sepak bola PSS Sleman. 
                    Dapatkan tiket pertandingan dengan mudah, cepat, dan aman.
                </p>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">Tautan</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ url('/') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Jadwal Pertandingan</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('tickets.search') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Cari Tiket</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('faq') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>FAQ & Bantuan</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('contact.index') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Kontak Kami</a>
                    </li>
                    @auth
                    <li class="mb-2">
                        <a href="{{ route('my.tickets') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Tiket Saya</a>
                    </li>
                    @else
                    <li class="mb-2">
                        <a href="{{ route('login') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Login</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('register') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Daftar</a>
                    </li>
                    @endauth
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">Kontak</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Komp. Ruko Delima, No. 1, Jl. Delima, Sanggrahan,
                        Condongcatur, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55283
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        bcsxpss@gmail.com
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        (+62) 800-1234-5678
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © 2025 Brigata Curva Sud - Created with by
        <a class="text-white" href="https://www.github.com/rahmatez">Rahmat Ashari</a>
    </div>
</footer>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="logoutModalLabel">
                    <i class="fas fa-sign-out-alt me-2"></i>Konfirmasi Logout
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin keluar dari akun Anda?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <form id="logout-form"
                      action="{{ Auth::guard('admin')->check() ? route('admin.logout') : route('logout') }}"
                      method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-info-circle me-2"></i>
            <strong class="me-auto">Notifikasi</strong>
            <small>Baru saja</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Notifikasi akan muncul di sini.
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<!-- Custom JavaScript -->
<script src="{{ asset('js/custom.js') }}"></script>

<!-- Additional Scripts -->
@yield('scripts')

<!-- Flash Message Script -->
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);
        const toastBody = toastEl.querySelector('.toast-body');
        const toastHeader = toastEl.querySelector('.toast-header i');
        
        toastHeader.className = 'fas fa-check-circle text-success me-2';
        toastBody.innerHTML = "{{ session('success') }}";
        toast.show();
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl);
        const toastBody = toastEl.querySelector('.toast-body');
        const toastHeader = toastEl.querySelector('.toast-header i');
        
        toastHeader.className = 'fas fa-exclamation-circle text-danger me-2';
        toastBody.innerHTML = "{{ session('error') }}";
        toast.show();
    });
</script>
@endif

@stack('scripts')
</body>
</html>
