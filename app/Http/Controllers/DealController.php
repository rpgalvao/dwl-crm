<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\User;
use Inertia\Inertia;

class DealController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Começa a montar a busca no banco trazendo o contato e o dono da negociação
        $query = Deal::with(['contact', 'user']);

        if($user->is_admin){
            // Se for admin traz a lista de todas as negociações para o filtro
            $sellers = User::orderBy('name')->get(['id', 'name']);

            // Se o admin usar o filtro deve selecionar apenas as negociações filtradas
            if($request->filled('seller_id')){
                $query->where('user_id', $request->seller_id);
            }
        } else {
            // Se for vendedor comum, mostra apenas as negociações dele mesmo e sem lista de filtros
            $query->where('user_id', $user->id);
            $sellers = [];
        }

        $deals = $query->get();

        return Inertia::render('Deals/Index', [
            'deals' => $deals,
            'sellers' => $sellers,
            'filters' => $request->only('seller_id'),
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

        return redirect()->route('deals.index')->with('success', 'Negociação criada com sucesso!');
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

        return redirect()->route('deals.index')->with('success', 'Negociação atualizada com sucesso');
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
