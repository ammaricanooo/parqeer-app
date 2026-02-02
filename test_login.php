<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Test user lookup
$user = \App\Models\User::where('username', 'ammar')->first();

echo "=== User Lookup ===\n";
if ($user) {
    echo "User found: " . $user->name . "\n";
    echo "Username: " . $user->username . "\n";
    echo "Password hash: " . substr($user->password, 0, 20) . "...\n";
    
    // Test password verification
    $password = 'password';
    $isValid = \Illuminate\Support\Facades\Hash::check($password, $user->password);
    echo "Password 'password' valid: " . ($isValid ? 'YES' : 'NO') . "\n";
} else {
    echo "User NOT found!\n";
    echo "Total users in DB: " . \App\Models\User::count() . "\n";
    \App\Models\User::all()->each(function($u) {
        echo "- " . $u->username . "\n";
    });
}
