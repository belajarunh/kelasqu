<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLearnerController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.learners.index', [
            'learners' => User::where('role', 'user')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.learners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:user,admin'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.learners.index')->with('status', 'Data learner berhasil disimpan.');
    }

    public function edit(User $user): View
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.learners.edit', [
            'learner' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:user,admin'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $validated['password'] ?? $user->password,
        ]);

        return redirect()->route('admin.learners.index')->with('status', 'Data learner berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        if (auth()->id() === $user->id) {
            return redirect()->route('admin.learners.index')->with('status', 'Anda tidak dapat menghapus akun admin yang sedang aktif.');
        }

        $user->delete();

        return redirect()->route('admin.learners.index')->with('status', 'Data learner berhasil dihapus.');
    }
}
