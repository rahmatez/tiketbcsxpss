@extends('layouts.admin')

@section('title', 'Tambah Pertandingan Baru - BCSXPSS')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Tambah Pertandingan Baru</h1>
            <p class="text-muted">
                Tambah jadwal pertandingan baru dan buat tiket untuk pertandingan home.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.games.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
            </a>
        </div>
    </div>
    
    @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
      <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-futbol me-2"></i>Informasi Pertandingan
            </h5>
        </div>
        <div class="card-body">            <form action="{{ route('admin.games.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="home_team" class="form-label">Tim Tuan Rumah:</label>
                        <input type="text" class="form-control" id="home_team" name="home_team" value="PSS Sleman" required>
                    </div>
                    <div class="col-md-6">
                        <label for="away_team" class="form-label">Tim Tamu:</label>
                        <input type="text" class="form-control" id="away_team" name="away_team" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="match_time" class="form-label">Waktu Pertandingan:</label>
                        <input type="datetime-local" class="form-control" id="match_time" name="match_time" required>
                    </div>
                    <div class="col-md-6">
                        <label for="is_home_game" class="form-label">Sebagai Tuan Rumah?</label>
                        <select id="is_home_game" name="is_home_game" class="form-select" required
                                onchange="toggleTicketFields()">
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>
                </div>                  <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="tournament_name" class="form-label">Nama Kompetisi:</label>
                        <input type="text" class="form-control" id="tournament_name" name="tournament_name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="purchase_deadline" class="form-label">Batas Waktu Pembelian Tiket:</label>
                        <input type="datetime-local" class="form-control" id="purchase_deadline" name="purchase_deadline"
                               required>
                        <small class="text-muted">Waktu terakhir tiket dapat dibeli sebelum pertandingan</small>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="stadium_name" class="form-label">Nama Stadion:</label>
                        <input type="text" class="form-control" id="stadium_name" name="stadium_name" required>
                    </div>                    <div class="col-md-6">
                        <label for="status" class="form-label">Status:</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div></div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="image" class="form-label">Gambar Pertandingan:</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Format: JPEG, PNG, JPG, GIF. Ukuran maksimal: 2MB</small>
                    </div>
                </div>
                
                <div id="ticket-fields">
                    <hr>
                    <h5 class="mb-4">
                        <i class="fas fa-ticket-alt me-2"></i>Informasi Tiket
                    </h5>                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> 
                        <strong>Catatan:</strong> Tiket hanya tersedia untuk pertandingan home. Jika form dibiarkan kosong, kategori tiket tetap akan terdaftar dengan nilai jumlah dan harga 0 yang dapat dikelola nanti di menu Kelola Tiket.
                    </div>
                      <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Tribun Selatan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="tribun_selatan_ticket_quantity" class="form-label">Jumlah Tiket:</label>
                                        <input type="number" class="form-control" id="tribun_selatan_ticket_quantity" name="tribun_selatan_ticket_quantity" min="0">
                                    </div>
                                    <div class="mb-3">
                                        <label for="tribun_selatan_ticket_price" class="form-label">Harga Tiket (Rp):</label>
                                        <input type="number" class="form-control" id="tribun_selatan_ticket_price" name="tribun_selatan_ticket_price" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Tribun Utara</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="tribun_utara_ticket_quantity" class="form-label">Jumlah Tiket:</label>
                                        <input type="number" class="form-control" id="tribun_utara_ticket_quantity" name="tribun_utara_ticket_quantity" min="0">
                                    </div>
                                    <div class="mb-3">
                                        <label for="tribun_utara_ticket_price" class="form-label">Harga Tiket (Rp):</label>
                                        <input type="number" class="form-control" id="tribun_utara_ticket_price" name="tribun_utara_ticket_price" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Tribun Timur</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="tribun_timur_ticket_quantity" class="form-label">Jumlah Tiket:</label>
                                        <input type="number" class="form-control" id="tribun_timur_ticket_quantity" name="tribun_timur_ticket_quantity" min="0">
                                    </div>
                                    <div class="mb-3">
                                        <label for="tribun_timur_ticket_price" class="form-label">Harga Tiket (Rp):</label>
                                        <input type="number" class="form-control" id="tribun_timur_ticket_price" name="tribun_timur_ticket_price" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">Tribun Barat</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="tribun_barat_ticket_quantity" class="form-label">Jumlah Tiket:</label>
                                        <input type="number" class="form-control" id="tribun_barat_ticket_quantity" name="tribun_barat_ticket_quantity" min="0">
                                    </div>
                                    <div class="mb-3">
                                        <label for="tribun_barat_ticket_price" class="form-label">Harga Tiket (Rp):</label>
                                        <input type="number" class="form-control" id="tribun_barat_ticket_price" name="tribun_barat_ticket_price" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                  <div class="mt-4 d-flex justify-content-end">
                    <button type="reset" class="btn btn-light me-2">
                        <i class="fas fa-redo me-2"></i>Reset Form
                    </button>
                    <button type="submit" id="submit_button" class="btn btn-admin-primary">
                        <i class="fas fa-save me-2"></i>Tambah Pertandingan & Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function toggleTicketFields() {
        const isHomeGame = document.getElementById('is_home_game').value;
        const ticketFields = document.getElementById('ticket-fields');
        const submitButton = document.getElementById('submit_button');
        
        if (isHomeGame === '1') {
            ticketFields.style.display = 'block';
            submitButton.innerText = 'Tambah Pertandingan & Tiket';
        } else {
            ticketFields.style.display = 'none';
            submitButton.innerText = 'Tambah Pertandingan';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleTicketFields();
        
        // Fill default dates
        const now = new Date();
        const matchDate = new Date();
        matchDate.setDate(now.getDate() + 7); // Default match one week from now
        
        const purchaseDate = new Date(matchDate);
        purchaseDate.setDate(purchaseDate.getDate() - 1); // Default purchase until one day before
          document.getElementById('match_time').value = formatDateForInput(matchDate);
        document.getElementById('purchase_deadline').value = formatDateForInput(purchaseDate);
    });
    
    function formatDateForInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    // Update purchase deadline when match time changes
    document.getElementById('match_time').addEventListener('change', function() {
        const matchTime = new Date(this.value);
        if (!isNaN(matchTime.getTime())) {
            const purchaseDeadline = new Date(matchTime);
            purchaseDeadline.setHours(purchaseDeadline.getHours() - 24); // Default 24 jam sebelum
            document.getElementById('purchase_deadline').value = formatDateForInput(purchaseDeadline);
        }
    });
</script>
@endsection
