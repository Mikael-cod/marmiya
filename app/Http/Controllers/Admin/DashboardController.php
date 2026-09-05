<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalUsers' => User::query()->count(),
            'adminCount' => User::query()->where('role', UserRole::Admin)->count(),
            'userCount' => User::query()->where('role', UserRole::User)->count(),
        ]);
    }
}
