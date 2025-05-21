@extends('layouts.app')

@section('title', 'Not Logged In')

@section('content')
    <div class="container text-center mt-5">
        <div class="alert alert-warning" role="alert">
            <p class="mb-0">Anda belum login</p>
        </div>
        <a href="{{ route('register') }}" class="btn btn-primary me-2">Register</a>
        <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
    </div>
    <br>
@endsection
