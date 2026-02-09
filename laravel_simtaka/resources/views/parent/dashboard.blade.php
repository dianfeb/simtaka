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
            <div class="col-auto ms-auto d-print-none">
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
        
        <!-- Statistics -->
        <div class="row row-cards mb-3">
            <div class="col-sm-6 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Total Anak Terdaftar</div>
                        <div class="h1 mb-3">{{ $stats['total_children'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Pembayaran Pending</div>
                        <div class="h1 mb-3">{{ $stats['pending_payments'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Children List -->
        <div class="row row-cards">
            @forelse($students as $student)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <span class="avatar avatar-xl mb-3 rounded" style="background-image: url({{ $student->photo ? asset('storage/' . $student->photo) : asset('images/default-avatar.png') }})"></span>
                        <h3 class="m-0 mb-1">{{ $student->name }}</h3>
                        <div class="text-secondary">{{ $student->nis }}</div>
                        @if($student->currentEnrollment)
                            <div class="mt-2">
                                <span class="badge badge-outline text-blue">{{ $student->currentEnrollment->classRoom->name }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="d-flex">
                        <a href="{{ route('parent.students.show', $student) }}" class="card-btn">
                            Detail
                        </a>
                        <a href="{{ route('parent.report-card', $student) }}" class="card-btn">
                            Nilai
                        </a>
                        <a href="{{ route('parent.savings', $student) }}" class="card-btn">
                            Tabungan
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-img"><img src="{{ asset('images/undraw_add_user.svg') }}" height="128" alt=""></div>
                    <p class="empty-title">Belum ada anak terdaftar</p>
                    <p class="empty-subtitle text-secondary">
                        Silakan daftarkan anak Anda untuk memulai
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('parent.students.register') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Daftar Anak
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection