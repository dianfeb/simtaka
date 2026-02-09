@extends('layouts.app')

@section('title', 'Kelola Absensi')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Kelola Absensi</h2>
                <div class="text-secondary mt-1">Kelas: {{ $class->name }}</div>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <input type="date" id="attendance-date" class="form-control" value="{{ request('date', date('Y-m-d')) }}" 
                        onchange="window.location.href='{{ route('teacher.attendance.index') }}?date=' + this.value">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                {{ session('error') }}
            </div>
        @endif

        <!-- Date Info -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Absensi: {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</h3>
                    </div>
                    <div class="col-auto">
                        @if($hasAttendance)
                            <span class="badge bg-green">Sudah Input</span>
                        @else
                            <span class="badge bg-yellow">Belum Input</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('teacher.attendance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="class_id" value="{{ $class->id }}">

            <!-- Attendance Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Siswa ({{ $students->count() }} siswa)</h3>
                    <div class="card-actions">
                        <button type="button" class="btn btn-sm" onclick="markAll('hadir')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-sm">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                            Semua Hadir
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th width="250">Status</th>
                                <th width="120">Jam Masuk</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            @php
                                $attendance = $student->attendances->where('date', $date)->first();
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student->nis }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm me-2" style="background-image: url({{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }})"></span>
                                        <strong>{{ $student->name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" name="students[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                    
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="students[{{ $student->id }}][status]" 
                                            value="hadir" id="hadir-{{ $student->id }}" 
                                            {{ $attendance && $attendance->status == 'hadir' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-green" for="hadir-{{ $student->id }}">Hadir</label>

                                        <input type="radio" class="btn-check" name="students[{{ $student->id }}][status]" 
                                            value="izin" id="izin-{{ $student->id }}"
                                            {{ $attendance && $attendance->status == 'izin' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-yellow" for="izin-{{ $student->id }}">Izin</label>

                                        <input type="radio" class="btn-check" name="students[{{ $student->id }}][status]" 
                                            value="sakit" id="sakit-{{ $student->id }}"
                                            {{ $attendance && $attendance->status == 'sakit' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-orange" for="sakit-{{ $student->id }}">Sakit</label>

                                        <input type="radio" class="btn-check" name="students[{{ $student->id }}][status]" 
                                            value="alpha" id="alpha-{{ $student->id }}"
                                            {{ $attendance && $attendance->status == 'alpha' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-red" for="alpha-{{ $student->id }}">Alpha</label>
                                    </div>
                                </td>
                                <td>
                                    <input type="time" name="students[{{ $student->id }}][check_in]" 
                                        class="form-control form-control-sm" 
                                        value="{{ $attendance ? $attendance->check_in?->format('H:i') : now()->format('H:i') }}">
                                </td>
                                <td>
                                    <input type="text" name="students[{{ $student->id }}][notes]" 
                                        class="form-control form-control-sm" 
                                        placeholder="Catatan (opsional)"
                                        value="{{ $attendance ? $attendance->notes : '' }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                        Simpan Absensi
                    </button>
                </div>
            </div>
        </form>

        <!-- Attendance History -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Riwayat Absensi (7 Hari Terakhir)</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Hadir</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alpha</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendanceHistory as $history)
                        <tr>
                            <td>{{ $history->date->format('d/m/Y - l') }}</td>
                            <td><span class="badge bg-green">{{ $history->hadir }}</span></td>
                            <td><span class="badge bg-yellow">{{ $history->izin }}</span></td>
                            <td><span class="badge bg-orange">{{ $history->sakit }}</span></td>
                            <td><span class="badge bg-red">{{ $history->alpha }}</span></td>
                            <td>{{ $history->total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAll(status) {
    document.querySelectorAll('input[value="' + status + '"]').forEach(function(radio) {
        radio.checked = true;
    });
}
</script>
@endpush
@endsection