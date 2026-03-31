<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\RapportController;
use Illuminate\Support\Facades\Route;

// ═══════════════ AUTH ═══════════════
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/mot-de-passe-oublie', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reinitialiser/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reinitialiser', [AuthController::class, 'resetPassword'])->name('password.update');
});

// ═══════════════ AUTHENTICATED ═══════════════
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Absences
    Route::prefix('absences')->name('absences.')->group(function () {
        Route::get('/', [AbsenceController::class, 'index'])->name('index');
        Route::get('/calendrier', [AbsenceController::class, 'calendrier'])->name('calendrier');
        Route::get('/creer', [AbsenceController::class, 'create'])->name('create');
        Route::post('/', [AbsenceController::class, 'store'])->name('store');
        Route::get('/{absence}', [AbsenceController::class, 'show'])->name('show');
        Route::post('/{absence}/valider', [AbsenceController::class, 'valider'])->name('valider')
            ->middleware('role:chef_etablissement|inspecteur|admin_drena|super_admin');
        Route::post('/{absence}/annuler', [AbsenceController::class, 'annuler'])->name('annuler');
        Route::post('/{absence}/suppleant', [AbsenceController::class, 'assignerSuppleant'])->name('assigner-suppleant')
            ->middleware('role:chef_etablissement|admin_drena');
    });

    // Personnel
    Route::prefix('personnel')->name('personnel.')->middleware('role:chef_etablissement|inspecteur|gestionnaire_rh|admin_drena|super_admin')->group(function () {
        Route::get('/', [PersonnelController::class, 'index'])->name('index');
        Route::get('/creer', [PersonnelController::class, 'create'])->name('create')
            ->middleware('role:admin_drena|super_admin|gestionnaire_rh');
        Route::post('/', [PersonnelController::class, 'store'])->name('store')
            ->middleware('role:admin_drena|super_admin|gestionnaire_rh');
        Route::get('/{personnel}', [PersonnelController::class, 'show'])->name('show');
        Route::get('/{personnel}/modifier', [PersonnelController::class, 'edit'])->name('edit')
            ->middleware('role:admin_drena|super_admin|gestionnaire_rh');
        Route::put('/{personnel}', [PersonnelController::class, 'update'])->name('update')
            ->middleware('role:admin_drena|super_admin|gestionnaire_rh');
        Route::delete('/{personnel}', [PersonnelController::class, 'destroy'])->name('destroy')
            ->middleware('role:admin_drena|super_admin');
    });

    // Rapports
    Route::prefix('rapports')->name('rapports.')->middleware('role:inspecteur|gestionnaire_rh|admin_drena|super_admin')->group(function () {
        Route::get('/', [RapportController::class, 'index'])->name('index');
        Route::get('/export-pdf', [RapportController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel', [RapportController::class, 'exportExcel'])->name('export-excel');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', function () {
            $notifications = auth()->user()->notifications()->paginate(20);
            return view('notifications.index', compact('notifications'));
        })->name('index');
        Route::post('/{id}/lire', function ($id) {
            auth()->user()->notifications()->findOrFail($id)->markAsRead();
            return back();
        })->name('lire');
        Route::post('/tout-lire', function () {
            auth()->user()->unreadNotifications->markAsRead();
            return back()->with('success', 'Toutes les notifications marquées comme lues.');
        })->name('tout-lire');
    });

    // Profil
    Route::get('/profil', function () {
        return view('personnel.show', ['personnel' => auth()->user()->load(['drena', 'iepp', 'etablissement']), 'statsAbsences' => []]);
    })->name('profil');

    // ═══════════════ ADMIN (Super Admin MENA) ═══════════════
    Route::prefix('admin')->name('admin.')->middleware('role:super_admin')->group(function () {
        Route::get('/drenas', [AdminController::class, 'drenas'])->name('drenas.index');
        Route::get('/drenas/creer', [AdminController::class, 'createDrena'])->name('drenas.create');
        Route::post('/drenas', [AdminController::class, 'storeDrena'])->name('drenas.store');
        Route::get('/drenas/{drena}/modifier', [AdminController::class, 'editDrena'])->name('drenas.edit');
        Route::put('/drenas/{drena}', [AdminController::class, 'updateDrena'])->name('drenas.update');
        Route::get('/drenas/{drena}/admin', [AdminController::class, 'createAdminDrena'])->name('drenas.create-admin');
        Route::post('/drenas/{drena}/admin', [AdminController::class, 'storeAdminDrena'])->name('drenas.store-admin');

        Route::get('/iepps', [AdminController::class, 'iepps'])->name('iepps.index');
        Route::post('/iepps', [AdminController::class, 'storeIepp'])->name('iepps.store');

        Route::get('/types-absence', [AdminController::class, 'typesAbsence'])->name('types-absence.index');
        Route::post('/types-absence', [AdminController::class, 'storeTypeAbsence'])->name('types-absence.store');

        Route::get('/annees-scolaires', [AdminController::class, 'anneesScolaires'])->name('annees-scolaires.index');
        Route::post('/annees-scolaires', [AdminController::class, 'storeAnneeScolaire'])->name('annees-scolaires.store');

        Route::get('/audit', [AdminController::class, 'auditLogs'])->name('audit');
    });

    // Admin DRENA routes
    Route::prefix('gestion')->name('gestion.')->middleware('role:admin_drena')->group(function () {
        Route::get('/etablissements', function () {
            $etablissements = \App\Models\Etablissement::parDrena(auth()->user()->drena_id)
                ->withCount('users')
                ->with('iepp')
                ->orderBy('nom')
                ->paginate(20);
            return view('etablissements.index', compact('etablissements'));
        })->name('etablissements.index');
    });
});
