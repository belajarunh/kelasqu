@extends('layouts.guest')

@section('title', 'Buat akun')

@section('content')
<main class="auth-shell">
    <section class="auth-visual" aria-label="Suasana belajar bersama">
        <a class="brand brand-light auth-brand" href="{{ route('login') }}"><span class="brand-mark"><i class="bi bi-layers-half"></i></span><span>Kelas<span class="brand-highlight">Qu</span></span></a>
        <div class="visual-copy"><span class="visual-kicker"><span></span> MULAI DARI SINI</span><h1>Rasa ingin tahu<br>punya rumah baru.</h1><p>Buat akunmu dan buka pintu menuju banyak kemungkinan.</p><div class="visual-credit"><span class="credit-line"></span><span>Mulai dari satu langkah kecil</span></div></div>
        <div class="visual-note"><i class="bi bi-stars"></i><span>Learn at your pace.<br><strong>Grow your way.</strong></span></div>
    </section>
    <section class="auth-form-side">
        <div class="auth-form-wrap register-wrap">
            <div class="mobile-brand brand"><span class="brand-mark"><i class="bi bi-layers-half"></i></span><span>Kelas<span class="brand-highlight">Qu</span></span></div>
            <div class="auth-heading"><span class="eyebrow">AKUN BARU, AWAL BARU</span><h2>Mulai perjalananmu.</h2><p>Isi beberapa detail, lalu jadilah bagian dari KelasQu.</p></div>
            <form method="POST" action="{{ route('register.store') }}" class="auth-form">
                @csrf
                <div class="mb-3"><label class="form-label" for="name">Nama lengkap</label><div class="input-shell"><i class="bi bi-person"></i><input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nama kamu" autocomplete="name" required autofocus></div>@error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label" for="email">Alamat email</label><div class="input-shell"><i class="bi bi-envelope"></i><input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required></div>@error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label" for="password">Kata sandi</label><div class="input-shell"><i class="bi bi-lock"></i><input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" autocomplete="new-password" required></div>@error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                <div class="mb-4"><label class="form-label" for="password_confirmation">Konfirmasi kata sandi</label><div class="input-shell"><i class="bi bi-check2-circle"></i><input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Ulangi kata sandi" autocomplete="new-password" required></div></div>
                <button class="btn btn-primary-forest w-100" type="submit">Buat akun <i class="bi bi-arrow-right"></i></button>
            </form>
            <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
            <div class="auth-footnote"><i class="bi bi-shield-check"></i> Akunmu aman bersama KelasQu</div>
        </div>
    </section>
</main>
@endsection