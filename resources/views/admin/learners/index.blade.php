@extends('layouts.dashboard')

@section('title', 'Data Learner')
@section('page-title', 'Data Learner')
@section('section', 'ADMIN')

@section('content')
    <section class="content-section" id="pengguna">
        <div class="section-heading">
            <div>
                <span class="eyebrow">KOMUNITAS</span>
                <h2>Kelola data learner</h2>
            </div>
            <a href="{{ route('admin.learners.create') }}" class="btn btn-primary-forest">Tambah learner</a>
        </div>

        <div class="table-wrap">
            <table class="table users-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($learners as $learner)
                        <tr>
                            <td><strong>{{ $learner->name }}</strong></td>
                            <td class="muted-cell">{{ $learner->email }}</td>
                            <td class="muted-cell">{{ $learner->role }}</td>
                            <td class="muted-cell">{{ $learner->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('admin.learners.edit', $learner) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.learners.destroy', $learner) }}" method="POST" onsubmit="return confirm('Hapus learner ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">Belum ada learner terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
