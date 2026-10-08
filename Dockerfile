FROM php:8.3-cli

# 1. Installer les dépendances système et les extensions PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libicu-dev \
    zip \
    unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd intl

# 2. Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Définir le dossier de travail
WORKDIR /var/www/html

# 4. Copier les fichiers du projet
COPY . /var/www/html

# 5. Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# 6. Permissions
RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/framework/cache && \
    chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# 7. Exposer le port de Render
EXPOSE 10000

# 8. Commande de démarrage avec migration fraîche et artisan serve
CMD php artisan migrate:fresh --seed --force && \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan serve --host=0.0.0.0 --port=10000