@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Selamat Datang</div>
                <h2 class="page-title">Dashboard Guru</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ route('teacher.attendance.index') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                            <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                        </svg>
                        Input Absensi Hari Ini
                    </a>
                </div>
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

        <!-- Stats Cards -->
        <div class="row row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Kelas Saya</div>
                        </div>
                        <div class="h1 mb-3">{{ $myClass ? $myClass->name : '-' }}</div>
                        <div class="d-flex mb-2">
                            <div>{{ $stats['total_students'] }} siswa aktif</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Kehadiran Hari Ini</div>
                        </div>
                        <div class="h1 mb-3 text-green">{{ $stats['attendance_today'] }}</div>
                        <div class="d-flex mb-2">
                            <div>dari {{ $stats['total_students'] }} siswa</div>
                        </div>
                        @if($stats['total_students'] > 0)
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-green" style="width: {{ ($stats['attendance_today'] / $stats['total_students']) * 100 }}%" role="progressbar"></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Input Nilai Semester Ini</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $stats['grades_inputted'] ?? 0 }}</div>
                            <div class="text-secondary">/ {{ $stats['total_expected'] ?? 0 }}</div>
                        </div>
                        <div class="d-flex mb-2">
                            <div class="text-secondary small">
                                @if(($stats['total_expected'] ?? 0) > 0)
                                    {{ number_format((($stats['grades_inputted'] ?? 0) / $stats['total_expected']) * 100, 1) }}% selesai
                                @else
                                    Belum ada data
                                @endif
                            </div>
                        </div>
                        @if(($stats['total_expected'] ?? 0) > 0)
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-blue" style="width: {{ (($stats['grades_inputted'] ?? 0) / $stats['total_expected']) * 100 }}%" role="progressbar"></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Tabungan</div>
                        </div>
                        <div class="h1 mb-3">Rp {{ number_format($stats['total_savings'], 0, ',', '.') }}</div>
                        <div class="d-flex mb-2">
                            <div>Semua siswa kelas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Today's Attendance -->
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Absensi Hari Ini - {{ now()->format('d F Y') }}</h3>
                        <div class="card-actions">
                            <a href="{{ route('teacher.attendance.index') }}" class="btn btn-sm btn-primary">
                                Input Absensi
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Status</th>
                                    <th>Jam Masuk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayAttendance as $index => $attendance)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $attendance->student->nis }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm me-2" style="background-image: url({{ $attendance->student->photo ? asset('storage/' . $attendance->student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($attendance->student->name) }})"></span>
                                            {{ $attendance->student->name }}
                                        </div>
                                    </td>
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
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-muted mb-2">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                        </svg>
                                        <div>Belum ada absensi hari ini.</div>
                                        <a href="{{ route('teacher.attendance.index') }}" class="btn btn-sm btn-primary mt-2">Input Absensi</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Attendance Summary This Week -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ringkasan Kehadiran Minggu Ini</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="text-center">
                                    <div class="h1 mb-1 text-green">{{ $weekStats['hadir'] ?? 0 }}</div>
                                    <div class="text-secondary">Hadir</div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-center">
                                    <div class="h1 mb-1 text-yellow">{{ $weekStats['izin'] ?? 0 }}</div>
                                    <div class="text-secondary">Izin</div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-center">
                                    <div class="h1 mb-1 text-orange">{{ $weekStats['sakit'] ?? 0 }}</div>
                                    <div class="text-secondary">Sakit</div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-center">
                                    <div class="h1 mb-1 text-red">{{ $weekStats['alpha'] ?? 0 }}</div>
                                    <div class="text-secondary">Alpha</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Menu Cepat</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('teacher.attendance.index') }}" class="list-group-item list-group-item-action">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar" style="background-color: #206bc4; color: white;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">Input Absensi</div>
                                    <div class="text-secondary small">Catat kehadiran siswa</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('teacher.grades.index') }}" class="list-group-item list-group-item-action">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar" style="background-color: #4299e1; color: white;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">Input Nilai</div>
                                    <div class="text-secondary small">Input nilai semester</div>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('teacher.savings.index') }}" class="list-group-item list-group-item-action">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar" style="background-color: #10b981; color: white;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">Buku Tabungan</div>
                                    <div class="text-secondary small">Kelola tabungan siswa</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Grade Progress -->
                @if(($stats['total_expected'] ?? 0) > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Progress Input Nilai</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-secondary">Nilai Sudah Diinput</span>
                                <span class="fw-bold">{{ $stats['grades_inputted'] ?? 0 }} / {{ $stats['total_expected'] }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-blue" 
                                     style="width: {{ (($stats['grades_inputted'] ?? 0) / $stats['total_expected']) * 100 }}%" 
                                     role="progressbar">
                                </div>
                            </div>
                        </div>
                        
                        @if(($stats['pending_grades'] ?? 0) > 0)
                        <div class="alert alert-warning mb-0">
                            <div class="d-flex align-items-center">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 9v4" />
                                        <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                                        <path d="M12 16h.01" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">Masih ada {{ $stats['pending_grades'] }} nilai yang belum diinput</h4>
                                    <div class="text-secondary">
                                        <a href="{{ route('teacher.grades.index') }}" class="alert-link">Klik di sini untuk input nilai</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-success mb-0">
                            <div class="d-flex align-items-center">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">Semua nilai sudah diinput!</h4>
                                    <div class="text-secondary">Semua siswa sudah memiliki nilai lengkap untuk semester ini.</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection