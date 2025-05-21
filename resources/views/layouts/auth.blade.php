<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masuk atau daftar untuk membeli tiket pertandingan BCSXPSS">
    <meta name="theme-color" content="#0d6a37">
    <title>@yield('title', 'Authentication') - BCSXPSS</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
      <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth-style.css') }}" rel="stylesheet">    <link href="{{ asset('css/register-custom.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @yield('styles')
</head>
<body class="auth-page">    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-left">
                <div class="auth-bg">
                    <div class="auth-overlay"></div>
                    <div class="auth-text">
                        <div class="logo-image">
                            <i class="fas fa-futbol"></i>
                        </div>
                        <div class="logo-text">BCSXPSS</div>
                        <h2>Dapatkan Tiket Pertandingan BCSXPSS</h2>
                        <p>Website resmi pembelian tiket pertandingan PSS Sleman. Dukung tim kesayangan Anda secara langsung di stadion.</p>
                    </div>
                </div>
            </div>            <div class="auth-right">
                <a href="{{ url('/') }}" class="back-to-home">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
                <div class="auth-content-wrapper">
                    <div class="auth-content">
                        @if(session('success'))
                            <div class="alert alert-success mb-4">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger mb-4">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            </div>
                        @endif
                        
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    
    @yield('scripts')
</body>
</html>
