<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Acesso negado.'
            ], 403);
        }

        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($users);
    }

    public function updateRole(Request $request, string $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Acesso negado.'
            ], 403);
        }

        $request->validate([
            'role' => 'required|in:admin,editor,viewer',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'Permissão atualizada com sucesso.',
            'user' => $user,
        ]);
    }
}