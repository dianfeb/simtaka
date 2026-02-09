@extends('layouts.app')

@section('title', 'Detail Siswa - ' . $student->name)

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('admin.students.index') }}" class="text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"></path>
                        </svg>
                        Kembali ke Daftar Siswa
                    </a>
                </div>
                <h2 class="page-title">
                    Detail Siswa
                </h2>
            </div>
            <div class="col-auto">
                @if($student->status == 'pending')
                    <a href="{{ route('admin.students.approval', ['status' => 'pending']) }}" class="btn btn-yellow me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                        Verifikasi Siswa
                    </a>
                @endif
                <button onclick="window.print()" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                        <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                    </svg>
                    Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Status Alert -->
        @if($student->status == 'pending')
            <div class="alert alert-warning mb-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 8l0 4" />
                            <path d="M12 16l.01 0" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="alert-title">Pendaftaran Menunggu Verifikasi</h4>
                        <div class="text-muted">Siswa ini baru mendaftar dan menunggu persetujuan admin. <a href="{{ route('admin.students.approval', ['status' => 'pending']) }}" class="alert-link">Klik di sini untuk verifikasi</a></div>
                    </div>
                </div>
            </div>
        @elseif($student->status == 'rejected')
            <div class="alert alert-danger mb-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="alert-title">Pendaftaran Ditolak</h4>
                        <div class="text-muted">
                            <strong>Alasan:</strong> {{ $student->rejection_reason ?? 'Tidak ada alasan yang diberikan' }}
                            <br>
                            <strong>Ditolak oleh:</strong> {{ $student->rejectedBy->name ?? '-' }} pada {{ $student->rejected_at ? $student->rejected_at->format('d/m/Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        @elseif($student->status == 'active' && $student->approved_at)
            <div class="alert alert-success mb-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="alert-title">Siswa Aktif</h4>
                        <div class="text-muted">
                            Disetujui oleh {{ $student->approvedBy->name ?? '-' }} pada {{ $student->approved_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Student Profile Card -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <span class="avatar avatar-xl mb-3 rounded" 
                              style="background-image: url({{ $student->photo ? Storage::url($student->photo) : asset('assets/img/default-avatar.png') }})">
                        </span>
                        <h3 class="mb-1">{{ $student->name }}</h3>
                        @if($student->nickname)
                            <div class="text-muted mb-3">"{{ $student->nickname }}"</div>
                        @endif
                        <div class="mt-3">
                            @if($student->status == 'pending')
                                <span class="badge bg-yellow-lt badge-pill">Pending Verifikasi</span>
                            @elseif($student->status == 'active')
                                <span class="badge bg-success-lt badge-pill">Aktif</span>
                            @elseif($student->status == 'rejected')
                                <span class="badge bg-danger-lt badge-pill">Ditolak</span>
                            @else
                                <span class="badge bg-secondary-lt badge-pill">{{ ucfirst($student->status) }}</span>
                            @endif
                            @if($student->gender == 'L')
                                <span class="badge bg-azure-lt badge-pill">Laki-laki</span>
                            @else
                                <span class="badge bg-pink-lt badge-pill">Perempuan</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Parent Info -->
                @if($student->parent)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Orang Tua</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Nama</div>
                                    <div class="datagrid-content">{{ $student->parent->name }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Email</div>
                                    <div class="datagrid-content">{{ $student->parent->email }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">No. HP</div>
                                    <div class="datagrid-content">{{ $student->parent->phone ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-8">
                <!-- Student Info -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Siswa</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">NIS</div>
                                <div class="datagrid-content">{{ $student->nis }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Tempat, Tanggal Lahir</div>
                                <div class="datagrid-content">
                                    {{ $student->birth_place }}, {{ $student->birth_date->format('d F Y') }}
                                    <span class="text-muted">({{ $student->birth_date->age }} tahun)</span>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Alamat</div>
                                <div class="datagrid-content">{{ $student->address }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Tanggal Pendaftaran</div>
                                <div class="datagrid-content">{{ $student->registration_date->format('d F Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent Details -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Data Orang Tua</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">Ayah</h4>
                                <div class="datagrid">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Nama</div>
                                        <div class="datagrid-content">{{ $student->father_name }}</div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">No. HP</div>
                                        <div class="datagrid-content">{{ $student->father_phone ?? '-' }}</div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Pekerjaan</div>
                                        <div class="datagrid-content">{{ $student->father_job ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-3">Ibu</h4>
                                <div class="datagrid">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Nama</div>
                                        <div class="datagrid-content">{{ $student->mother_name }}</div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">No. HP</div>
                                        <div class="datagrid-content">{{ $student->mother_phone ?? '-' }}</div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Pekerjaan</div>
                                        <div class="datagrid-content">{{ $student->mother_job ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enrollment History -->
                @if($student->status == 'active')
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Riwayat Kelas</h3>
                    </div>
                    <div class="card-body">
                        @if($student->enrollments->isEmpty())
                            <div class="text-muted text-center py-3">Belum ada riwayat kelas</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tahun Ajaran</th>
                                            <th>Kelas</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->enrollments as $enrollment)
                                            <tr>
                                                <td>{{ $enrollment->academicYear->name }}</td>
                                                <td>{{ $enrollment->classRoom->name }}</td>
                                                <td>{{ $enrollment->enrollment_date->format('d/m/Y') }}</td>
                                                <td>
                                                    @if($enrollment->status == 'active')
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($enrollment->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment History -->
        @if($student->status == 'active')
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Riwayat Pembayaran</h3>
                    </div>
                    <div class="card-body">
                        @if($student->payments->isEmpty())
                            <div class="text-muted text-center py-3">Belum ada riwayat pembayaran</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jenis Pembayaran</th>
                                            <th>Bulan</th>
                                            <th class="text-end">Jumlah</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->payments->take(10) as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                                <td>{{ $payment->paymentType->name }}</td>
                                                <td>
                                                    @if($payment->month)
                                                        {{ DateTime::createFromFormat('Y-m', $payment->month)->format('F Y') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-end">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($payment->status == 'verified')
                                                        <span class="badge bg-success">Verified</span>
                                                    @elseif($payment->status == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @else
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total Pembayaran:</strong></td>
                                            <td class="text-end"><strong>Rp {{ number_format($student->payments->where('status', 'verified')->sum('amount'), 0, ',', '.') }}</strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Savings Book -->
        @if($student->savingsBook)
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Buku Tabungan</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">No. Buku</div>
                                        <div class="datagrid-content">{{ $student->savingsBook->book_number }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Tanggal Buka</div>
                                        <div class="datagrid-content">{{ $student->savingsBook->opened_date->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Saldo</div>
                                        <div class="datagrid-content">
                                            <strong class="text-primary">Rp {{ number_format($student->savingsBook->balance, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($student->savingsBook->transactions->isNotEmpty())
                                <h4 class="mt-4 mb-3">Transaksi Terakhir</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis</th>
                                                <th class="text-end">Jumlah</th>
                                                <th class="text-end">Saldo</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($student->savingsBook->transactions->take(5) as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if($transaction->type == 'credit')
                                                            <span class="badge bg-success">Setoran</span>
                                                        @else
                                                            <span class="badge bg-danger">Penarikan</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                                        {{ $transaction->type == 'credit' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</td>
                                                    <td class="text-muted">{{ $transaction->description ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @endif
    </div>
</div>

<style>
    @media print {
        .page-header .btn,
        .navbar,
        .footer,
        .alert {
            display: none !important;
        }
        
        .card {
            border: 1px solid #ddd;
            box-shadow: none;
            page-break-inside: avoid;
        }
        
        body {
            background: white;
        }
    }
</style>
@endsection