#!/bin/bash

# ═══════════════════════════════════════════════════════════
# Déploiement zero-downtime — DRENA Absences
# Usage : bash deploy/deploy.sh
# ═══════════════════════════════════════════════════════════

set -e

APP_DIR="/var/www/drena-app"
cd $APP_DIR

echo "[$(date)] Début du déploiement..."

# Mode maintenance (avec retry pour les clients)
php artisan down --retry=60 --secret="drena-bypass-2026"

# Pull du code
git pull origin main

# Dépendances
composer install --optimize-autoloader --no-dev --no-interaction

# Migrations
php artisan migrate --force

# Caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Redémarrer les workers
supervisorctl restart drena-worker:*

# Fin du mode maintenance
php artisan up

echo "[$(date)] Déploiement terminé avec succès !"
echo "Secret bypass: https://absences-drena.ci/drena-bypass-2026"
