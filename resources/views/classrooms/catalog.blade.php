@extends('layouts.dashboard')

@section('title', 'Daftar Kelas')
@section('page-title', 'Daftar Kelas')
@section('section', 'KATALOG')

@section('content')
    <section class="welcome-band learner-welcome">
        <div>
            <span class="welcome-label">CATALOG</span>
            <h2>Jelajahi kelas yang tersedia <span>✦</span></h2>
            <p>Semua kelas yang dibuka oleh admin tersedia untuk dipilih dan dibeli oleh learner.</p>
        </div>
        <div class="welcome-art learner-art">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            <span>Temukan minatmu,<br><strong>pilih kelasnya.</strong></span>
        </div>
    </section>

    <section class="content-section" id="kelas">
        <div class="section-heading">
            <div>
                <span class="eyebrow">KATALOG</span>
                <h2>Semua kelas</h2>
            </div>
        </div>

        @if ($classrooms->isEmpty())
            <div class="empty-state-box">
                <p>Belum ada kelas yang tersedia di katalog.</p>
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
                            <div class="mb-3 text-muted small">Oleh: {{ $classroom->user->name }}</div>
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('kelas.show', $classroom) }}" class="btn btn-primary-forest">Lihat kelas</a>
                            @elseif (auth()->user()->enrolledClassrooms()->whereKey($classroom->id)->exists())
                                <a href="{{ route('kelas.show', $classroom) }}" class="btn btn-outline-primary">Masuk kelas</a>
                            @else
                                <form action="{{ route('kelas.enroll', $classroom) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary-forest">Enroll kelas</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
