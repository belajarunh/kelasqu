@extends('layouts.dashboard')

@section('title', 'Buat Kelas')
@section('page-title', 'Buat Kelas')
@section('section', 'KELAS')

@section('content')
    <section class="content-section" id="kelas">
        <div class="section-heading">
            <div>
                <span class="eyebrow">BARU</span>
                <h2>Buat kelas baru</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('kelas.store') }}" enctype="multipart/form-data" class="auth-form" style="max-width: 760px;">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="name">Judul kelas</label>
                <div class="input-shell">
                    <i class="bi bi-journal-text"></i>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Pemrograman Web Dasar" required>
                </div>
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="category">Kategori</label>
                <div class="input-shell">
                    <i class="bi bi-tags"></i>
                    <select id="category" name="category" class="form-control @error('category') is-invalid @enderror" required>
                        <option value="">Pilih kategori</option>
                        <option value="Teknologi" {{ old('category') === 'Teknologi' ? 'selected' : '' }}>Teknologi</option>
                        <option value="Kreativitas" {{ old('category') === 'Kreativitas' ? 'selected' : '' }}>Kreativitas</option>
                        <option value="Produktivitas" {{ old('category') === 'Produktivitas' ? 'selected' : '' }}>Produktivitas</option>
                        <option value="Pengembangan Diri" {{ old('category') === 'Pengembangan Diri' ? 'selected' : '' }}>Pengembangan Diri</option>
                    </select>
                </div>
                @error('category')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="cover_image">Cover kelas</label>
                <div class="input-shell">
                    <i class="bi bi-image"></i>
                    <input id="cover_image" name="cover_image" type="file" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                </div>
                @error('cover_image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label" for="description">Deskripsi</label>
                <div class="input-shell textarea-shell">
                    <i class="bi bi-card-text"></i>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="5" placeholder="Jelaskan kelas yang ingin kamu buat..." required>{{ old('description') }}</textarea>
                </div>
                @error('description')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-3">
                <button class="btn btn-primary-forest" type="submit">Simpan kelas</button>
                <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
