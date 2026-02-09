@extends('layouts.app')

@section('title', 'Upload Pembayaran')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Upload Bukti Pembayaran</h2>
                <div class="text-secondary mt-1">Upload bukti transfer pembayaran untuk diverifikasi admin</div>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('parent.payments.index') }}" class="btn">
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

        <form action="{{ route('parent.payments.store') }}" method="POST" enctype="multipart/form-data">
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
                                <label class="form-label required">Pilih Anak</label>
                                <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" 
                                    id="student-select" required onchange="updatePaymentType()">
                                    <option value="">Pilih anak...</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id', request('student')) == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->nis }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
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

                            <div class="mb-3" id="month-field" style="display: none;">
                                <label class="form-label">Untuk Bulan</label>
                                <input type="month" name="month" class="form-control @error('month') is-invalid @enderror" 
                                    value="{{ old('month', date('Y-m')) }}">
                                <small class="form-hint">Hanya untuk pembayaran SPP bulanan</small>
                                @error('month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Jumlah Bayar</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="amount" id="amount-display" class="form-control" readonly>
                                    </div>
                                    <small class="form-hint">Jumlah otomatis sesuai jenis pembayaran</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Tanggal Transfer</label>
                                    <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" 
                                        value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                    @error('payment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="notes" rows="3" class="form-control" 
                                    placeholder="Tambahkan catatan jika ada">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Upload Bukti -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Bukti Transfer</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <img id="preview" src="{{ asset('images/upload-placeholder.png') }}" 
                                    class="img-fluid rounded border mb-3" style="width: 100%; max-height: 300px; object-fit: contain;">
                                <input type="file" name="proof_image" class="form-control @error('proof_image') is-invalid @enderror" 
                                    accept="image/*" required onchange="previewImage(event)">
                                <small class="form-hint">Format: JPG, PNG, PDF. Max: 2MB</small>
                                @error('proof_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bank Info -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Rekening Sekolah</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-secondary small">Bank BCA</div>
                                <div class="h3">1234567890</div>
                                <div>a/n TK Permata Bunda</div>
                            </div>
                            <hr>
                            <div class="mb-0">
                                <div class="text-secondary small">Bank Mandiri</div>
                                <div class="h3">0987654321</div>
                                <div>a/n TK Permata Bunda</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="card mt-3">
                <div class="card-footer text-end">
                    <a href="{{ route('parent.payments.index') }}" class="btn btn-link">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 9l5 -5l5 5" />
                            <path d="M12 4l0 12" />
                        </svg>
                        Upload Pembayaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('preview');
        preview.src = reader.result;
    }
    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

function updateAmount() {
    const select = document.getElementById('payment-type-select');
    const selectedOption = select.options[select.selectedIndex];
    const amount = selectedOption.getAttribute('data-amount');
    const frequency = selectedOption.getAttribute('data-frequency');
    
    if(amount) {
        // Format number dengan separator
        const formatted = new Intl.NumberFormat('id-ID').format(amount);
        document.getElementById('amount-display').value = formatted;
        
        // Show/hide month field untuk SPP bulanan
        const monthField = document.getElementById('month-field');
        if(frequency === 'monthly') {
            monthField.style.display = 'block';
        } else {
            monthField.style.display = 'none';
        }
    }
}

function updatePaymentType() {
    // Reset payment type when student changes
    document.getElementById('payment-type-select').selectedIndex = 0;
    document.getElementById('amount-display').value = '';
    document.getElementById('month-field').style.display = 'none';
}

// Auto-update amount if payment type already selected on page load
document.addEventListener('DOMContentLoaded', function() {
    updateAmount();
});
</script>
@endpush
@endsection