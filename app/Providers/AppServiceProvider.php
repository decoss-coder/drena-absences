<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Strict mode en développement
        Model::shouldBeStrict(!$this->app->isProduction());

        // Pagination avec Tailwind
        Paginator::useTailwind();

        // Locale française
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'French');
        \Carbon\Carbon::setLocale('fr');

        // Super admin bypass toutes les permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Directives Blade personnalisées
        Blade::if('drena', function ($drenaId) {
            return auth()->check() && (
                auth()->user()->hasRole('super_admin') ||
                auth()->user()->drena_id == $drenaId
            );
        });

        Blade::directive('formatDate', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse({$expression})->format('d/m/Y'); ?>";
        });

        Blade::directive('formatDateTime', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse({$expression})->format('d/m/Y H:i'); ?>";
        });

        // Partager les données globales avec toutes les vues
        view()->composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $view->with('unreadNotificationsCount', auth()->user()->unreadNotifications->count());
            }
        });
    }
}
