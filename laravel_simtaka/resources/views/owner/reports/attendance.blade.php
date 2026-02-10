@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Laporan Absensi</h2>
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
                                <option value="week" {{ request('period', 'week') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                                <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
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
                        <div class="subheader">Total Kehadiran</div>
                        <div class="h1 text-green">{{ $summary['hadir'] }}</div>
                        <div class="text-secondary">{{ number_format($summary['hadir_percentage'], 1) }}%</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Izin</div>
                        <div class="h1 text-yellow">{{ $summary['izin'] }}</div>
                        <div class="text-secondary">{{ number_format($summary['izin_percentage'], 1) }}%</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Sakit</div>
                        <div class="h1 text-orange">{{ $summary['sakit'] }}</div>
                        <div class="text-secondary">{{ number_format($summary['sakit_percentage'], 1) }}%</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Alpha</div>
                        <div class="h1 text-red">{{ $summary['alpha'] }}</div>
                        <div class="text-secondary">{{ number_format($summary['alpha_percentage'], 1) }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- By Class -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Absensi Per Kelas</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Total Siswa</th>
                            <th>Hadir</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alpha</th>
                            <th>Tingkat Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byClass as $class)
                        <tr>
                            <td>{{ $class->name }}</td>
                            <td>{{ $class->total_students }}</td>
                            <td><span class="badge bg-green">{{ $class->hadir }}</span></td>
                            <td><span class="badge bg-yellow">{{ $class->izin }}</span></td>
                            <td><span class="badge bg-orange">{{ $class->sakit }}</span></td>
                            <td><span class="badge bg-red">{{ $class->alpha }}</span></td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-green" style="width: {{ $class->attendance_rate }}%"></div>
                                </div>
                                <small>{{ number_format($class->attendance_rate, 1) }}%</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daily Trend -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tren Harian</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Hadir</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alpha</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyTrend as $day)
                        <tr>
                            <td>{{ $day->date->format('d/m/Y') }}</td>
                            <td>{{ $day->date->format('l') }}</td>
                            <td><span class="badge bg-green">{{ $day->hadir }}</span></td>
                            <td><span class="badge bg-yellow">{{ $day->izin }}</span></td>
                            <td><span class="badge bg-orange">{{ $day->sakit }}</span></td>
                            <td><span class="badge bg-red">{{ $day->alpha }}</span></td>
                            <td>{{ $day->total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection