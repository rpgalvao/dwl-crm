<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use Illuminate\Http\Request;

class DealNoteController extends Controller
{
    public function store(Request $request, Deal $deal)
    {
        // Por enquanto vamos focar no texto. O anexo deixamos preparado para o futuro.
        $request->validate([
            'note' => 'required|string',
        ]);

        // Salva a anotação já amarrando com a negociação atual e com o usuário logado
        $deal->notes()->create([
            'user_id' => auth()->id(),
            'note' => $request->note
        ]);

        return back();
    }
}
