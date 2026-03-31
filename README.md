# DRENA Absences — Application de Gestion des Absences du Personnel

**Ministère de l'Éducation Nationale et de l'Alphabétisation (MENA) — Côte d'Ivoire**

Application web Laravel pour la gestion complète des absences du personnel des Directions Régionales de l'Éducation Nationale et de l'Alphabétisation (DRENA).

---

## Stack technique

| Composant | Technologie |
|-----------|-------------|
| Backend | Laravel 11 (PHP 8.3) |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Base de données | MySQL 8.0 |
| Auth | Laravel Sanctum + Spatie Permission |
| Notifications | Laravel Notifications (mail + database) |
| Audit | Spatie Activity Log |
| Export | Maatwebsite Excel + Barryvdh DomPDF |
| Hébergement | Hostinger VPS (Ubuntu + Nginx) |

---

## Installation locale

### Prérequis
- PHP 8.2+ avec extensions : mysql, xml, mbstring, curl, zip, bcmath, gd
- Composer 2.x
- MySQL 8.0
- Node.js 18+ (optionnel, pour compilation assets)

### Étapes

```bash
# 1. Cloner le projet
git clone git@github.com:mena-ci/drena-absences.git
cd drena-absences

# 2. Installer les dépendances
composer install

# 3. Configuration
cp .env.example .env
php artisan key:generate
# Éditer .env avec vos paramètres MySQL

# 4. Base de données
php artisan migrate --seed

# 5. Publier les packages
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"

# 6. Liens de stockage
php artisan storage:link

# 7. Lancer le serveur
php artisan serve
```

### Comptes de démonstration

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Super Admin MENA | admin@education.gouv.ci | Mena@2026 |
| Admin DRENA Abidjan 1 | abj1.admin@education.gouv.ci | Drena@2026 |
| Tous les autres comptes | voir base de données | Drena@2026 |

---

## Déploiement sur Hostinger VPS

### 1. Préparer le serveur

```bash
# Connexion SSH
ssh root@votre-ip-hostinger

# Installer les dépendances
apt update && apt upgrade -y
apt install -y nginx mysql-server php8.3-fpm php8.3-cli php8.3-mysql \
    php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-bcmath \
    php8.3-gd php8.3-intl supervisor certbot python3-certbot-nginx

# Installer Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

### 2. Configurer MySQL

```sql
CREATE DATABASE drena_absences CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'drena_user'@'localhost' IDENTIFIED BY 'VotreMotDePasseSecurise2026!';
GRANT ALL PRIVILEGES ON drena_absences.* TO 'drena_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Déployer le code

```bash
cd /var/www
git clone git@github.com:mena-ci/drena-absences.git drena-app
cd drena-app

composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate

# Configurer .env avec les vrais paramètres
nano .env

php artisan migrate --seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 4. Configurer Nginx

```nginx
server {
    listen 80;
    server_name absences-drena.ci www.absences-drena.ci;
    root /var/www/drena-app/public;
    index index.php;

    client_max_body_size 10M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
ln -s /etc/nginx/sites-available/drena /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
```

### 5. SSL Let's Encrypt

```bash
certbot --nginx -d absences-drena.ci -d www.absences-drena.ci
```

### 6. Queue Worker (Supervisor)

```ini
# /etc/supervisor/conf.d/drena-worker.conf
[program:drena-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/drena-app/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/drena-app/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start drena-worker:*
```

### 7. Cron Laravel

```bash
# crontab -e
* * * * * cd /var/www/drena-app && php artisan schedule:run >> /dev/null 2>&1
```

### 8. Firewall

```bash
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw enable
```

### 9. Backup automatique

```bash
# Ajouter dans crontab
0 2 * * * cd /var/www/drena-app && php artisan backup:run --only-db >> /dev/null 2>&1
```

---

## Hiérarchie des rôles

```
MENA (super_admin)
└── Admin DRENA (admin_drena) × 41
    ├── Gestionnaire RH (gestionnaire_rh)
    ├── Inspecteur IEPP (inspecteur)
    │   └── Chef d'établissement (chef_etablissement)
    │       └── Enseignant / Agent (enseignant)
    └── ...
```

---

## Structure du projet

```
drena-app/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/AuthController.php      # Authentification
│   │   ├── DashboardController.php      # Dashboards par rôle
│   │   ├── AbsenceController.php        # Module absences
│   │   ├── PersonnelController.php      # Gestion du personnel
│   │   ├── RapportController.php        # Rapports & exports
│   │   └── Admin/AdminController.php    # Administration MENA
│   ├── Models/                          # 14 modèles Eloquent
│   │   ├── User.php, Drena.php, Iepp.php, Etablissement.php
│   │   ├── Absence.php, TypeAbsence.php, Validation.php
│   │   ├── Justificatif.php, Suppleance.php
│   │   ├── AnneeScolaire.php, CongeSolde.php
│   │   ├── LoginHistory.php, NotificationPreference.php
│   │   └── SeuilAlerte.php, JourFerie.php
│   └── Notifications/                   # SMS + Email
├── database/
│   ├── migrations/                      # 7 fichiers de migration
│   └── seeders/DatabaseSeeder.php       # Données de démo complètes
├── resources/views/                     # ~30 vues Blade
│   ├── layouts/app.blade.php            # Layout principal
│   ├── auth/                            # Login, reset password
│   ├── dashboard/                       # 5 dashboards par rôle
│   ├── absences/                        # CRUD + calendrier
│   ├── personnel/                       # CRUD agents
│   ├── rapports/                        # Stats + PDF export
│   ├── admin/                           # Config MENA
│   └── notifications/                   # Centre de notifications
├── routes/web.php                       # Routes complètes avec RBAC
├── composer.json
├── .env.example
└── README.md
```

---

## Fonctionnalités clés (111 au total)

- **6 rôles** : Super Admin MENA → Admin DRENA → Inspecteur → Gestionnaire RH → Chef d'établissement → Enseignant
- **Workflow de validation** à 3 niveaux avec escalade automatique (48h)
- **10 types d'absences** paramétrables (maladie, congé, maternité, mission, etc.)
- **Suppléance automatique** : suggestion de remplaçants disponibles
- **Notifications** : email + SMS + in-app
- **Rapports** : PDF + Excel + graphiques Chart.js
- **Audit complet** : traçabilité de chaque action
- **Sécurité** : RBAC, 2FA, verrouillage de compte, CSRF, rate limiting
- **Hors-ligne** : architecture PWA-ready

---

## Licence

Propriétaire — Ministère de l'Éducation Nationale et de l'Alphabétisation, Côte d'Ivoire.
