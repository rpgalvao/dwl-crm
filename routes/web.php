<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DealController; // <-- Não esqueça de importar o Controller
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Nossa nova rota do CRM protegida por senha
Route::middleware('auth')->group(function () {
    Route::get('/negociacoes', [DealController::class, 'index'])->name('deals.index');
    Route::get('/negociacoes/criar', [DealController::class, 'create'])->name('deals.create');
    Route::post('/negociacoes', [DealController::class, 'store'])->name('deals.store');
    Route::patch('/negociacoes/{deal}/status', [DealController::class, 'updateStatus'])->name('deals.update-status');
    Route::get('/negociacoes/{deal}/editar', [DealController::class, 'edit'])->name('deals.edit');
    Route::put('/negociacoes/{deal}', [DealController::class, 'update'])->name('deals.update');

    // Rotas do perfil geradas pelo Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
