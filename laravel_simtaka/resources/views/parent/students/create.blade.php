@extends('layouts.app')

@section('title', 'Daftar Anak Baru')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Pendaftaran Anak Baru</h2>
                <div class="text-secondary mt-1">Lengkapi data anak Anda untuk proses pendaftaran</div>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('parent.dashboard') }}" class="btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l14 0" />
                        <path d="M5 12l6 6" />
                        <path d="M5 12l6 -6" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <h4>Terdapat kesalahan:</h4>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('parent.students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Data Anak -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Data Anak</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label required">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                        placeholder="Nama lengkap anak" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama Panggilan</label>
                                    <input type="text" name="nickname" class="form-control" 
                                        placeholder="Nama panggilan" value="{{ old('nickname') }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label required">Jenis Kelamin</label>
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                        <option value="">Pilih...</option>
                                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Tempat Lahir</label>
                                    <input type="text" name="birth_place" class="form-control @error('birth_place') is-invalid @enderror" 
                                        placeholder="Kota kelahiran" value="{{ old('birth_place') }}" required>
                                    @error('birth_place')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Tanggal Lahir</label>
                                    <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" 
                                        value="{{ old('birth_date') }}" required>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Alamat Lengkap</label>
                                <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror" 
                                    placeholder="Alamat tempat tinggal anak" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Data Orang Tua -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Data Orang Tua</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Nama Ayah</label>
                                    <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" 
                                        placeholder="Nama lengkap ayah" value="{{ old('father_name') }}" required>
                                    @error('father_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pekerjaan Ayah</label>
                                    <input type="text" name="father_job" class="form-control" 
                                        placeholder="Pekerjaan ayah" value="{{ old('father_job') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. Telepon Ayah</label>
                                <input type="text" name="father_phone" class="form-control" 
                                    placeholder="081234567890" value="{{ old('father_phone') }}">
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Nama Ibu</label>
                                    <input type="text" name="mother_name" class="form-control @error('mother_name') is-invalid @enderror" 
                                        placeholder="Nama lengkap ibu" value="{{ old('mother_name') }}" required>
                                    @error('mother_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pekerjaan Ibu</label>
                                    <input type="text" name="mother_job" class="form-control" 
                                        placeholder="Pekerjaan ibu" value="{{ old('mother_job') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. Telepon Ibu</label>
                                <input type="text" name="mother_phone" class="form-control" 
                                    placeholder="081234567890" value="{{ old('mother_phone') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Foto Anak -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Foto Anak</h3>
                        </div>
                        <div class="card-body text-center">
                            <img id="preview" src="{{ asset('images/default-avatar.png') }}" 
                                class="rounded mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" 
                                accept="image/*" onchange="previewImage(event)">
                            <small class="text-secondary">Format: JPG, PNG. Max: 2MB</small>
                            @error('photo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="card">
                        <div class="card-body">
                            <h4>Informasi Pendaftaran</h4>
                            <ul class="mb-0">
                                <li>NIS akan digenerate otomatis setelah submit</li>
                                <li>Data dapat diubah setelah pendaftaran</li>
                                <li>Status pendaftaran dapat dilihat di dashboard</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="card mt-3">
                <div class="card-footer text-end">
                    <a href="{{ route('parent.dashboard') }}" class="btn btn-link">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Daftarkan Anak
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('preview');
        preview.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endpush
@endsection