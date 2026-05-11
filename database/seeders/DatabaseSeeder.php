<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Deal;
use App\Models\DealItem;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criando uma Empresa
        $company = Company::create([
            'name' => 'Laboratório Vida e Saúde',
            'cnpj' => '12.345.678/0001-99',
            'email' => 'compras@vidaesaude.com.br',
            'phone' => '(41) 3333-4444'
        ]);

        // 2. Criando Contatos vinculados à empresa
        $contact = Contact::create([
            'company_id' => $company->id,
            'name' => 'Carlos Almeida',
            'email' => 'carlos.almeida@vidaesaude.com.br',
            'phone' => '(41) 99999-8888',
            'role' => 'Coordenador de Laboratório'
        ]);

        // 3. Criando o Catálogo de Reagentes
        $product1 = Product::create([
            'name' => 'Reagente Hematológico 500ml',
            'sku' => 'HEMA-500',
            'price' => 250.00,
            'description' => 'Solução para contagem de células.'
        ]);

        $product2 = Product::create([
            'name' => 'Kit Bioquímica - Glicose',
            'sku' => 'BIO-GLI-100',
            'price' => 180.50,
            'description' => 'Kit com 100 testes de glicemia.'
        ]);

        // 4. Criando uma Negociação (Deal) no Funil
        $deal = Deal::create([
            'contact_id' => $contact->id,
            'title' => 'Renovação de Estoque Trimestral',
            'estimated_value' => 680.50,
            'status' => 'cotacao',
            'expected_closed_at' => now()->addDays(7),
            'last_contact_at' => now(),
        ]);

        // 5. Adicionando os Reagentes a essa Negociação
        DealItem::create([
            'deal_id' => $deal->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'unit_price' => 250.00 // R$ 500.00
        ]);

        DealItem::create([
            'deal_id' => $deal->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'unit_price' => 180.50 // R$ 180.50
        ]);
    }
}
