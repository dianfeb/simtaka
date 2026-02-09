@extends('layouts.app')

@section('title', 'Daftar Siswa')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Daftar Siswa
                </h2>
                <div class="text-muted mt-1">Data semua siswa yang terdaftar</div>
            </div>
            <div class="col-auto">
                <div class="btn-list">
                    <a href="{{ route('admin.students.approval') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        Verifikasi Pendaftaran
                        @if($stats['pending'] > 0)
                            <span class="badge bg-red ms-2">{{ $stats['pending'] }}</span>
                        @endif
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                            <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                        </svg>
                        Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Summary Cards -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="small text-muted">Total Siswa</div>
                                <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M5 12l5 5l10 -10"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="small text-muted">Siswa Aktif</div>
                                <h3 class="mb-0 text-success">{{ $stats['active'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-yellow" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M12 8l0 4" />
                                    <path d="M12 16l.01 0" />
                                </svg>
                            </div>
                            <div>
                                <div class="small text-muted">Pending</div>
                                <h3 class="mb-0 text-yellow">{{ $stats['pending'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M18 6l-12 12" />
                                    <path d="M6 6l12 12" />
                                </svg>
                            </div>
                            <div>
                                <div class="small text-muted">Ditolak</div>
                                <h3 class="mb-0 text-red">{{ $stats['rejected'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($students->isEmpty())
            <div class="empty">
                <div class="empty-img">
                    <img src="{{ asset('assets/img/undraw_students.svg') }}" height="128" alt="">
                </div>
                <p class="empty-title">Belum ada siswa</p>
                <p class="empty-subtitle text-muted">
                    Belum ada siswa yang terdaftar di sistem.
                </p>
            </div>
        @else
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Siswa</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Gender</th>
                                <th>Tanggal Lahir</th>
                                <th>Kelas</th>
                                <th>Orang Tua</th>
                                <th>Status</th>
                                <th class="w-1">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>
                                        <span class="text-muted">{{ $student->nis }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm me-2" 
                                                  style="background-image: url({{ $student->photo ? Storage::url($student->photo) : asset('assets/img/default-avatar.png') }})">
                                            </span>
                                            <div>
                                                <div class="font-weight-medium">{{ $student->name }}</div>
                                                @if($student->nickname)
                                                    <div class="text-muted small">({{ $student->nickname }})</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($student->gender == 'L')
                                            <span class="badge bg-azure-lt">Laki-laki</span>
                                        @else
                                            <span class="badge bg-pink-lt">Perempuan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $student->birth_place }}, {{ $student->birth_date->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $student->birth_date->age }} tahun</div>
                                    </td>
                                    <td>
                                        @if($student->currentEnrollment)
                                            <span class="badge bg-blue-lt">{{ $student->currentEnrollment->classRoom->name }}</span>
                                        @else
                                            <span class="text-muted">Belum ada kelas</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->parent)
                                            <div>{{ $student->parent->name }}</div>
                                            <div class="text-muted small">{{ $student->parent->email }}</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->status == 'pending')
                                            <span class="badge bg-yellow">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                    <path d="M12 8l0 4" />
                                                    <path d="M12 16l.01 0" />
                                                </svg>
                                                Pending
                                            </span>
                                        @elseif($student->status == 'active')
                                            <span class="badge bg-success">Aktif</span>
                                        @elseif($student->status == 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                                            </svg>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $students->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    @media print {
        .page-header,
        .btn,
        .card-footer,
        .navbar,
        .footer {
            display: none !important;
        }
        
        .card {
            border: none;
            box-shadow: none;
        }
        
        body {
            background: white;
        }
    }
</style>
@endsection