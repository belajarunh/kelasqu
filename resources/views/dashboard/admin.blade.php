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
    <article class="stat-card"><div class="stat-top"><span>Jumlah kelas</span><span class="stat-icon stat-icon-blue"><i class="bi bi-journals"></i></span></div><strong class="stat-number">{{ number_format($totalClasses, 0, ',', '.') }}</strong><span class="stat-foot">Kelas aktif di katalog</span></article>
</section>
<section class="content-section" id="pengguna">
    <div class="section-heading"><div><span class="eyebrow">KOMUNITAS</span><h2>Pengguna terbaru</h2></div><a href="{{ route('admin.learners.index') }}" class="btn btn-primary-forest btn-sm">Kelola learner</a></div>
    <div class="table-wrap"><table class="table users-table align-middle mb-0"><thead><tr><th>Nama</th><th>Email</th><th>Bergabung</th><th>Status</th></tr></thead><tbody>
        @forelse ($recentUsers as $learner)
            <tr><td><div class="table-person"><span class="avatar avatar-soft">{{ strtoupper(substr($learner->name, 0, 1)) }}</span><strong>{{ $learner->name }}</strong></div></td><td class="muted-cell">{{ $learner->email }}</td><td class="muted-cell">{{ $learner->created_at->format('d M Y') }}</td><td><span class="status-pill"><span></span> Aktif</span></td></tr>
        @empty
            <tr><td colspan="4" class="empty-state">Belum ada learner terdaftar. Pengguna baru akan muncul di sini.</td></tr>
        @endforelse
    </tbody></table></div>
</section>
<section class="content-section" id="kelas">
    <div class="section-heading"><div><span class="eyebrow">KATALOG</span><h2>Kelas terbaru</h2></div><a href="{{ route('kelas.index') }}" class="btn btn-primary-forest btn-sm">Kelola semua kelas</a></div>
    <div class="table-wrap"><table class="table users-table align-middle mb-0"><thead><tr><th>Nama kelas</th><th>Kategori</th><th>Admin</th><th>Waktu</th></tr></thead><tbody>
        @forelse ($recentClasses as $classroom)
            <tr>
                <td><strong>{{ $classroom->name }}</strong></td>
                <td class="muted-cell">{{ $classroom->category }}</td>
                <td class="muted-cell">{{ $classroom->user->name ?? 'Admin' }}</td>
                <td class="muted-cell">{{ $classroom->created_at->format('d M Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty-state">Belum ada kelas yang ditambahkan.</td></tr>
        @endforelse
    </tbody></table></div>
</section>
<section class="content-section" id="aktivitas">
    <div class="section-heading"><div><span class="eyebrow">AKTIVITAS</span><h2>Aktivitas terbaru</h2></div><span class="section-count">{{ $recentActivities->count() }} event</span></div>
    <div class="table-wrap"><table class="table users-table align-middle mb-0"><thead><tr><th>Aktivitas</th><th>Detail</th><th>Waktu</th></tr></thead><tbody>
        @forelse ($recentActivities as $activity)
            <tr><td><strong>{{ $activity['title'] }}</strong></td><td class="muted-cell">{{ $activity['description'] }}</td><td class="muted-cell">{{ $activity['time'] }}</td></tr>
        @empty
            <tr><td colspan="3" class="empty-state">Belum ada aktivitas baru.</td></tr>
        @endforelse
    </tbody></table></div>
</section>
<section class="admin-tip"><i class="bi bi-info-circle"></i><p><strong>Catatan:</strong> learner dapat mengakses kelas setelah melakukan enrollment. Admin tetap memegang kontrol penuh atas katalog dan data pengguna.</p></section>
@endsection