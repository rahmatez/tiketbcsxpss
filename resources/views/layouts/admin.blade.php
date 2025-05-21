<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BCSXPSS - Admin Dashboard">
    <meta name="theme-color" content="#1E5128">
    <title>@yield('title', 'Admin Dashboard - BCSXPSS')</title>
    
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
    
    <!-- Custom Admin CSS -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/table-styles.css') }}" rel="stylesheet">
      @yield('styles')
    @stack('css')
</head>
<body>    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">        <div class="container-fluid">            
            <!-- Logo on the left -->
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-futbol me-2"></i>
                <span>BCSXPSS Admin</span>
            </a>
            
            <!-- Mobile sidebar toggle -->
            <button id="sidebar-toggle" class="navbar-toggler" type="button">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Navbar items on the right -->
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-icon" href="#" id="notificationDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="badge rounded-pill bg-danger">3</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                            <li><h6 class="dropdown-header">Notifikasi</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item notification-item" href="#">
                                    <div class="notification-icon bg-primary">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                    <div class="notification-details">
                                        <p class="notification-title">Tiket Terjual</p>
                                        <p class="notification-text">10 tiket baru terjual untuk pertandingan hari ini</p>
                                        <span class="notification-time">30 menit yang lalu</span>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item notification-item" href="#">
                                    <div class="notification-icon bg-success">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="notification-details">
                                        <p class="notification-title">Pengguna Baru</p>
                                        <p class="notification-text">5 pengguna baru mendaftar</p>
                                        <span class="notification-time">1 jam yang lalu</span>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item notification-item" href="#">
                                    <div class="notification-icon bg-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="notification-details">
                                        <p class="notification-title">Peringatan Sistem</p>
                                        <p class="notification-text">Server mengalami beban tinggi</p>
                                        <span class="notification-time">2 jam yang lalu</span>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-center view-all-link" href="#">
                                    Lihat Semua Notifikasi
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Profile and Logout -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle profile-link" href="#" id="profileDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="profile-image">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <span class="profile-name">Admin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                    <i class="fas fa-user me-2"></i>Profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.settings') }}">
                                    <i class="fas fa-cog me-2"></i>Pengaturan
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="admin-wrapper">        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-futbol"></i>
                    <span>BCSXPSS</span>
                </div>
            </div>
            
            <!-- Sidebar collapse button -->
            <button id="sidebar-collapse-toggle" class="sidebar-collapse-btn d-none d-md-flex">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="sidebar-menu">
                <div class="menu-header">Main Menu</div>
                <ul class="menu-items">
                    <li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="menu-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="menu-item {{ request()->is('admin/games*') ? 'active' : '' }}">
                        <a href="{{ route('admin.games.index') }}" class="menu-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Kelola Pertandingan</span>
                        </a>
                    </li>
                      <li class="menu-item {{ request()->is('admin/tickets*') ? 'active' : '' }}">
                        <a href="{{ route('admin.tickets.index') }}" class="menu-link">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Kelola Tiket</span>
                        </a>
                    </li>
                    
                    <li class="menu-item {{ request()->is('admin/orders*') ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.index') }}" class="menu-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Kelola Pesanan</span>
                        </a>
                    </li>
                    
                    <li class="menu-item {{ request()->is('admin/scan*') ? 'active' : '' }}">
                        <a href="{{ route('admin.scan') }}" class="menu-link">
                            <i class="fas fa-qrcode"></i>
                            <span>Pindai Tiket</span>
                        </a>
                    </li>
                      <li class="menu-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="menu-link">
                            <i class="fas fa-users"></i>
                            <span>Manajemen Pengguna</span>
                        </a>
                    </li>
                    
                    <li class="menu-item {{ request()->is('admin/contact*') ? 'active' : '' }}">
                        <a href="{{ route('admin.contact.index') }}" class="menu-link">
                            <i class="fas fa-envelope"></i>
                            <span>Pesan Kontak</span>
                            @php
                                $pendingCount = \App\Models\ContactMessage::where('status', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="badge badge-danger ml-2">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
                
                <div class="menu-header">Reporting</div>
                <ul class="menu-items">
                    <li class="menu-item {{ request()->is('admin/reports/sales*') ? 'active' : '' }}">
                        <a href="{{ route('admin.reports.sales') }}" class="menu-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Laporan Penjualan</span>
                        </a>
                    </li>
                    
                    <li class="menu-item {{ request()->is('admin/reports/attendance*') ? 'active' : '' }}">
                        <a href="{{ route('admin.reports.attendance') }}" class="menu-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Laporan Kehadiran</span>
                        </a>
                    </li>
                </ul>
                
                <div class="menu-header">Settings</div>
                <ul class="menu-items">
                    <li class="menu-item {{ request()->is('admin/settings*') ? 'active' : '' }}">
                        <a href="{{ route('admin.settings') }}" class="menu-link">
                            <i class="fas fa-cogs"></i>
                            <span>Pengaturan Sistem</span>
                        </a>
                    </li>
                    
                    <li class="menu-item {{ request()->is('admin/pdf-templates*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pdf-templates.index') }}" class="menu-link">
                            <i class="fas fa-file-pdf"></i>
                            <span>Template PDF Tiket</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">            <!-- Content Heading -->
            <div class="content-header">
                <div class="page-title">@yield('page-title', 'Dashboard')</div>
            </div>
            
            <!-- Main Content Area -->
            <div class="content-body">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <footer class="footer">
                <div class="footer-content">
                    <div class="footer-left">
                        <p>&copy; {{ date('Y') }} <strong>BCSXPSS</strong>. All Rights Reserved</p>
                    </div>
                    <div class="footer-right">
                        <p>Dibuat dengan <i class="fas fa-heart text-danger"></i> oleh Tim BCSXPSS</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">
                        <i class="fas fa-sign-out-alt me-2"></i>Konfirmasi Logout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin keluar dari sistem admin?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-inline">
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
        <div id="adminToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    
    <!-- Custom Admin JavaScript -->
    <script src="{{ asset('js/admin.js') }}"></script>
    
    <!-- Additional Scripts -->
    @yield('scripts')
    
    <!-- Flash Message Script -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastEl = document.getElementById('adminToast');
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
            const toastEl = document.getElementById('adminToast');
            const toast = new bootstrap.Toast(toastEl);
            const toastBody = toastEl.querySelector('.toast-body');
            const toastHeader = toastEl.querySelector('.toast-header i');
            
            toastHeader.className = 'fas fa-exclamation-circle text-danger me-2';
            toastBody.innerHTML = "{{ session('error') }}";
            toast.show();
        });
    </script>
    @endif
</body>
</html>
</body>
</html>
