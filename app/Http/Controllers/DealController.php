<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deal;
use Inertia\Inertia;

class DealController extends Controller
{
    public function index()
    {
        // Buscamos as negociações e já trazemos os dados do contato (Eager Loading)
        $deals = Deal::with('contact')->get();

        // Renderizamos a view do Vue.js, passando os dados
        return Inertia::render('Deals/Index', [
            'deals' => $deals
        ]);
    }
}
