<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Cálculos de Quantidade Gerais
        $totalClientes = Contact::count();
        $negociacoesAtivas = Deal::whereIn('status', ['novo', 'cotacao', 'aprovacao'])->count();
        $negociacoesGanhas = Deal::where('status', 'ganho')->count();

        // 2. Cálculos Financeiros
        $valorTotalGanho = Deal::where('status', 'ganho')->sum('estimated_value');
        $valorFunil = Deal::whereIn('status', ['novo', 'cotacao', 'aprovacao'])->sum('estimated_value');

        // 3. Dados para o Gráfico (Contagem separada por etapa)
        $graficoFunil = [
            Deal::where('status', 'novo')->count(),
            Deal::where('status', 'cotacao')->count(),
            Deal::where('status', 'aprovacao')->count(),
            Deal::where('status', 'ganho')->count(),
        ];

        // 4. Últimas negociações
        $ultimasNegociacoes = Deal::with('contact')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'metrics' => [
                'totalClientes' => $totalClientes,
                'ativas' => $negociacoesAtivas,
                'ganhas' => $negociacoesGanhas,
                'valorGanho' => $valorTotalGanho,
                'valorFunil' => $valorFunil,
            ],
            'chartData' => $graficoFunil,
            'recentDeals' => $ultimasNegociacoes
        ]);
    }
}