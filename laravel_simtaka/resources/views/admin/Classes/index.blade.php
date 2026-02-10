@extends('layouts.app')

@section('title', 'Kelola Kelas')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Kelola Kelas</h2>
            </div>
            <div class="col-auto ms-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-class">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Tambah Kelas
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
            @forelse($classes as $class)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span class="avatar avatar-lg" style="background-color: #206bc4; color: white;">
                                    {{ substr($class->name, 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="card-title mb-1">{{ $class->name }}</h3>
                                <div class="text-secondary">Level {{ $class->level }}</div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="row mb-2">
                                <div class="col-6 text-secondary">Wali Kelas:</div>
                                <div class="col-6">{{ $class->teacher->name ?? 'Belum ditentukan' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 text-secondary">Kapasitas:</div>
                                <div class="col-6">{{ $class->capacity }} siswa</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 text-secondary">Siswa Aktif:</div>
                                <div class="col-6">
                                    <span class="badge bg-blue">{{ $class->active_students_count }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 text-secondary">Okupansi:</div>
                                <div class="col-6">
                                    <div class="progress" style="height: 8px;">
                                        @php
                                            $occupancy = $class->capacity > 0 ? ($class->active_students_count / $class->capacity) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar {{ $occupancy >= 90 ? 'bg-red' : ($occupancy >= 70 ? 'bg-yellow' : 'bg-green') }}" 
                                            style="width: {{ $occupancy }}%"></div>
                                    </div>
                                    <small>{{ number_format($occupancy, 0) }}%</small>
                                </div>
                            </div>
                        </div>

                        @if($class->description)
                        <div class="text-secondary small mt-2">
                            {{ Str::limit($class->description, 100) }}
                        </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn btn-sm btn-primary w-100" 
                                    onclick="editClass({{ $class->id }}, '{{ $class->name }}', '{{ $class->level }}', {{ $class->teacher_id ?? 'null' }}, {{ $class->capacity }}, '{{ addslashes($class->description ?? '') }}')">
                                    Edit
                                </button>
                            </div>
                            <div class="col">
                                <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" 
                                    onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger w-100">Hapus</button>
                                </form>
                            </div>
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
                            <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" />
                            <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" />
                        </svg>
                    </div>
                    <p class="empty-title">Belum ada kelas</p>
                    <p class="empty-subtitle text-secondary">Klik tombol "Tambah Kelas" untuk membuat kelas baru</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Add/Edit -->
<div class="modal modal-blur fade" id="modal-class" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-class" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama Kelas</label>
                        <input type="text" name="name" id="name" class="form-control" required placeholder="TK A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Level</label>
                        <select name="level" id="level" class="form-select" required>
                            <option value="">Pilih level...</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Wali Kelas</label>
                        <select name="teacher_id" id="teacher_id" class="form-select">
                            <option value="">Belum ditentukan</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Kapasitas</label>
                        <input type="number" name="capacity" id="capacity" class="form-control" required min="1" value="20">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Deskripsi kelas (opsional)"></textarea>
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
function editClass(id, name, level, teacherId, capacity, description) {
    document.getElementById('modal-title').textContent = 'Edit Kelas';
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('form-class').action = '/admin/classes/' + id;
    document.getElementById('name').value = name;
    document.getElementById('level').value = level;
    document.getElementById('teacher_id').value = teacherId || '';
    document.getElementById('capacity').value = capacity;
    document.getElementById('description').value = description;
    
    new bootstrap.Modal(document.getElementById('modal-class')).show();
}

// Reset form on modal close
document.getElementById('modal-class').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modal-title').textContent = 'Tambah Kelas';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('form-class').action = '{{ route('admin.classes.store') }}';
    document.getElementById('form-class').reset();
});
</script>
@endpush
@endsection