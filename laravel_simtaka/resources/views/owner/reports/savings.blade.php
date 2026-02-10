@extends('layouts.app')

@section('title', 'Laporan Tabungan')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Laporan Tabungan</h2>
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
                                <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>Semua</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="class_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary -->
        <div class="row row-cards mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Total Saldo</div>
                        <div class="h1 text-primary">Rp {{ number_format($summary['total_balance'], 0, ',', '.') }}</div>
                        <div class="text-secondary">{{ $summary['total_books'] }} buku tabungan</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Total Setoran</div>
                        <div class="h1 text-green">Rp {{ number_format($summary['total_deposits'], 0, ',', '.') }}</div>
                        <div class="text-secondary">{{ $summary['deposit_count'] }} transaksi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Total Penarikan</div>
                        <div class="h1 text-orange">Rp {{ number_format($summary['total_withdrawals'], 0, ',', '.') }}</div>
                        <div class="text-secondary">{{ $summary['withdrawal_count'] }} transaksi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Siswa Aktif Menabung</div>
                        <div class="h1">{{ $summary['active_savers'] }}</div>
                        <div class="text-secondary">dari {{ $summary['total_students'] }} siswa</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Savers -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top 10 Penabung Terbesar</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th class="text-end">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topSavers as $index => $saver)
                                <tr>
                                    <td>
                                        @if($index == 0)
                                            <span class="badge bg-yellow">🥇</span>
                                        @elseif($index == 1)
                                            <span class="badge bg-secondary">🥈</span>
                                        @elseif($index == 2)
                                            <span class="badge bg-orange">🥉</span>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm me-2" style="background-image: url({{ $saver->student->photo ? asset('storage/' . $saver->student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($saver->student->name) }})"></span>
                                            {{ $saver->student->name }}
                                        </div>
                                    </td>
                                    <td>{{ $saver->student->currentEnrollment->classRoom->name ?? '-' }}</td>
                                    <td class="text-end"><strong class="text-primary">Rp {{ number_format($saver->balance, 0, ',', '.') }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transaksi Terbaru</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $trx)
                                <tr>
                                    <td>{{ $trx->created_at->format('d/m H:i') }}</td>
                                    <td>{{ $trx->savingsBook->student->name }}</td>
                                    <td>
                                        @if($trx->type == 'deposit')
                                            <span class="badge bg-green">Setor</span>
                                        @else
                                            <span class="badge bg-orange">Tarik</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($trx->type == 'deposit')
                                            <span class="text-green">+Rp {{ number_format($trx->amount, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-orange">-Rp {{ number_format($trx->amount, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- By Class -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Tabungan Per Kelas</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Jumlah Penabung</th>
                            <th class="text-end">Total Saldo</th>
                            <th class="text-end">Rata-rata Saldo</th>
                            <th class="text-end">Saldo Tertinggi</th>
                            <th class="text-end">Saldo Terendah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byClass as $class)
                        <tr>
                            <td><strong>{{ $class->name }}</strong></td>
                            <td>{{ $class->savers_count }}</td>
                            <td class="text-end"><strong>Rp {{ number_format($class->total_balance, 0, ',', '.') }}</strong></td>
                            <td class="text-end">Rp {{ number_format($class->average_balance, 0, ',', '.') }}</td>
                            <td class="text-end text-green">Rp {{ number_format($class->max_balance, 0, ',', '.') }}</td>
                            <td class="text-end text-orange">Rp {{ number_format($class->min_balance, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>TOTAL</th>
                            <th>{{ $summary['active_savers'] }}</th>
                            <th class="text-end"><strong>Rp {{ number_format($summary['total_balance'], 0, ',', '.') }}</strong></th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Monthly Trend -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tren Transaksi Bulanan</h3>
            </div>
            <div class="card-body">
                <div id="chart-savings" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const savingsData = @json($monthlyTrend);
    
    const chart = new ApexCharts(document.getElementById('chart-savings'), {
        chart: { type: 'area', height: 300, toolbar: { show: false } },
        series: [
            { name: 'Setoran', data: savingsData.deposits },
            { name: 'Penarikan', data: savingsData.withdrawals }
        ],
        xaxis: { categories: savingsData.labels },
        colors: ['#2fb344', '#fd7e14'],
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } }
    });
    chart.render();
});
</script>
@endpush
@endsection