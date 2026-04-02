<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Etablissement;
use App\Models\Justificatif;
use App\Models\Suppleance;
use App\Models\TypeAbsence;
use App\Models\User;
use App\Notifications\AbsenceSoumise;
use App\Notifications\AbsenceValidee;
use App\Notifications\AbsenceRefusee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Absence::with(['user', 'typeAbsence', 'etablissement', 'drena']);

        // Scope par rôle
        if ($user->hasRole('super_admin')) {
            // Voir toutes les absences
        } elseif ($user->hasRole('admin_drena')) {
            $query->parDrena($user->drena_id);
        } elseif ($user->hasRole('inspecteur')) {
            $query->where('iepp_id', $user->iepp_id);
        } elseif ($user->hasRole('chef_etablissement')) {
            $query->parEtablissement($user->etablissement_id);
        } else {
            $query->where('user_id', $user->id);
        }

        // Filtres
        if ($request->filled('statut')) $query->parStatut($request->statut);
        if ($request->filled('type_absence_id')) $query->where('type_absence_id', $request->type_absence_id);
        if ($request->filled('date_debut')) $query->where('date_debut', '>=', $request->date_debut);
        if ($request->filled('date_fin')) $query->where('date_fin', '<=', $request->date_fin);
        if ($request->filled('etablissement_id')) $query->parEtablissement($request->etablissement_id);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q) => $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%")
                      ->orWhere('matricule', 'like', "%{$search}%"));
            });
        }

        $absences = $query->latest()->paginate(20)->withQueryString();
        $typesAbsence = TypeAbsence::actifs()->get();

        return view('absences.index', compact('absences', 'typesAbsence'));
    }

    public function create()
    {
        $user = Auth::user();
        $typesAbsence = TypeAbsence::actifs()->get();

        // Si c'est un chef d'établissement, il peut déclarer pour ses agents
        $agents = collect();
        if ($user->hasRole('chef_etablissement')) {
            $agents = User::actifs()
                ->parEtablissement($user->etablissement_id)
                ->orderBy('nom')
                ->get();
        }

        return view('absences.create', compact('typesAbsence', 'agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'type_absence_id' => 'required|exists:type_absences,id',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'demi_journee_debut' => 'boolean',
            'demi_journee_fin' => 'boolean',
            'motif' => 'required|string|max:1000',
            'commentaire_agent' => 'nullable|string|max:500',
            'justificatifs.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'date_debut.after_or_equal' => 'La date de début doit être aujourd\'hui ou ultérieure.',
            'date_fin.after_or_equal' => 'La date de fin doit être après la date de début.',
            'justificatifs.*.max' => 'Chaque fichier ne doit pas dépasser 5 Mo.',
        ]);

        $user = Auth::user();
        $agentId = $request->user_id ?? $user->id;
        $agent = User::findOrFail($agentId);

        // Vérifier les chevauchements
        $chevauchement = Absence::where('user_id', $agentId)
            ->whereNotIn('statut', ['annulee', 'refusee'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('date_debut', [$request->date_debut, $request->date_fin])
                  ->orWhereBetween('date_fin', [$request->date_debut, $request->date_fin])
                  ->orWhere(function ($q) use ($request) {
                      $q->where('date_debut', '<=', $request->date_debut)
                        ->where('date_fin', '>=', $request->date_fin);
                  });
            })->exists();

        if ($chevauchement) {
            return back()->withErrors(['date_debut' => 'Une absence existe déjà sur cette période.'])->withInput();
        }

        // Calculer le nombre de jours
        $debut = \Carbon\Carbon::parse($request->date_debut);
        $fin = \Carbon\Carbon::parse($request->date_fin);
        $nombreJours = $debut->diffInWeekdays($fin) + 1;
        if ($request->boolean('demi_journee_debut')) $nombreJours -= 0.5;
        if ($request->boolean('demi_journee_fin')) $nombreJours -= 0.5;

        // Vérifier le type d'absence
        $typeAbsence = TypeAbsence::findOrFail($request->type_absence_id);
        if ($typeAbsence->justificatif_obligatoire && !$request->hasFile('justificatifs')) {
            return back()->withErrors(['justificatifs' => 'Un justificatif est obligatoire pour ce type d\'absence.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $absence = Absence::create([
                'reference' => Absence::genererReference(),
                'user_id' => $agentId,
                'type_absence_id' => $request->type_absence_id,
                'etablissement_id' => $agent->etablissement_id,
                'drena_id' => $agent->drena_id,
                'iepp_id' => $agent->iepp_id,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'nombre_jours' => $nombreJours,
                'demi_journee_debut' => $request->boolean('demi_journee_debut'),
                'demi_journee_fin' => $request->boolean('demi_journee_fin'),
                'motif' => $request->motif,
                'commentaire_agent' => $request->commentaire_agent,
                'statut' => 'soumise',
                'niveau_validation_actuel' => 1,
                'declaree_par' => $user->id,
            ]);

            // Upload des justificatifs
            if ($request->hasFile('justificatifs')) {
                foreach ($request->file('justificatifs') as $file) {
                    $path = $file->store("justificatifs/{$absence->id}", 'local');
                    Justificatif::create([
                        'absence_id' => $absence->id,
                        'nom_fichier' => $file->hashName(),
                        'nom_original' => $file->getClientOriginalName(),
                        'chemin' => $path,
                        'type_mime' => $file->getMimeType(),
                        'taille' => $file->getSize(),
                        'uploade_par' => $user->id,
                    ]);
                }
            }

            // Soumettre l'absence (détermine le circuit primaire/secondaire)
            $absence->soumettre();

            // Notifier le chef d'établissement
            $chefEtab = User::role('chef_etablissement')
                ->where('etablissement_id', $agent->etablissement_id)
                ->first();

            if ($chefEtab) {
                $chefEtab->notify(new AbsenceSoumise($absence));
            }

            DB::commit();

            return redirect()->route('absences.show', $absence)
                ->with('success', "Absence {$absence->reference} déclarée avec succès.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la création : ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Absence $absence)
    {
        $this->autoriserAcces($absence);

        $absence->load([
            'user', 'typeAbsence', 'etablissement', 'drena', 'iepp',
            'validations.valideur', 'justificatifs', 'suppleance.suppleant',
            'declarant',
        ]);

        $suppleantsPossibles = collect();
        if (Auth::user()->hasAnyRole(['chef_etablissement', 'admin_drena']) && $absence->statut === 'approuvee' && !$absence->suppleance) {
            $suppleantsPossibles = Suppleance::trouverSuppleants($absence);
        }

        return view('absences.show', compact('absence', 'suppleantsPossibles'));
    }

    public function valider(Request $request, Absence $absence)
    {
        $request->validate([
            'decision' => 'required|in:approuvee,refusee,complement_requis',
            'commentaire' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Vérifier que l'utilisateur peut valider (tient compte du circuit)
        if (!$absence->peutEtreValideePar($user)) {
            abort(403, 'Vous n\'êtes pas autorisé à valider cette absence.');
        }

        $validation = $absence->valider($user, $request->decision, $request->commentaire);

        // Notifications selon le nouveau statut
        $absence->refresh();

        if ($absence->statut === Absence::STATUT_APPROUVEE) {
            // Fin du circuit → notifier l'agent
            $absence->user->notify(new AbsenceValidee($absence));
        } elseif ($absence->statut === Absence::STATUT_REFUSEE) {
            $absence->user->notify(new AbsenceRefusee($absence));
        } elseif ($absence->statut === Absence::STATUT_EN_VALIDATION_INSPECTEUR) {
            // Primaire : escalade vers l'inspecteur
            $inspecteur = User::role('inspecteur')
                ->where('iepp_id', $absence->iepp_id)
                ->first();
            if ($inspecteur) {
                $inspecteur->notify(new AbsenceSoumise($absence));
            }
        } elseif ($absence->statut === Absence::STATUT_EN_VALIDATION_DRENA) {
            // Escalade vers la DRENA (primaire après inspecteur, ou secondaire après chef)
            $adminDrena = User::role('admin_drena')
                ->where('drena_id', $absence->drena_id)
                ->first();
            if ($adminDrena) {
                $adminDrena->notify(new AbsenceSoumise($absence));
            }
        }

        $message = match($request->decision) {
            'approuvee' => 'Absence approuvée avec succès.',
            'refusee' => 'Absence refusée.',
            'complement_requis' => 'Demande de complément envoyée.',
        };

        return redirect()->route('absences.show', $absence)->with('success', $message);
    }

    public function annuler(Absence $absence)
    {
        $user = Auth::user();

        if (!$absence->peutEtreAnnulee()) {
            return back()->withErrors(['error' => 'Cette absence ne peut plus être annulée.']);
        }

        if ($user->id !== $absence->user_id && $user->id !== $absence->declaree_par) {
            abort(403);
        }

        $absence->update(['statut' => 'annulee']);

        return redirect()->route('absences.index')
            ->with('success', "Absence {$absence->reference} annulée.");
    }

    public function assignerSuppleant(Request $request, Absence $absence)
    {
        $request->validate([
            'suppleant_id' => 'required|exists:users,id',
            'commentaire' => 'nullable|string|max:500',
        ]);

        $suppleance = Suppleance::create([
            'absence_id' => $absence->id,
            'titulaire_id' => $absence->user_id,
            'suppleant_id' => $request->suppleant_id,
            'etablissement_id' => $absence->etablissement_id,
            'assigne_par' => Auth::id(),
            'date_debut' => $absence->date_debut,
            'date_fin' => $absence->date_fin,
            'statut' => 'proposee',
            'commentaire' => $request->commentaire,
        ]);

        // Notifier le suppléant
        $suppleant = User::find($request->suppleant_id);
        // $suppleant->notify(new SuppleanceProposee($suppleance));

        return redirect()->route('absences.show', $absence)
            ->with('success', 'Suppléant assigné avec succès.');
    }

    public function calendrier(Request $request)
    {
        $user = Auth::user();
        $mois = $request->get('mois', now()->month);
        $annee = $request->get('annee', now()->year);

        $query = Absence::with(['user', 'typeAbsence'])
            ->where('statut', 'approuvee')
            ->whereMonth('date_debut', '<=', $mois)
            ->whereMonth('date_fin', '>=', $mois)
            ->whereYear('date_debut', $annee);

        if ($user->hasRole('chef_etablissement')) {
            $query->parEtablissement($user->etablissement_id);
        } elseif ($user->hasRole('inspecteur')) {
            $query->where('iepp_id', $user->iepp_id);
        } elseif ($user->hasRole('admin_drena')) {
            $query->parDrena($user->drena_id);
        }

        $absences = $query->get();

        return view('absences.calendrier', compact('absences', 'mois', 'annee'));
    }

    private function autoriserAcces(Absence $absence): void
    {
        $user = Auth::user();

        $autorise = $user->hasRole('super_admin')
            || ($user->hasRole('admin_drena') && $user->drena_id === $absence->drena_id)
            || ($user->hasRole('inspecteur') && $user->iepp_id === $absence->iepp_id)
            || ($user->hasRole('chef_etablissement') && $user->etablissement_id === $absence->etablissement_id)
            || ($user->hasRole('gestionnaire_rh') && $user->drena_id === $absence->drena_id)
            || $user->id === $absence->user_id;

        if (!$autorise) abort(403);
    }
}
