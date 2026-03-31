<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Drena;
use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match(true) {
            $user->hasRole('super_admin') => $this->dashboardMena(),
            $user->hasRole('admin_drena') => $this->dashboardDrena($user),
            $user->hasRole('inspecteur') => $this->dashboardInspecteur($user),
            $user->hasRole('chef_etablissement') => $this->dashboardChefEtablissement($user),
            $user->hasRole('gestionnaire_rh') => $this->dashboardGestionnaireRh($user),
            default => $this->dashboardAgent($user),
        };
    }

    private function dashboardMena()
    {
        $stats = [
            'total_drena' => Drena::actives()->count(),
            'total_agents' => User::actifs()->count(),
            'total_etablissements' => Etablissement::actifs()->count(),
            'absences_en_cours' => Absence::enCours()->count(),
            'absences_en_attente' => Absence::enAttente()->count(),
            'taux_absenteisme' => $this->calculerTauxNational(),
        ];

        $absencesParDrena = Drena::actives()
            ->withCount(['absences as absences_en_cours_count' => fn($q) => $q->enCours()])
            ->orderByDesc('absences_en_cours_count')
            ->limit(10)
            ->get();

        $evolutionMensuelle = $this->getEvolutionMensuelle();

        $repartitionParType = Absence::select('type_absence_id', DB::raw('count(*) as total'))
            ->whereYear('absences.created_at', now()->year)
            ->where('absences.statut', 'approuvee')
            ->groupBy('type_absence_id')
            ->with('typeAbsence')
            ->get();

        $alertes = $this->getAlertesNationales();

        return view('dashboard.mena', compact(
            'stats', 'absencesParDrena', 'evolutionMensuelle', 'repartitionParType', 'alertes'
        ));
    }

    private function dashboardDrena(User $user)
    {
        $drenaId = $user->drena_id;

        $stats = [
            'total_agents' => User::actifs()->parDrena($drenaId)->count(),
            'total_etablissements' => Etablissement::actifs()->parDrena($drenaId)->count(),
            'absences_en_cours' => Absence::enCours()->parDrena($drenaId)->count(),
            'absences_en_attente' => Absence::enAttente()->parDrena($drenaId)->count(),
            'taux_absenteisme' => $this->calculerTauxDrena($drenaId),
            'heures_perdues' => $this->calculerHeuresPerdues($drenaId),
        ];

        $absencesParIepp = DB::table('absences')
            ->join('iepps', 'absences.iepp_id', '=', 'iepps.id')
            ->where('absences.drena_id', $drenaId)
            ->where('absences.statut', 'approuvee')
            ->where('absences.date_debut', '<=', today())
            ->where('absences.date_fin', '>=', today())
            ->select('iepps.nom', DB::raw('count(*) as total'))
            ->groupBy('iepps.nom')
            ->orderByDesc('total')
            ->get();

        $demandesEnAttente = Absence::with(['user', 'typeAbsence', 'etablissement'])
            ->parDrena($drenaId)
            ->enAttente()
            ->latest()
            ->limit(10)
            ->get();

        $evolutionMensuelle = $this->getEvolutionMensuelle($drenaId);
        $topAbsents = $this->getTopAbsents($drenaId);

        return view('dashboard.drena', compact(
            'stats', 'absencesParIepp', 'demandesEnAttente', 'evolutionMensuelle', 'topAbsents'
        ));
    }

    private function dashboardInspecteur(User $user)
    {
        $ieppId = $user->iepp_id;

        $stats = [
            'total_etablissements' => Etablissement::actifs()->parIepp($ieppId)->count(),
            'absences_en_cours' => Absence::enCours()->where('iepp_id', $ieppId)->count(),
            'a_valider' => Absence::aValiderPar($user)->count(),
        ];

        $demandesAValider = Absence::with(['user', 'typeAbsence', 'etablissement'])
            ->aValiderPar($user)
            ->latest()
            ->limit(20)
            ->get();

        $absencesParEtablissement = Etablissement::parIepp($ieppId)
            ->withCount(['absences as absences_count' => fn($q) => $q->enCours()])
            ->orderByDesc('absences_count')
            ->get();

        return view('dashboard.inspecteur', compact('stats', 'demandesAValider', 'absencesParEtablissement'));
    }

    private function dashboardChefEtablissement(User $user)
    {
        $etablissementId = $user->etablissement_id;

        $stats = [
            'total_enseignants' => User::actifs()->parEtablissement($etablissementId)->count(),
            'absences_du_jour' => Absence::enCours()->parEtablissement($etablissementId)->count(),
            'a_valider' => Absence::aValiderPar($user)->count(),
            'suppleances_en_cours' => DB::table('suppleances')
                ->where('etablissement_id', $etablissementId)
                ->where('statut', 'en_cours')
                ->count(),
        ];

        $demandesAValider = Absence::with(['user', 'typeAbsence'])
            ->aValiderPar($user)
            ->latest()
            ->get();

        $calendrierMensuel = Absence::with(['user', 'typeAbsence'])
            ->parEtablissement($etablissementId)
            ->where('statut', 'approuvee')
            ->where('date_debut', '<=', now()->endOfMonth())
            ->where('date_fin', '>=', now()->startOfMonth())
            ->get();

        $personnelAbsentAujourdhui = Absence::with('user')
            ->enCours()
            ->parEtablissement($etablissementId)
            ->get();

        return view('dashboard.chef-etablissement', compact(
            'stats', 'demandesAValider', 'calendrierMensuel', 'personnelAbsentAujourdhui'
        ));
    }

    private function dashboardGestionnaireRh(User $user)
    {
        $drenaId = $user->drena_id;

        $stats = [
            'total_agents' => User::actifs()->parDrena($drenaId)->count(),
            'absences_en_cours' => Absence::enCours()->parDrena($drenaId)->count(),
            'conges_en_cours' => Absence::enCours()->parDrena($drenaId)
                ->whereHas('typeAbsence', fn($q) => $q->where('code', 'CONGE'))->count(),
        ];

        return view('dashboard.gestionnaire-rh', compact('stats'));
    }

    private function dashboardAgent(User $user)
    {
        $mesAbsences = $user->absences()
            ->with('typeAbsence')
            ->latest()
            ->limit(10)
            ->get();

        $stats = [
            'total_absences' => $user->absences()->whereYear('created_at', now()->year)->count(),
            'jours_absences' => $user->absences()
                ->where('statut', 'approuvee')
                ->whereYear('created_at', now()->year)
                ->sum('nombre_jours'),
            'en_attente' => $user->absences()->enAttente()->count(),
            'solde_conge' => $user->getSoldeConge(),
        ];

        $absenceEnCours = $user->absences()->enCours()->with('suppleance.suppleant')->first();

        return view('dashboard.agent', compact('mesAbsences', 'stats', 'absenceEnCours'));
    }

    // ──── Helper Methods ────

    private function calculerTauxNational(): float
    {
        $total = User::actifs()->count();
        if ($total === 0) return 0;
        $absents = Absence::enCours()->distinct('user_id')->count('user_id');
        return round(($absents / $total) * 100, 2);
    }

    private function calculerTauxDrena(int $drenaId): float
    {
        $total = User::actifs()->parDrena($drenaId)->count();
        if ($total === 0) return 0;
        $absents = Absence::enCours()->parDrena($drenaId)->distinct('user_id')->count('user_id');
        return round(($absents / $total) * 100, 2);
    }

    private function calculerHeuresPerdues(int $drenaId): float
    {
        return Absence::enCours()
            ->parDrena($drenaId)
            ->join('users', 'absences.user_id', '=', 'users.id')
            ->sum(DB::raw('users.volume_horaire_hebdo / 5 * absences.nombre_jours'));
    }

    private function getEvolutionMensuelle(?int $drenaId = null): array
    {
        $query = Absence::select(
            DB::raw("DATE_FORMAT(absences.date_debut, '%Y-%m') as mois"),
            DB::raw('count(*) as total'),
            DB::raw('sum(absences.nombre_jours) as total_jours')
        )
        ->where('absences.statut', 'approuvee')
        ->where('absences.date_debut', '>=', now()->subMonths(12))
        ->groupBy('mois')
        ->orderBy('mois');

        if ($drenaId) $query->where('absences.drena_id', $drenaId);

        return $query->get()->toArray();
    }

    private function getTopAbsents(?int $drenaId = null): \Illuminate\Support\Collection
    {
        $query = User::select('users.*', DB::raw('sum(absences.nombre_jours) as total_jours'))
            ->join('absences', 'users.id', '=', 'absences.user_id')
            ->where('absences.statut', 'approuvee')
            ->whereYear('absences.date_debut', now()->year)
            ->groupBy('users.id')
            ->orderByDesc('total_jours')
            ->limit(10);

        if ($drenaId) $query->where('users.drena_id', $drenaId);

        return $query->get();
    }

    private function getAlertesNationales(): \Illuminate\Support\Collection
    {
        return Drena::actives()
            ->withCount(['absences as absences_en_cours_count' => fn($q) => $q->enCours()])
            ->having('absences_en_cours_count', '>', 20)
            ->orderByDesc('absences_en_cours_count')
            ->get();
    }
}
