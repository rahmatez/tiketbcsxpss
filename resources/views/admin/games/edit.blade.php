@extends('layouts.admin')

@section('title', 'Edit Pertandingan - BCSXPSS')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Edit Pertandingan</h1>
            <p class="text-muted">
                Perbarui informasi pertandingan dan status untuk {{ $game->home_team }} vs {{ $game->away_team }}.
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
                <i class="fas fa-futbol me-2"></i>Edit Informasi Pertandingan
            </h5>
        </div>
        <div class="card-body">            <form action="{{ route('admin.games.update', $game->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="home_team" class="form-label">Tim Tuan Rumah:</label>
                        <input type="text" class="form-control" id="home_team" name="home_team" value="{{ $game->home_team }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="away_team" class="form-label">Tim Tamu:</label>
                        <input type="text" class="form-control" id="away_team" name="away_team" value="{{ $game->away_team }}" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="home_team_logo" class="form-label">Logo Tim Tuan Rumah:</label>
                        <input type="file" class="form-control" id="home_team_logo" name="home_team_logo" accept="image/*">
                        <small class="text-muted">Format: JPEG, PNG, JPG, GIF. Ukuran maksimal: 2MB</small>
                        
                        @if($game->home_team_logo)
                            <div class="mt-2">
                                <p><strong>Logo saat ini:</strong></p>
                                <img src="{{ Storage::url($game->home_team_logo) }}" alt="{{ $game->home_team }}" class="img-thumbnail" style="max-width: 100px;">
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label for="away_team_logo" class="form-label">Logo Tim Tamu:</label>
                        <input type="file" class="form-control" id="away_team_logo" name="away_team_logo" accept="image/*">
                        <small class="text-muted">Format: JPEG, PNG, JPG, GIF. Ukuran maksimal: 2MB</small>
                        
                        @if($game->away_team_logo)
                            <div class="mt-2">
                                <p><strong>Logo saat ini:</strong></p>
                                <img src="{{ Storage::url($game->away_team_logo) }}" alt="{{ $game->away_team }}" class="img-thumbnail" style="max-width: 100px;">
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="match_time" class="form-label">Waktu Pertandingan:</label>
                        <input type="datetime-local" class="form-control" id="match_time" name="match_time" 
                               value="{{ \Carbon\Carbon::parse($game->match_time)->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="is_home_game" class="form-label">Sebagai Tuan Rumah?</label>
                        <select id="is_home_game" name="is_home_game" class="form-select" required 
                                {{ $game->is_home_game ? 'disabled' : '' }}>
                            <option value="1" {{ $game->is_home_game ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ !$game->is_home_game ? 'selected' : '' }}>Tidak</option>
                        </select>
                        @if($game->is_home_game)
                            <div class="form-text text-muted">
                                Home/Away status tidak dapat diubah setelah tiket dibuat.
                                <input type="hidden" name="is_home_game" value="1">
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="tournament_name" class="form-label">Nama Kompetisi:</label>
                        <input type="text" class="form-control" id="tournament_name" name="tournament_name" 
                               value="{{ $game->tournament_name }}" required>
                    </div>                    <div class="col-md-6">
                        <label for="purchase_deadline" class="form-label">Batas Waktu Pembelian Tiket:</label>
                        <input type="datetime-local" class="form-control" id="purchase_deadline" name="purchase_deadline"
                               value="{{ $game->purchase_deadline ? date('Y-m-d\TH:i', strtotime($game->purchase_deadline)) : '' }}" required>
                        <small class="text-muted">Waktu terakhir tiket dapat dibeli sebelum pertandingan</small>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="stadium_name" class="form-label">Nama Stadion:</label>
                        <input type="text" class="form-control" id="stadium_name" name="stadium_name" 
                               value="{{ $game->stadium_name }}" required>
                    </div>                    <div class="col-md-6">
                        <label for="status" class="form-label">Status:</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="upcoming" {{ $game->status == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="ongoing" {{ $game->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ $game->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $game->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="image" class="form-label">Gambar Pertandingan:</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Format: JPEG, PNG, JPG, GIF. Ukuran maksimal: 2MB</small>
                        
                        @if($game->image_path)
                            <div class="mt-3">
                                <p><strong>Gambar saat ini:</strong></p>
                                <img src="{{ Storage::url($game->image_path) }}" alt="{{ $game->home_team }} vs {{ $game->away_team }}" class="img-thumbnail" style="max-width: 300px;">
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($game->is_home_game)
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Informasi Tiket</h5>
                    <a href="{{ route('admin.tickets.index', ['game_id' => $game->id]) }}" class="btn btn-sm btn-admin-primary">
                        <i class="fas fa-ticket-alt me-2"></i>Kelola Tiket
                    </a>
                </div>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Kategori Tiket</th>
                                <th>Harga (Rp)</th>
                                <th>Kuantitas</th>
                                <th>Terjual</th>
                                <th>Sisa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets ?? [] as $ticket)
                                @php
                                    $soldCount = App\Models\Order::where('ticket_id', $ticket->id)->sum('quantity');
                                    $remainingCount = $ticket->quantity - $soldCount;
                                    $soldPercent = $ticket->quantity > 0 ? ($soldCount / $ticket->quantity) * 100 : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $ticket->category }}</strong>
                                    </td>
                                    <td>Rp {{ number_format($ticket->price, 0, ',', '.') }}</td>
                                    <td>{{ $ticket->quantity }} tiket</td>
                                    <td>{{ $soldCount }} tiket</td>
                                    <td>
                                        @if($remainingCount > 0)
                                            <span class="badge bg-success">{{ $remainingCount }} tiket</span>
                                        @else
                                            <span class="badge bg-danger">{{ $remainingCount }} tiket</span>
                                        @endif
                                    </td>
                                    <td>                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar progress-bar-striped {{ $soldPercent > 80 ? 'bg-danger' : ($soldPercent > 50 ? 'bg-warning' : 'bg-success') }}" 
                                                 role="progressbar" 
                                                 data-width="{{ $soldPercent }}"
                                                 aria-valuenow="{{ $soldPercent }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100"
                                                 style="width: 0%">
                                            </div>
                                        </div>
                                        <small class="d-block mt-1 text-muted">{{ number_format($soldPercent, 1) }}% terjual</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">
                                        <i class="fas fa-ticket-alt text-muted mb-2 fa-2x"></i>
                                        <p class="text-muted mb-0">Belum ada tiket untuk pertandingan ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif
                  <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('admin.games.index') }}" class="btn btn-light">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="fas fa-save me-2"></i>Update Pertandingan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Any form changes detection
        const form = document.querySelector('form');
        const originalFormData = new FormData(form);
        
        form.addEventListener('change', function() {
            const currentFormData = new FormData(form);
            let isChanged = false;
            
            for (const [key, value] of currentFormData.entries()) {
                if (originalFormData.get(key) !== value) {
                    isChanged = true;
                    break;
                }
            }
            
            const submitButton = document.querySelector('button[type="submit"]');
            if (isChanged) {
                submitButton.classList.add('btn-pulse');
            } else {
                submitButton.classList.remove('btn-pulse');
            }
        });
        
        // Set progress bar widths
        document.querySelectorAll('.progress-bar').forEach(function(progressBar) {
            const width = progressBar.getAttribute('data-width');
            if (width) {
                progressBar.style.width = width + '%';
            }
        });

        // Update purchase deadline when match time changes
        document.getElementById('match_time').addEventListener('change', function() {
            const matchTime = new Date(this.value);
            if (!isNaN(matchTime.getTime())) {
                const purchaseDeadline = new Date(matchTime);
                purchaseDeadline.setHours(purchaseDeadline.getHours() - 24); // Default 24 jam sebelum
                document.getElementById('purchase_deadline').value = formatDateForInput(purchaseDeadline);
            }
        });
        
        function formatDateForInput(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }
    });
</script>
@endsection