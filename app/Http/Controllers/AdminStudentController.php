<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'student');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show(User $student)
    {
        return redirect()->route('admin.students.index');
    }

    public function edit(User $student)
    {
        if ($student->role !== 'student') {
            return redirect()->route('admin.students.index')
                ->with('error', 'Akses ditolak.');
        }

        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        if ($student->role !== 'student') {
            return redirect()->route('admin.students.index')
                ->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $student->name = $request->name;
        $student->email = $request->email;

        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
        }

        $student->save();

        return redirect()->route('admin.students.index')
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(User $student)
    {
        if ($student->role !== 'student') {
            return redirect()->route('admin.students.index')
                ->with('error', 'Akses ditolak.');
        }

        $hasActive = $student->borrowings()
            ->whereIn('status', ['pending', 'approved', 'return_requested'])
            ->exists();

        if ($hasActive) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Mahasiswa tidak dapat dihapus karena memiliki transaksi peminjaman aktif.');
        }

        // Delete past completed/rejected borrowings to clean up
        $student->borrowings()->delete();
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
