<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Deal;
use Inertia\Inertia;

class DealController extends Controller
{
    public function index()
    {
        // Começa a montar a busca no banco
        $query = Deal::with('contact');

        // Se o usuário NÃO for admin, filtra apenas as negociações dele
        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }

        $deals = $query->get();

        // Agrupa os resultados por status (seu código atual de agrupamento continua aqui)
        $groupedDeals = [
            'novo' => $deals->where('status', 'novo')->values(),
            'cotacao' => $deals->where('status', 'cotacao')->values(),
            'aprovacao' => $deals->where('status', 'aprovacao')->values(),
            'ganho' => $deals->where('status', 'ganho')->values(),
        ];

        return Inertia::render('Deals/Index', [
            'deals' => $groupedDeals
        ]);
    }

    // Função para exibir a tela de cadastro
    public function create()
    {
        // Buscamos os contatos para o vendedor poder escolher o cliente
        $contacts = Contact::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Deals/Create', [
            'contacts' => $contacts
        ]);
    }

    // Função para salvar os dados no banco
    public function store(Request $request)
    {
        // 1. Validação de segurança (o Laravel não deixa passar se faltar algo)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'contact_id' => 'required|exists:contacts,id',
            'estimated_value' => 'required|numeric',
            'expected_closed_at' => 'required|date',
        ]);

        // Pega os dados validados e junta com o ID do usuário logado
        $dealData = array_merge($validated, [
            'user_id' => auth()->id()
        ]);

        Deal::create($dealData);

        return redirect()->back()->with('success', 'Negociação criada com sucesso!');
    }

    // Atualiza apenas o status quando o card é arrastado
    public function updateStatus(Request $request, Deal $deal)
    {
        $request->validate([
            'status' => 'required|string|in:novo,cotacao,aprovacao,ganho',
        ]);

        $deal->update([
            'status' => $request->status
        ]);

        // Redireciona de volta silenciosamente
        return redirect()->back();
    }

    // Carrega a tela de edição com os dados da negociação
    public function edit(Deal $deal)
    {
        $contacts = Contact::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Deals/Edit', [
            'deal' => $deal,
            'contacts' => $contacts
        ]);
    }

    // Salva as alterações no banco de dados
    public function update(Request $request, Deal $deal)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'contact_id' => 'required|exists:contacts,id',
            'estimated_value' => 'required|numeric',
            'expected_closed_at' => 'required|date',
        ]);

        $deal->update($validated);

        return redirect()->route('deals.index');
    }

    // Exibe a tela de Detalhes da Negociação
    public function show(Deal $deal)
    {
        // Carrega a negociação junto com o cliente e os itens/produtos já vinculados
        $deal->load('contact', 'items.product');
        
        // Puxa o catálogo de reagentes para o vendedor escolher no dropdown
        $products = Product::orderBy('name')->get();

        return Inertia::render('Deals/Show', [
            'deal' => $deal,
            'products' => $products
        ]);
    }

    // Salva um novo reagente dentro desta negociação
    public function storeItem(Request $request, Deal $deal)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Cria o item vinculado a esta negociação específica
        $deal->items()->create($validated);

        // Bônus: Atualiza o valor estimado da negociação somando todos os itens!
        $novoValorTotal = 0;
        foreach ($deal->items as $item) {
            $novoValorTotal += ($item->quantity * $item->unit_price);
        }
        $deal->update(['estimated_value' => $novoValorTotal]);

        // Recarrega a página silenciosamente
        return redirect()->back();
    }
}
