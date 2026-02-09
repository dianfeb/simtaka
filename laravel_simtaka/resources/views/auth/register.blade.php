@extends('layouts.guest')

@section('title', 'Registrasi')

@section('content')
<div class="text-center mb-4">
    <a href="{{ url('/') }}" class="navbar-brand navbar-brand-autodark">
        <img src="{{ asset('images/logo.png') }}" height="36" alt="">
    </a>
</div>

<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">Daftar Akun Orang Tua</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('register') }}" method="POST" autocomplete="off">
            @csrf
            
            <div class="mb-3">
                <label class="form-label required">Nama Lengkap</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                    placeholder="Nama lengkap Anda" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label required">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                    placeholder="your@email.com" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label required">No. Telepon</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                    placeholder="081234567890" value="{{ old('phone') }}" required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label required">Alamat</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                    rows="3" placeholder="Alamat lengkap" required>{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label required">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                    placeholder="Minimal 8 karakter" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label required">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" 
                    placeholder="Ketik ulang password" required>
            </div>
            
            <div class="mb-3">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" required/>
                    <span class="form-check-label">Saya setuju dengan <a href="#" tabindex="-1">syarat dan ketentuan</a>.</span>
                </label>
            </div>
            
            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Daftar</button>
            </div>
        </form>
    </div>
</div>

<div class="text-center text-secondary mt-3">
    Sudah punya akun? <a href="{{ route('login') }}" tabindex="-1">Login</a>
</div>
@endsection