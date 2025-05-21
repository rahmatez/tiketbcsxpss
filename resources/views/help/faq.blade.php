@extends('layouts.app')

@section('title', 'FAQ & Bantuan')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="mb-4 text-center">Pertanyaan yang Sering Diajukan (FAQ)</h2>
            
            <div class="accordion" id="faqAccordion">
                <!-- Pertanyaan tentang Akun -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Akun & Pendaftaran</h3>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="accountFaq">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accountFaq1">
                                        Bagaimana cara mendaftar akun baru?
                                    </button>
                                </h2>
                                <div id="accountFaq1" class="accordion-collapse collapse" data-bs-parent="#accountFaq">
                                    <div class="accordion-body">
                                        <p>Untuk mendaftar akun baru, klik tombol "Daftar" di pojok kanan atas halaman. 
                                        Isi formulir dengan data diri Anda termasuk nama, email, dan password. 
                                        Setelah mendaftar, Anda akan dapat langsung login menggunakan email dan password yang telah didaftarkan.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accountFaq2">
                                        Saya lupa password akun saya. Apa yang harus saya lakukan?
                                    </button>
                                </h2>
                                <div id="accountFaq2" class="accordion-collapse collapse" data-bs-parent="#accountFaq">
                                    <div class="accordion-body">
                                        <p>Jika Anda lupa password, klik "Lupa Password" pada halaman login. 
                                        Masukkan alamat email yang terdaftar, dan kami akan mengirimkan instruksi untuk mengatur ulang password Anda.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pertanyaan tentang Pembelian Tiket -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Pembelian Tiket</h3>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="ticketFaq">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ticketFaq1">
                                        Berapa banyak tiket yang bisa saya beli dalam satu transaksi?
                                    </button>
                                </h2>
                                <div id="ticketFaq1" class="accordion-collapse collapse" data-bs-parent="#ticketFaq">
                                    <div class="accordion-body">
                                        <p>Untuk memastikan distribusi tiket yang adil, setiap akun hanya dapat membeli maksimal 2 tiket per pertandingan.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ticketFaq2">
                                        Apakah saya bisa membatalkan pembelian tiket?
                                    </button>
                                </h2>
                                <div id="ticketFaq2" class="accordion-collapse collapse" data-bs-parent="#ticketFaq">
                                    <div class="accordion-body">
                                        <p>Pembelian tiket tidak dapat dibatalkan setelah pembayaran berhasil dilakukan. 
                                        Mohon pastikan informasi pembelian Anda sudah benar sebelum melanjutkan ke pembayaran.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ticketFaq3">
                                        Sampai kapan saya bisa membeli tiket pertandingan?
                                    </button>
                                </h2>
                                <div id="ticketFaq3" class="accordion-collapse collapse" data-bs-parent="#ticketFaq">
                                    <div class="accordion-body">
                                        <p>Tiket pertandingan biasanya tersedia sampai 24 jam sebelum jadwal kick-off atau sampai tiket habis terjual. 
                                        Untuk pertandingan tertentu, periode pembelian tiket mungkin berbeda sesuai kebijakan klub.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pertanyaan tentang Pembayaran -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Pembayaran</h3>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="paymentFaq">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#paymentFaq1">
                                        Metode pembayaran apa saja yang tersedia?
                                    </button>
                                </h2>
                                <div id="paymentFaq1" class="accordion-collapse collapse" data-bs-parent="#paymentFaq">
                                    <div class="accordion-body">
                                        <p>Kami menerima berbagai metode pembayaran melalui Midtrans, termasuk:</p>
                                        <ul>
                                            <li>Transfer Bank (BCA, Mandiri, BNI, BRI)</li>
                                            <li>E-wallet (GoPay, OVO, DANA, LinkAja, ShopeePay)</li>
                                            <li>Kartu Kredit (Visa, Mastercard)</li>
                                            <li>Alfamart/Indomaret</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#paymentFaq2">
                                        Berapa lama batas waktu pembayaran?
                                    </button>
                                </h2>
                                <div id="paymentFaq2" class="accordion-collapse collapse" data-bs-parent="#paymentFaq">
                                    <div class="accordion-body">
                                        <p>Batas waktu pembayaran adalah 1 jam setelah pemesanan tiket. 
                                        Jika pembayaran tidak dilakukan dalam waktu tersebut, pemesanan akan dibatalkan secara otomatis.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#paymentFaq3">
                                        Pembayaran saya sudah berhasil tetapi status pesanan belum berubah?
                                    </button>
                                </h2>
                                <div id="paymentFaq3" class="accordion-collapse collapse" data-bs-parent="#paymentFaq">
                                    <div class="accordion-body">
                                        <p>Biasanya diperlukan waktu 5-15 menit untuk memproses pembayaran. 
                                        Jika status belum berubah setelah 30 menit, silakan hubungi customer service kami dengan 
                                        menyertakan bukti pembayaran dan ID pemesanan Anda.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pertanyaan tentang Tiket & QR Code -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Tiket & QR Code</h3>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="qrFaq">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#qrFaq1">
                                        Bagaimana cara mengakses tiket saya?
                                    </button>
                                </h2>
                                <div id="qrFaq1" class="accordion-collapse collapse" data-bs-parent="#qrFaq">
                                    <div class="accordion-body">
                                        <p>Setelah pembayaran berhasil, tiket dengan QR code akan tersedia di halaman "Tiket Saya". 
                                        Anda dapat mengaksesnya melalui menu akun Anda. Tiket juga akan dikirimkan ke email Anda.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#qrFaq2">
                                        Apakah saya perlu mencetak tiket?
                                    </button>
                                </h2>
                                <div id="qrFaq2" class="accordion-collapse collapse" data-bs-parent="#qrFaq">
                                    <div class="accordion-body">
                                        <p>Tidak perlu mencetak tiket. Cukup tunjukkan QR code dari aplikasi atau screenshot tiket 
                                        pada petugas di pintu masuk stadion. Pastikan layar ponsel Anda memiliki kecerahan yang cukup 
                                        untuk pemindaian QR code.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#qrFaq3">
                                        Dapatkah saya mentransfer tiket ke orang lain?
                                    </button>
                                </h2>
                                <div id="qrFaq3" class="accordion-collapse collapse" data-bs-parent="#qrFaq">
                                    <div class="accordion-body">
                                        <p>Untuk alasan keamanan dan mencegah penjualan kembali tiket, transfer tiket ke akun lain 
                                        tidak diperbolehkan. Tiket hanya bisa digunakan oleh pemilik akun yang membelinya.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 mb-4">
                <h3>Masih memiliki pertanyaan?</h3>
                <p>Jika Anda memiliki pertanyaan lain yang tidak tercantum di sini, silakan hubungi tim dukungan pelanggan kami:</p>
                <div class="row mt-4">
                    <div class="col-md-4 text-center mb-3">
                        <div class="d-inline-block p-3 bg-light rounded-circle mb-2">
                            <i class="fas fa-envelope fa-2x text-primary"></i>
                        </div>
                        <h5>Email</h5>
                        <p>bcsxpss@gmail.com</p>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="d-inline-block p-3 bg-light rounded-circle mb-2">
                            <i class="fas fa-phone fa-2x text-primary"></i>
                        </div>
                        <h5>Telepon</h5>
                        <p>+62 812-3456-7890</p>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="d-inline-block p-3 bg-light rounded-circle mb-2">
                            <i class="fab fa-whatsapp fa-2x text-primary"></i>
                        </div>
                        <h5>WhatsApp</h5>
                        <p>+62 812-3456-7890</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
