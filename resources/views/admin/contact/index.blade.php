@extends('layouts.admin')

@section('title', 'Manajemen Pesan - Admin BCSXPSS')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Manajemen Pesan Kontak</h1>
    <p class="mb-4">Kelola dan tanggapi pesan yang dikirim oleh pengunjung/pelanggan melalui form kontak.</p>

    <!-- Filter and Search Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter & Pencarian Pesan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.contact.index') }}" method="GET" class="mb-0">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                            <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Sudah Dibalas</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="search">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari nama, email, subjek, atau ID pesanan..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search fa-sm"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pesan</h6>
            <div>
                <span class="badge badge-primary">Total: {{ $messages->total() }}</span>
                <span class="badge badge-warning">Pending: {{ $messages->where('status', 'pending')->count() }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Subjek</th>
                            <th>ID Pesanan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                            <tr class="{{ $message->status === 'pending' ? 'table-warning' : '' }}">
                                <td>{{ $message->id }}</td>
                                <td>{{ $message->name }}</td>
                                <td>{{ $message->email }}</td>
                                <td>{{ $message->subject_type }}</td>
                                <td>{{ $message->order_id ?? '-' }}</td>
                                <td>
                                    @if($message->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($message->status === 'read')
                                        <span class="badge badge-info">Sudah Dibaca</span>
                                    @elseif($message->status === 'replied')
                                        <span class="badge badge-success">Sudah Dibalas</span>
                                    @endif
                                </td>
                                <td>{{ $message->created_at->format('d-m-Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.contact.show', $message->id) }}" class="btn btn-sm btn-info" title="Lihat detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.contact.destroy', $message->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah anda yakin ingin menghapus pesan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada pesan ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-end">
                {{ $messages->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
