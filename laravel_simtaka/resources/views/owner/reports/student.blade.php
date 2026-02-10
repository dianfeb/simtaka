@extends('layouts.app')

@section('title', 'Laporan Siswa')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Laporan Siswa</h2>
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
        <!-- Summary -->
        <div class="row row-cards mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Total Siswa</div>
                        <div class="h1">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Siswa Aktif</div>
                        <div class="h1 text-green">{{ $stats['active'] }}</div>
                        <div class="text-secondary">{{ number_format($stats['active_percentage'], 1) }}%</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Pending Approval</div>
                        <div class="h1 text-yellow">{{ $stats['pending'] }}</div>
                        <div class="text-secondary">{{ number_format($stats['pending_percentage'], 1) }}%</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Lulus</div>
                        <div class="h1 text-blue">{{ $stats['graduated'] }}</div>
                        <div class="text-secondary">{{ number_format($stats['graduated_percentage'], 1) }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gender Distribution -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Distribusi Gender</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 text-center">
                                <div class="h1 text-azure">{{ $stats['male'] }}</div>
                                <div class="text-secondary">Laki-laki</div>
                                <div class="text-muted small">{{ number_format($stats['male_percentage'], 1) }}%</div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="h1 text-pink">{{ $stats['female'] }}</div>
                                <div class="text-secondary">Perempuan</div>
                                <div class="text-muted small">{{ number_format($stats['female_percentage'], 1) }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Distribusi Usia</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    @foreach($ageDistribution as $age)
                                    <tr>
                                        <td>{{ $age->age }} tahun</td>
                                        <td>{{ $age->count }} siswa</td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $age->percentage }}%"></div>
                                            </div>
                                        </td>
                                        <td class="text-end">{{ number_format($age->percentage, 1) }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- By Class -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Siswa Per Kelas</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Wali Kelas</th>
                            <th>Kapasitas</th>
                            <th>Siswa Aktif</th>
                            <th>Laki-laki</th>
                            <th>Perempuan</th>
                            <th>Tingkat Okupansi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byClass as $class)
                        <tr>
                            <td><strong>{{ $class->name }}</strong></td>
                            <td>{{ $class->teacher->name ?? '-' }}</td>
                            <td>{{ $class->capacity }}</td>
                            <td>{{ $class->active_students }}</td>
                            <td><span class="badge bg-azure-lt">{{ $class->male }}</span></td>
                            <td><span class="badge bg-pink-lt">{{ $class->female }}</span></td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $class->occupancy >= 90 ? 'bg-red' : ($class->occupancy >= 70 ? 'bg-yellow' : 'bg-green') }}" 
                                        style="width: {{ $class->occupancy }}%"></div>
                                </div>
                                <small>{{ number_format($class->occupancy, 1) }}%</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- New Registrations Trend -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Tren Pendaftaran (12 Bulan Terakhir)</h3>
            </div>
            <div class="card-body">
                <div id="chart-registrations" style="height: 300px;"></div>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pendaftaran Terbaru</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Orang Tua</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRegistrations as $student)
                        <tr>
                            <td>{{ $student->registration_date->format('d/m/Y') }}</td>
                            <td>{{ $student->nis }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2" style="background-image: url({{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }})"></span>
                                    {{ $student->name }}
                                </div>
                            </td>
                            <td>{{ $student->currentEnrollment->classRoom->name ?? '-' }}</td>
                            <td>{{ $student->parent->name }}</td>
                            <td>
                                @if($student->status == 'active')
                                    <span class="badge bg-green">Aktif</span>
                                @elseif($student->status == 'pending')
                                    <span class="badge bg-yellow">Pending</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const registrationData = @json($registrationTrend);
    
    const chart = new ApexCharts(document.getElementById('chart-registrations'), {
        chart: { type: 'line', height: 300, toolbar: { show: false } },
        series: [{ name: 'Pendaftaran', data: registrationData.values }],
        xaxis: { categories: registrationData.labels },
        colors: ['#206bc4'],
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 5 }
    });
    chart.render();
});
</script>
@endpush
@endsection