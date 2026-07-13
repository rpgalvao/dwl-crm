<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deal_notes', function (Blueprint $table) {
            $table->id();
            // Vincula com a Negociação (se a negociação for apagada, as notas somem junto)
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            // Vincula com o Usuário (Autor da nota)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // O texto da anotação
            $table->text('note');
            // O caminho do arquivo anexo (nullable porque é opcional)
            $table->string('attachment_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_notes');
    }
};
