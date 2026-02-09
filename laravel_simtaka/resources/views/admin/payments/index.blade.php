@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Verifikasi Pembayaran</h2>
                <div class="text-secondary mt-1">Verifikasi bukti transfer dari orang tua</div>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                {{ session('error') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Pending</div>
                            <div class="ms-auto">
                                <span class="badge bg-yellow">{{ $stats['pending'] ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="h1 mb-0 mt-2">{{ $stats['pending'] ?? 0 }}</div>
                        <div class="text-secondary">Menunggu verifikasi</div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Verified</div>
                            <div class="ms-auto">
                                <span class="badge bg-green">{{ $stats['verified'] ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="h1 mb-0 mt-2">{{ $stats['verified'] ?? 0 }}</div>
                        <div class="text-secondary">Terverifikasi</div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Rejected</div>
                            <div class="ms-auto">
                                <span class="badge bg-red">{{ $stats['rejected'] ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="h1 mb-0 mt-2">{{ $stats['rejected'] ?? 0 }}</div>
                        <div class="text-secondary">Ditolak</div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Bulan Ini</div>
                        </div>
                        <div class="h1 mb-0 mt-2">Rp {{ number_format($stats['total_month'] ?? 0, 0, ',', '.') }}</div>
                        <div class="text-secondary">Pembayaran terverifikasi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="card mb-3">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" 
                            class="nav-link {{ $status == 'pending' ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 8l0 4" />
                                <path d="M12 16l.01 0" />
                            </svg>
                            Pending 
                            @if($stats['pending'] > 0)
                                <span class="badge bg-yellow ms-2">{{ $stats['pending'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.payments.index', ['status' => 'verified']) }}" 
                            class="nav-link {{ $status == 'verified' ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                            Verified
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.payments.index', ['status' => 'rejected']) }}" 
                            class="nav-link {{ $status == 'rejected' ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                            Rejected
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Payment List -->
        <div class="row row-cards">
            @forelse($payments as $payment)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-status-top 
                        {{ $payment->status == 'pending' ? 'bg-yellow' : '' }}
                        {{ $payment->status == 'verified' ? 'bg-green' : '' }}
                        {{ $payment->status == 'rejected' ? 'bg-red' : '' }}">
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                            <div class="col">
                                <h3 class="card-title mb-1">{{ $payment->payment_code }}</h3>
                                <div class="text-secondary">
                                    {{ $payment->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                @if($payment->status == 'pending')
                                    <span class="badge bg-yellow">Pending</span>
                                @elseif($payment->status == 'verified')
                                    <span class="badge bg-green">Verified</span>
                                @else
                                    <span class="badge bg-red">Rejected</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="row">
                                <div class="col-5 text-secondary">Siswa:</div>
                                <div class="col-7"><strong>{{ $payment->student->name }}</strong></div>
                            </div>
                            <div class="row">
                                <div class="col-5 text-secondary">Orang Tua:</div>
                                <div class="col-7">{{ $payment->student->parent->name }}</div>
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="mb-2">
                            <div class="row">
                                <div class="col-5 text-secondary">Jenis:</div>
                                <div class="col-7">{{ $payment->paymentType->name }}</div>
                            </div>
                            @if($payment->month)
                            <div class="row">
                                <div class="col-5 text-secondary">Bulan:</div>
                                <div class="col-7">{{ \Carbon\Carbon::parse($payment->month)->format('F Y') }}</div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-5 text-secondary">Jumlah:</div>
                                <div class="col-7"><strong class="text-primary">Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></div>
                            </div>
                            <div class="row">
                                <div class="col-5 text-secondary">Tgl Bayar:</div>
                                <div class="col-7">{{ $payment->payment_date->format('d M Y') }}</div>
                            </div>
                        </div>

                        @if($payment->status == 'verified')
                        <div class="text-secondary small">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-sm">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                            Verified by {{ $payment->verifiedBy->name }}
                            <br>{{ $payment->verified_at->format('d M Y H:i') }}
                        </div>
                        @endif

                        @if($payment->status == 'rejected' && $payment->rejection_reason)
                        <div class="alert alert-danger mt-2 mb-0 py-2">
                            <strong>Alasan penolakan:</strong><br>
                            {{ $payment->rejection_reason }}
                        </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary w-100" 
                            data-bs-toggle="modal" data-bs-target="#modal-{{ $payment->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                            </svg>
                            Lihat & Verifikasi
                        </button>
                    </div>
                </div>

                <!-- Modal Verification -->
                <div class="modal modal-blur fade" id="modal-{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Verifikasi Pembayaran - {{ $payment->payment_code }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <!-- Payment Info -->
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Detail Pembayaran</h4>
                                        <table class="table table-sm">
                                            <tr>
                                                <td class="text-secondary" width="40%">Kode:</td>
                                                <td><strong>{{ $payment->payment_code }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-secondary">Siswa:</td>
                                                <td>{{ $payment->student->name }} ({{ $payment->student->nis }})</td>
                                            </tr>
                                            <tr>
                                                <td class="text-secondary">Orang Tua:</td>
                                                <td>{{ $payment->student->parent->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-secondary">Email:</td>
                                                <td>{{ $payment->student->parent->email }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-secondary">Telepon:</td>
                                                <td>{{ $payment->student->parent->phone }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-secondary">Jenis:</td>
                                                <td>{{ $payment->paymentType->name }}</td>
                                            </tr>
                                            @if($payment->month)
                                            <tr>
                                                <td class="text-secondary">Bulan:</td>
                                                <td>{{ \Carbon\Carbon::parse($payment->month)->format('F Y') }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td class="text-secondary">Jumlah:</td>
                                                <td><strong class="text-primary h3">Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-secondary">Tgl Transfer:</td>
                                                <td>{{ $payment->payment_date->format('d F Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-secondary">Upload:</td>
                                                <td>{{ $payment->created_at->format('d F Y H:i') }}</td>
                                            </tr>
                                            @if($payment->notes)
                                            <tr>
                                                <td class="text-secondary">Catatan:</td>
                                                <td>{{ $payment->notes }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>

                                    <!-- Proof Image -->
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Bukti Transfer</h4>
                                        <div class="border rounded p-2 mb-3">
                                            <img src="{{ asset('storage/' . $payment->proof_image) }}" 
                                                class="img-fluid rounded" 
                                                alt="Bukti Transfer"
                                                style="cursor: zoom-in;"
                                                onclick="window.open(this.src, '_blank')">
                                        </div>
                                        <small class="text-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-sm">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg>
                                            Klik gambar untuk memperbesar
                                        </small>
                                    </div>
                                </div>

                                @if($payment->status == 'pending')
                                <hr>
                                <!-- Verification Form -->
                                <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" id="form-verify-{{ $payment->id }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label">Tindakan</label>
                                            <div class="form-selectgroup">
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="action" value="verify" class="form-selectgroup-input" required>
                                                    <span class="form-selectgroup-label">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-green">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M5 12l5 5l10 -10" />
                                                        </svg>
                                                        Verifikasi (Terima)
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
                                                        Tolak
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3" id="reject-reason-{{ $payment->id }}" style="display: none;">
                                        <label class="form-label required">Alasan Penolakan</label>
                                        <textarea name="rejection_reason" class="form-control" rows="3" 
                                            placeholder="Jelaskan alasan penolakan (mis: bukti transfer tidak jelas, jumlah tidak sesuai, dll)"></textarea>
                                    </div>
                                </form>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
                                @if($payment->status == 'pending')
                                <button type="submit" form="form-verify-{{ $payment->id }}" class="btn btn-primary">
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
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        </svg>
                    </div>
                    <p class="empty-title">Tidak ada pembayaran {{ $status }}</p>
                    <p class="empty-subtitle text-secondary">
                        @if($status == 'pending')
                            Belum ada pembayaran yang menunggu verifikasi
                        @elseif($status == 'verified')
                            Belum ada pembayaran yang terverifikasi
                        @else
                            Belum ada pembayaran yang ditolak
                        @endif
                    </p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="mt-4">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle rejection reason field
    document.querySelectorAll('input[name="action"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const paymentId = this.closest('form').id.replace('form-verify-', '');
            const rejectReasonDiv = document.getElementById('reject-reason-' + paymentId);
            const rejectReasonTextarea = rejectReasonDiv.querySelector('textarea');
            
            if(this.value === 'reject') {
                rejectReasonDiv.style.display = 'block';
                rejectReasonTextarea.required = true;
            } else {
                rejectReasonDiv.style.display = 'none';
                rejectReasonTextarea.required = false;
            }
        });
    });
});
</script>
@endpush
@endsection