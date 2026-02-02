<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::query();
        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%')
                ->orWhere('username', 'like', '%' . request('search') . '%');
        }
        $users = $query->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string',
            'role' => 'required|in:admin,petugas,owner',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '-' . Str::slug($request->username) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('users', $filename, 'public');

            // Simpan data produk ke database
            User::create([
                'photo' => $filename,
                'name' => $request->name,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'role' => $request->role,
                'status' => $request->status
            ]);
        }
        return redirect('/admin/users')->with('success', 'Data user berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,petugas,owner',
            'status' => 'required|in:active,inactive',
        ];

        $validatedData = $request->validate($rules);

        // Kalau ada foto
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '-' . Str::slug($request->username) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('users', $filename, 'public');
            $validatedData['photo'] = $filename;
        }

        if ($request->filled('password')) {
            $validatedData['password'] = bcrypt($request->password);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        return redirect('/admin/users')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->photo) {
            Storage::delete('users/' . $user->photo);
        }

        // Hapus post dari database
        $user->delete();
        return redirect('/admin/users')->with('success', 'Data user berhasil dihapus!');
    }

    public function exportExcel()
    {
        return Excel::download(new UsersExport, "data_user_parqeer.xlsx");
    }
}
