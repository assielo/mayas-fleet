FROM php:8.2-apache

# 1. Installer les dépendances système et extensions nécessaires
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# 2. Installer les extensions PHP
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# 3. Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Définir le répertoire de travail
WORKDIR /var/www/html

# 5. Copier les fichiers du projet
COPY . /var/www/html

# 6. Installer les dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# 7. Rediriger le DocumentRoot d'Apache vers le dossier public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -s 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -s 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 8. Activer mod_rewrite
RUN a2enmod rewrite

# 9. Préparer les dossiers storage/bootstrap et le fichier de log avec les bons droits
RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/framework/cache && \
    touch /var/www/html/storage/logs/laravel.log && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Créer le script de démarrage
RUN echo '#!/bin/sh' > /var/www/html/start.sh && \
    echo 'chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache' >> /var/www/html/start.sh && \
    echo 'chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache' >> /var/www/html/start.sh && \
    echo 'php artisan config:clear' >> /var/www/html/start.sh && \
    echo 'php artisan route:clear' >> /var/www/html/start.sh && \
    echo 'php artisan migrate --force' >> /var/www/html/start.sh && \
    echo 'apache2-foreground' >> /var/www/html/start.sh && \
    chmod +x /var/www/html/start.sh

# 11. Exposer le port 80 et lancer le script
EXPOSE 80
CMD ["/var/www/html/start.sh"]
