@extends('layouts.app')

@section('title', 'Kelola Buku Tabungan')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Kelola Buku Tabungan</h2>
                <div class="text-secondary mt-1">Kelas: {{ $class->name }}</div>
            </div>
            <div class="col-auto ms-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-transaction">
                    ➕ Transaksi Baru
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
<div class="container-xl">

{{-- ALERT --}}
@foreach (['success','error'] as $msg)
    @if(session($msg))
        <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }} alert-dismissible">
            <button class="btn-close" data-bs-dismiss="alert"></button>
            {{ session($msg) }}
        </div>
    @endif
@endforeach

{{-- SUMMARY --}}
<div class="row row-cards mb-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total Tabungan Kelas</div>
                <div class="h1 text-primary">
                    Rp {{ number_format($totalSavings,0,',','.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Siswa Aktif Menabung</div>
                <div class="h1 text-green">{{ $activeSavers }}</div>
                <div class="text-secondary">dari {{ $students->count() }} siswa</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Transaksi Bulan Ini</div>
                <div class="h1">{{ $monthlyTransactions }}</div>
            </div>
        </div>
    </div>
</div>

{{-- TABLE --}}
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Saldo Tabungan Siswa</h3>
        <input type="text"
            class="form-control form-control-sm w-25"
            placeholder="Cari NIS / Nama"
            id="search-student"
            onkeyup="searchStudent()">
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter" id="savings-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>No Rekening</th>
                    <th>Saldo</th>
                    <th>Transaksi Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($students as $i => $student)
            @php
                $book = $student->savingsBook;
                $lastTrx = $book?->transactions()->latest()->first();
            @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $student->nis }}</td>
                    <td><strong>{{ $student->name }}</strong></td>
                    <td>
                        @if($book)
                            <span class="badge bg-blue">{{ $book->book_number }}</span>
                        @else
                            <span class="badge bg-secondary">-</span>
                        @endif
                    </td>
                    <td>
                        <strong class="text-primary">
                            Rp {{ number_format($book->balance ?? 0,0,',','.') }}
                        </strong>
                    </td>
                    <td>
                        @if($lastTrx)
                            <span class="badge {{ $lastTrx->type == 'deposit' ? 'bg-green' : 'bg-orange' }}">
                                {{ $lastTrx->type == 'deposit' ? 'Setor' : 'Tarik' }}
                            </span>
                            <div class="small text-secondary">
                                {{ $lastTrx->created_at->diffForHumans() }}
                            </div>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-success"
                                onclick="openTransaction({{ $student->id }},'deposit','{{ $student->name }}')">
                                Setor
                            </button>
                            <button class="btn btn-warning"
                                onclick="openTransaction({{ $student->id }},'withdraw','{{ $student->name }}')"
                                {{ !$book || $book->balance <= 0 ? 'disabled' : '' }}>
                                Tarik
                            </button>
                            <button class="btn btn-ghost-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-history-{{ $student->id }}">
                                Riwayat
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-secondary">
                        Tidak ada data siswa
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
</div>

{{-- ================= MODAL TRANSACTION ================= --}}
<div class="modal fade" id="modal-transaction">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('teacher.savings.transaction') }}">
            @csrf
            <div class="modal-header">
                <h5 id="modal-title">Transaksi</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="student_id" id="student-id">
                <input type="hidden" name="type" id="transaction-type">

                <div class="mb-3">
                    <label>Siswa</label>
                    <input class="form-control" id="student-name" readonly>
                </div>

                <div class="mb-3">
                    <label>Jumlah</label>
                    <input type="number" name="amount" class="form-control"
                        min="1000" step="1000" required>
                </div>

                <div class="mb-3">
                    <label>Catatan</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL RIWAYAT ================= --}}
@foreach($students as $student)
@php $book = $student->savingsBook; @endphp
<div class="modal fade" id="modal-history-{{ $student->id }}">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Riwayat - {{ $student->name }}</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($book)
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($book->transactions as $trx)
                        <tr>
                            <td>{{ $trx->transaction_date->format('d/m/Y H:i') }}</td>
                            <td>{{ $trx->transaction_code }}</td>
                            <td>
                                <span class="badge {{ $trx->type == 'deposit' ? 'bg-green' : 'bg-orange' }}">
                                    {{ $trx->type == 'deposit' ? 'Setor' : 'Tarik' }}
                                </span>
                            </td>
                            <td>
                                {{ $trx->type == 'deposit' ? '+' : '-' }}
                                Rp {{ number_format($trx->amount,0,',','.') }}
                            </td>
                            <td>
                                Rp {{ number_format($trx->balance_after,0,',','.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary">
                                Belum ada transaksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @else
                    <div class="text-center text-secondary py-4">
                        Belum memiliki buku tabungan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
function openTransaction(id,type,name){
    document.getElementById('student-id').value = id;
    document.getElementById('transaction-type').value = type;
    document.getElementById('student-name').value = name;
    document.getElementById('modal-title').innerText =
        type === 'deposit' ? 'Setor Tabungan' : 'Tarik Tabungan';
    new bootstrap.Modal(document.getElementById('modal-transaction')).show();
}

function searchStudent(){
    let filter = document.getElementById('search-student').value.toUpperCase();
    document.querySelectorAll('#savings-table tbody tr').forEach(row=>{
        row.style.display = row.innerText.toUpperCase().includes(filter) ? '' : 'none';
    });
}
</script>
@endpush
