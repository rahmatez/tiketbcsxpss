@extends('layouts.app')

@section('title', 'Order Confirmation')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Pembelian Berhasil</h1>
        <div class="alert alert-success" role="alert">
            <p>Terimakasih telah membeli!</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-primary">Ke Home</a>
        <a href="{{ route('my.tickets') }}" class="btn btn-secondary">Tiket Saya</a>
    </div>

    <br>

@endsection
