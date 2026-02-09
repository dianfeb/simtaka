@extends('layouts.app')

@section('title', 'Nilai Semester - ' . $student->name)

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('parent.dashboard') }}" class="text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"></path>
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>
                <h2 class="page-title">
                    Nilai Semester
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Student Info Card -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-xl rounded" 
                              style="background-image: url({{ $student->photo ? Storage::url($student->photo) : asset('assets/img/default-avatar.png') }})"></span>
                    </div>
                    <div class="col">
                        <h3 class="mb-1">{{ $student->name }}</h3>
                        <div class="text-muted">
                            <div><strong>NIS:</strong> {{ $student->nis }}</div>
                            @if($student->currentEnrollment)
                                <div><strong>Kelas:</strong> {{ $student->currentEnrollment->classRoom->name }}</div>
                                @if($student->currentEnrollment->classRoom->teacher)
                                    <div><strong>Wali Kelas:</strong> {{ $student->currentEnrollment->classRoom->teacher->name }}</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('parent.report-card', $student) }}">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $academicYear->id == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select" onchange="this.form.submit()">
                                <option value="1" {{ $semester == '1' ? 'selected' : '' }}>Semester 1</option>
                                <option value="2" {{ $semester == '2' ? 'selected' : '' }}>Semester 2</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($grades->isEmpty())
            <!-- Empty State -->
            <div class="empty">
                <div class="empty-img">
                    <img src="{{ asset('assets/img/undraw_education.svg') }}" height="128" alt="">
                </div>
                <p class="empty-title">Belum ada nilai</p>
                <p class="empty-subtitle text-muted">
                    Nilai untuk semester ini belum tersedia atau belum diinput oleh guru.
                </p>
            </div>
        @else
            <!-- Report Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Rapor - Semester {{ $semester }}
                    </h3>
                    <div class="card-actions">
                        <button onclick="window.print()" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                            </svg>
                            Cetak Rapor
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Info Box -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 9h.01"></path>
                                    <path d="M11 12h1v4h1"></path>
                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Keterangan Penilaian</h4>
                                <div class="text-muted">
                                    <span class="badge bg-success me-2">BSB</span> Berkembang Sangat Baik<br>
                                    <span class="badge bg-info me-2">BSH</span> Berkembang Sesuai Harapan<br>
                                    <span class="badge bg-warning me-2">MB</span> Mulai Berkembang<br>
                                    <span class="badge bg-secondary me-2">BB</span> Belum Berkembang
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grades Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-vcenter">
                            <thead>
                                <tr class="bg-light">
                                    <th width="50" class="text-center">No</th>
                                    <th>Mata Pelajaran</th>
                                    <th width="100" class="text-center">Nilai</th>
                                    <th width="100" class="text-center">Grade</th>
                                    <th width="100" class="text-center">Predikat</th>
                                    <th>Deskripsi</th>
                                    <th width="150">Guru</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grades as $index => $grade)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="font-weight-medium">{{ $grade->subject->name }}</div>
                                            <div class="text-muted small">{{ $grade->subject->code }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-outline text-blue fs-3">{{ $grade->score }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-blue-lt fs-4">{{ $grade->grade }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $predicateColor = match($grade->predicate) {
                                                    'BSB' => 'success',
                                                    'BSH' => 'info',
                                                    'MB' => 'warning',
                                                    'BB' => 'secondary',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $predicateColor }} fs-4">{{ $grade->predicate }}</span>
                                        </td>
                                        <td>
                                            <div class="text-muted small">{{ $grade->description ?: '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="text-muted small">{{ $grade->teacher->name }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Statistics Footer -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <div class="text-muted mb-1">Rata-rata Nilai</div>
                                <div class="display-6 fw-bold text-primary">
                                    {{ number_format($grades->avg('score'), 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border-start">
                                <div class="text-muted mb-1">Total Mata Pelajaran</div>
                                <div class="display-6 fw-bold">
                                    {{ $grades->count() }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center p-3 border-start">
                                <div class="text-muted mb-1">BSB</div>
                                <div class="h2 mb-0 text-success">
                                    {{ $grades->where('predicate', 'BSB')->count() }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center p-3 border-start">
                                <div class="text-muted mb-1">BSH</div>
                                <div class="h2 mb-0 text-info">
                                    {{ $grades->where('predicate', 'BSH')->count() }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center p-3 border-start">
                                <div class="text-muted mb-1">MB</div>
                                <div class="h2 mb-0 text-warning">
                                    {{ $grades->where('predicate', 'MB')->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    @media print {
        .page-header,
        .navbar,
        .footer,
        .btn,
        .card-actions {
            display: none !important;
        }
        
        .card {
            border: none;
            box-shadow: none;
        }
        
        body {
            background: white;
        }
    }
</style>
@endsection