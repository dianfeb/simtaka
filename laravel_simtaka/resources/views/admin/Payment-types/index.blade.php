@extends('layouts.app')

@section('title', 'Kelola Jenis Pembayaran')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Kelola Jenis Pembayaran</h2>
            </div>
            <div class="col-auto ms-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-payment-type">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Tambah Jenis Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                {{ session('success') }}
            </div>
        @endif

        <div class="row row-cards">
            @forelse($paymentTypes as $type)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span class="avatar avatar-lg" style="background-color: {{ $type->is_active ? '#206bc4' : '#6c757d' }}; color: white;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                    </svg>
                                </span>
                            </div>
                            <div>
                                <h3 class="card-title mb-1">{{ $type->name }}</h3>
                                @if($type->is_active)
                                    <span class="badge bg-green">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="row mb-2">
                                <div class="col-5 text-secondary">Jumlah:</div>
                                <div class="col-7">
                                    <strong class="text-primary">Rp {{ number_format($type->amount, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-secondary">Tipe:</div>
                                <div class="col-7">
                                    @if($type->is_monthly)
                                        <span class="badge bg-blue">Bulanan</span>
                                    @else
                                        <span class="badge bg-cyan">Sekali Bayar</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($type->description)
                        <div class="text-secondary small mt-2">
                            {{ Str::limit($type->description, 100) }}
                        </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn btn-sm btn-primary w-100" 
                                    onclick="editPaymentType({{ $type->id }}, '{{ $type->name }}', {{ $type->amount }}, {{ $type->is_monthly ? 'true' : 'false' }}, {{ $type->is_active ? 'true' : 'false' }}, '{{ addslashes($type->description ?? '') }}')">
                                    Edit
                                </button>
                            </div>
                            <div class="col">
                                <form action="{{ route('admin.payment-types.destroy', $type) }}" method="POST" 
                                    onsubmit="return confirm('Yakin ingin menghapus jenis pembayaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger w-100">Hapus</button>
                                </form>
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
                            <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                        </svg>
                    </div>
                    <p class="empty-title">Belum ada jenis pembayaran</p>
                    <p class="empty-subtitle text-secondary">Klik tombol "Tambah Jenis Pembayaran" untuk membuat jenis baru</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Add/Edit -->
<div class="modal modal-blur fade" id="modal-payment-type" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-payment-type" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Jenis Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama Jenis Pembayaran</label>
                        <input type="text" name="name" id="name" class="form-control" required placeholder="SPP Bulanan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Jumlah</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="amount" id="amount" class="form-control" required min="0" step="1000" placeholder="350000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input type="checkbox" name="is_monthly" id="is_monthly" class="form-check-input">
                            <span class="form-check-label">Pembayaran Bulanan</span>
                        </label>
                        <small class="form-hint">Centang jika pembayaran ini dilakukan setiap bulan (seperti SPP)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Deskripsi jenis pembayaran (opsional)"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" checked>
                            <span class="form-check-label">Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editPaymentType(id, name, amount, isMonthly, isActive, description) {
    document.getElementById('modal-title').textContent = 'Edit Jenis Pembayaran';
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('form-payment-type').action = '/admin/payment-types/' + id;
    document.getElementById('name').value = name;
    document.getElementById('amount').value = amount;
    document.getElementById('is_monthly').checked = isMonthly;
    document.getElementById('is_active').checked = isActive;
    document.getElementById('description').value = description;
    
    new bootstrap.Modal(document.getElementById('modal-payment-type')).show();
}

// Reset form on modal close
document.getElementById('modal-payment-type').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modal-title').textContent = 'Tambah Jenis Pembayaran';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('form-payment-type').action = '{{ route('admin.payment-types.store') }}';
    document.getElementById('form-payment-type').reset();
    document.getElementById('is_active').checked = true;
});
</script>
@endpush
@endsection