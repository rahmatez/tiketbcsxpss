@extends('layouts.admin')

@section('title', 'Detail Pesan - Admin BCSXPSS')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pesan #{{ $message->id }}</h1>
        <div>
            <a href="{{ route('admin.contact.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Message Details -->
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pesan</h6>
                    <div>
                        @if($message->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($message->status === 'read')
                            <span class="badge badge-info">Sudah Dibaca</span>
                        @elseif($message->status === 'replied')
                            <span class="badge badge-success">Sudah Dibalas</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pengirim</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $message->name }}</div>
                                <div><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tanggal</div>
                                <div>{{ $message->created_at->format('d M Y') }}</div>
                                <div>{{ $message->created_at->format('H:i:s') }}</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Subjek</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $message->subject_type }}</div>
                        </div>
                        
                        @if($message->order_id)
                        <div class="mb-3">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ID Pesanan</div>
                            <div>
                                <a href="{{ route('admin.orders.show', $message->order_id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-search me-1"></i> Lihat Order #{{ $message->order_id }}
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pesan</div>
                            <div class="border rounded p-3 bg-light mt-1" style="white-space: pre-wrap;">{{ $message->message }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reply Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Balas Pesan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contact.reply', $message->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="reply_message">Pesan Balasan</label>
                            <textarea class="form-control @error('reply_message') is-invalid @enderror" id="reply_message" name="reply_message" rows="5" required>{{ old('reply_message') }}</textarea>
                            @error('reply_message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Balasan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Admin Notes and Status -->
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Catatan Admin & Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contact.update', $message->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="status">Status Pesan</label>
                            <select class="form-control" id="status" name="status">
                                <option value="pending" {{ $message->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="read" {{ $message->status == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                                <option value="replied" {{ $message->status == 'replied' ? 'selected' : '' }}>Sudah Dibalas</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="admin_notes">Catatan Admin (hanya untuk internal)</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="5">{{ $message->admin_notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div>
                        <h6 class="font-weight-bold">Info Lainnya:</h6>
                        <ul class="list-unstyled">
                            @if($message->admin)
                                <li><strong>Ditangani oleh:</strong> {{ $message->admin->name }}</li>
                            @endif
                            <li><strong>Status terakhir:</strong> 
                                @if($message->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($message->status === 'read')
                                    <span class="badge badge-info">Sudah Dibaca</span>
                                @elseif($message->status === 'replied')
                                    <span class="badge badge-success">Sudah Dibalas</span>
                                @endif
                            </li>
                            <li><strong>Terakhir diperbarui:</strong> {{ $message->updated_at->diffForHumans() }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Delete Button -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Zona Berbahaya</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contact.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Apakah anda yakin ingin menghapus pesan ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-2"></i>Hapus Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
