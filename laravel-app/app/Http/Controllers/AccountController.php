<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Customer,Technician,Admin,customer,technician,admin',
        ]);

        $role = strtolower($request->role);
        $bioMeta = match ($role) {
            'customer' => '0 repairs',
            'technician' => '0 active',
            'admin' => 'Administrator',
            default => '',
        };

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'avatar_initials' => User::getInitials($request->name),
            'active' => true,
            'technician_capacity' => $role === 'technician' ? 3 : 0,
            'technician_title' => $role === 'technician' ? 'Repair technician' : ($role === 'admin' ? 'Administrator' : null),
            'bio_meta' => $bioMeta,
        ]);

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
            'active' => 'required|boolean',
        ]);

        $user->name = $request->name;
        $user->avatar_initials = User::getInitials($request->name);
        $user->active = $request->active;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            // fallback to first customer if unauthenticated in preview
            $user = User::first();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->avatar_initials = User::getInitials($request->name);
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('email') && $user->role !== 'customer') $user->email = $request->email;
        $user->save();

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }
}
