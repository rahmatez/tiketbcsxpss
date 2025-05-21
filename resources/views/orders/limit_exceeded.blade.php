@extends('layouts.app')

@section('title', 'Limit Exceeded')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Limit Exceeded</h1>
        <div class="alert alert-warning" role="alert">
            <p>Anda telah mencapai batas kuota pembelian pada pertandingan ini.</p>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali ke Halaman Sebelumnya</a>
        <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Home</a>
    </div>
    <br>
@endsection
