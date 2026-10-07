<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#183b35">
    <title>@yield('title', 'Dashboard') | KelasQu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/kelasqu.css') }}" rel="stylesheet">
</head>
<body class="dashboard-body">
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand brand-light" href="{{ route('dashboard') }}">
                <span class="brand-mark"><i class="bi bi-layers-half"></i></span>
                <span>Kelas<span class="brand-highlight">Qu</span></span>
            </a>
            <div class="sidebar-caption">MENU UTAMA</div>
            <nav class="sidebar-nav" aria-label="Navigasi utama">
                <a class="sidebar-link active" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
                <a class="sidebar-link" href="{{ route('dashboard') }}#kelas"><i class="bi bi-journals"></i><span>{{ auth()->user()->role === 'admin' ? 'Pengguna' : 'Kelas saya' }}</span></a>
                <a class="sidebar-link" href="{{ route('dashboard') }}#aktivitas"><i class="bi bi-calendar3"></i><span>Aktivitas</span></a>
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-help"><span class="help-icon"><i class="bi bi-lightbulb"></i></span><strong>Terus bertumbuh</strong><p>Belajar sedikit demi sedikit, hasilnya berarti.</p></div>
                <div class="sidebar-profile">
                    <div class="avatar avatar-lime">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Learner' }}</small></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="icon-button logout-button" type="submit" aria-label="Keluar" title="Keluar"><i class="bi bi-box-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </aside>
        <main class="main-panel">
            <header class="topbar">
                <div class="topbar-context"><span class="eyebrow">KELASQU / @yield('section', 'DASHBOARD')</span><h1>@yield('page-title', 'Dashboard')</h1></div>
                <div class="topbar-right"><span class="today-label"><i class="bi bi-calendar-event"></i> {{ now()->locale('id')->translatedFormat('l, d F Y') }}</span><div class="avatar avatar-forest">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div></div>
            </header>
            <div class="page-content">
                @if (session('status'))
                    <div class="alert alert-success border-0 rounded-3">{{ session('status') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>