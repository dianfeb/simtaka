@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Riwayat Pembayaran</h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('parent.payments.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Upload Pembayaran Baru
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

        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('parent.payments.index') }}">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn">Filter</button>
                            <a href="{{ route('parent.payments.index') }}" class="btn btn-link">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment List -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Siswa</th>
                            <th>Jenis Pembayaran</th>
                            <th>Bulan</th>
                            <th>Jumlah</th>
                            <th>Tanggal Upload</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_code }}</td>
                            <td>{{ $payment->student->name }}</td>
                            <td>{{ $payment->paymentType->name }}</td>
                            <td>{{ $payment->month ? \Carbon\Carbon::parse($payment->month)->format('F Y') : '-' }}</td>
                            <td class="font-weight-bold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($payment->status == 'pending')
                                    <span class="badge bg-yellow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-sm">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                            <path d="M12 8l0 4" />
                                            <path d="M12 16l.01 0" />
                                        </svg>
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($payment->status == 'verified')
                                    <span class="badge bg-green">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-sm">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                        Terverifikasi
                                    </span>
                                    <div class="small text-secondary">
                                        {{ $payment->verified_at->format('d/m/Y H:i') }}
                                    </div>
                                @else
                                    <span class="badge bg-red">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-sm">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M18 6l-12 12" />
                                            <path d="M6 6l12 12" />
                                        </svg>
                                        Ditolak
                                    </span>
                                    @if($payment->rejection_reason)
                                    <div class="small text-danger">{{ $payment->rejection_reason }}</div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-ghost-primary" 
                                    data-bs-toggle="modal" data-bs-target="#modal-{{ $payment->id }}">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Detail -->
                        <div class="modal modal-blur fade" id="modal-{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Pembayaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-5 text-secondary">Kode:</div>
                                            <div class="col-7"><strong>{{ $payment->payment_code }}</strong></div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-5 text-secondary">Siswa:</div>
                                            <div class="col-7">{{ $payment->student->name }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-5 text-secondary">Jenis:</div>
                                            <div class="col-7">{{ $payment->paymentType->name }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-5 text-secondary">Jumlah:</div>
                                            <div class="col-7"><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-5 text-secondary">Tanggal Bayar:</div>
                                            <div class="col-7">{{ $payment->payment_date->format('d F Y') }}</div>
                                        </div>
                                        @if($payment->notes)
                                        <div class="row mb-3">
                                            <div class="col-5 text-secondary">Catatan:</div>
                                            <div class="col-7">{{ $payment->notes }}</div>
                                        </div>
                                        @endif
                                        <div class="row mb-3">
                                            <div class="col-12 text-secondary mb-2">Bukti Transfer:</div>
                                            <div class="col-12">
                                                <img src="{{ asset('storage/' . $payment->proof_image) }}" 
                                                    class="img-fluid rounded border" alt="Bukti Transfer">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-3">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M9 14l6 0" />
                                    <path d="M9 17l3 0" />
                                </svg>
                                <h3>Belum ada pembayaran</h3>
                                <p class="text-secondary">
                                    Klik tombol "Upload Pembayaran Baru" untuk menambahkan pembayaran
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
            <div class="card-footer">
                {{ $payments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection