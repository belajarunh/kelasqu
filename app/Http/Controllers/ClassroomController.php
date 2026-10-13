<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClassroomController extends Controller
{
    public function index(): View
    {
        $classrooms = auth()->user()->role === 'admin'
            ? Classroom::with('user')->latest()->get()
            : auth()->user()->enrolledClassrooms()->with('user')->latest()->get();

        return view('classrooms.index', [
            'classrooms' => $classrooms,
        ]);
    }

    public function catalog(): View
    {
        return view('classrooms.catalog', [
            'classrooms' => Classroom::with('user')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('classrooms.create');
    }

    public function show(Classroom $classroom): View
    {
        if (auth()->user()->role !== 'admin' && ! $classroom->isEnrolledFor(auth()->user())) {
            abort(403);
        }

        $classroom->load('user');

        return view('classrooms.show', [
            'classroom' => $classroom,
        ]);
    }

    public function edit(Classroom $classroom): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('classrooms.edit', [
            'classroom' => $classroom,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:2000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $classroom = Classroom::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? 'Kelas baru dibuat oleh administrator.',
            'status' => 'draft',
            'cover_image' => $this->storeCoverImage($request),
        ]);

        if ($classroom->cover_image) {
            $classroom->save();
        }

        return redirect()->route('kelas.index')->with('status', 'Kelas berhasil dibuat.');
    }

    public function update(Request $request, Classroom $classroom): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:2000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $coverImage = $this->storeCoverImage($request);

        $classroom->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? $classroom->description,
            'cover_image' => $coverImage ?? $classroom->cover_image,
        ]);

        return redirect()->route('kelas.show', $classroom)->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $classroom->delete();

        return redirect()->route('kelas.index')->with('status', 'Kelas berhasil dihapus.');
    }

    public function enroll(Classroom $classroom): RedirectResponse
    {
        if (auth()->user()->role === 'admin') {
            abort(403, 'Admin tidak perlu melakukan enroll kelas.');
        }

        $classroom->students()->syncWithoutDetaching([auth()->id()]);

        return redirect()->route('kelas.catalog')->with('status', 'Kelas berhasil ditambahkan ke ruang belajarmu.');
    }

    public function unenroll(Classroom $classroom): RedirectResponse
    {
        if (auth()->user()->role === 'admin') {
            abort(403, 'Admin tidak dapat menghapus enrollment kelas.');
        }

        $classroom->students()->detach(auth()->id());

        return redirect()->route('kelas.index')->with('status', 'Kelas berhasil dihapus dari ruang belajarmu.');
    }

    protected function storeCoverImage(Request $request): ?string
    {
        if (! $request->hasFile('cover_image')) {
            return null;
        }

        return $request->file('cover_image')->store('classroom-covers', 'public');
    }
}
