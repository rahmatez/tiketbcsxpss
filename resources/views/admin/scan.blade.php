@extends('layouts.admin')

@section('title', 'Scan QR Code - BCSXPSS')

@section('page-title', 'Scan QR Code Tiket')

@push('css')
<link rel="stylesheet" href="{{ asset('css/qr-scanner.css') }}">
@endpush

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Scan QR Code Tiket</h1>
            <p class="text-muted">
                Scan QR Code tiket pengunjung untuk memvalidasi kehadiran.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.scan.history') }}" class="btn btn-info">
                <i class="fas fa-history"></i> Riwayat Scan
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card card-modern mb-4">                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-qrcode me-2"></i>Scanner QR Code</h5>
                        <button onclick="window.location.reload()" class="btn btn-sm btn-light">
                            <i class="fas fa-sync-alt me-1"></i> Muat Ulang
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="reader" class="qr-reader-container">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h4 class="mt-3">Memuat Scanner...</h4>
                            <p class="text-muted">Mohon tunggu atau izinkan akses kamera jika diminta</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-modern">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-search me-2"></i>Hasil Pemindaian</h5>
                </div>
                <div class="card-body">
                    <div id="scan-result" class="d-none qr-result">
                        <div id="scan-success" class="alert alert-success d-none qr-success">
                            <h4><i class="fas fa-check-circle"></i> Tiket Valid!</h4>
                            <hr>
                            <div id="ticket-info">
                                <p><strong>ID Pesanan:</strong> <span id="order-id"></span></p>
                                <p><strong>Nama Pengguna:</strong> <span id="user-name"></span></p>
                                <p><strong>Pertandingan:</strong> <span id="game-name"></span></p>
                                <p><strong>Kategori Tiket:</strong> <span id="ticket-category"></span></p>
                                <p><strong>Jumlah:</strong> <span id="ticket-quantity"></span></p>
                            </div>
                        </div>
                        
                        <div id="scan-error" class="alert alert-danger d-none qr-error">
                            <h4><i class="fas fa-times-circle"></i> Tiket Tidak Valid!</h4>
                            <hr>
                            <p id="error-message"></p>
                            <div id="error-details" class="d-none">
                                <p><strong>Detail Pesanan:</strong></p>
                                <p><strong>ID Pesanan:</strong> <span id="error-order-id"></span></p>
                                <p><strong>Nama Pengguna:</strong> <span id="error-user-name"></span></p>
                                <p><strong>Tiket sudah digunakan pada:</strong> <span id="error-redeemed-at"></span></p>
                            </div>
                        </div>
                    </div>
                    
                    <div id="scan-waiting">
                        <div class="text-center py-5">
                            <i class="fas fa-qrcode fa-5x text-muted mb-3"></i>
                            <h4>Silahkan Scan QR Code</h4>
                            <p class="text-muted">Arahkan kode QR pada tiket ke kamera</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gunakan versi spesifik dari library, tidak perlu integrity yang bisa menyebabkan masalah -->
