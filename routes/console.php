<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Tâches planifiées — DRENA Absences
|--------------------------------------------------------------------------
|
| Ces tâches sont exécutées automatiquement via le cron Laravel :
| * * * * * cd /var/www/drena-app && php artisan schedule:run >> /dev/null 2>&1
|
*/

// Escalade automatique toutes les heures (absences non traitées > 48h)
Schedule::command('absences:escalade')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/escalade.log'));

// Rappels de validation — toutes les 6 heures
Schedule::command('absences:verifier-seuils')
    ->everySixHours()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/seuils.log'));

// Résumé hebdomadaire — chaque lundi à 7h (heure Abidjan)
Schedule::command('absences:resume-hebdo')
    ->weeklyOn(1, '07:00')
    ->timezone('Africa/Abidjan')
    ->appendOutputTo(storage_path('logs/resume-hebdo.log'));

// Nettoyage des jobs échoués — quotidien
Schedule::command('queue:prune-failed --hours=168')
    ->daily();

// Backup quotidien de la base — à 2h du matin
Schedule::command('backup:run --only-db')
    ->dailyAt('02:00')
    ->timezone('Africa/Abidjan')
    ->appendOutputTo(storage_path('logs/backup.log'));

// Nettoyage des anciens backups (> 7 jours)
Schedule::command('backup:clean')
    ->dailyAt('03:00')
    ->timezone('Africa/Abidjan');

// Purge des logs d'activité > 90 jours
Schedule::command('activitylog:clean --days=90')
    ->monthlyOn(1, '04:00')
    ->timezone('Africa/Abidjan');
