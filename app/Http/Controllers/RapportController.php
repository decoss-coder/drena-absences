<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Drena;
use App\Models\Etablissement;
use App\Models\TypeAbsence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RapportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $periode = $request->get('periode', 'mois');
        $dateDebut = $this->getDateDebut($periode, $request);
        $dateFin = $request->get('date_fin', now()->toDateString());

        $drenaId = $user->hasRole('super_admin') ? $request->get('drena_id') : $user->drena_id;

        $data = [
            'tauxAbsenteisme' => $this->getTauxAbsenteisme($drenaId, $dateDebut, $dateFin),
            'repartitionParType' => $this->getRepartitionParType($drenaId, $dateDebut, $dateFin),
            'evolutionMensuelle' => $this->getEvolutionMensuelle($drenaId),
            'topEtablissements' => $this->getTopEtablissements($drenaId, $dateDebut, $dateFin),
            'topAgents' => $this->getTopAgents($drenaId, $dateDebut, $dateFin),
            'repartitionParJour' => $this->getRepartitionParJour($drenaId, $dateDebut, $dateFin),
            'heuresCoursPerdu' => $this->getHeuresCoursPerdu($drenaId, $dateDebut, $dateFin),
            'comparatif' => $user->hasRole('super_admin') ? $this->getComparatifDrena($dateDebut, $dateFin) : null,
        ];

        $drenas = $user->hasRole('super_admin') ? Drena::actives()->orderBy('nom')->get() : collect();

        return view('rapports.index', compact('data', 'drenas', 'periode', 'dateDebut', 'dateFin', 'drenaId'));
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $drenaId = $user->hasRole('super_admin') ? $request->get('drena_id') : $user->drena_id;
        $dateDebut = $request->get('date_debut', now()->startOfMonth()->toDateString());
        $dateFin = $request->get('date_fin', now()->toDateString());

        $data = [
            'tauxAbsenteisme' => $this->getTauxAbsenteisme($drenaId, $dateDebut, $dateFin),
            'repartitionParType' => $this->getRepartitionParType($drenaId, $dateDebut, $dateFin),
            'topEtablissements' => $this->getTopEtablissements($drenaId, $dateDebut, $dateFin),
            'topAgents' => $this->getTopAgents($drenaId, $dateDebut, $dateFin),
            'drena' => $drenaId ? Drena::find($drenaId) : null,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('rapports.pdf.rapport', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'rapport_absences_' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $drenaId = $user->hasRole('super_admin') ? $request->get('drena_id') : $user->drena_id;
        $dateDebut = $request->get('date_debut', now()->startOfMonth()->toDateString());
        $dateFin = $request->get('date_fin', now()->toDateString());

        $query = Absence::with(['user', 'typeAbsence', 'etablissement', 'drena'])
            ->where('statut', 'approuvee')
            ->whereBetween('date_debut', [$dateDebut, $dateFin]);

        if ($drenaId) $query->where('drena_id', $drenaId);

        $absences = $query->orderBy('date_debut')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=absences_' . now()->format('Y-m-d') . '.csv',
        ];

        $callback = function () use ($absences) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, ['Référence', 'Matricule', 'Nom', 'Prénoms', 'Établissement', 'DRENA', 'Type', 'Date début', 'Date fin', 'Jours', 'Statut']);
            foreach ($absences as $a) {
                fputcsv($f, [
                    $a->reference, $a->user->matricule, $a->user->nom, $a->user->prenoms,
                    $a->etablissement->nom, $a->drena->nom, $a->typeAbsence->libelle,
                    $a->date_debut->format('d/m/Y'), $a->date_fin->format('d/m/Y'),
                    $a->nombre_jours, $a->statut,
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ──── Stats Methods ────

    private function getTauxAbsenteisme(?int $drenaId, string $debut, string $fin): array
    {
        $queryTotal = User::actifs();
        $queryAbsents = Absence::where('absences.statut', 'approuvee')
            ->where('absences.date_debut', '<=', $fin)->where('absences.date_fin', '>=', $debut);

        if ($drenaId) {
            $queryTotal->parDrena($drenaId);
            $queryAbsents->where('absences.drena_id', $drenaId);
        }

        $total = $queryTotal->count();
        $absents = $queryAbsents->distinct('user_id')->count('user_id');
        $totalJours = $queryAbsents->sum('absences.nombre_jours');

        return [
            'total_agents' => $total,
            'agents_absents' => $absents,
            'taux' => $total > 0 ? round(($absents / $total) * 100, 2) : 0,
            'total_jours' => $totalJours,
        ];
    }

    private function getRepartitionParType(?int $drenaId, string $debut, string $fin)
    {
        $query = Absence::select('type_absence_id', DB::raw('count(*) as total'), DB::raw('sum(absences.nombre_jours) as total_jours'))
            ->where('absences.statut', 'approuvee')
            ->whereBetween('absences.date_debut', [$debut, $fin])
            ->groupBy('type_absence_id');

        if ($drenaId) $query->where('absences.drena_id', $drenaId);

        return $query->with('typeAbsence')->get();
    }

    private function getEvolutionMensuelle(?int $drenaId): array
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

    private function getTopEtablissements(?int $drenaId, string $debut, string $fin)
    {
        $query = Etablissement::select('etablissements.*', DB::raw('count(absences.id) as total_absences'), DB::raw('sum(absences.nombre_jours) as total_jours'))
            ->join('absences', 'etablissements.id', '=', 'absences.etablissement_id')
            ->where('absences.statut', 'approuvee')
            ->whereBetween('absences.date_debut', [$debut, $fin])
            ->groupBy('etablissements.id')
            ->orderByDesc('total_absences')
            ->limit(10);

        if ($drenaId) $query->where('etablissements.drena_id', $drenaId);

        return $query->get();
    }

    private function getTopAgents(?int $drenaId, string $debut, string $fin)
    {
        $query = User::select('users.*', DB::raw('sum(absences.nombre_jours) as total_jours'), DB::raw('count(absences.id) as total_absences'))
            ->join('absences', 'users.id', '=', 'absences.user_id')
            ->where('absences.statut', 'approuvee')
            ->whereBetween('absences.date_debut', [$debut, $fin])
            ->groupBy('users.id')
            ->orderByDesc('total_jours')
            ->limit(10);

        if ($drenaId) $query->where('users.drena_id', $drenaId);

        return $query->get();
    }

    private function getRepartitionParJour(?int $drenaId, string $debut, string $fin): array
    {
        $query = Absence::select(DB::raw("DAYOFWEEK(absences.date_debut) as jour"), DB::raw('count(*) as total'))
            ->where('absences.statut', 'approuvee')
            ->whereBetween('absences.date_debut', [$debut, $fin])
            ->groupBy('jour')
            ->orderBy('jour');

        if ($drenaId) $query->where('absences.drena_id', $drenaId);

        return $query->get()->toArray();
    }

    private function getHeuresCoursPerdu(?int $drenaId, string $debut, string $fin): float
    {
        $query = Absence::join('users', 'absences.user_id', '=', 'users.id')
            ->where('absences.statut', 'approuvee')
            ->whereBetween('absences.date_debut', [$debut, $fin]);

        if ($drenaId) $query->where('absences.drena_id', $drenaId);

        return round($query->sum(DB::raw('users.volume_horaire_hebdo / 5 * absences.nombre_jours')), 1);
    }

    private function getComparatifDrena(string $debut, string $fin)
    {
        return Drena::actives()
            ->withCount(['users as total_agents' => fn($q) => $q->actifs()])
            ->withCount(['absences as total_absences' => fn($q) => $q->where('absences.statut', 'approuvee')->whereBetween('absences.date_debut', [$debut, $fin])])
            ->orderByDesc('total_absences')
            ->get();
    }

    private function getDateDebut(string $periode, Request $request): string
    {
        if ($request->filled('date_debut')) return $request->date_debut;
        return match($periode) {
            'semaine' => now()->startOfWeek()->toDateString(),
            'mois' => now()->startOfMonth()->toDateString(),
            'trimestre' => now()->startOfQuarter()->toDateString(),
            'annee' => now()->startOfYear()->toDateString(),
            default => now()->startOfMonth()->toDateString(),
        };
    }
}
