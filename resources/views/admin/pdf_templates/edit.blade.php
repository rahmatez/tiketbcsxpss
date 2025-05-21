@extends('layouts.admin')

@section('title', 'Edit Template PDF - BCSXPSS')

@section('page-title', 'Edit Template PDF')

@section('styles')
<style>
    .code-editor {
        min-height: 400px;
        font-family: monospace;
    }
    .placeholder-list {
        max-height: 250px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Edit Template PDF</h1>
            <p class="text-muted">
                Edit template PDF untuk tiket pertandingan.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.pdf-templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Template
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Form Template</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('admin.pdf-templates.update', $template->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Template</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $template->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="html_content" class="form-label">Konten HTML</label>
                    <textarea class="form-control code-editor @error('html_content') is-invalid @enderror" id="html_content" name="html_content" rows="20" required>{{ old('html_content', $template->html_content) }}</textarea>
                    @error('html_content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $template->is_default) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_default">
                            Jadikan template default
                        </label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Aktif
                        </label>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Placeholder yang Tersedia</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Placeholder berikut dapat digunakan dalam template dan akan diganti dengan data sebenarnya saat PDF dibuat:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group placeholder-list">
                                    <li class="list-group-item"><code>{ORDER_ID}</code> - Nomor ID pesanan</li>
                                    <li class="list-group-item"><code>{USER_NAME}</code> - Nama pengguna</li>
                                    <li class="list-group-item"><code>{MATCH_TEAMS}</code> - Tim yang bertanding</li>
                                    <li class="list-group-item"><code>{MATCH_DATE}</code> - Tanggal pertandingan</li>
                                    <li class="list-group-item"><code>{MATCH_TIME}</code> - Waktu pertandingan</li>
                                    <li class="list-group-item"><code>{STADIUM}</code> - Nama stadion</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group placeholder-list">
                                    <li class="list-group-item"><code>{SEAT_CATEGORY}</code> - Kategori kursi</li>
                                    <li class="list-group-item"><code>{QUANTITY}</code> - Jumlah tiket</li>
                                    <li class="list-group-item"><code>{PURCHASE_DATE}</code> - Tanggal pembelian</li>
                                    <li class="list-group-item"><code>{QR_CODE}</code> - Kode QR SVG</li>
                                    <li class="list-group-item"><code>{TICKET_STATUS}</code> - Status tiket</li>
                                    <li class="list-group-item"><code>{BASE_URL}</code> - URL dasar website</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Perbarui Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const htmlEditor = CodeMirror.fromTextArea(document.getElementById('html_content'), {
            mode: 'htmlmixed',
            theme: 'monokai',
            lineNumbers: true,
            indentUnit: 4,
            indentWithTabs: false,
            lineWrapping: true,
            autoCloseTags: true,
            autoCloseBrackets: true,
            matchBrackets: true,
            matchTags: {bothTags: true}
        });
    });
</script>
@endsection
