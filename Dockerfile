FROM php:8.3-apache

# 1. Installer les dépendances système et les extensions PHP nécessaires à Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP (avec pdo_pgsql pour PostgreSQL et pdo_mysql)
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# 2. Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Définir le dossier de travail
WORKDIR /var/www/html

# 4. Copier les fichiers du projet
COPY . /var/www/html

# 5. Installer les dépendances PHP via Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# 6. Configurer Apache pour pointer vers le dossier public/ de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Autoriser Apache à utiliser le fichier .htaccess de Laravel (Corrige l'erreur 404)
RUN echo '<Directory /var/www/html/public/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# 7. Activer le module de réécriture d'Apache
RUN a2enmod rewrite

# 8. Préparer les dossiers storage/bootstrap et donner les permissions
RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/framework/cache && \
    touch /var/www/html/storage/logs/laravel.log && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Créer le script de démarrage (migrations auto + lancement Apache)
RUN echo '#!/bin/sh' > /var/www/html/start.sh && \
    echo 'chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache' >> /var/www/html/start.sh && \
    echo 'chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache' >> /var/www/html/start.sh && \
    echo 'php artisan config:clear' >> /var/www/html/start.sh && \
    echo 'php artisan route:clear' >> /var/www/html/start.sh && \
    echo 'php artisan migrate --force' >> /var/www/html/start.sh && \
    echo 'apache2-foreground' >> /var/www/html/start.sh && \
    chmod +x /var/www/html/start.sh

# 10. Exposer le port 80 et lancer le script
EXPOSE 80
CMD ["/var/www/html/start.sh"]