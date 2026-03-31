<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Drena;
use App\Models\Iepp;
use App\Models\Etablissement;
use App\Models\TypeAbsence;
use App\Models\AnneeScolaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ═══════════════ DRENA Management ═══════════════

    public function drenas()
    {
        $drenas = Drena::withCount(['users', 'etablissements', 'iepps'])
            ->orderBy('nom')
            ->paginate(20);
        return view('admin.drenas.index', compact('drenas'));
    }

    public function createDrena()
    {
        return view('admin.drenas.create');
    }

    public function storeDrena(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:drenas',
            'nom' => 'required|string|max:200',
            'region' => 'required|string|max:100',
            'chef_lieu' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'adresse' => 'nullable|string',
        ]);

        $drena = Drena::create($validated);

        return redirect()->route('admin.drenas.index')
            ->with('success', "DRENA {$drena->nom} créée avec succès.");
    }

    public function editDrena(Drena $drena)
    {
        return view('admin.drenas.edit', compact('drena'));
    }

    public function updateDrena(Request $request, Drena $drena)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:200',
            'region' => 'required|string|max:100',
            'chef_lieu' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'adresse' => 'nullable|string',
            'actif' => 'boolean',
        ]);

        $drena->update($validated);
        return redirect()->route('admin.drenas.index')->with('success', 'DRENA mise à jour.');
    }

    // ═══════════════ IEPP Management ═══════════════

    public function iepps(Request $request)
    {
        $query = Iepp::with('drena')->withCount('etablissements');
        if ($request->filled('drena_id')) $query->where('drena_id', $request->drena_id);
        $iepps = $query->orderBy('nom')->paginate(20)->withQueryString();
        $drenas = Drena::actives()->orderBy('nom')->get();
        return view('admin.iepps.index', compact('iepps', 'drenas'));
    }

    public function storeIepp(Request $request)
    {
        $validated = $request->validate([
            'drena_id' => 'required|exists:drenas,id',
            'code' => 'required|string|max:10|unique:iepps',
            'nom' => 'required|string|max:200',
            'localite' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]);

        Iepp::create($validated);
        return redirect()->route('admin.iepps.index')->with('success', 'IEPP créée.');
    }

    // ═══════════════ Types d'absences ═══════════════

    public function typesAbsence()
    {
        $types = TypeAbsence::orderBy('ordre')->get();
        return view('admin.types-absence.index', compact('types'));
    }

    public function storeTypeAbsence(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:type_absences',
            'libelle' => 'required|string|max:100',
            'description' => 'nullable|string',
            'justificatif_obligatoire' => 'boolean',
            'duree_max_jours' => 'nullable|integer|min:1',
            'niveau_validation_requis' => 'required|integer|min:1|max:3',
            'couleur' => 'required|string|max:7',
            'deductible_conge' => 'boolean',
        ]);

        TypeAbsence::create($validated);
        return redirect()->route('admin.types-absence.index')->with('success', 'Type d\'absence créé.');
    }

    // ═══════════════ Année scolaire ═══════════════

    public function anneesScolaires()
    {
        $annees = AnneeScolaire::orderByDesc('date_debut')->get();
        return view('admin.annees-scolaires.index', compact('annees'));
    }

    public function storeAnneeScolaire(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:20',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'trimestre1_debut' => 'nullable|date',
            'trimestre1_fin' => 'nullable|date',
            'trimestre2_debut' => 'nullable|date',
            'trimestre2_fin' => 'nullable|date',
            'trimestre3_debut' => 'nullable|date',
            'trimestre3_fin' => 'nullable|date',
        ]);

        if ($request->boolean('en_cours')) {
            AnneeScolaire::where('en_cours', true)->update(['en_cours' => false]);
            $validated['en_cours'] = true;
        }

        AnneeScolaire::create($validated);
        return redirect()->route('admin.annees-scolaires.index')->with('success', 'Année scolaire créée.');
    }

    // ═══════════════ Comptes Admin DRENA ═══════════════

    public function createAdminDrena(Drena $drena)
    {
        return view('admin.drenas.create-admin', compact('drena'));
    }

    public function storeAdminDrena(Request $request, Drena $drena)
    {
        $validated = $request->validate([
            'matricule' => 'required|string|max:20|unique:users',
            'nom' => 'required|string|max:100',
            'prenoms' => 'required|string|max:150',
            'email' => 'required|email|unique:users',
            'telephone' => 'nullable|string|max:20',
            'genre' => 'required|in:M,F',
        ]);

        $validated['drena_id'] = $drena->id;
        $validated['password'] = Hash::make('Drena@Admin' . date('Y'));

        $user = User::create($validated);
        $user->assignRole('admin_drena');

        return redirect()->route('admin.drenas.index')
            ->with('success', "Admin DRENA créé pour {$drena->nom}.");
    }

    // ═══════════════ Audit Logs ═══════════════

    public function auditLogs(Request $request)
    {
        $query = \Spatie\Activitylog\Models\Activity::with('causer')
            ->latest();

        if ($request->filled('causer_id')) $query->where('causer_id', $request->causer_id);
        if ($request->filled('subject_type')) $query->where('subject_type', 'like', "%{$request->subject_type}%");
        if ($request->filled('date')) $query->whereDate('created_at', $request->date);

        $logs = $query->paginate(30)->withQueryString();
        return view('admin.audit-logs', compact('logs'));
    }
}
