<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Accept both English headings (name) and template headings in Indonesian (nama)
        $name = $row['name'] ?? $row['nama'] ?? null;
        $username = $row['username'] ?? null;

        if (!$name || !$username) {
            return null; // skip invalid rows
        }

        $role = strtolower(trim($row['role'] ?? 'attendant'));
        $allowedRoles = ['admin', 'attendant', 'owner'];
        if (!in_array($role, $allowedRoles)) {
            $role = 'attendant';
        }

        $status = strtolower(trim($row['status'] ?? 'active'));
        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        // Skip if username already exists
        if (User::where('username', $username)->exists()) {
            return null;
        }

        return new User([
            'name' => $name,
            'username' => $username,
            'role' => $role,
            'status' => $status,
            // User model casts password to hashed automatically
            'password' => 'admin1234#',
        ]);
    }

    public function rules(): array
    {
        return [
            '*.username' => 'required|string',
            '*.name' => 'required|string',
        ];
    }
}
