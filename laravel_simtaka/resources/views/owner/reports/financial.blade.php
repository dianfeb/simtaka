@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Laporan Keuangan</h2>
            </div>
            <div class="col-auto ms-auto">
                <button onclick="window.print()" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                    </svg>
                    Print
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <select name="period" class="form-select" onchange="this.form.submit()">
                                <option value="month" {{ request('period', 'month') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                                <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>
                        @if(request('period') == 'custom')
                        <div class="col-md-2">
                            <input type="date" name="start" class="form-control" value="{{ request('start') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="end" class="form-control" value="{{ request('end') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Apply</button>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary -->
        <div class="row row-cards mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Total Pendapatan</div>
                        <div class="h1 text-primary">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
                        <div class="text-secondary">{{ $summary['total_count'] }} pembayaran</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Verified</div>
                        <div class="h1 text-green">Rp {{ number_format($summary['verified'], 0, ',', '.') }}</div>
                        <div class="text-secondary">{{ $summary['verified_count'] }} transaksi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Pending</div>
                        <div class="h1 text-yellow">Rp {{ number_format($summary['pending'], 0, ',', '.') }}</div>
                        <div class="text-secondary">{{ $summary['pending_count'] }} transaksi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Rejected</div>
                        <div class="h1 text-red">Rp {{ number_format($summary['rejected'], 0, ',', '.') }}</div>
                        <div class="text-secondary">{{ $summary['rejected_count'] }} transaksi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- By Payment Type -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Pendapatan Per Jenis Pembayaran</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Jenis Pembayaran</th>
                            <th>Jumlah Transaksi</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byPaymentType as $type)
                        <tr>
                            <td>{{ $type->name }}</td>
                            <td>{{ $type->count }}</td>
                            <td class="text-end"><strong>Rp {{ number_format($type->total, 0, ',', '.') }}</strong></td>
                            <td class="text-end">Rp {{ number_format($type->average, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>TOTAL</th>
                            <th>{{ $summary['total_count'] }}</th>
                            <th class="text-end"><strong>Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</strong></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Monthly Breakdown -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Breakdown Bulanan</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>SPP</th>
                            <th>Uang Pangkal</th>
                            <th>Seragam</th>
                            <th>Kegiatan</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyBreakdown as $month)
                        <tr>
                            <td>{{ $month->month_name }}</td>
                            <td>Rp {{ number_format($month->spp, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($month->pangkal, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($month->seragam, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($month->kegiatan, 0, ',', '.') }}</td>
                            <td class="text-end"><strong>Rp {{ number_format($month->total, 0, ',', '.') }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection