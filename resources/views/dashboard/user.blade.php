@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('section', 'LEARNER')
@section('page-title', 'Dashboard')

@section('content')
<section class="welcome-band learner-welcome">
    <div><span class="welcome-label">RUANG BELAJARMU</span><h2>Hai, {{ auth()->user()->name }} <span>✦</span></h2><p>Hari yang baik untuk menemukan sesuatu yang baru.</p></div>
    <div class="welcome-art learner-art"><i class="bi bi-book-half"></i><span>Pelan-pelan saja,<br><strong>yang penting tumbuh.</strong></span></div>
</section>
<section class="learner-summary" id="aktivitas">
    <div class="summary-icon"><i class="bi bi-compass"></i></div>
    <div><span class="eyebrow">LANGKAH BERIKUTNYA</span><h2>Jelajahi kelas yang sesuai minatmu</h2><p>Daftar kelas tersedia siap dibuka. Setelah enroll, kelas akan muncul di ruang belajarmu.</p></div>
    <a href="{{ route('kelas.catalog') }}" class="btn btn-primary-forest">Lihat semua kelas</a>
</section>
<section class="content-section" id="kelas">
    <div class="section-heading">
        <div><span class="eyebrow">KELAS YANG DIMILIKI</span><h2>Ruang belajarmu</h2></div>
        <a href="{{ route('kelas.catalog') }}" class="btn btn-primary-forest">Enroll kelas baru</a>
    </div>
    <div class="topic-grid">
        @forelse ($classrooms as $classroom)
            <article class="topic-card topic-tech">
                <div class="topic-image" role="img" aria-label="Kelas {{ $classroom->name }}"></div>
                <div class="topic-content">
                    <span class="topic-category">{{ strtoupper($classroom->category) }}</span>
                    <h3>{{ $classroom->name }}</h3>
                    <p>{{ \Illuminate\Support\Str::limit($classroom->description, 110) }}</p>
                    <a href="{{ route('kelas.show', $classroom) }}" class="topic-link">Masuk kelas <i class="bi bi-arrow-up-right"></i></a>
                </div>
            </article>
        @empty
            <article class="topic-card topic-design">
                <div class="topic-image" role="img" aria-label="Kelas baru"></div>
                <div class="topic-content">
                    <span class="topic-category">BELUM ADA</span>
                    <h3>Belum ada kelas yang kamu miliki</h3>
                    <p>Daftar kelas tersedia dapat kamu enroll sesuai kebutuhan belajar dan minatmu.</p>
                    <a href="{{ route('kelas.catalog') }}" class="topic-link">Lihat semua kelas <i class="bi bi-arrow-up-right"></i></a>
                </div>
            </article>
        @endforelse
    </div>
</section>
@endsection