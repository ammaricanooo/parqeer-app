<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DebugController extends Controller
{
    public function testUser()
    {
        $user = User::where('username', 'ammar')->first();
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $passwordCheck = Hash::check('password', $user->password);

        return response()->json([
            'user_found' => true,
            'name' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
            'status' => $user->status,
            'password_hash_preview' => substr($user->password, 0, 20) . '...',
            'password_check_with_password' => $passwordCheck,
        ]);
    }
}
