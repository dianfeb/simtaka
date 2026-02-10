@extends('layouts.app')

@section('title', 'Kelola Tahun Ajaran')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Kelola Tahun Ajaran</h2>
            </div>
            <div class="col-auto ms-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-academic-year">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Tambah Tahun Ajaran
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                {{ session('success') }}
            </div>
        @endif

        <div class="row row-cards">
            @forelse($academicYears as $year)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-status-top {{ $year->is_active ? 'bg-green' : 'bg-secondary' }}"></div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span class="avatar avatar-lg" style="background-color: {{ $year->is_active ? '#2fb344' : '#6c757d' }}; color: white;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                    </svg>
                                </span>
                            </div>
                            <div>
                                <h3 class="card-title mb-1">{{ $year->name }}</h3>
                                @if($year->is_active)
                                    <span class="badge bg-green">Aktif Sekarang</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="row mb-2">
                                <div class="col-5 text-secondary">Semester:</div>
                                <div class="col-7">
                                    <span class="badge bg-blue">{{ $year->semester }}</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-secondary">Periode:</div>
                                <div class="col-7">
                                    {{ $year->start_date->format('d M Y') }} - {{ $year->end_date->format('d M Y') }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-secondary">Durasi:</div>
                                <div class="col-7">
                                    {{ $year->start_date->diffInDays($year->end_date) }} hari
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5 text-secondary">Total Siswa:</div>
                                <div class="col-7">
                                    <span class="badge bg-primary">{{ $year->enrollments_count }}</span>
                                </div>
                            </div>
                        </div>

                        @if($year->description)
                        <div class="text-secondary small mt-2">
                            {{ Str::limit($year->description, 100) }}
                        </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            @if(!$year->is_active)
                            <div class="col">
                                <form action="{{ route('admin.academic-years.activate', $year) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success w-100">Aktifkan</button>
                                </form>
                            </div>
                            @endif
                            <div class="col">
                                <button type="button" class="btn btn-sm btn-primary w-100" 
                                    onclick="editAcademicYear({{ $year->id }}, '{{ $year->name }}', '{{ $year->semester }}', '{{ $year->start_date->format('Y-m-d') }}', '{{ $year->end_date->format('Y-m-d') }}', '{{ addslashes($year->description ?? '') }}')">
                                    Edit
                                </button>
                            </div>
                            @if(!$year->is_active)
                            <div class="col">
                                <form action="{{ route('admin.academic-years.destroy', $year) }}" method="POST" 
                                    onsubmit="return confirm('Yakin ingin menghapus tahun ajaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger w-100">Hapus</button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-img">
                        <svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                        </svg>
                    </div>
                    <p class="empty-title">Belum ada tahun ajaran</p>
                    <p class="empty-subtitle text-secondary">Klik tombol "Tambah Tahun Ajaran" untuk membuat tahun ajaran baru</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Add/Edit -->
<div class="modal modal-blur fade" id="modal-academic-year" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-academic-year" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama Tahun Ajaran</label>
                        <input type="text" name="name" id="name" class="form-control" required placeholder="2024/2025">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Semester</label>
                        <select name="semester" id="semester" class="form-select" required>
                            <option value="">Pilih semester...</option>
                            <option value="1">Semester 1 (Ganjil)</option>
                            <option value="2">Semester 2 (Genap)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Deskripsi tahun ajaran (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editAcademicYear(id, name, semester, startDate, endDate, description) {
    document.getElementById('modal-title').textContent = 'Edit Tahun Ajaran';
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('form-academic-year').action = '/admin/academic-years/' + id;
    document.getElementById('name').value = name;
    document.getElementById('semester').value = semester;
    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    document.getElementById('description').value = description;
    
    new bootstrap.Modal(document.getElementById('modal-academic-year')).show();
}

// Reset form on modal close
document.getElementById('modal-academic-year').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modal-title').textContent = 'Tambah Tahun Ajaran';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('form-academic-year').action = '{{ route('admin.academic-years.store') }}';
    document.getElementById('form-academic-year').reset();
});
</script>
@endpush
@endsection