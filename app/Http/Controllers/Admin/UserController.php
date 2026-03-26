<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.manage');
    }

    public function index()
    {
        $users = User::with('roles')->latest()->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)],
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Usuario creado exitosamente',
                'user'    => $this->mapUser($user->load('roles')),
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', Password::min(8)],
            'role'     => 'required|exists:roles,name',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Usuario actualizado exitosamente',
                'user'    => $this->mapUser($user->fresh()->load('roles')),
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(Request $request, User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No puedes eliminar tu propia cuenta'], 403);
            }
            return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propia cuenta');
        }

        $user->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Usuario eliminado exitosamente']);
        }

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente');
    }

    private function mapUser(User $user): array
    {
        return [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'role'     => $user->getRoleNames()->first(),
            'initials' => $user->initials,
            'created_at' => $user->created_at?->format('d/m/Y'),
        ];
    }
}
