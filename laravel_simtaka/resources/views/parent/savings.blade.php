@extends('layouts.app')

@section('title', 'Buku Tabungan - ' . $student->name)

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Buku Tabungan</h2>
                <div class="text-secondary mt-1">{{ $student->name }}</div>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('parent.dashboard') }}" class="btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l14 0" />
                        <path d="M5 12l6 6" />
                        <path d="M5 12l6 -6" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <!-- Student Info -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <img src="{{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&size=200' }}" 
                            class="rounded mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h3 class="mb-1">{{ $student->name }}</h3>
                        <div class="text-secondary">{{ $student->nis }}</div>
                        
                        @if($savingsBook)
                        <div class="mt-4">
                            <div class="text-secondary small">Saldo Tabungan</div>
                            <div class="h1 text-primary mb-0">Rp {{ number_format($savingsBook->balance, 0, ',', '.') }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($savingsBook)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Buku</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">No. Buku</div>
                                <div class="datagrid-content">{{ $savingsBook->book_number }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Tanggal Buka</div>
                                <div class="datagrid-content">{{ $savingsBook->opened_date->format('d/m/Y') }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Total Transaksi</div>
                                <div class="datagrid-content">{{ $savingsBook->transactions->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Transaction History -->
            <div class="col-lg-8">
                @if(!$savingsBook)
                <div class="empty">
                    <div class="empty-img">
                        <svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                        </svg>
                    </div>
                    <p class="empty-title">Belum Memiliki Buku Tabungan</p>
                    <p class="empty-subtitle text-secondary">
                        Buku tabungan akan dibuat oleh guru/admin saat setoran pertama
                    </p>
                </div>
                @else
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Riwayat Transaksi</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode Transaksi</th>
                                    <th>Tipe</th>
                                    <th>Jumlah</th>
                                    <th>Saldo</th>
                                    <th>Keterangan</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($savingsBook->transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-blue-lt">{{ $transaction->transaction_code }}</span>
                                    </td>
                                    <td>
                                        @if($transaction->type == 'deposit')
                                            <span class="badge bg-green">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-sm">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M12 5l0 14" />
                                                    <path d="M5 12l14 0" />
                                                </svg>
                                                Setor
                                            </span>
                                        @else
                                            <span class="badge bg-orange">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-sm">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M5 12l14 0" />
                                                </svg>
                                                Tarik
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->type == 'deposit')
                                            <span class="text-green">+Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-orange">-Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>{{ $transaction->description ?? '-' }}</td>
                                    <td>
                                        <div class="small">{{ $transaction->createdBy->name ?? '-' }}</div>
                                        <div class="text-secondary small">{{ $transaction->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-secondary py-5">
                                        Belum ada transaksi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary Stats -->
                @if($savingsBook->transactions->count() > 0)
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="subheader">Total Setoran</div>
                                <div class="h2 text-green mb-0">
                                    Rp {{ number_format($savingsBook->transactions->where('type', 'deposit')->sum('amount'), 0, ',', '.') }}
                                </div>
                                <div class="text-secondary">
                                    {{ $savingsBook->transactions->where('type', 'deposit')->count() }} transaksi
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="subheader">Total Penarikan</div>
                                <div class="h2 text-orange mb-0">
                                    Rp {{ number_format($savingsBook->transactions->where('type', 'withdraw')->sum('amount'), 0, ',', '.') }}
                                </div>
                                <div class="text-secondary">
                                    {{ $savingsBook->transactions->where('type', 'withdraw')->count() }} transaksi
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="subheader">Saldo Akhir</div>
                                <div class="h2 text-primary mb-0">
                                    Rp {{ number_format($savingsBook->balance, 0, ',', '.') }}
                                </div>
                                <div class="text-secondary">
                                    Per {{ now()->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection