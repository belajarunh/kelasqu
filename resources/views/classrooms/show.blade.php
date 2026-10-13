@extends('layouts.dashboard')

@section('title', $classroom->name)
@section('page-title', 'Detail Kelas')
@section('section', 'KELAS')

@section('content')
    <section class="content-section" id="kelas">
        <div class="section-heading">
            <div>
                <span class="eyebrow">DETAIL</span>
                <h2>{{ $classroom->name }}</h2>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('kelas.edit', $classroom) }}" class="btn btn-primary-forest">Edit</a>
                <form action="{{ route('kelas.destroy', $classroom) }}" method="POST" onsubmit="return confirm('Hapus kelas ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Hapus</button>
                </form>
            </div>
        </div>

        <div class="card p-4" style="max-width: 760px; border-radius: 18px; border: 1px solid rgba(24,59,53,0.12); background: #fff;">
            <div class="mb-3">
                <span class="badge bg-success-subtle text-success-emphasis px-3 py-2 rounded-pill">{{ strtoupper($classroom->category) }}</span>
            </div>
            <p class="text-muted mb-3">Status: <strong>{{ ucfirst($classroom->status) }}</strong></p>
            <p style="line-height: 1.8;">{{ $classroom->description }}</p>
        </div>
    </section>
@endsection
