<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Força o uso de HTTPS quando o sistema estiver em produção (Render)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // 2. Adicione este bloco para definir a regra global
        Password::defaults(function () {
            return Password::min(5)->letters()->numbers(); // Troque o 6 para o tamanho que você desejar
            
            // Dica: Se quiser forçar senhas complexas no futuro, você pode encadear regras:
            // return Password::min(8)->letters()->numbers()->symbols();
        });
    }
}
