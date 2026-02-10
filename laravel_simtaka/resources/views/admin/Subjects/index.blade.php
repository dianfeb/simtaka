@extends('layouts.app')

@section('title', 'Kelola Mata Pelajaran')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Kelola Mata Pelajaran</h2>
                </div>
                <div class="col-auto ms-auto">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-subject">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Tambah Mata Pelajaran
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th width="50">Urutan</th>
                                <th>Nama Mata Pelajaran</th>
                                <th>Kode</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th class="w-1">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-subjects">
                            @forelse($subjects as $subject)
                                <tr data-id="{{ $subject->id }}">
                                    <td>
                                        <span class="handle" style="cursor: move;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                            </svg>
                                            {{ $subject->order }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $subject->name }}</strong></td>
                                    <td><span class="badge bg-blue-lt">{{ $subject->code }}</span></td>
                                    <td>{{ Str::limit($subject->description ?? '-', 50) }}</td>
                                    <td>
                                        @if ($subject->is_active)
                                            <span class="badge bg-green">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-primary"
                                                onclick="editSubject({{ $subject->id }}, '{{ $subject->name }}', '{{ $subject->code }}', '{{ addslashes($subject->description ?? '') }}', {{ $subject->is_active ? '1' : '0' }})">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus mata pelajaran ini?')"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary">Belum ada mata pelajaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit -->
    <div class="modal modal-blur fade" id="modal-subject" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-subject" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title">Tambah Mata Pelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Nama Mata Pelajaran</label>
                            <input type="text" name="name" id="name" class="form-control" required
                                placeholder="Matematika">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Kode</label>
                            <input type="text" name="code" id="code" class="form-control" required
                                placeholder="MTK">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control" rows="3"
                                placeholder="Deskripsi mata pelajaran (opsional)"></textarea>
                        </div>
                        <div class="mb-3">
                            <input type="hidden" name="is_active" value="0">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                                    value="1">
                                <span class="form-check-label">Aktif</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            // Drag and drop sorting
            const sortable = Sortable.create(document.getElementById('sortable-subjects'), {
                handle: '.handle',
                animation: 150,
                onEnd: function(evt) {
                    const order = Array.from(document.querySelectorAll('#sortable-subjects tr')).map((tr, index) =>
                        ({
                            id: tr.dataset.id,
                            order: index + 1
                        }));

                    fetch('{{ route('admin.subjects.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order
                        })
                    });
                }
            });

            function editSubject(id, name, code, description, isActive) {
                document.getElementById('modal-title').textContent = 'Edit Mata Pelajaran';
                document.getElementById('form-method').value = 'PUT';
                document.getElementById('form-subject').action = '/admin/subjects/' + id;
                document.getElementById('name').value = name;
                document.getElementById('code').value = code;
                document.getElementById('description').value = description;
                document.getElementById('is_active').checked = isActive == 1 || isActive == true; // Perbaiki ini

                new bootstrap.Modal(document.getElementById('modal-subject')).show();
            }

            // Reset form on modal close
            document.getElementById('modal-subject').addEventListener('hidden.bs.modal', function() {
                document.getElementById('modal-title').textContent = 'Tambah Mata Pelajaran';
                document.getElementById('form-method').value = 'POST';
                document.getElementById('form-subject').action = '{{ route('admin.subjects.store') }}';
                document.getElementById('form-subject').reset();
                document.getElementById('is_active').checked = true;
            });
        </script>
    @endpush
@endsection
