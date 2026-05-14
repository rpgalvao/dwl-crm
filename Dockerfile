# Usa o PHP 8.4 oficial
FROM php:8.4-cli

# Instala dependências do sistema e o Node.js 20 (para compilar o Vue)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libicu-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala as extensões do PHP necessárias para o Laravel e o banco Neon (PostgreSQL)
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd intl

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define a pasta de trabalho dentro do servidor
WORKDIR /app

# Copia todos os arquivos do seu projeto para dentro do servidor
COPY . .

# Instala as dependências do PHP (ignora as de desenvolvimento para ficar mais leve)
RUN composer install --no-dev --optimize-autoloader

# Instala as dependências do Vue.js e gera os arquivos finais
RUN npm install
RUN npm run build

# Dá as permissões corretas para o Laravel poder salvar arquivos de log e cache
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Comando final: Exclui o banco fantasma, recria o .env perfeitamente com os dados do Render e liga o app
CMD rm -f database/database.sqlite && \
    echo "APP_ENV=production" > .env && \
    echo "APP_KEY=\"${APP_KEY}\"" >> .env && \
    echo "APP_URL=\"${APP_URL}\"" >> .env && \
    echo "APP_LOCALE=\"${APP_LOCALE}\"" >> .env && \
    echo "DB_CONNECTION=pgsql" >> .env && \
    echo "DB_HOST=\"${DB_HOST}\"" >> .env && \
    echo "DB_PORT=\"${DB_PORT}\"" >> .env && \
    echo "DB_DATABASE=\"${DB_DATABASE}\"" >> .env && \
    echo "DB_USERNAME=\"${DB_USERNAME}\"" >> .env && \
    echo "DB_PASSWORD=\"${DB_PASSWORD}\"" >> .env && \
    echo "PGSSLMODE=require" >> .env && \
    php artisan config:clear && \
    php artisan db:show && \
    php artisan migrate:fresh --force && \
    php artisan serve --host=0.0.0.0 --port=$PORT
