@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('admin.users.index') }}" class="text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"></path>
                        </svg>
                        Kembali ke Daftar User
                    </a>
                </div>
                <h2 class="page-title">
                    Edit User
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi User</h3>
                        </div>
                        <div class="card-body">
                            <!-- Name -->
                            <div class="mb-3">
                                <label class="form-label required">Nama Lengkap</label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $user->name) }}"
                                       placeholder="Masukkan nama lengkap"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label required">Email</label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}"
                                       placeholder="user@example.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" 
                                       name="password" 
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Kosongkan jika tidak ingin mengubah password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</small>
                            </div>

                            <!-- Role (Disabled - cannot change) -->
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $user->role == 'guru' ? 'Guru' : 'Orang Tua' }}"
                                       disabled>
                                <small class="form-hint text-muted">Role tidak dapat diubah</small>
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" 
                                       name="phone" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="08xxxxxxxxxx">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" 
                                          rows="3" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          placeholder="Alamat lengkap">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="is_active"
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <span class="form-check-label">User Aktif</span>
                                </label>
                                <small class="form-hint">User yang nonaktif tidak dapat login ke sistem</small>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-link">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M5 12l5 5l10 -10"></path>
                                </svg>
                                Update User
                            </button>
                        </div>
                    </div>
                </form>

                <!-- User Info Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Tambahan</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Terdaftar Sejak</div>
                                <div class="datagrid-content">{{ $user->created_at->format('d F Y, H:i') }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Terakhir Diupdate</div>
                                <div class="datagrid-content">{{ $user->updated_at->format('d F Y, H:i') }}</div>
                            </div>
                            @if($user->role == 'orang_tua')
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Jumlah Anak</div>
                                    <div class="datagrid-content">
                                        <span class="badge bg-blue">{{ $user->children()->count() }} anak</span>
                                    </div>
                                </div>
                            @endif
                            @if($user->role == 'guru')
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Kelas yang Diajar</div>
                                    <div class="datagrid-content">
                                        @if($user->classes()->count() > 0)
                                            @foreach($user->classes as $class)
                                                <span class="badge bg-azure me-1">{{ $class->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Belum ada kelas</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection