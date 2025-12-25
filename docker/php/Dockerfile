FROM php:8.4-fpm-alpine

# Argumenty budowania
ARG USER_ID=1000
ARG GROUP_ID=1000

# Instalacja zależności systemowych
RUN apk add --no-cache \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    icu-dev \
    libxml2-dev \
    postgresql-dev \
    sqlite-dev \
    linux-headers \
    $PHPIZE_DEPS

# Instalacja rozszerzeń PHP wymaganych przez Laravel + Filament
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
        soap

# Tutaj póżniej wpasnie REDIS itd

# Instalacja rozszerzeń PHP wymaganych przez Laravel + Filament
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Konfiguracja PHP dla produkcji/development
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Tworzenie użytkownika aplikacji (dla bezpieczeństwa nie używamy root)
RUN addgroup -g ${GROUP_ID} laravel \
    && adduser -D -u ${USER_ID} -G laravel laravel

# Ustawianie katalogu roboczego
WORKDIR /var/www/html

# ===== NOWA CZĘŚĆ - KOPIOWANIE KODU APLIKCAJI DO OBRAZY (tylko prod) =====
# Najpierw composer.json i composer.lock (dla cache)
COPY --chown=laravel:laravel composer.json composer.lock ./

# Instalacja zależności (bez autoloadera - zrobimy to później)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Kopiowanie całego kodu aplikacji
COPY --chown=laravel:laravel . .

# Generowanie autoloadera
RUN composer dump-autoload --optimize

# Uprawnienia dla storage i cache
RUN chown -R laravel:laravel /var/www/html/storage /var/www/html/bootstrap/cache

# ===== KONIEC NOWEJ CZĘŚCI =====

# Przełączenie na użytkownika laravel
USER laravel

# Expose portu PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
