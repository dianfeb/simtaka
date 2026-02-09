@extends('layouts.app')

@section('title', 'Dashboard Orang Tua')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Selamat Datang</div>
                <h2 class="page-title">Dashboard Orang Tua</h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('parent.students.register') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Daftar Anak Baru
                </a>
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
            <div class="col-sm-6 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Anak</div>
                        </div>
                        <div class="h1 mb-3">{{ $stats['total_children'] }}</div>
                        <div class="d-flex mb-2">
                            <div>Anak terdaftar</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Pembayaran Pending</div>
                        </div>
                        <div class="h1 mb-3 text-yellow">{{ $stats['pending_payments'] }}</div>
                        <div class="d-flex mb-2">
                            <div>Menunggu verifikasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children List -->
        <h3 class="mb-3">Daftar Anak</h3>
        
        @forelse($students as $student)
        <div class="card mb-3">
            <div class="card-status-top 
                {{ $student->status == 'pending' ? 'bg-yellow' : '' }}
                {{ $student->status == 'active' ? 'bg-green' : '' }}
                {{ $student->status == 'rejected' ? 'bg-red' : '' }}">
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-lg" style="background-image: url({{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&size=200' }})"></span>
                    </div>
                    <div class="col">
                        <h3 class="card-title mb-1">{{ $student->name }}</h3>
                        <div class="text-secondary">NIS: {{ $student->nis }}</div>
                        
                        @if($student->status == 'pending')
                            <div class="mt-2">
                                <span class="badge bg-yellow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-sm">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M12 8l0 4" />
                                        <path d="M12 16l.01 0" />
                                    </svg>
                                    Menunggu Verifikasi Admin
                                </span>
                            </div>
                        @elseif($student->status == 'rejected')
                            <div class="mt-2">
                                <span class="badge bg-red">Pendaftaran Ditolak</span>
                                @if($student->rejection_reason)
                                    <div class="alert alert-danger mt-2 mb-0 py-2">
                                        <strong>Alasan:</strong> {{ $student->rejection_reason }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="mt-2">
                                @if($student->currentEnrollment)
                                    <span class="badge bg-blue">{{ $student->currentEnrollment->classRoom->name }}</span>
                                @endif
                                <span class="badge bg-green">Siswa Aktif</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-auto">
                        @if($student->status == 'active')
                        <div class="btn-list">
                            <a href="{{ route('parent.students.show', $student) }}" class="btn btn-primary">
                                Detail
                            </a>
                            <a href="{{ route('parent.payments.create') }}?student={{ $student->id }}" class="btn btn-success">
                                Upload Pembayaran
                            </a>
                        </div>
                        @elseif($student->status == 'pending')
                        <div class="text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 7v5l3 3" />
                            </svg>
                        </div>
                        @else
                        <div class="text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-red">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                        </div>
                        @endif
                    </div>
                </div>

                @if($student->status == 'active')
                <hr>
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="subheader">Saldo Tabungan</div>
                        <div class="h3 mb-0">
                            Rp {{ $student->savingsBook ? number_format($student->savingsBook->balance, 0, ',', '.') : '0' }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="subheader">Pembayaran Pending</div>
                        <div class="h3 mb-0 text-yellow">
                            {{ $student->payments->where('status', 'pending')->count() }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="subheader">Kelas</div>
                        <div class="h3 mb-0">
                            {{ $student->currentEnrollment ? $student->currentEnrollment->classRoom->name : '-' }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="subheader">Wali Kelas</div>
                        <div class="h3 mb-0">
                            {{ $student->currentEnrollment ? $student->currentEnrollment->classRoom->teacher->name : '-' }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @empty
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
            <p class="empty-title">Belum Ada Anak Terdaftar</p>
            <p class="empty-subtitle text-secondary">
                Klik tombol "Daftar Anak Baru" untuk mendaftarkan anak Anda
            </p>
            <div class="empty-action">
                <a href="{{ route('parent.students.register') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Daftar Anak Baru
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection