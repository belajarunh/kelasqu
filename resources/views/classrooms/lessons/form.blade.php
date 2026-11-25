@extends('layouts.dashboard')

@section('title', $lesson ? 'Kelola Pertemuan' : 'Tambah Pertemuan')
@section('page-title', $lesson ? 'Kelola Pertemuan' : 'Tambah Pertemuan')
@section('section', 'KELAS')

@section('content')
    <section class="content-section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">{{ $classroom->name }}</span>
                <h2>{{ $lesson ? 'Perbarui materi pertemuan' : 'Susun pertemuan baru' }}</h2>
            </div>
        </div>

        <form method="POST" action="{{ $lesson ? route('kelas.pertemuan.update', [$classroom, $lesson]) : route('kelas.pertemuan.store', $classroom) }}" enctype="multipart/form-data" class="auth-form card p-4 mb-5" style="max-width: 900px;">
            @csrf
            @if ($lesson)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="title">Judul pertemuan</label>
                    <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $lesson?->title) }}" maxlength="160" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="position">Urutan pertemuan</label>
                    <input class="form-control @error('position') is-invalid @enderror" id="position" name="position" type="number" min="1" max="65535" value="{{ old('position', $nextPosition) }}" required>
                    @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label" for="content">Materi teks</label>
                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="7" maxlength="50000" placeholder="Tulis materi dan penjelasan pertemuan...">{{ old('content', $lesson?->content) }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mt-3">
                <label class="form-label" for="video_url">URL video (YouTube atau Vimeo bisa ditampilkan langsung)</label>
                <input class="form-control @error('video_url') is-invalid @enderror" id="video_url" name="video_url" type="url" value="{{ old('video_url', $lesson?->video_url) }}" placeholder="https://...">
                @error('video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mt-3">
                <label class="form-label" for="attachment">Lampiran (PDF, dokumen, presentasi, gambar, atau ZIP; maks. 20 MB)</label>
                <input class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.png,.jpg,.jpeg,.webp,.txt">
                @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if ($lesson?->attachment_path)
                    <div class="form-check mt-2">
                        <input class="form-check-input" id="remove_attachment" name="remove_attachment" type="checkbox" value="1">
                        <label class="form-check-label" for="remove_attachment">Hapus lampiran saat ini: {{ $lesson->attachment_name }}</label>
                    </div>
                @endif
            </div>

            <div class="mt-3">
                <label class="form-label" for="assignment">Instruksi tugas</label>
                <textarea class="form-control @error('assignment') is-invalid @enderror" id="assignment" name="assignment" rows="4" maxlength="50000" placeholder="Tuliskan instruksi atau tautan tugas...">{{ old('assignment', $lesson?->assignment) }}</textarea>
                @error('assignment')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mt-3">
                <label class="form-label" for="quiz_pass_percentage">Nilai minimum lulus kuis (%)</label>
                <input class="form-control @error('quiz_pass_percentage') is-invalid @enderror" id="quiz_pass_percentage" name="quiz_pass_percentage" type="number" min="1" max="100" value="{{ old('quiz_pass_percentage', $lesson?->quiz_pass_percentage ?? 75) }}" required>
                @error('quiz_pass_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Peserta dapat mencoba kuis tanpa batas; sistem menyimpan nilai terbaik.</div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary-forest" type="submit">{{ $lesson ? 'Simpan perubahan' : 'Buat pertemuan' }}</button>
                <a class="btn btn-outline-secondary" href="{{ route('kelas.show', $classroom) }}">Kembali ke kelas</a>
            </div>
        </form>

        @if ($lesson)
            <section style="max-width: 900px;">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">KUIS PILIHAN GANDA</span>
                        <h2>Soal pertemuan</h2>
                    </div>
                </div>

                @forelse ($lesson->questions as $question)
                    <div class="card border rounded-4 p-4 mb-3">
                        <form method="POST" action="{{ route('kelas.pertemuan.questions.update', [$classroom, $lesson, $question]) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label" for="prompt-{{ $question->id }}">Pertanyaan</label>
                                <textarea class="form-control @error('prompt') is-invalid @enderror" id="prompt-{{ $question->id }}" name="prompt" rows="2" required>{{ old('prompt', $question->prompt) }}</textarea>
                                @error('prompt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="row g-2">
                                @foreach (range(0, 3) as $optionIndex)
                                    <div class="col-md-6">
                                        <label class="form-label" for="question-{{ $question->id }}-option-{{ $optionIndex }}">Pilihan {{ chr(65 + $optionIndex) }}</label>
                                        <input class="form-control @error('options.'.$optionIndex) is-invalid @enderror" id="question-{{ $question->id }}-option-{{ $optionIndex }}" name="options[{{ $optionIndex }}]" value="{{ old('options.'.$optionIndex, $question->options[$optionIndex] ?? '') }}" maxlength="500" required>
                                        @error('options.'.$optionIndex)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <label class="form-label" for="correct-{{ $question->id }}">Jawaban benar</label>
                                <select class="form-select" id="correct-{{ $question->id }}" name="correct_option" required>
                                    @foreach (range(0, 3) as $optionIndex)
                                        <option value="{{ $optionIndex }}" @selected((int) old('correct_option', $question->correct_option) === $optionIndex)>Pilihan {{ chr(65 + $optionIndex) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-sm btn-primary-forest mt-3" type="submit">Simpan soal</button>
                        </form>
                        <form class="mt-2" method="POST" action="{{ route('kelas.pertemuan.questions.destroy', [$classroom, $lesson, $question]) }}" onsubmit="return confirm('Hapus soal ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus soal</button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted">Belum ada soal. Tambahkan soal pilihan ganda untuk membuat kuis pertemuan.</p>
                @endforelse

                <form method="POST" action="{{ route('kelas.pertemuan.questions.store', [$classroom, $lesson]) }}" class="card border rounded-4 p-4">
                    @csrf
                    <h3 class="h5">Tambah soal</h3>
                    <div class="mb-3">
                        <label class="form-label" for="new-prompt">Pertanyaan</label>
                        <textarea class="form-control @error('prompt') is-invalid @enderror" id="new-prompt" name="prompt" rows="2" required>{{ old('prompt') }}</textarea>
                        @error('prompt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-2">
                        @foreach (range(0, 3) as $optionIndex)
                            <div class="col-md-6">
                                <label class="form-label" for="new-option-{{ $optionIndex }}">Pilihan {{ chr(65 + $optionIndex) }}</label>
                                <input class="form-control @error('options.'.$optionIndex) is-invalid @enderror" id="new-option-{{ $optionIndex }}" name="options[{{ $optionIndex }}]" value="{{ old('options.'.$optionIndex) }}" maxlength="500" required>
                                @error('options.'.$optionIndex)<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="new-correct">Jawaban benar</label>
                        <select class="form-select" id="new-correct" name="correct_option" required>
                            @foreach (range(0, 3) as $optionIndex)
                                <option value="{{ $optionIndex }}" @selected((int) old('correct_option', 0) === $optionIndex)>Pilihan {{ chr(65 + $optionIndex) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary-forest mt-3 align-self-start" type="submit">Tambah soal</button>
                </form>
            </section>
        @else
            <div class="alert alert-info border-0" style="max-width: 900px;">Simpan pertemuan terlebih dahulu untuk menambahkan soal kuis.</div>
        @endif
    </section>
@endsection
