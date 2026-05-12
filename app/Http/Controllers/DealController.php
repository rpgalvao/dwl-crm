<?php

namespace App\Http\Controllers;

use App\Models\Contact;
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

        // 2. Cria a negociação definindo o status inicial como 'novo'
        Deal::create([
            'title' => $validated['title'],
            'contact_id' => $validated['contact_id'],
            'estimated_value' => $validated['estimated_value'],
            'expected_closed_at' => $validated['expected_closed_at'],
            'status' => 'novo', // Todo novo negócio entra na primeira coluna!
            'last_contact_at' => now(),
        ]);

        // 3. Redireciona de volta para o Kanban
        return redirect()->route('deals.index');
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
}
