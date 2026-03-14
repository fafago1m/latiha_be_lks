<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('manage-approval')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $users = User::with('umkmProfile')->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diambil',
            'data' => $users
        ]);
    }


    public function update(Request $request, $id)
    {
        if (!Gate::allows('manage-approval')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,approver,umkm',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diperbarui!',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        if (!Gate::allows('manage-approval')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $user = User::findOrFail($id);
        

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus (Soft Delete).'
        ]);
    }
}
