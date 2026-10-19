<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 422);
        }

        if (!$user->active) {
            return response()->json([
                'success' => false,
                'message' => 'This account has been deactivated. Please contact workshop administration.',
            ], 403);
        }

        Auth::login($user, $request->boolean('remember'));

        return response()->json([
            'success' => true,
            'user' => $user,
            'role' => $user->role,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:50',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'avatar_initials' => User::getInitials($request->name),
            'active' => true,
            'bio_meta' => 'Customer since ' . date('M Y'),
        ]);

        Auth::login($user);

        return response()->json([
            'success' => true,
            'user' => $user,
            'role' => 'customer',
        ]);
    }

    public function previewLogin(Request $request)
    {
        $role = $request->input('role', 'customer');
        $user = User::where('role', $role)->where('active', true)->first();

        if ($user) {
            Auth::login($user);
            return response()->json([
                'success' => true,
                'user' => $user,
                'role' => $user->role,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "No active {$role} found.",
        ], 404);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['success' => true]);
    }

    public function currentUser()
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
        ]);
    }
}
