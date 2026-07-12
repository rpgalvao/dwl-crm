<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona a coluna role (cargo) com o padrão 'seller' (vendedor)
            $table->string('role')->default('seller')->after('email');

            // Remove a coluna antiga
            $table->dropColumn('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Se um dia precisarmos desfazer, ele recria o is_admin e apaga o role
            $table->boolean('is_admin')->default(false);
            $table->dropColumn('role');
        });
    }
};
