<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * Display all users with search/filter
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->paginate(15);

        // Get role counts for stats
        $customers = User::whereHas('roles', function ($q) {
            $q->where('name', 'customer');
        })->count();

        $vendors = User::whereHas('roles', function ($q) {
            $q->where('name', 'vendor');
        })->count();

        $admins = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })->count();

        return view('admin.users.index', compact('users', 'customers', 'vendors', 'admins'));
    }
}
