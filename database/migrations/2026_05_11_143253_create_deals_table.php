<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Ex: "Venda de Reagentes - Laboratório Central"
            $table->decimal('estimated_value', 12, 2)->nullable();

            // Etapas do Funil: novo, cotacao, enviado, aprovacao, ganho, perdido
            $table->string('status')->default('novo');

            $table->date('expected_closed_at')->nullable(); // Previsão de fecho
            $table->date('last_contact_at')->nullable(); // Data do último contacto
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