<script src="https://unpkg.com/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
<script>
    // Variabel global untuk scanner
    let html5QrcodeScanner = null;
    
    // Mulai scanner saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        startScanner();
    });
    
    function startScanner() {
        // Pastikan elemen reader ada
        if (!document.getElementById('reader')) {
            console.error('Element with ID "reader" not found!');
            return;
        }
        
        // Hapus scanner lama jika ada
        if (html5QrcodeScanner) {
            try {
                html5QrcodeScanner.clear();
            } catch (error) {
                console.error("Error clearing scanner:", error);
            }
            html5QrcodeScanner = null;
        }
        
        // Reset elemen reader
        document.getElementById('reader').innerHTML = '';
        
        // Konfigurasi dasar scanner
        const config = { 
            fps: 10,
            qrbox: {width: 250, height: 250}
        };
        
        // Buat scanner baru dan render
        try {
            html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            console.log("Scanner started successfully");
        } catch (error) {
            console.error("Failed to start scanner:", error);
            document.getElementById('reader').innerHTML = `
                <div class="alert alert-danger text-center">
                    <h5>Gagal Memuat Scanner</h5>
                    <p>Silakan coba muat ulang halaman</p>
                    <button onclick="startScanner()" class="btn btn-primary mt-2">Coba Lagi</button>
                </div>
            `;
        }
    }
      // Fungsi untuk menginisialisasi ulang scanner
    function reinitializeScanner() {
        window.location.reload();
    }
    
    // Fungsi untuk menampilkan pesan error
    function showErrorMessage(message) {
        document.getElementById('scan-result').classList.remove('d-none');
        document.getElementById('scan-waiting').classList.add('d-none');
        document.getElementById('scan-success').classList.add('d-none');
        document.getElementById('scan-error').classList.remove('d-none');
        document.getElementById('error-message').textContent = message;
        document.getElementById('error-details').classList.add('d-none');
    }
    
    // Fungsi sederhana untuk memvalidasi QR Code
    function validateQrCode(qrCode) {
        const url = "/admin/update-order-status";
        const token = "{{ csrf_token() }}";
        
        console.log('Mengirim data QR ke server:', qrCode);
        
        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                qr_code: qrCode.trim()
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respons server:', data);
            
            // Tampilkan hasil scan
            document.getElementById('scan-waiting').classList.add('d-none');
            
            if (data.success) {
                // Tiket valid
                document.getElementById('scan-success').classList.remove('d-none');
                document.getElementById('scan-error').classList.add('d-none');
                
                // Tampilkan informasi tiket
                document.getElementById('order-id').textContent = data.order?.id || 'N/A';
                document.getElementById('user-name').textContent = data.user?.name || 'N/A';
                document.getElementById('game-name').textContent = 
                    (data.game?.home_team && data.game?.away_team) ? 
                    `${data.game.home_team} vs ${data.game.away_team}` : 'N/A';
                document.getElementById('ticket-category').textContent = data.ticket?.category || 'N/A';
                document.getElementById('ticket-quantity').textContent = data.order?.quantity || 'N/A';
                
                // Putar suara sukses
                try {
                    let successSound = new Audio('/sounds/success.mp3');
                    successSound.play().catch(e => console.log('Tidak dapat memutar suara'));
                } catch(e) {}
            } else {
                // Tiket tidak valid
                document.getElementById('scan-success').classList.add('d-none');
                document.getElementById('scan-error').classList.remove('d-none');
                document.getElementById('error-message').textContent = data.message || 'Tiket tidak valid';
                
                // Tampilkan detail error jika ada
                if (data.order) {
                    document.getElementById('error-details').classList.remove('d-none');
                    document.getElementById('error-order-id').textContent = data.order.id || 'N/A';
                    document.getElementById('error-user-name').textContent = data.user?.name || 'N/A';
                    document.getElementById('error-redeemed-at').textContent = data.redeemed_at || 'N/A';
                } else {
                    document.getElementById('error-details').classList.add('d-none');
                }
                
                // Putar suara error
                try {
                    let errorSound = new Audio('/sounds/error.mp3');
                    errorSound.play().catch(e => console.log('Tidak dapat memutar suara'));
                } catch(e) {}
            }
            
            // Reset scanner setelah beberapa detik
            setTimeout(() => {
                resetScanner();
            }, 5000);
        })
        .catch(error => {
            console.error('Error validasi QR:', error);
            document.getElementById('scan-waiting').classList.add('d-none');
            document.getElementById('scan-success').classList.add('d-none');
            document.getElementById('scan-error').classList.remove('d-none');
            document.getElementById('error-message').textContent = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
            document.getElementById('error-details').classList.add('d-none');
            
            // Reset scanner setelah beberapa detik
            setTimeout(() => {
                resetScanner();
            }, 5000);
        });
    }
    
    function onScanSuccess(decodedText, decodedResult) {
        console.log("QR Code terdeteksi:", decodedText);
        
        // Pastikan QR code tidak kosong
        if (!decodedText || decodedText.trim() === '') {
            showErrorMessage('QR code tidak terdeteksi atau kosong');
            return;
        }
        
        // Tampilkan status menunggu
        document.getElementById('scan-result').classList.remove('d-none');
        document.getElementById('scan-waiting').classList.remove('d-none');
        document.getElementById('scan-success').classList.add('d-none');
        document.getElementById('scan-error').classList.add('d-none');
        
        // Kirim QR code ke server
        validateQrCode(decodedText);
        
        // Tampilkan loading state
        document.getElementById('scan-waiting').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h4 class="mt-3">Memverifikasi tiket...</h4>
                <p class="text-muted">Sedang memproses data QR code...</p>
            </div>
        `;
        
        // Log data yang akan dikirim untuk debugging
        console.log('Mengirim data QR: ', decodedText.trim());
    }
    
    function onScanFailure(error) {
        // Abaikan kesalahan pemindaian standar
        // console.warn("QR scan error:", error);
    }
      // Fungsi untuk me-reset tampilan scanner
    function resetScanner() {
        // Sembunyikan hasil scan
        document.getElementById('scan-result').classList.add('d-none');
        document.getElementById('scan-waiting').classList.remove('d-none');
        document.getElementById('scan-waiting').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-qrcode fa-5x text-muted mb-3"></i>
                <h4>Silahkan Scan QR Code</h4>
                <p class="text-muted">Arahkan kode QR pada tiket ke kamera</p>
            </div>
        `;
    }
    
    // Fungsi untuk mencatat riwayat scan
    function logTicketScan(orderId, result, notes) {
        const url = "{{ route('admin.log_scan') }}";
        const token = "{{ csrf_token() }}";
        
        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token
            },
            body: JSON.stringify({
                order_id: orderId,
                status: result ? "success" : "failed",
                notes: notes || ""
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                console.error('Gagal mencatat scan:', data.message);
                // Tambahkan notifikasi kecil untuk admin
                const toastElement = document.createElement('div');
                toastElement.className = 'alert alert-warning alert-dismissible fade show position-fixed bottom-0 end-0 m-3';
                toastElement.innerHTML = `
                    <strong>Peringatan:</strong> Hasil scan tidak tercatat di riwayat. 
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                document.body.appendChild(toastElement);
                setTimeout(() => {
                    toastElement.remove();
                }, 5000);
            }
        })
        .catch(error => {
            console.error('Error logging scan:', error);
            // Jangan menampilkan pesan error ke pengguna, karena ini hanya untuk logging
        });
    }

    // Add a function to check if a ticket has already been successfully redeemed
    function checkTicketStatus(qrCode) {
        const checkUrl = "/admin/check-ticket-status";
        const token = "{{ csrf_token() }}";
        
        // This is a quiet check that runs in the background
        fetch(checkUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                qr_code: qrCode.trim()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'redeemed') {
                    // If ticket is already redeemed, show ERROR message
                    document.getElementById('scan-result').classList.remove('d-none');
                    document.getElementById('scan-waiting').classList.add('d-none');
                    document.getElementById('scan-success').classList.add('d-none');
                    document.getElementById('scan-error').classList.remove('d-none');
                    
                    document.getElementById('error-message').textContent = 'Tiket sudah digunakan sebelumnya. Akses ditolak.';
                    
                    // Show error details
                    if (data.order) {
                        document.getElementById('error-details').classList.remove('d-none');
                        document.getElementById('error-order-id').textContent = data.order.id || 'N/A';
                        document.getElementById('error-user-name').textContent = data.user?.name || 'N/A';
                        document.getElementById('error-redeemed-at').textContent = data.redeemed_at || 'N/A';
                        
                        // Play error sound
                        try {
                            let errorSound = new Audio('/sounds/error.mp3');
                            errorSound.play().catch(e => console.warn('Could not play error sound', e));
                        } catch(e) {
                            console.warn('Error playing sound:', e);
                        }
                    }
                } else if (data.status === 'paid') {
                    // This is a valid ticket that hasn't been used yet
                    console.log("Ticket is valid but not yet redeemed");
                }
            }
        })
        .catch(error => {
            // Silent fail - this is just a background check
            console.log('Background status check failed, continuing...', error);
        });
    }
</script>

<!-- Tambahkan script untuk debugging QR scanner -->
<script>
    // Menampilkan status kamera dan opsi QR code reader
    window.addEventListener('load', function() {
        // Cek apakah browser mendukung getUserMedia
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            console.log("Browser mendukung getUserMedia API");
            
            // Cek akses kamera
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    console.log("Akses kamera berhasil");
                    // Menutup stream kamera setelah diuji
                    stream.getTracks().forEach(track => track.stop());
                })
                .catch(function(err) {
                    console.error("Error akses kamera:", err);
                    // Tampilkan pesan error di tampilan jika akses kamera gagal
                    let readerElement = document.getElementById('reader');
                    if (readerElement) {
                        readerElement.innerHTML = `
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <h5>Akses Kamera Ditolak</h5>
                                <p>Silakan izinkan akses kamera untuk memindai QR code tiket.</p>
                                <button onclick="requestCameraPermission()" class="btn btn-danger mt-2">
                                    <i class="fas fa-camera me-2"></i> Izinkan Kamera
                                </button>
                            </div>
                        `;
                    }
                });
        } else {
            console.error("Browser tidak mendukung getUserMedia");
            // Tampilkan pesan error jika browser tidak mendukung kamera
            let readerElement = document.getElementById('reader');
            if (readerElement) {
                readerElement.innerHTML = `
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                        <h5>Browser Tidak Didukung</h5>
                        <p>Browser Anda tidak mendukung akses kamera. Silakan gunakan Chrome, Firefox, atau Edge versi terbaru.</p>
                    </div>
                `;
            }
        }
    });
      // Fungsi untuk meminta izin kamera
    function requestCameraPermission() {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                console.log("Akses kamera berhasil diberikan");
                stream.getTracks().forEach(track => track.stop());
                // Reload halaman untuk memuat ulang scanner
                location.reload();
            })
            .catch(function(err) {
                console.error("Akses kamera masih ditolak:", err);
                alert("Anda perlu memberikan izin kamera untuk menggunakan scanner QR code. Silakan periksa pengaturan izin browser Anda.");
            });
    }
</script>
@endsection
