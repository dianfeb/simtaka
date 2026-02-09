@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Overview</div>
                <h2 class="page-title">Dashboard Admin</h2>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <!-- Statistics Cards -->
        <div class="row row-deck row-cards">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Siswa Aktif</div>
                        </div>
                        <div class="h1 mb-3">{{ $stats['total_students'] }}</div>
                        <div class="d-flex mb-2">
                            <div>Siswa terdaftar aktif</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Guru</div>
                        </div>
                        <div class="h1 mb-3">{{ $stats['total_teachers'] }}</div>
                        <div class="d-flex mb-2">
                            <div>Guru aktif</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Orang Tua</div>
                        </div>
                        <div class="h1 mb-3">{{ $stats['total_parents'] }}</div>
                        <div class="d-flex mb-2">
                            <div>Orang tua terdaftar</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Pembayaran Pending</div>
                        </div>
                        <div class="h1 mb-3">{{ $stats['pending_payments'] }}</div>
                        <div class="d-flex mb-2">
                            <div>Menunggu verifikasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Payments -->
        <div class="row row-cards mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pembayaran Menunggu Verifikasi</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Siswa</th>
                                    <th>Jenis Pembayaran</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_code }}</td>
                                    <td>{{ $payment->student->name }}</td>
                                    <td>{{ $payment->paymentType->name }}</td>
                                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-primary">
                                            Verifikasi
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada pembayaran pending</td>
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