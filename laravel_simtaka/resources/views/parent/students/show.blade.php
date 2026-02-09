@extends('layouts.app')

@section('title', 'Detail Anak - ' . $student->name)

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Detail Anak</h2>
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
        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <img src="{{ $student->photo ? asset('storage/' . $student->photo) : asset('images/default-avatar.png') }}" 
                            class="rounded mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                        <h3 class="mb-1">{{ $student->name }}</h3>
                        @if($student->nickname)
                            <div class="text-secondary">"{{ $student->nickname }}"</div>
                        @endif
                        <div class="mt-3">
                            <span class="badge badge-outline text-blue">{{ $student->nis }}</span>
                        </div>
                        @if($student->currentEnrollment)
                            <div class="mt-2">
                                <span class="badge bg-green">{{ $student->currentEnrollment->classRoom->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="card">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('parent.report-card', $student) }}" class="list-group-item list-group-item-action">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            </svg>
                            Lihat Nilai Semester
                        </a>
                        <a href="{{ route('parent.savings', $student) }}" class="list-group-item list-group-item-action">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                            </svg>
                            Buku Tabungan
                        </a>
                        <a href="{{ route('parent.payments.create') }}?student={{ $student->id }}" class="list-group-item list-group-item-action">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Upload Pembayaran
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Personal Info -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Data Pribadi</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-5 text-secondary">Jenis Kelamin:</div>
                            <div class="col-7">{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-secondary">Tempat, Tanggal Lahir:</div>
                            <div class="col-7">{{ $student->birth_place }}, {{ $student->birth_date->format('d F Y') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-secondary">Usia:</div>
                            <div class="col-7">{{ $student->age }} tahun</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-secondary">Alamat:</div>
                            <div class="col-7">{{ $student->address }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-secondary">Status:</div>
                            <div class="col-7">
                                @if($student->status == 'active')
                                    <span class="badge bg-green">Aktif</span>
                                @elseif($student->status == 'inactive')
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @else
                                    <span class="badge bg-blue">Lulus</span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 text-secondary">Tanggal Pendaftaran:</div>
                            <div class="col-7">{{ $student->registration_date->format('d F Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Parent Info -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Data Orang Tua</h3>
                    </div>
                    <div class="card-body">
                        <h4 class="mb-3">Ayah</h4>
                        <div class="row mb-2">
                            <div class="col-4 text-secondary">Nama:</div>
                            <div class="col-8">{{ $student->father_name }}</div>
                        </div>
                        @if($student->father_phone)
                        <div class="row mb-2">
                            <div class="col-4 text-secondary">Telepon:</div>
                            <div class="col-8">{{ $student->father_phone }}</div>
                        </div>
                        @endif
                        @if($student->father_job)
                        <div class="row mb-3">
                            <div class="col-4 text-secondary">Pekerjaan:</div>
                            <div class="col-8">{{ $student->father_job }}</div>
                        </div>
                        @endif

                        <hr>

                        <h4 class="mb-3">Ibu</h4>
                        <div class="row mb-2">
                            <div class="col-4 text-secondary">Nama:</div>
                            <div class="col-8">{{ $student->mother_name }}</div>
                        </div>
                        @if($student->mother_phone)
                        <div class="row mb-2">
                            <div class="col-4 text-secondary">Telepon:</div>
                            <div class="col-8">{{ $student->mother_phone }}</div>
                        </div>
                        @endif
                        @if($student->mother_job)
                        <div class="row">
                            <div class="col-4 text-secondary">Pekerjaan:</div>
                            <div class="col-8">{{ $student->mother_job }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Attendance -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Absensi Terakhir</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Jam Masuk</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student->attendances->take(10) as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($attendance->status == 'hadir')
                                            <span class="badge bg-green">Hadir</span>
                                        @elseif($attendance->status == 'izin')
                                            <span class="badge bg-yellow">Izin</span>
                                        @elseif($attendance->status == 'sakit')
                                            <span class="badge bg-orange">Sakit</span>
                                        @else
                                            <span class="badge bg-red">Alpha</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}</td>
                                    <td>{{ $attendance->notes ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary">Belum ada data absensi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection