<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (auth()->user()->role == 'pegawai') {
            abort(403, 'Anda tidak diperbolehkan mengakses halaman ini');
        }

        // Mengatur default filter untuk menampilkan hanya akun yang tidak terhapus
        $query = User::where('is_deleted', false);

        // Filter berdasarkan pencarian
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan status akun
        if ($request->has('filter')) {
            if ($request->filter == 'inactive') {
                $query = User::where('is_deleted', true);
            }
        }

        // Paginasi dan ambil data user terbaru
        $users = $query->latest()->paginate(9)->withQueryString();

        return view('dashboard.users.index', [
            'title' => 'User',
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->role == 'pegawai') {
            abort(403, 'Anda tidak diperbolehkan mengakses halaman ini');
        }
        return view('dashboard.users.create', [
            'title' => 'Create User',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Request data:', $request->all());
    
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:admin,pegawai',
            'password' => 'required|string|min:8',
        ]);
    
        // Logging data yang divalidasi
        \Log::info('Validated data:', $validatedData);
    
        // Simpan data user baru ke database
        User::create([
            'name' => $validatedData['name'],
            'position' => $validatedData['position'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'password' => Hash::make($validatedData['password']),
        ]);
    
        return redirect('/dashboard/users')->with('success', 'User berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (auth()->user()->role == 'pegawai') {
            abort(403, 'Anda tidak diperbolehkan mengakses halaman ini');
        }
        $roles = ['Admin', 'Pegawai'];
        return view('dashboard.users.edit', [
            'title' => 'Edit Akun',
            'user' => $user,
            'roles' => $roles, // Menambahkan variabel $roles ke dalam array data yang dilewatkan ke view
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validasi input dari pengguna
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'position' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|string|in:admin,pegawai', // Menambahkan validasi untuk peran
        ]);

        // Update user data
        $user->name = $validatedData['name'];
        $user->position = $validatedData['position'];
        $user->email = $validatedData['email'];
        $user->role = $validatedData['role'];

        $user->save();

        // Mengarahkan pengguna kembali ke halaman yang sesuai
        return redirect('/dashboard/users')->with('success', 'User has been updated successfully!');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request, User $user)
    {
        // Validasi input dari pengguna
        $validatedData = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verifikasi password saat ini
        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai'])->withInput();
        }

        // Perbarui password
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        // Mengarahkan pengguna kembali ke halaman yang sesuai
        return redirect('/dashboard/users')->with('success', 'Password has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->update(['is_deleted' => 1]);

        return redirect('/dashboard/users')->with('success', 'Akun Telah Dihapus!');
    }

    public function restore(User $user)
    {
        $user->update(['is_deleted' => 0]);
        return redirect('/dashboard/users')->with('success', 'Akun berhasil diaktifkan kembali!');
    }
}