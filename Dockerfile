FROM php:8.2-apache

# ── Enable Apache mod_rewrite ─────────────────────────
RUN a2enmod rewrite

# ── PHP extensions required by Laravel ───────────────
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Install Composer ──────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ── Apache: allow .htaccess overrides ─────────────────
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# ── Set document root to Laravel public/ ──────────────
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

# ── Copy project files ─────────────────────────────────
WORKDIR /var/www/html
COPY . .

# ── Install PHP dependencies ───────────────────────────
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction --prefer-dist --optimize-autoloader

# ── Laravel bootstrap ──────────────────────────────────
RUN cp .env.example .env \
    && php artisan key:generate \
    && php artisan storage:link || true

# ── File permissions ──────────────────────────────────
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# ── Cron for appointment reminders ────────────────────
RUN apt-get update && apt-get install -y cron && apt-get clean
RUN echo '0 8 * * * www-data php /var/www/html/artisan clinichub:reminders >> /var/log/clinichub-cron.log 2>&1' \
    > /etc/cron.d/clinichub-reminders \
    && chmod 0644 /etc/cron.d/clinichub-reminders

EXPOSE 80

CMD service cron start && apache2-foreground
