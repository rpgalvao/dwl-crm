#!/usr/bin/env bash
# Interrompe a execução se der algum erro
set -o errexit

echo "Instalando dependências do PHP..."
composer install --no-dev --optimize-autoloader

echo "Instalando dependências do Node e compilando o Vue..."
npm install
npm run build

echo "Limpando e recriando os caches do Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Rodando as migrações do Banco de Dados..."
php artisan migrate --force