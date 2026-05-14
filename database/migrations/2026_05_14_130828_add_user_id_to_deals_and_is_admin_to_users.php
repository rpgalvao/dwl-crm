<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adiciona o nível de acesso no Usuário
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        // 2. Adiciona o "Dono" na Negociação (Nullable para não quebrar os dados que você já cadastrou)
        Schema::table('deals', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};