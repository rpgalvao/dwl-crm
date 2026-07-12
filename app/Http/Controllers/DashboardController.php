<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Deal;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Inicia as queries-base
        $dealsQuery = Deal::query();

        // Se NÃO for admin, trava tudo para o ID dele
        if (! $user->is_admin) {
            $dealsQuery->where('user_id', $user->id);
        }

        // 1. Cálculos de Quantidade usando a query filtrada
        $totalClientes = Contact::count(); // Clientes geralmente são visíveis para todos
        $negociacoesAtivas = (clone $dealsQuery)->whereIn('status', ['novo', 'cotacao', 'aprovacao'])->count();
        $negociacoesGanhas = (clone $dealsQuery)->where('status', 'ganho')->count();

        // 2. Cálculos Financeiros usando a query filtrada
        $valorTotalGanho = (clone $dealsQuery)->where('status', 'ganho')->sum('estimated_value');
        $valorFunil = (clone $dealsQuery)->whereIn('status', ['novo', 'cotacao', 'aprovacao'])->sum('estimated_value');

        // 3. Dados para o Gráfico
        $graficoFunil = [
            (clone $dealsQuery)->where('status', 'novo')->count(),
            (clone $dealsQuery)->where('status', 'cotacao')->count(),
            (clone $dealsQuery)->where('status', 'aprovacao')->count(),
            (clone $dealsQuery)->where('status', 'ganho')->count(),
        ];

        // 4. Últimas negociações
        $ultimasNegociacoes = (clone $dealsQuery)->with('contact')
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
            'recentDeals' => $ultimasNegociacoes,
        ]);
    }
}
