@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<main class="auth-shell">
    <section class="auth-visual" aria-label="Suasana belajar bersama">
        <a class="brand brand-light auth-brand" href="{{ route('login') }}"><span class="brand-mark"><i class="bi bi-layers-half"></i></span><span>Kelas<span class="brand-highlight">Qu</span></span></a>
        <div class="visual-copy"><span class="visual-kicker"><span></span> RUANG UNTUK BERTUMBUH</span><h1>Belajar hari ini,<br>lebih siap esok hari.</h1><p>Temukan kelas yang membuat rasa ingin tahumu terus hidup.</p><div class="visual-credit"><span class="credit-line"></span><span>Belajar dengan ritmemu sendiri</span></div></div>
        <div class="visual-note"><i class="bi bi-stars"></i><span>Small steps.<br><strong>Big possibilities.</strong></span></div>
    </section>
    <section class="auth-form-side">
        <div class="auth-form-wrap">
            <div class="mobile-brand brand"><span class="brand-mark"><i class="bi bi-layers-half"></i></span><span>Kelas<span class="brand-highlight">Qu</span></span></div>
            <div class="auth-heading"><span class="eyebrow">SELAMAT DATANG KEMBALI</span><h2>Senang melihatmu lagi.</h2><p>Masuk untuk melanjutkan perjalanan belajarmu.</p></div>
            <form method="POST" action="{{ route('login.store') }}" class="auth-form">
                @csrf
                <div class="mb-3"><label class="form-label" for="email">Alamat email</label><div class="input-shell"><i class="bi bi-envelope"></i><input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required autofocus></div>@error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><div class="label-row"><label class="form-label" for="password">Kata sandi</label></div><div class="input-shell"><i class="bi bi-lock"></i><input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi" autocomplete="current-password" required></div>@error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label" id="captcha-question" for="captcha_answer">Verifikasi: {{ $captchaQuestion }}</label><div class="input-shell"><i class="bi bi-shield-check"></i><input id="captcha_answer" name="captcha_answer" type="number" class="form-control @error('captcha_answer') is-invalid @enderror" aria-describedby="captcha-question" placeholder="Jawaban" inputmode="numeric" required></div>@error('captcha_answer')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <label class="remember-row"><input type="checkbox" name="remember" value="1"><span>Ingat saya di perangkat ini</span></label>
                <button class="btn btn-primary-forest w-100" type="submit">Masuk ke KelasQu <i class="bi bi-arrow-right"></i></button>
            </form>
            <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
            <div class="auth-footnote"><i class="bi bi-shield-check"></i> Akunmu aman bersama KelasQu</div>
        </div>
    </section>
</main>
@endsection