<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\QueryException;

class ContactController extends Controller
{
    // Lista os contatos
    public function index()
    {
        $contacts = Contact::orderBy('name')->get();
        return Inertia::render('Contacts/Index', ['contacts' => $contacts]);
    }

    // Abre o formulário de novo contato
    public function create()
    {
        return Inertia::render('Contacts/Create');
    }

    // Salva no banco de dados
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Contact::create($validated);

        return redirect()->route('contacts.index');
    }

    // Abre a tela de edição
    public function edit(Contact $contact)
    {
        return Inertia::render('Contacts/Edit', ['contact' => $contact]);
    }

    // Atualiza os dados
    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $contact->update($validated);

        return redirect()->route('contacts.index');
    }

    // Exclui o contato (com proteção caso ele tenha negociações)
    public function destroy(Contact $contact)
    {
        try {
            $contact->delete();
        } catch (QueryException $e) {
            // Tratamento de erro de chave estrangeira
            if ($e->getCode() == '23503') {
                return redirect()->back()->withErrors([
                    'error' => 'Este cliente não pode ser excluído porque possui negociações vinculadas ao funil.'
                ]);
            }
            throw $e;
        }

        return redirect()->route('contacts.index');
    }
}