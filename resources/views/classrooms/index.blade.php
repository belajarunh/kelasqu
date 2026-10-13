@extends('layouts.dashboard')

@section('title', auth()->user()->role === 'admin' ? 'Kelola Kelas' : 'Kelas Saya')
@section('page-title', auth()->user()->role === 'admin' ? 'Kelola Kelas' : 'Kelas Saya')
@section('section', 'KELAS')

@section('content')
    <section class="welcome-band {{ auth()->user()->role === 'admin' ? 'admin-welcome' : 'learner-welcome' }}">
        <div>
            <span class="welcome-label">{{ auth()->user()->role === 'admin' ? 'MANAGEMENT' : 'RUANG BELAJARMU' }}</span>
            <h2>{{ auth()->user()->role === 'admin' ? 'Kelola katalog kelas' : 'Kelas yang kamu miliki' }} <span>✦</span></h2>
            <p>{{ auth()->user()->role === 'admin' ? 'Kelola semua kelas yang tersedia di katalog KelasQu.' : 'Kelas yang sudah kamu enroll akan muncul di sini.' }}</p>
        </div>
        <div class="welcome-art {{ auth()->user()->role === 'admin' ? '' : 'learner-art' }}">
            <i class="bi {{ auth()->user()->role === 'admin' ? 'bi-journals' : 'bi-journal-bookmark-fill' }}"></i>
            <span>{{ auth()->user()->role === 'admin' ? 'Katalog aktif' : 'Siap belajar,' }}<br><strong>{{ auth()->user()->role === 'admin' ? 'KelasQu' : 'mulai dari sini.' }}</strong></span>
        </div>
    </section>

    <section class="content-section" id="kelas">
        <div class="section-heading">
            <div>
                <span class="eyebrow">KELAS</span>
                <h2>{{ auth()->user()->role === 'admin' ? 'Daftar semua kelas' : 'Kelas yang saya miliki' }}</h2>
            </div>
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('kelas.create') }}" class="btn btn-primary-forest">Buat kelas baru</a>
            @else
                <a href="{{ route('kelas.catalog') }}" class="btn btn-primary-forest">Enroll kelas baru</a>
            @endif
        </div>

        @if ($classrooms->isEmpty())
            <div class="empty-state-box">
                <p>{{ auth()->user()->role === 'admin' ? 'Belum ada kelas yang tersedia di katalog.' : 'Belum ada kelas yang kamu miliki. Enroll kelas baru untuk mulai belajar.' }}</p>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('kelas.create') }}" class="btn btn-primary-forest">Buat kelas</a>
                @else
                    <a href="{{ route('kelas.catalog') }}" class="btn btn-primary-forest">Lihat katalog</a>
                @endif
            </div>
        @else
            <div class="topic-grid">
                @foreach ($classrooms as $classroom)
                    <article class="topic-card topic-tech">
                        <div class="topic-image" role="img" aria-label="Kelas {{ $classroom->name }}"></div>
                        <div class="topic-content">
                            <span class="topic-category">{{ strtoupper($classroom->category) }}</span>
                            <h3>{{ $classroom->name }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($classroom->description, 110) }}</p>
                            @if (auth()->user()->role === 'admin')
                                <div class="d-flex gap-2 mt-3 flex-wrap">
                                    <a href="{{ route('kelas.show', $classroom) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                    <a href="{{ route('kelas.edit', $classroom) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="{{ route('kelas.destroy', $classroom) }}" method="POST" onsubmit="return confirm('Hapus kelas ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            @else
                                <div class="d-flex gap-2 mt-3 flex-wrap">
                                    <a href="{{ route('kelas.show', $classroom) }}" class="btn btn-sm btn-outline-primary">Masuk</a>
                                    <form action="{{ route('kelas.unenroll', $classroom) }}" method="POST" onsubmit="return confirm('Hapus kelas ini dari ruang belajarmu?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            @endif
                            <span class="topic-link mt-3 d-inline-block">Status: {{ ucfirst($classroom->status) }} <i class="bi bi-arrow-up-right"></i></span>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
