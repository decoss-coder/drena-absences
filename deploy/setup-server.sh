#!/bin/bash

# ═══════════════════════════════════════════════════════════
# Script de déploiement — DRENA Absences
# Usage : bash deploy/setup-server.sh
# Exécuter en tant que root sur le VPS Hostinger
# ═══════════════════════════════════════════════════════════

set -e

APP_DIR="/var/www/drena-app"
DOMAIN="absences-drena.ci"
DB_NAME="drena_absences"
DB_USER="drena_user"
PHP_VERSION="8.3"

echo "═══════════════════════════════════════════"
echo "  DRENA Absences — Installation Hostinger  "
echo "═══════════════════════════════════════════"

# ──── 1. Mise à jour système ────
echo "[1/10] Mise à jour du système..."
apt update && apt upgrade -y

# ──── 2. Installation des paquets ────
echo "[2/10] Installation de PHP ${PHP_VERSION}, Nginx, MySQL..."
apt install -y \
    nginx \
    mysql-server \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-tokenizer \
    supervisor \
    certbot python3-certbot-nginx \
    unzip curl git fail2ban ufw

# ──── 3. Composer ────
echo "[3/10] Installation de Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ──── 4. MySQL ────
echo "[4/10] Configuration MySQL..."
read -sp "Mot de passe MySQL pour ${DB_USER}: " DB_PASS
echo ""
mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"
echo "  Base '${DB_NAME}' créée."

# ──── 5. Application ────
echo "[5/10] Déploiement de l'application..."
if [ ! -d "$APP_DIR" ]; then
    mkdir -p $(dirname $APP_DIR)
    echo "  Copiez le code dans ${APP_DIR} ou clonez via git."
    echo "  Ex: git clone git@github.com:mena-ci/drena-absences.git ${APP_DIR}"
    read -p "Appuyez sur Entrée quand le code est en place..."
fi

cd $APP_DIR
composer install --optimize-autoloader --no-dev

if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate

    sed -i "s/DB_DATABASE=drena_absences/DB_DATABASE=${DB_NAME}/" .env
    sed -i "s/DB_USERNAME=drena_user/DB_USERNAME=${DB_USER}/" .env
    sed -i "s/DB_PASSWORD=/DB_PASSWORD=${DB_PASS}/" .env
    sed -i "s|APP_URL=https://absences-drena.ci|APP_URL=https://${DOMAIN}|" .env

    echo "  .env configuré. Vérifiez les paramètres SMS/Email dans .env"
fi

php artisan migrate --seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ──── 6. Permissions ────
echo "[6/10] Configuration des permissions..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# ──── 7. Nginx ────
echo "[7/10] Configuration Nginx..."
cp $APP_DIR/deploy/nginx-drena.conf /etc/nginx/sites-available/drena
ln -sf /etc/nginx/sites-available/drena /etc/nginx/sites-enabled/drena
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

# ──── 8. SSL ────
echo "[8/10] Installation du certificat SSL..."
certbot --nginx -d ${DOMAIN} -d www.${DOMAIN} --non-interactive --agree-tos --email admin@education.gouv.ci
echo "  Auto-renouvellement configuré via certbot."

# ──── 9. Supervisor (Queue Workers) ────
echo "[9/10] Configuration Supervisor..."
cp $APP_DIR/deploy/supervisor-drena.conf /etc/supervisor/conf.d/drena.conf
supervisorctl reread
supervisorctl update
supervisorctl start drena-worker:*

# ──── 10. Firewall & Sécurité ────
echo "[10/10] Configuration du firewall..."
ufw default deny incoming
ufw default allow outgoing
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# Fail2Ban
systemctl enable fail2ban
systemctl start fail2ban

# Cron
(crontab -l 2>/dev/null; echo "* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1") | crontab -

echo ""
echo "═══════════════════════════════════════════"
echo "  Installation terminée avec succès !"
echo "═══════════════════════════════════════════"
echo ""
echo "  URL : https://${DOMAIN}"
echo "  Admin MENA : admin@education.gouv.ci / Mena@2026"
echo ""
echo "  Prochaines étapes :"
echo "  1. Vérifier .env (SMS, Email)"
echo "  2. Changer les mots de passe par défaut"
echo "  3. Tester l'application"
echo ""
