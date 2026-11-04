<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(8)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,customer',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', "Kata sandi untuk {$user->name} berhasil di-reset.");
    }
}
