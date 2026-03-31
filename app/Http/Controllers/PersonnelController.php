<?php

namespace App\Http\Controllers;

use App\Models\Drena;
use App\Models\Etablissement;
use App\Models\Iepp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PersonnelController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = User::with(['drena', 'iepp', 'etablissement']);

        if ($user->hasRole('admin_drena')) $query->parDrena($user->drena_id);
        elseif ($user->hasRole('inspecteur')) $query->where('iepp_id', $user->iepp_id);
        elseif ($user->hasRole('chef_etablissement')) $query->parEtablissement($user->etablissement_id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nom', 'like', "%{$s}%")
                ->orWhere('prenoms', 'like', "%{$s}%")
                ->orWhere('matricule', 'like', "%{$s}%"));
        }
        if ($request->filled('statut')) $query->where('statut', $request->statut);
        if ($request->filled('grade')) $query->where('grade', $request->grade);
        if ($request->filled('specialite')) $query->where('specialite', $request->specialite);
        if ($request->filled('etablissement_id')) $query->parEtablissement($request->etablissement_id);

        $personnel = $query->orderBy('nom')->paginate(20)->withQueryString();
        $etablissements = $this->getEtablissementsAccessibles($user);

        return view('personnel.index', compact('personnel', 'etablissements'));
    }

    public function create()
    {
        $user = Auth::user();
        $etablissements = $this->getEtablissementsAccessibles($user);
        $drenas = $user->hasRole('super_admin') ? Drena::actives()->get() : collect();
        return view('personnel.create', compact('etablissements', 'drenas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string|max:20|unique:users',
            'nom' => 'required|string|max:100',
            'prenoms' => 'required|string|max:150',
            'email' => 'required|email|unique:users',
            'telephone' => 'nullable|string|max:20',
            'genre' => 'required|in:M,F',
            'date_naissance' => 'nullable|date|before:today',
            'grade' => 'nullable|string|max:50',
            'echelon' => 'nullable|string|max:20',
            'specialite' => 'nullable|string|max:100',
            'date_integration' => 'nullable|date',
            'volume_horaire_hebdo' => 'nullable|integer|min:0|max:40',
            'etablissement_id' => 'required|exists:etablissements,id',
            'photo' => 'nullable|image|max:2048',
        ]);

        $etablissement = Etablissement::findOrFail($validated['etablissement_id']);
        $validated['drena_id'] = $etablissement->drena_id;
        $validated['iepp_id'] = $etablissement->iepp_id;
        $validated['password'] = Hash::make('Drena@' . date('Y'));

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $user = User::create($validated);
        $user->assignRole('enseignant');

        return redirect()->route('personnel.show', $user)
            ->with('success', 'Agent créé avec succès. Mot de passe par défaut : Drena@' . date('Y'));
    }

    public function show(User $personnel)
    {
        $personnel->load(['drena', 'iepp', 'etablissement', 'absences' => fn($q) => $q->latest()->limit(10), 'absences.typeAbsence', 'congeSoldes']);

        $statsAbsences = [
            'total_annee' => $personnel->absences()->whereYear('date_debut', now()->year)->where('statut', 'approuvee')->sum('nombre_jours'),
            'en_cours' => $personnel->absences()->enCours()->count(),
            'solde_conge' => $personnel->getSoldeConge(),
        ];

        return view('personnel.show', compact('personnel', 'statsAbsences'));
    }

    public function edit(User $personnel)
    {
        $user = Auth::user();
        $etablissements = $this->getEtablissementsAccessibles($user);
        return view('personnel.edit', compact('personnel', 'etablissements'));
    }

    public function update(Request $request, User $personnel)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenoms' => 'required|string|max:150',
            'email' => "required|email|unique:users,email,{$personnel->id}",
            'telephone' => 'nullable|string|max:20',
            'genre' => 'required|in:M,F',
            'grade' => 'nullable|string|max:50',
            'echelon' => 'nullable|string|max:20',
            'specialite' => 'nullable|string|max:100',
            'volume_horaire_hebdo' => 'nullable|integer|min:0|max:40',
            'etablissement_id' => 'required|exists:etablissements,id',
            'statut' => 'required|in:actif,conge,suspendu,radie,retraite',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($personnel->photo) Storage::disk('public')->delete($personnel->photo);
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $etablissement = Etablissement::findOrFail($validated['etablissement_id']);
        $validated['drena_id'] = $etablissement->drena_id;
        $validated['iepp_id'] = $etablissement->iepp_id;

        $personnel->update($validated);

        return redirect()->route('personnel.show', $personnel)
            ->with('success', 'Agent mis à jour avec succès.');
    }

    public function destroy(User $personnel)
    {
        $personnel->update(['actif' => false]);
        $personnel->delete();

        return redirect()->route('personnel.index')
            ->with('success', 'Agent désactivé avec succès.');
    }

    private function getEtablissementsAccessibles(User $user)
    {
        if ($user->hasRole('super_admin')) return Etablissement::actifs()->with('drena')->get();
        if ($user->hasRole('admin_drena')) return Etablissement::actifs()->parDrena($user->drena_id)->get();
        if ($user->hasRole('inspecteur')) return Etablissement::actifs()->parIepp($user->iepp_id)->get();
        return Etablissement::where('id', $user->etablissement_id)->get();
    }
}
