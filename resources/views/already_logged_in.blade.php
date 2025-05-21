@extends('layouts.app')

@section('title', 'Already Logged In')

@section('content')
    <div class="container text-center mt-5">
        <div class="alert alert-info" role="alert">
            <p class="mb-0">Anda telah login</p>
        </div>
        <a href="{{ url('/') }}" class="btn btn-primary me-2">Go Home</a>
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-secondary">Logout</button>
        </form>
    </div>
    <br>
@endsection
