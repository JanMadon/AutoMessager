#!/bin/bash

echo "🚀 Inicjalizacja aplikacji Laravel..."

# Instalacja zależności Composer
echo "📦 Instalacja zależności PHP..."
docker compose exec app composer install --optimize-autoloader

# Instalacja zależności NPM
echo "📦 Instalacja zależności Node.js..."
docker compose exec node npm install

# Generowanie klucza aplikacji
echo "🔑 Generowanie klucza aplikacji..."
docker compose exec app php artisan key:generate

# Tworzenie bazy SQLite jeśli nie istnieje
echo "🗄️ Przygotowanie bazy danych..."
docker compose exec app touch database/database.sqlite

# Migracje
echo "🔄 Uruchamianie migracji..."
docker compose exec app php artisan migrate --force

# Linki symboliczne dla storage
echo "🔗 Tworzenie linków symbolicznych..."
docker compose exec app php artisan storage:link

# Cache dla lepszej wydajności
echo "⚡ Optymalizacja..."
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Uprawnienia
echo "🔐 Ustawianie uprawnień..."
docker compose exec app chown -R laravel:laravel /var/www/html/storage
docker compose exec app chown -R laravel:laravel /var/www/html/bootstrap/cache

echo "✅ Inicjalizacja zakończona!"
echo "🌐 Aplikacja dostępna pod: http://localhost:8000"
echo "📧 Panel maili (Mailpit): http://localhost:8025"
echo "🎨 Vite dev server: http://localhost:5173"