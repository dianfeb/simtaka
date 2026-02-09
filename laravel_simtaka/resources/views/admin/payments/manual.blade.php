@extends('layouts.app')

@section('title', 'Input Pembayaran Manual')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Input Pembayaran Manual</h2>
                <div class="text-secondary mt-1">Input pembayaran langsung (cash) atau transfer manual</div>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('admin.payments.index') }}" class="btn">
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
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <h4>Terdapat kesalahan:</h4>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.payments.manual.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    <!-- Payment Info -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Pembayaran</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Pilih Siswa</label>
                                <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                    <option value="">Pilih siswa...</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->nis }}) - {{ $student->parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label required">Jenis Pembayaran</label>
                                    <select name="payment_type_id" class="form-select @error('payment_type_id') is-invalid @enderror" 
                                        id="payment-type-select" required onchange="updateAmount()">
                                        <option value="">Pilih jenis...</option>
                                        @foreach($paymentTypes as $type)
                                            <option value="{{ $type->id }}" 
                                                data-amount="{{ $type->amount }}" 
                                                data-frequency="{{ $type->frequency }}"
                                                {{ old('payment_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }} - Rp {{ number_format($type->amount, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Untuk Bulan</label>
                                    <input type="month" name="month" class="form-control" 
                                        value="{{ old('month', date('Y-m')) }}" id="month-field">
                                    <small class="form-hint">Untuk SPP bulanan</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Jumlah Bayar</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                            id="amount-input" value="{{ old('amount') }}" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Tanggal Bayar</label>
                                    <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" 
                                        value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                    @error('payment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <div class="form-selectgroup">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="payment_method" value="cash" class="form-selectgroup-input" 
                                            {{ old('payment_method', 'cash') == 'cash' ? 'checked' : '' }}>
                                        <span class="form-selectgroup-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                                <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                                            </svg>
                                            Tunai (Cash)
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="payment_method" value="transfer" class="form-selectgroup-input"
                                            {{ old('payment_method') == 'transfer' ? 'checked' : '' }}>
                                        <span class="form-selectgroup-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
                                                <path d="M3 10l18 0" />
                                                <path d="M7 15l.01 0" />
                                                <path d="M11 15l2 0" />
                                            </svg>
                                            Transfer Bank
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" rows="3" class="form-control" 
                                    placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                            </div>

                            <div class="alert alert-info">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M12 8l0 4" />
                                    <path d="M12 16l.01 0" />
                                </svg>
                                <strong>Catatan:</strong> Pembayaran manual akan langsung terverifikasi (verified) oleh sistem.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Info -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Panduan Input</h3>
                        </div>
                        <div class="card-body">
                            <ol class="mb-0 ps-3">
                                <li>Pilih siswa yang melakukan pembayaran</li>
                                <li>Pilih jenis pembayaran (SPP, Uang Pangkal, dll)</li>
                                <li>Jumlah akan otomatis terisi, bisa diubah jika berbeda</li>
                                <li>Pilih metode pembayaran (Cash/Transfer)</li>
                                <li>Klik simpan</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pembayaran Terakhir</h3>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($recentPayments->take(5) as $recent)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <strong>{{ $recent->student->name }}</strong>
                                        <div class="text-secondary small">
                                            {{ $recent->paymentType->name }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="text-end">
                                            <div class="small">Rp {{ number_format($recent->amount, 0, ',', '.') }}</div>
                                            <div class="text-secondary small">{{ $recent->payment_date->format('d/m') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="list-group-item text-center text-secondary">
                                Belum ada pembayaran
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="card mt-3">
                <div class="card-footer text-end">
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-link">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                        Simpan Pembayaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateAmount() {
    const select = document.getElementById('payment-type-select');
    const selectedOption = select.options[select.selectedIndex];
    const amount = selectedOption.getAttribute('data-amount');
    
    if(amount) {
        document.getElementById('amount-input').value = amount;
    }
}

// Auto-update amount on page load if payment type selected
document.addEventListener('DOMContentLoaded', function() {
    updateAmount();
});
</script>
@endpush
@endsection