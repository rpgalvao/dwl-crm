<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento com a Empresa. 
            // O nullable() permite cadastrar um lead pessoa física antes de saber a empresa.
            // O nullOnDelete() faz com que, se a empresa for apagada, o contato não suma, apenas perca o vínculo.
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('role')->nullable(); // Cargo (ex: Comprador, Coordenador de Laboratório)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};