@extends('layouts.app')

@section('title', 'Input Nilai Semester')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Input Nilai Semester</h2>
                <div class="text-secondary mt-1">Kelas: {{ $class->name }}</div>
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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="academic_year" class="form-select">
                                <option value="">Pilih Tahun Ajaran...</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" 
                                        {{ request('academic_year', $currentAcademicYear?->id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select">
                                <option value="1" {{ request('semester', '1') == '1' ? 'selected' : '' }}>Semester 1</option>
                                <option value="2" {{ request('semester', '1') == '2' ? 'selected' : '' }}>Semester 2</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mata Pelajaran</label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">Pilih Mata Pelajaran...</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                    <path d="M21 21l-6 -6" />
                                </svg>
                                Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedSubject && $selectedAcademicYear)
        <form action="{{ route('teacher.grades.store') }}" method="POST">
            @csrf
            <input type="hidden" name="academic_year_id" value="{{ $selectedAcademicYear->id }}">
            <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <input type="hidden" name="semester" value="{{ $selectedSemester }}">

            <!-- Grades Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Input Nilai: {{ $selectedSubject->name }} - {{ $selectedAcademicYear->name }} - Semester {{ $selectedSemester }}
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Info Card -->
                    <div class="alert alert-info mb-3">
                        <h4 class="alert-title">Keterangan Penilaian TK:</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nilai Angka:</strong>
                                <ul class="mb-0">
                                    <li>86-100: A (Sangat Baik)</li>
                                    <li>71-85: B (Baik)</li>
                                    <li>56-70: C (Cukup)</li>
                                    <li>41-55: D (Kurang)</li>
                                    <li>0-40: E (Sangat Kurang)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <strong>Predikat Perkembangan:</strong>
                                <ul class="mb-0">
                                    <li><strong>BSB:</strong> Berkembang Sangat Baik (86-100)</li>
                                    <li><strong>BSH:</strong> Berkembang Sesuai Harapan (71-85)</li>
                                    <li><strong>MB:</strong> Mulai Berkembang (0-70)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if($students->isEmpty())
                    <div class="alert alert-warning">
                        <strong>Tidak ada siswa!</strong> Tidak ada siswa yang terdaftar di kelas ini untuk tahun ajaran yang dipilih.
                    </div>
                    @endif
                </div>
                
                @if($students->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th width="120">Nilai (0-100)</th>
                                <th width="100">Grade</th>
                                <th width="120">Predikat</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            @php
                                $grade = $student->grades->first();
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
                                    <input type="number" 
                                        name="grades[{{ $student->id }}][score]" 
                                        class="form-control score-input" 
                                        min="0" max="100" step="0.1"
                                        value="{{ $grade ? $grade->score : '' }}"
                                        data-row="{{ $student->id }}"
                                        onchange="calculateGrade(this)">
                                </td>
                                <td>
                                    <input type="text" 
                                        name="grades[{{ $student->id }}][grade]" 
                                        class="form-control grade-display" 
                                        data-row="{{ $student->id }}"
                                        value="{{ $grade ? $grade->grade : '' }}" 
                                        readonly>
                                </td>
                                <td>
                                    <select name="grades[{{ $student->id }}][predicate]" 
                                        class="form-select predicate-select" 
                                        data-row="{{ $student->id }}">
                                        <option value="">-</option>
                                        <option value="MB" {{ $grade && $grade->predicate == 'MB' ? 'selected' : '' }}>MB</option>
                                        <option value="BSH" {{ $grade && $grade->predicate == 'BSH' ? 'selected' : '' }}>BSH</option>
                                        <option value="BSB" {{ $grade && $grade->predicate == 'BSB' ? 'selected' : '' }}>BSB</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" 
                                        name="grades[{{ $student->id }}][description]" 
                                        class="form-control" 
                                        placeholder="Deskripsi perkembangan"
                                        value="{{ $grade ? $grade->description : '' }}">
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
                        Simpan Nilai
                    </button>
                </div>
                @endif
            </div>
        </form>
        @else
        <div class="empty">
            <div class="empty-img">
                <svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                </svg>
            </div>
            <p class="empty-title">Pilih Mata Pelajaran</p>
            <p class="empty-subtitle text-secondary">
                Pilih tahun ajaran, semester, dan mata pelajaran untuk mulai input nilai
            </p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function calculateGrade(input) {
    const row = input.dataset.row;
    const score = parseFloat(input.value);
    const gradeDisplay = document.querySelector(`.grade-display[data-row="${row}"]`);
    const predicateSelect = document.querySelector(`.predicate-select[data-row="${row}"]`);
    
    if (isNaN(score) || score < 0 || score > 100) {
        gradeDisplay.value = '';
        predicateSelect.value = '';
        return;
    }
    
    // Calculate grade
    let grade = '';
    if (score >= 86) {
        grade = 'A';
    } else if (score >= 71) {
        grade = 'B';
    } else if (score >= 56) {
        grade = 'C';
    } else if (score >= 41) {
        grade = 'D';
    } else {
        grade = 'E';
    }
    
    gradeDisplay.value = grade;
    
    // Auto-select predicate based on score
    if (score >= 86) {
        predicateSelect.value = 'BSB';
    } else if (score >= 71) {
        predicateSelect.value = 'BSH';
    } else {
        predicateSelect.value = 'MB';
    }
}

// Auto-calculate on page load for existing scores
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.score-input').forEach(function(input) {
        if (input.value) {
            calculateGrade(input);
        }
    });
});
</script>
@endpush
@endsection