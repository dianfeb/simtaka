@extends('layouts.app')

@section('title', 'Laporan Pembayaran')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Laporan Pembayaran</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <button onclick="window.print()" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                            <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                        </svg>
                        Print
                    </button>
                    <button onclick="exportExcel()" class="btn btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            <path d="M10 12l4 5" />
                            <path d="M10 17l4 -5" />
                        </svg>
                        Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Periode</label>
                            <select name="period" class="form-select" onchange="this.form.submit()">
                                <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                                <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                                <option value="month" {{ request('period', 'month') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                                <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>
                        
                        @if(request('period') == 'custom')
                        <div class="col-md-2">
                            <label class="form-label">Dari</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sampai</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        @endif

                        <div class="col-md-3">
                            <label class="form-label">Jenis Pembayaran</label>
                            <select name="payment_type" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Jenis</option>
                                @foreach($paymentTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('payment_type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <a href="{{ route('admin.payments.report') }}" class="btn w-100">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Total Pembayaran</div>
                        <div class="h1 mb-0">{{ $summary['total_count'] }}</div>
                        <div class="text-secondary">Transaksi</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Total Nominal</div>
                        <div class="h1 mb-0 text-primary">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Verified</div>
                        <div class="h1 mb-0 text-green">Rp {{ number_format($summary['verified_amount'], 0, ',', '.') }}</div>
                        <div class="text-secondary">{{ $summary['verified_count'] }} transaksi</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Pending</div>
                        <div class="h1 mb-0 text-yellow">Rp {{ number_format($summary['pending_amount'], 0, ',', '.') }}</div>
                        <div class="text-secondary">{{ $summary['pending_count'] }} transaksi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Pembayaran</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table" id="payment-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Siswa</th>
                            <th>Jenis</th>
                            <th>Bulan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Verified By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $payment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td>{{ $payment->payment_code }}</td>
                            <td>
                                <strong>{{ $payment->student->name }}</strong><br>
                                <small class="text-secondary">{{ $payment->student->nis }}</small>
                            </td>
                            <td>{{ $payment->paymentType->name }}</td>
                            <td>{{ $payment->month ? \Carbon\Carbon::parse($payment->month)->format('M Y') : '-' }}</td>
                            <td><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
                            <td>
                                @if($payment->status == 'verified')
                                    <span class="badge bg-green">Verified</span>
                                @elseif($payment->status == 'pending')
                                    <span class="badge bg-yellow">Pending</span>
                                @else
                                    <span class="badge bg-red">Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->verifiedBy)
                                    {{ $payment->verifiedBy->name }}<br>
                                    <small class="text-secondary">{{ $payment->verified_at->format('d/m H:i') }}</small>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-secondary">Tidak ada data pembayaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="6" class="text-end"><strong>TOTAL:</strong></td>
                            <td colspan="3"><strong class="text-primary">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportExcel() {
    const table = document.getElementById('payment-table');
    let csv = [];
    
    // Headers
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent);
    csv.push(headers.join(','));
    
    // Rows
    table.querySelectorAll('tbody tr').forEach(row => {
        const rowData = Array.from(row.querySelectorAll('td')).map(td => {
            return '"' + td.textContent.trim().replace(/"/g, '""') + '"';
        });
        csv.push(rowData.join(','));
    });
    
    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'laporan-pembayaran-' + new Date().toISOString().slice(0,10) + '.csv';
    a.click();
}

// Auto submit form when period is custom
document.querySelector('select[name="period"]').addEventListener('change', function() {
    if(this.value === 'custom') {
        // Don't submit, wait for date inputs
    } else {
        this.form.submit();
    }
});
</script>
@endpush
@endsection