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
    <div><span class="eyebrow">LANGKAH BERIKUTNYA</span><h2>Temukan kelas pertamamu</h2><p>Ruang belajarmu sudah siap. Jelajahi kelas yang sesuai dengan minatmu dan mulai kapan saja.</p></div>
    <span class="summary-status"><i class="bi bi-check2-circle"></i> Akun aktif</span>
</section>
<section class="content-section" id="kelas">
    <div class="section-heading"><div><span class="eyebrow">MULAI DARI MINATMU</span><h2>Jelajahi topik belajar</h2></div></div>
    <div class="topic-grid">
        <article class="topic-card topic-design"><div class="topic-image" role="img" aria-label="Meja kerja kreatif dengan laptop dan buku"></div><div class="topic-content"><span class="topic-category">KREATIVITAS</span><h3>Desain & visual</h3><p>Bangun cara pandang kreatif dan wujudkan ide menjadi karya.</p><span class="topic-link">Segera hadir <i class="bi bi-arrow-up-right"></i></span></div></article>
        <article class="topic-card topic-tech"><div class="topic-image" role="img" aria-label="Suasana belajar teknologi di depan laptop"></div><div class="topic-content"><span class="topic-category">TEKNOLOGI</span><h3>Teknologi digital</h3><p>Kenali keterampilan digital baru untuk masa depan.</p><span class="topic-link">Segera hadir <i class="bi bi-arrow-up-right"></i></span></div></article>
        <article class="topic-card topic-business"><div class="topic-image" role="img" aria-label="Catatan perencanaan dan alat tulis di atas meja"></div><div class="topic-content"><span class="topic-category">PENGEMBANGAN DIRI</span><h3>Ide & produktivitas</h3><p>Ubah rasa ingin tahu menjadi kebiasaan belajar yang baik.</p><span class="topic-link">Segera hadir <i class="bi bi-arrow-up-right"></i></span></div></article>
    </div>
</section>
@endsection