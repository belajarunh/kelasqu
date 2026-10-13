<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            abort_unless($user->role === 'admin', 403);

            $recentUsers = User::where('role', 'user')->latest()->take(6)->get();
            $recentClasses = Classroom::with('user')->latest()->take(6)->get();

            $recentActivities = collect();

            foreach ($recentUsers as $userEntry) {
                $recentActivities->push([
                    'title' => 'Pengguna baru terdaftar',
                    'description' => $userEntry->name . ' bergabung dengan akun learner.',
                    'time' => $userEntry->created_at->diffForHumans(),
                    'created_at' => $userEntry->created_at,
                ]);
            }

            foreach ($recentClasses as $classroom) {
                $recentActivities->push([
                    'title' => 'Kelas dibuat',
                    'description' => $classroom->user->name . ' menambahkan kelas "' . $classroom->name . '".',
                    'time' => $classroom->created_at->diffForHumans(),
                    'created_at' => $classroom->created_at,
                ]);
            }

            return view('dashboard.admin', [
                'totalUsers' => User::where('role', 'user')->count(),
                'totalAdmins' => User::where('role', 'admin')->count(),
                'newUsers' => User::where('role', 'user')->whereDate('created_at', today())->count(),
                'totalClasses' => Classroom::count(),
                'recentUsers' => $recentUsers,
                'recentClasses' => $recentClasses,
                'recentActivities' => $recentActivities->sortByDesc('created_at')->values(),
            ]);
        }

        return view('dashboard.user', [
            'classrooms' => $user->enrolledClassrooms()->with('user')->latest()->get(),
            'catalog' => Classroom::with('user')->latest()->take(6)->get(),
        ]);
    }
}