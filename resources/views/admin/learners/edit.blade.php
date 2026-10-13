@extends('layouts.dashboard')

@section('title', 'Edit Learner')
@section('page-title', 'Edit Learner')
@section('section', 'ADMIN')

@section('content')
    <section class="content-section" id="pengguna">
        <div class="section-heading">
            <div>
                <span class="eyebrow">EDIT</span>
                <h2>Perbarui data learner</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.learners.update', $learner) }}" class="auth-form" style="max-width: 760px;">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label" for="name">Nama lengkap</label>
                <div class="input-shell">
                    <i class="bi bi-person"></i>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $learner->name) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <div class="input-shell">
                    <i class="bi bi-envelope"></i>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $learner->email) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="role">Role</label>
                <div class="input-shell">
                    <i class="bi bi-shield-check"></i>
                    <select id="role" name="role" class="form-control" required>
                        <option value="user" {{ old('role', $learner->role) === 'user' ? 'selected' : '' }}>Learner</option>
                        <option value="admin" {{ old('role', $learner->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password">Password baru (opsional)</label>
                <div class="input-shell">
                    <i class="bi bi-key"></i>
                    <input id="password" name="password" type="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password_confirmation">Konfirmasi password</label>
                <div class="input-shell">
                    <i class="bi bi-key"></i>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Konfirmasi password">
                </div>
            </div>

            <div class="d-flex gap-3">
                <button class="btn btn-primary-forest" type="submit">Perbarui</button>
                <a href="{{ route('admin.learners.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
