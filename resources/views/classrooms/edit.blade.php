@extends('layouts.dashboard')

@section('title', 'Edit Kelas')
@section('page-title', 'Edit Kelas')
@section('section', 'KELAS')

@section('content')
    <section class="content-section" id="kelas">
        <div class="section-heading">
            <div>
                <span class="eyebrow">EDIT</span>
                <h2>Perbarui kelas</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('kelas.update', $classroom) }}" enctype="multipart/form-data" class="auth-form" style="max-width: 760px;">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label" for="name">Judul kelas</label>
                <div class="input-shell">
                    <i class="bi bi-journal-text"></i>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $classroom->name) }}" required>
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
                        <option value="Teknologi" {{ old('category', $classroom->category) === 'Teknologi' ? 'selected' : '' }}>Teknologi</option>
                        <option value="Kreativitas" {{ old('category', $classroom->category) === 'Kreativitas' ? 'selected' : '' }}>Kreativitas</option>
                        <option value="Produktivitas" {{ old('category', $classroom->category) === 'Produktivitas' ? 'selected' : '' }}>Produktivitas</option>
                        <option value="Pengembangan Diri" {{ old('category', $classroom->category) === 'Pengembangan Diri' ? 'selected' : '' }}>Pengembangan Diri</option>
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
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>{{ old('description', $classroom->description) }}</textarea>
                </div>
                @error('description')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-3">
                <button class="btn btn-primary-forest" type="submit">Perbarui kelas</button>
                <a href="{{ route('kelas.show', $classroom) }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
