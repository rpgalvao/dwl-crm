<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        // Trava de Segurança: Se o cargo não for admin e nem supervisor, toma erro 403 (Acesso Negado)
        abort_if(! in_array(auth()->user()->role, ['admin', 'supervisor']), 403, 'Acesso negado.');

        // Puxa todos os usuários em ordem alfabética
        $users = User::orderBy('name')->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        // Bloqueio extra de segurança no backend: Apenas Admin pode alterar cargos
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        $request->validate([
            'role' => 'required|string|in:admin,supervisor,seller',
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', 'Permissão do usuário atualizada!');
    }
}
