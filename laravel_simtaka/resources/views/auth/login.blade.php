@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="text-center mb-4">
    <a href="{{ url('/') }}" class="navbar-brand navbar-brand-autodark">
        <img src="{{ asset('images/logo.png') }}" height="36" alt="">
    </a>
</div>

<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">Login ke Akun Anda</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('login') }}" method="POST" autocomplete="off">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                    placeholder="your@email.com" value="{{ old('email') }}" autocomplete="off" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-2">
                <label class="form-label">
                    Password
                    <span class="form-label-description">
                        <a href="#">Lupa password?</a>
                    </span>
                </label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                    placeholder="Your password" autocomplete="off" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-2">
                <label class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input"/>
                    <span class="form-check-label">Ingat saya</span>
                </label>
            </div>
            
            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </div>
        </form>
    </div>
</div>

<div class="text-center text-secondary mt-3">
    Belum punya akun? <a href="{{ route('register') }}" tabindex="-1">Daftar</a>
</div>
@endsection