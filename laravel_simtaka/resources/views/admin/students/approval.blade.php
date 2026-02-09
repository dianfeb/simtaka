@extends('layouts.app')

@section('title', 'Verifikasi Pendaftaran Siswa')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Verifikasi Pendaftaran Siswa</h2>
                <div class="text-secondary mt-1">Approve atau reject pendaftaran siswa baru</div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                {{ session('success') }}
            </div>
        @endif

        <!-- Stats -->
        <div class="row row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Pending</div>
                        <div class="h1 mb-0 text-yellow">{{ $stats['pending'] }}</div>
                        <div class="text-secondary">Menunggu verifikasi</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Active</div>
                        <div class="h1 mb-0 text-green">{{ $stats['active'] }}</div>
                        <div class="text-secondary">Siswa aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Rejected</div>
                        <div class="h1 mb-0 text-red">{{ $stats['rejected'] }}</div>
                        <div class="text-secondary">Ditolak</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Total</div>
                        <div class="h1 mb-0">{{ $stats['total'] }}</div>
                        <div class="text-secondary">Semua siswa</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="card mb-3">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a href="{{ route('admin.students.approval', ['status' => 'pending']) }}" 
                            class="nav-link {{ $status == 'pending' ? 'active' : '' }}">
                            Pending
                            @if($stats['pending'] > 0)
                                <span class="badge bg-yellow ms-2">{{ $stats['pending'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.students.approval', ['status' => 'active']) }}" 
                            class="nav-link {{ $status == 'active' ? 'active' : '' }}">
                            Active
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.students.approval', ['status' => 'rejected']) }}" 
                            class="nav-link {{ $status == 'rejected' ? 'active' : '' }}">
                            Rejected
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Students List -->
        <div class="row row-cards">
            @forelse($students as $student)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-status-top 
                        {{ $student->status == 'pending' ? 'bg-yellow' : '' }}
                        {{ $student->status == 'active' ? 'bg-green' : '' }}
                        {{ $student->status == 'rejected' ? 'bg-red' : '' }}">
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                            <div class="col-auto">
                                <span class="avatar avatar-lg" style="background-image: url({{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&size=200' }})"></span>
                            </div>
                            <div class="col">
                                <h3 class="card-title mb-1">{{ $student->name }}</h3>
                                <div class="text-secondary">{{ $student->nis }}</div>
                                @if($student->status == 'pending')
                                    <span class="badge bg-yellow mt-1">Pending</span>
                                @elseif($student->status == 'active')
                                    <span class="badge bg-green mt-1">Active</span>
                                @else
                                    <span class="badge bg-red mt-1">Rejected</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="row mb-1">
                                <div class="col-5 text-secondary small">Tanggal Lahir:</div>
                                <div class="col-7 small">{{ $student->birth_place }}, {{ $student->birth_date->format('d/m/Y') }}</div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-5 text-secondary small">Jenis Kelamin:</div>
                                <div class="col-7 small">{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-5 text-secondary small">Orang Tua:</div>
                                <div class="col-7 small">{{ $student->parent->name }}</div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-5 text-secondary small">Telepon:</div>
                                <div class="col-7 small">{{ $student->parent->phone ?? '-' }}</div>
                            </div>
                            <div class="row">
                                <div class="col-5 text-secondary small">Tgl Daftar:</div>
                                <div class="col-7 small">{{ $student->registration_date->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary btn-sm w-100" 
                            data-bs-toggle="modal" data-bs-target="#modal-{{ $student->id }}">
                            Lihat Detail & Verifikasi
                        </button>
                    </div>
                </div>

                <!-- Modal Detail -->
                <div class="modal modal-blur fade" id="modal-{{ $student->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detail Pendaftaran - {{ $student->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4 text-center mb-3">
                                        <img src="{{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&size=300' }}" 
                                            class="rounded mb-2" style="width: 200px; height: 200px; object-fit: cover;">
                                        <div><strong>{{ $student->name }}</strong></div>
                                        <div class="text-secondary">{{ $student->nis }}</div>
                                    </div>
                                    
                                    <div class="col-md-8">
                                        <h4 class="mb-3">Data Anak</h4>
                                        <table class="table table-sm">
                                            <tr>
                                                <td width="40%" class="text-secondary">Nama Lengkap:</td>
                                                <td><strong>{{ $student->name }}</strong></td>
                                            </tr>
                                            @if($student->nickname)
                                            <tr>
                                                <td class="text-secondary">Nama Panggilan:</td>
                                                <td>{{ $student->nickname }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td class="text-secondary">Jenis Kelamin:</td>
                                                <td>{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-secondary">Tempat, Tgl Lahir:</td>
                                                <td>{{ $student->birth_place }}, {{ $student->birth_date->format('d F Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-secondary">Usia:</td>
                                                <td>{{ $student->age }} tahun</td>
                                            </tr>
                                            <tr>
                                                <td class="text-secondary">Alamat:</td>
                                                <td>{{ $student->address }}</td>
                                            </tr>
                                        </table>

                                        <h4 class="mt-4 mb-3">Data Orang Tua</h4>
                                        <table class="table table-sm">
                                            <tr>
                                                <td width="40%" class="text-secondary">Nama Ayah:</td>
                                                <td>{{ $student->father_name }}</td>
                                            </tr>
                                            @if($student->father_phone)
                                            <tr>
                                                <td class="text-secondary">Telepon Ayah:</td>
                                                <td>{{ $student->father_phone }}</td>
                                            </tr>
                                            @endif
                                            @if($student->father_job)
                                            <tr>
                                                <td class="text-secondary">Pekerjaan Ayah:</td>
                                                <td>{{ $student->father_job }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td class="text-secondary">Nama Ibu:</td>
                                                <td>{{ $student->mother_name }}</td>
                                            </tr>
                                            @if($student->mother_phone)
                                            <tr>
                                                <td class="text-secondary">Telepon Ibu:</td>
                                                <td>{{ $student->mother_phone }}</td>
                                            </tr>
                                            @endif
                                            @if($student->mother_job)
                                            <tr>
                                                <td class="text-secondary">Pekerjaan Ibu:</td>
                                                <td>{{ $student->mother_job }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td class="text-secondary">Akun Orang Tua:</td>
                                                <td>{{ $student->parent->email }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if($student->status == 'pending')
                                <hr>
                                <form action="{{ route('admin.students.approve', $student) }}" method="POST" id="form-approve-{{ $student->id }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label">Tindakan</label>
                                            <div class="form-selectgroup">
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="action" value="approve" class="form-selectgroup-input" required>
                                                    <span class="form-selectgroup-label">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-green">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M5 12l5 5l10 -10" />
                                                        </svg>
                                                        Approve (Terima sebagai siswa)
                                                    </span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="action" value="reject" class="form-selectgroup-input" required>
                                                    <span class="form-selectgroup-label">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-red">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M18 6l-12 12" />
                                                            <path d="M6 6l12 12" />
                                                        </svg>
                                                        Reject (Tolak)
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3" id="reject-reason-{{ $student->id }}" style="display: none;">
                                        <label class="form-label required">Alasan Penolakan</label>
                                        <textarea name="rejection_reason" class="form-control" rows="3" 
                                            placeholder="Jelaskan alasan penolakan (akan dikirim ke email orang tua)"></textarea>
                                    </div>

                                    <div class="mt-3" id="class-select-{{ $student->id }}" style="display: none;">
                                        <label class="form-label required">Masukkan ke Kelas</label>
                                        <select name="class_id" class="form-select">
                                            <option value="">Pilih kelas...</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-hint">Siswa akan di-enroll ke kelas ini untuk tahun ajaran aktif</small>
                                    </div>
                                </form>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Tutup</button>
                                @if($student->status == 'pending')
                                <button type="submit" form="form-approve-{{ $student->id }}" class="btn btn-primary">
                                    Simpan Verifikasi
                                </button>
                                @endif
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
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M9 10l.01 0" />
                            <path d="M15 10l.01 0" />
                            <path d="M9.5 15a3.5 3.5 0 0 0 5 0" />
                        </svg>
                    </div>
                    <p class="empty-title">Tidak ada siswa {{ $status }}</p>
                    <p class="empty-subtitle text-secondary">
                        @if($status == 'pending')
                            Belum ada pendaftaran siswa yang menunggu verifikasi
                        @elseif($status == 'active')
                            Belum ada siswa yang aktif
                        @else
                            Belum ada pendaftaran yang ditolak
                        @endif
                    </p>
                </div>
            </div>
            @endforelse
        </div>

        @if($students->hasPages())
        <div class="mt-4">
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="action"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const studentId = this.closest('form').id.replace('form-approve-', '');
            const rejectReasonDiv = document.getElementById('reject-reason-' + studentId);
            const classSelectDiv = document.getElementById('class-select-' + studentId);
            const rejectReasonTextarea = rejectReasonDiv.querySelector('textarea');
            const classSelect = classSelectDiv.querySelector('select');
            
            if(this.value === 'reject') {
                rejectReasonDiv.style.display = 'block';
                classSelectDiv.style.display = 'none';
                rejectReasonTextarea.required = true;
                classSelect.required = false;
            } else {
                rejectReasonDiv.style.display = 'none';
                classSelectDiv.style.display = 'block';
                rejectReasonTextarea.required = false;
                classSelect.required = true;
            }
        });
    });
});
</script>
@endpush
@endsection