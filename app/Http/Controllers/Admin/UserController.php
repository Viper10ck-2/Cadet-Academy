<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('nip_nis', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'nip_nis' => 'nullable|string|max:30|unique:users',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'role' => 'required|in:admin,instructor,cadet',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('status', 'User berhasil ditambahkan!');
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'nip_nis' => 'nullable|string|max:30|unique:users,nip_nis,' . $user->id,
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'role' => 'required|in:admin,instructor,cadet',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);
        $user->syncRoles($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('status', 'User berhasil diperbarui!');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('status', 'User berhasil dihapus!');
    }
}
