@extends('layouts.app')

@section('title', 'Buku Tabungan - ' . $student->name)

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
                    Buku Tabungan
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
                            @if($savingsBook)
                                <div><strong>No. Buku Tabungan:</strong> {{ $savingsBook->book_number }}</div>
                                <div><strong>Tanggal Buka:</strong> {{ $savingsBook->opened_date->format('d F Y') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!$savingsBook)
            <!-- No Savings Book -->
            <div class="empty">
                <div class="empty-img">
                    <img src="{{ asset('assets/img/undraw_savings.svg') }}" height="128" alt="">
                </div>
                <p class="empty-title">Belum memiliki buku tabungan</p>
                <p class="empty-subtitle text-muted">
                    {{ $student->name }} belum memiliki buku tabungan aktif.<br>
                    Silakan hubungi admin atau guru untuk pembuatan buku tabungan.
                </p>
            </div>
        @else
            <!-- Balance Cards -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"></path>
                                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="small opacity-75 mb-1">Saldo Tabungan</div>
                                    <h2 class="mb-0">Rp {{ number_format($savingsBook->balance, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M7 10l5 -6l5 6"></path>
                                        <path d="M21 10l-2 8a2 2.5 0 0 1 -2 2h-10a2 2.5 0 0 1 -2 -2l-2 -8z"></path>
                                        <path d="M12 15m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="small opacity-75 mb-1">Total Setoran</div>
                                    <h3 class="mb-0">Rp {{ number_format($savingsBook->transactions->where('type', 'credit')->sum('amount'), 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M17 14l-5 6l-5 -6"></path>
                                        <path d="M21 10l-2 8a2 2.5 0 0 1 -2 2h-10a2 2.5 0 0 1 -2 -2l-2 -8z"></path>
                                        <path d="M12 15m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="small opacity-75 mb-1">Total Penarikan</div>
                                    <h3 class="mb-0">Rp {{ number_format($savingsBook->transactions->where('type', 'debit')->sum('amount'), 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($savingsBook->transactions->isEmpty())
                <!-- No Transactions -->
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('assets/img/undraw_empty.svg') }}" height="128" alt="">
                    </div>
                    <p class="empty-title">Belum ada transaksi</p>
                    <p class="empty-subtitle text-muted">
                        Belum ada transaksi pada buku tabungan ini.
                    </p>
                </div>
            @else
                <!-- Transaction History -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Riwayat Transaksi</h3>
                        <div class="card-actions">
                            <button onclick="window.print()" class="btn btn-primary btn-sm">
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
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode Transaksi</th>
                                    <th>Jenis</th>
                                    <th class="text-end">Saldo Sebelum</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Saldo Setelah</th>
                                    <th>Keterangan</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($savingsBook->transactions->sortByDesc('transaction_date') as $transaction)
                                    <tr>
                                        <td>
                                            <div>{{ $transaction->transaction_date->format('d/m/Y') }}</div>
                                            <div class="text-muted small">{{ $transaction->transaction_date->format('H:i') }}</div>
                                        </td>
                                        <td>
                                            <span class="badge badge-outline">{{ $transaction->transaction_code }}</span>
                                        </td>
                                        <td>
                                            @if($transaction->type == 'credit')
                                                <span class="badge bg-success">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M7 10l5 -6l5 6"></path>
                                                        <path d="M21 10l-2 8a2 2.5 0 0 1 -2 2h-10a2 2.5 0 0 1 -2 -2l-2 -8z"></path>
                                                    </svg>
                                                    Setoran
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M17 14l-5 6l-5 -6"></path>
                                                        <path d="M21 10l-2 8a2 2.5 0 0 1 -2 2h-10a2 2.5 0 0 1 -2 -2l-2 -8z"></path>
                                                    </svg>
                                                    Penarikan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end text-muted">
                                            Rp {{ number_format($transaction->balance_before, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            <strong class="{{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->type == 'credit' ? '+' : '-' }}
                                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                        <td class="text-end">
                                            <strong>Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <div class="text-muted small">{{ $transaction->description ?: '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="text-muted small">
                                                @if($transaction->createdBy)
                                                    {{ $transaction->createdBy->name }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="4" class="text-end"><strong>Total Transaksi:</strong></td>
                                    <td class="text-end">
                                        <strong>{{ $savingsBook->transactions->count() }} transaksi</strong>
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Statistik Setoran</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-muted mb-1">Jumlah Setoran</div>
                                        <h3 class="mb-0">{{ $savingsBook->transactions->where('type', 'credit')->count() }}x</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted mb-1">Rata-rata</div>
                                        <h3 class="mb-0 text-success">
                                            @php
                                                $creditTransactions = $savingsBook->transactions->where('type', 'credit');
                                                $avgCredit = $creditTransactions->count() > 0 ? $creditTransactions->avg('amount') : 0;
                                            @endphp
                                            Rp {{ number_format($avgCredit, 0, ',', '.') }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Statistik Penarikan</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-muted mb-1">Jumlah Penarikan</div>
                                        <h3 class="mb-0">{{ $savingsBook->transactions->where('type', 'debit')->count() }}x</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted mb-1">Rata-rata</div>
                                        <h3 class="mb-0 text-danger">
                                            @php
                                                $debitTransactions = $savingsBook->transactions->where('type', 'debit');
                                                $avgDebit = $debitTransactions->count() > 0 ? $debitTransactions->avg('amount') : 0;
                                            @endphp
                                            Rp {{ number_format($avgDebit, 0, ',', '.') }}
                                        </h3>
                                    </div>
                                </div>
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
        .page-header,
        .navbar,
        .footer,
        .btn,
        .card-actions {
            display: none !important;
        }
        
        .card {
            border: 1px solid #ddd;
            box-shadow: none;
        }
        
        body {
            background: white;
        }
        
        .row.mt-3 {
            display: none;
        }
    }
</style>
@endsection