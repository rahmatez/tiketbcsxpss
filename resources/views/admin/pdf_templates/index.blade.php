@extends('layouts.admin')

@section('title', 'PDF Templates - BCSXPSS')

@section('page-title', 'Kelola Template PDF')

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Template PDF Tiket</h1>
            <p class="text-muted">
                Buat dan kelola template PDF untuk tiket pertandingan.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.pdf-templates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Template Baru
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Template Tersedia</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            @if($templates->isEmpty())
                <div class="alert alert-info">
                    Belum ada template PDF yang dibuat. Silakan buat template baru.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Default</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th>Diperbarui</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                            <tr>
                                <td>{{ $template->name }}</td>
                                <td>
                                    @if($template->is_default)
                                    <span class="badge bg-success">Default</span>
                                    @else
                                    <form action="{{ route('admin.pdf-templates.set-default', $template->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Jadikan Default</button>
                                    </form>
                                    @endif
                                </td>
                                <td>
                                    @if($template->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                    @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $template->created_at->format('d M Y') }}</td>
                                <td>{{ $template->updated_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.pdf-templates.edit', $template->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.pdf-templates.preview', $template->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(!$template->is_default)
                                    <form action="{{ route('admin.pdf-templates.destroy', $template->id) }}" method="POST" class="d-inline confirmation-form" data-confirm="Apakah Anda yakin ingin menghapus template ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
