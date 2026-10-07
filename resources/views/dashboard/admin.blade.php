@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')
@section('section', 'ADMIN')
@section('page-title', 'Ringkasan')

@section('content')
<section class="welcome-band admin-welcome">
    <div><span class="welcome-label">PANEL ADMINISTRATOR</span><h2>Halo, {{ auth()->user()->name }} <span>✦</span></h2><p>Pantau komunitas belajar KelasQu dari satu tempat.</p></div>
    <div class="welcome-art"><i class="bi bi-bar-chart-line"></i><span>Ruang kendali<br><strong>KelasQu</strong></span></div>
</section>
<section class="stats-grid" aria-label="Statistik pengguna">
    <article class="stat-card"><div class="stat-top"><span>Total learner</span><span class="stat-icon stat-icon-green"><i class="bi bi-people"></i></span></div><strong class="stat-number">{{ number_format($totalUsers, 0, ',', '.') }}</strong><span class="stat-foot">Pengguna terdaftar</span></article>
    <article class="stat-card"><div class="stat-top"><span>Administrator</span><span class="stat-icon stat-icon-orange"><i class="bi bi-person-gear"></i></span></div><strong class="stat-number">{{ number_format($totalAdmins, 0, ',', '.') }}</strong><span class="stat-foot">Akun dengan akses admin</span></article>
    <article class="stat-card"><div class="stat-top"><span>Learner baru</span><span class="stat-icon stat-icon-lime"><i class="bi bi-person-plus"></i></span></div><strong class="stat-number">{{ number_format($newUsers, 0, ',', '.') }}</strong><span class="stat-foot">Bergabung hari ini</span></article>
</section>
<section class="content-section" id="kelas">
    <div class="section-heading"><div><span class="eyebrow">KOMUNITAS</span><h2>Pengguna terbaru</h2></div><span class="section-count">{{ $totalUsers }} learner</span></div>
    <div class="table-wrap"><table class="table users-table align-middle mb-0"><thead><tr><th>Nama</th><th>Email</th><th>Bergabung</th><th>Status</th></tr></thead><tbody>
        @forelse ($recentUsers as $learner)
            <tr><td><div class="table-person"><span class="avatar avatar-soft">{{ strtoupper(substr($learner->name, 0, 1)) }}</span><strong>{{ $learner->name }}</strong></div></td><td class="muted-cell">{{ $learner->email }}</td><td class="muted-cell">{{ $learner->created_at->format('d M Y') }}</td><td><span class="status-pill"><span></span> Aktif</span></td></tr>
        @empty
            <tr><td colspan="4" class="empty-state">Belum ada learner terdaftar. Pengguna baru akan muncul di sini.</td></tr>
        @endforelse
    </tbody></table></div>
</section>
<section class="admin-tip" id="aktivitas"><i class="bi bi-info-circle"></i><p><strong>Catatan:</strong> akun yang dibuat melalui halaman daftar otomatis mendapat akses learner. Peran admin hanya diberikan melalui konfigurasi seeder.</p></section>
@endsection