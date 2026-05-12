<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    // Lista todos os produtos
    public function index()
    {
        $products = Product::orderBy('name')->get();
        return Inertia::render('Products/Index', ['products' => $products]);
    }

    // Abre o formulário de novo produto
    public function create()
    {
        return Inertia::render('Products/Create');
    }

    // Salva o produto no banco
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create($validated);

        return redirect()->route('products.index');
    }

    // Abre a tela de edição
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product
        ]);
    }

    // Salva a alteração
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('products.index');
    }

    // Remove o produto com proteção contra chave estrangeira
    public function destroy(Product $product)
    {
        try {
            $product->delete();
        } catch (QueryException $e) {
            // O código 23503 é o padrão do PostgreSQL para Violação de Chave Estrangeira
            if ($e->getCode() == '23503') {
                return redirect()->back()->withErrors([
                    'error' => 'Este reagente não pode ser excluído porque já está vinculado a uma negociação em andamento ou finalizada.'
                ]);
            }
            
            throw $e; // Se for outro tipo de erro, o Laravel trata normalmente
        }

        return redirect()->route('products.index');
    }
}