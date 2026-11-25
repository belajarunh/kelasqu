@extends('layouts.dashboard')

@section('title', $classroom->name)
@section('page-title', 'Detail Kelas')
@section('section', 'KELAS')

@section('content')
    <section class="content-section" id="kelas">
        <div class="section-heading">
            <div>
                <span class="eyebrow">DETAIL KELAS</span>
                <h2>{{ $classroom->name }}</h2>
            </div>
            @if (auth()->user()->role === 'admin')
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('kelas.edit', $classroom) }}" class="btn btn-primary-forest">Edit kelas</a>
                    <form action="{{ route('kelas.destroy', $classroom) }}" method="POST" onsubmit="return confirm('Hapus kelas ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">Hapus kelas</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="card p-4 mb-4" style="max-width: 900px; border-radius: 18px; border: 1px solid rgba(24,59,53,0.12); background: #fff;">
            <div class="mb-3">
                <span class="badge bg-success-subtle text-success-emphasis px-3 py-2 rounded-pill">{{ strtoupper($classroom->category) }}</span>
            </div>
            <p class="text-muted mb-3">Status: <strong>{{ ucfirst($classroom->status) }}</strong></p>
            <p class="mb-0" style="line-height: 1.8;">{{ $classroom->description }}</p>
        </div>

        <div class="section-heading mt-5">
            <div>
                <span class="eyebrow">KURIKULUM KELAS</span>
                <h2>Pertemuan belajar</h2>
                @if (auth()->user()->role !== 'admin')
                    <p class="text-muted mb-0">
                        {{ count($completedLessonIds) }} dari {{ $classroom->lessons->count() }} pertemuan selesai
                        @if ($classroom->lessons->isNotEmpty())
                            ({{ (int) round(count($completedLessonIds) / $classroom->lessons->count() * 100) }}%)
                        @endif
                    </p>
                @endif
            </div>
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('kelas.pertemuan.create', $classroom) }}" class="btn btn-primary-forest">
                    <i class="bi bi-plus-lg"></i> Tambah pertemuan
                </a>
            @endif
        </div>

        @forelse ($classroom->lessons as $lesson)
            @php
                $isCompleted = in_array($lesson->id, $completedLessonIds, true);
                $bestAttempt = $bestQuizAttempts->get($lesson->id);
                $quizResult = session('quiz_result');
            @endphp
            <details class="card mb-3 border rounded-4 overflow-hidden" @if ($loop->first) open @endif>
                <summary class="card-header d-flex align-items-center justify-content-between gap-3 py-3" style="cursor: pointer;">
                    <span class="d-flex align-items-center gap-3">
                        <span class="badge rounded-pill text-bg-success">Pertemuan {{ $lesson->position }}</span>
                        <strong>{{ $lesson->title }}</strong>
                    </span>
                    @if (auth()->user()->role !== 'admin' && $isCompleted)
                        <span class="badge bg-success-subtle text-success-emphasis">Selesai</span>
                    @endif
                </summary>
                <div class="card-body">
                    @if (auth()->user()->role === 'admin')
                        <div class="d-flex gap-2 flex-wrap mb-4">
                            <a href="{{ route('kelas.pertemuan.edit', [$classroom, $lesson]) }}" class="btn btn-sm btn-outline-success">Kelola materi & kuis</a>
                            <form action="{{ route('kelas.pertemuan.destroy', [$classroom, $lesson]) }}" method="POST" onsubmit="return confirm('Hapus pertemuan ini beserta kuis dan progres peserta?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus pertemuan</button>
                            </form>
                        </div>
                    @endif

                    @if ($lesson->content)
                        <section class="mb-4">
                            <h3 class="h6">Materi</h3>
                            <div style="white-space: pre-wrap; line-height: 1.8;">{{ $lesson->content }}</div>
                        </section>
                    @endif

                    @if ($lesson->video_url)
                        <section class="mb-4">
                            <h3 class="h6">Video pembelajaran</h3>
                            @if ($lesson->getVideoEmbedUrl())
                                <div class="ratio ratio-16x9 mb-2" style="max-width: 800px;">
                                    <iframe src="{{ $lesson->getVideoEmbedUrl() }}" title="Video: {{ $lesson->title }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                </div>
                            @endif
                            <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener noreferrer">Buka tautan video <i class="bi bi-box-arrow-up-right"></i></a>
                        </section>
                    @endif

                    @if ($lesson->attachment_path)
                        <section class="mb-4">
                            <h3 class="h6">Lampiran</h3>
                            <a href="{{ route('kelas.pertemuan.attachment', [$classroom, $lesson]) }}">
                                <i class="bi bi-paperclip"></i> {{ $lesson->attachment_name }}
                            </a>
                        </section>
                    @endif

                    @if ($lesson->assignment)
                        <section class="mb-4">
                            <h3 class="h6">Tugas</h3>
                            <div style="white-space: pre-wrap; line-height: 1.8;">{{ $lesson->assignment }}</div>
                        </section>
                    @endif

                    @if (auth()->user()->role === 'admin')
                        <p class="text-muted mb-0">{{ $lesson->questions->count() }} soal kuis · Nilai lulus {{ $lesson->quiz_pass_percentage }}%</p>
                    @elseif ($lesson->questions->isNotEmpty())
                        <section class="border-top pt-4 mt-4">
                            <h3 class="h5">Kuis pertemuan</h3>
                            <p class="text-muted">Nilai lulus: {{ $lesson->quiz_pass_percentage }}%. Percobaan tidak dibatasi; nilai terbaik akan disimpan.</p>

                            @if ($bestAttempt)
                                <div class="alert {{ $bestAttempt->passed ? 'alert-success' : 'alert-info' }} border-0">
                                    Nilai terbaik: <strong>{{ $bestAttempt->score_percentage }}%</strong>
                                    ({{ $bestAttempt->correct_answers }}/{{ $bestAttempt->total_questions }} benar) —
                                    {{ $bestAttempt->passed ? 'Lulus' : 'Belum lulus' }}
                                </div>
                            @endif

                            @if (is_array($quizResult) && $quizResult['lesson_id'] === $lesson->id)
                                <div class="alert {{ $quizResult['passed'] ? 'alert-success' : 'alert-warning' }} border-0">
                                    Percobaan ini mendapat {{ $quizResult['score'] }}%.
                                    {{ $quizResult['passed'] ? 'Kuis lulus.' : 'Silakan pelajari materi dan coba lagi.' }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('kelas.pertemuan.quiz', [$classroom, $lesson]) }}">
                                @csrf
                                @foreach ($lesson->questions as $question)
                                    <fieldset class="mb-4">
                                        <legend class="h6">{{ $loop->iteration }}. {{ $question->prompt }}</legend>
                                        @foreach ($question->options as $optionIndex => $option)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="answer-{{ $question->id }}-{{ $optionIndex }}" value="{{ $optionIndex }}" @checked(old('answers.'.$question->id) == $optionIndex) required>
                                                <label class="form-check-label" for="answer-{{ $question->id }}-{{ $optionIndex }}">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                    </fieldset>
                                @endforeach
                                @error('answers')
                                    <div class="text-danger mb-3">{{ $message }}</div>
                                @enderror
                                <button class="btn btn-primary-forest" type="submit">Kirim jawaban</button>
                            </form>
                        </section>
                    @endif

                    @if (auth()->user()->role !== 'admin')
                        <div class="border-top pt-3 mt-4">
                            @if ($isCompleted)
                                <form method="POST" action="{{ route('kelas.pertemuan.uncomplete', [$classroom, $lesson]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-success" type="submit"><i class="bi bi-check-circle"></i> Selesai — batalkan</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('kelas.pertemuan.complete', [$classroom, $lesson]) }}">
                                    @csrf
                                    <button class="btn btn-primary-forest" type="submit">Tandai pertemuan selesai</button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </details>
        @empty
            <div class="card border rounded-4 p-4">
                <p class="text-muted mb-0">
                    @if (auth()->user()->role === 'admin')
                        Belum ada pertemuan. Tambahkan pertemuan pertama untuk mulai menyusun kurikulum kelas.
                    @else
                        Materi pertemuan untuk kelas ini belum tersedia.
                    @endif
                </p>
            </div>
        @endforelse
    </section>
@endsection
