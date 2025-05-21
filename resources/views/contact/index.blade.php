@extends('layouts.app')

@section('title', 'Kontak & Dukungan')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="text-center mb-5">
                <h2>Hubungi Kami</h2>
                <p class="lead">Kami siap membantu Anda dengan pertanyaan dan masalah seputar pembelian tiket</p>
            </div>
            
            <div class="row mb-5">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px">
                                <i class="fas fa-map-marker-alt fa-2x text-white"></i>
                            </div>
                            <h4>Alamat</h4>
                            <p>Komp. Ruko Delima, No. 1, Jl. Delima<br>Sanggrahan, Condongcatur, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55283<br>Indonesia</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px">
                                <i class="fas fa-phone fa-2x text-white"></i>
                            </div>
                            <h4>Telepon</h4>
                            <p>Layanan Pelanggan:<br>+62 877 2012 1976</p>
                            <p>Kantor:<br>+62 877 2012 1976</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px">
                                <i class="fas fa-envelope fa-2x text-white"></i>
                            </div>
                            <h4>Email</h4>
                            <p>Dukungan Pelanggan:<br>bcsxpss@gmail.com</p>
                            <p>Informasi Umum:<br>bcsxpss@gmail.com</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Kirim Pesan</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('contact.send') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subjek</label>
                            <select class="form-select @error('subject') is-invalid @enderror" id="subject" name="subject" required>
                                <option value="" selected disabled>Pilih subjek</option>
                                <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>Pertanyaan Umum</option>
                                <option value="payment" {{ old('subject') == 'payment' ? 'selected' : '' }}>Masalah Pembayaran</option>
                                <option value="ticket" {{ old('subject') == 'ticket' ? 'selected' : '' }}>Masalah Tiket</option>
                                <option value="account" {{ old('subject') == 'account' ? 'selected' : '' }}>Akun</option>
                                <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="order_id" class="form-label">ID Pesanan (opsional)</label>
                            <input type="text" class="form-control @error('order_id') is-invalid @enderror" id="order_id" name="order_id" value="{{ old('order_id') }}">
                            <div class="form-text">Jika pesan Anda terkait dengan pesanan tertentu, mohon sertakan ID pesanan</div>
                            @error('order_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Pesan</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Peta Lokasi -->
    <div class="row justify-content-center mt-5">
        <div class="col-md-10">
            <h3 class="mb-4">Lokasi Kami</h3>
            <div class="ratio ratio-21x9">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7906.739238004214!2d110.3940914413239!3d-7.750563469073017!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a599f868a1e69%3A0xde3c1e0aa389b3f4!2sCURVASUDSHOP!5e0!3m2!1sid!2sid!4v1747795123504!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection
