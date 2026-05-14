<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // Redireciona qualquer acesso na raiz direto para a tela de login
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Nossa nova rota do CRM protegida por senha
Route::middleware('auth')->group(function () {
    Route::get('/negociacoes', [DealController::class, 'index'])->name('deals.index');
    Route::get('/negociacoes/criar', [DealController::class, 'create'])->name('deals.create');
    Route::post('/negociacoes', [DealController::class, 'store'])->name('deals.store');
    Route::patch('/negociacoes/{deal}/status', [DealController::class, 'updateStatus'])->name('deals.update-status');
    Route::get('/negociacoes/{deal}/editar', [DealController::class, 'edit'])->name('deals.edit');
    Route::put('/negociacoes/{deal}', [DealController::class, 'update'])->name('deals.update');
    Route::get('/negociacoes/{deal}/detalhes', [DealController::class, 'show'])->name('deals.show');
    Route::post('/negociacoes/{deal}/itens', [DealController::class, 'storeItem'])->name('deals.items.store');

    Route::get('/produtos', [ProductController::class, 'index'])->name('products.index');
    Route::get('/produtos/novo', [ProductController::class, 'create'])->name('products.create');
    Route::post('/produtos', [ProductController::class, 'store'])->name('products.store');
    Route::get('/produtos/{product}/editar', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/produtos/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/produtos/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Rotas de Contatos (Clientes)
    Route::get('/contatos', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contatos/novo', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/contatos', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contatos/{contact}/editar', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/contatos/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contatos/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    // Gestão de Equipe (Usuários)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');

    // Rotas do perfil geradas pelo Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rota secreta para virar Admin (vamos apagar isso depois!)
Route::get('/virar-admin', function () {
    $user = User::where('email', 'renato@dwldiagnostica.com')->first();
    
    if (!$user) {
        return 'Usuário não encontrado. Você já criou a sua conta na tela de registro do sistema?';
    }

    $user->update(['is_admin' => true]);
    return 'SUCESSO ABSOLUTO! Você agora é Administrador. Pode voltar para o sistema e ver o menu Equipe!';
});

require __DIR__.'/auth.php';
