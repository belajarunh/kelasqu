@extends('layouts.dashboard')

@section('title', 'Tambah Learner')
@section('page-title', 'Tambah Learner')
@section('section', 'ADMIN')

@section('content')
    <section class="content-section" id="pengguna">
        <div class="section-heading">
            <div>
                <span class="eyebrow">BARU</span>
                <h2>Tambah learner</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.learners.store') }}" class="auth-form" style="max-width: 760px;">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="name">Nama lengkap</label>
                <div class="input-shell">
                    <i class="bi bi-person"></i>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <div class="input-shell">
                    <i class="bi bi-envelope"></i>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="role">Role</label>
                <div class="input-shell">
                    <i class="bi bi-shield-check"></i>
                    <select id="role" name="role" class="form-control" required>
                        <option value="user">Learner</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <div class="input-shell">
                    <i class="bi bi-key"></i>
                    <input id="password" name="password" type="password" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password_confirmation">Konfirmasi password</label>
                <div class="input-shell">
                    <i class="bi bi-key"></i>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                </div>
            </div>

            <div class="d-flex gap-3">
                <button class="btn btn-primary-forest" type="submit">Simpan</button>
                <a href="{{ route('admin.learners.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
