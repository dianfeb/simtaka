@extends('layouts.app')

@section('title', 'Dashboard Owner')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Selamat Datang</div>
                <h2 class="page-title">Dashboard Owner</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <select class="form-select" id="period-select" onchange="window.location.href='?period=' + this.value">
                        <option value="today" {{ request('period', 'today') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <!-- Main Stats -->
        <div class="row row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Siswa</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $stats['total_students'] }}</div>
                        </div>
                        <div class="mt-2">
                            <span class="text-green">{{ $stats['active_students'] }} aktif</span>
                            <span class="text-muted ms-2">{{ $stats['pending_students'] }} pending</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Kelas</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $stats['total_classes'] }}</div>
                        </div>
                        <div class="mt-2">
                            <span class="text-muted">{{ $stats['total_teachers'] }} guru</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Pendapatan {{ ucfirst($period) }}</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2 text-primary">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
                        </div>
                        <div class="mt-2">
                            <span class="text-green">{{ $stats['verified_payments'] }} verified</span>
                            <span class="text-yellow ms-2">{{ $stats['pending_payments'] }} pending</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Tabungan</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2 text-success">Rp {{ number_format($stats['total_savings'], 0, ',', '.') }}</div>
                        </div>
                        <div class="mt-2">
                            <span class="text-muted">{{ $stats['active_savers'] }} siswa menabung</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Reports -->
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('owner.reports.financial') }}" class="card card-link">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar" style="background-color: #206bc4; color: white;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Laporan Keuangan</div>
                                <div class="text-secondary">Pembayaran & Pendapatan</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-3">
                <a href="{{ route('owner.reports.attendance') }}" class="card card-link">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar" style="background-color: #4299e1; color: white;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Laporan Absensi</div>
                                <div class="text-secondary">Kehadiran Siswa</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-3">
                <a href="{{ route('owner.reports.student') }}" class="card card-link">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar" style="background-color: #10b981; color: white;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Laporan Siswa</div>
                                <div class="text-secondary">Data & Statistik</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-3">
                <a href="{{ route('owner.reports.savings') }}" class="card card-link">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar" style="background-color: #f59e0b; color: white;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 21l18 0" />
                                        <path d="M3 10l18 0" />
                                        <path d="M5 6l7 -3l7 3" />
                                        <path d="M4 10l0 11" />
                                        <path d="M20 10l0 11" />
                                        <path d="M8 14l0 3" />
                                        <path d="M12 14l0 3" />
                                        <path d="M16 14l0 3" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Laporan Tabungan</div>
                                <div class="text-secondary">Saldo & Transaksi</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mt-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pendapatan 7 Hari Terakhir</h3>
                    </div>
                    <div class="card-body">
                        <div id="chart-revenue" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Kehadiran Siswa (Minggu Ini)</h3>
                    </div>
                    <div class="card-body">
                        <div id="chart-attendance" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Aktivitas Terbaru</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Aktivitas</th>
                                    <th>Detail</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $activity->type }}</td>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->user->name }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary">Tidak ada aktivitas</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Revenue Chart
document.addEventListener('DOMContentLoaded', function () {
    const revenueData = @json($chartData['revenue']);
    
    const revenueChart = new ApexCharts(document.getElementById('chart-revenue'), {
        chart: { type: 'area', height: 300, toolbar: { show: false } },
        series: [{ name: 'Pendapatan', data: revenueData.values }],
        xaxis: { categories: revenueData.labels },
        colors: ['#206bc4'],
        stroke: { curve: 'smooth' },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.3 } }
    });
    revenueChart.render();

    // Attendance Chart
    const attendanceData = @json($chartData['attendance']);
    
    const attendanceChart = new ApexCharts(document.getElementById('chart-attendance'), {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [
            { name: 'Hadir', data: attendanceData.hadir },
            { name: 'Izin', data: attendanceData.izin },
            { name: 'Sakit', data: attendanceData.sakit },
            { name: 'Alpha', data: attendanceData.alpha }
        ],
        xaxis: { categories: attendanceData.labels },
        colors: ['#2fb344', '#fab005', '#fd7e14', '#d63939'],
        plotOptions: { bar: { columnWidth: '50%' } }
    });
    attendanceChart.render();
});
</script>
@endpush
@endsection