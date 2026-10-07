<?php

namespace App\Http\Controllers;

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

            return view('dashboard.admin', [
                'totalUsers' => User::where('role', 'user')->count(),
                'totalAdmins' => User::where('role', 'admin')->count(),
                'newUsers' => User::where('role', 'user')->whereDate('created_at', today())->count(),
                'recentUsers' => User::where('role', 'user')->latest()->take(6)->get(),
            ]);
        }

        return view('dashboard.user');
    }
}