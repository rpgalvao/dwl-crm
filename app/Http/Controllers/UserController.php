<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        // Trava de Segurança: Se não for admin, toma erro 403 (Acesso Negado)
        abort_if(!auth()->user()->is_admin, 403, 'Acesso negado.');

        // Puxa todos os usuários em ordem alfabética
        $users = User::orderBy('name')->get();

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }

    public function toggleAdmin(User $user)
    {
        abort_if(!auth()->user()->is_admin, 403, 'Acesso negado.');

        // Trava para evitar que o Admin remova o próprio acesso sem querer e tranque o sistema
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Você não pode alterar sua própria permissão.');
        }

        // Inverte o status atual (se é true vira false, se é false vira true)
        $user->update([
            'is_admin' => !$user->is_admin
        ]);

        return back()->with('success', 'Permissão do usuário atualizada!');
    }
}