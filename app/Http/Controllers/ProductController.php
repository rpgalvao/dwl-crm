<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
}