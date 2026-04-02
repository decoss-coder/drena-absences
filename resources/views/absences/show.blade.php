@extends('layouts.app')
@section('title', 'Absence ' . $absence->reference)
@section('page-title', 'Absence ' . $absence->reference)
@section('page-subtitle', $absence->user->nom_complet . ' — ' . $absence->typeAbsence->libelle)
@section('content')
<div class="max-w-4xl">

{{-- Badge circuit --}}
<div class="flex items-center gap-3 mb-6">
    <span class="badge {{ $absence->circuit_validation === 'primaire' ? 'badge-blue' : 'badge-amber' }} text-xs px-3 py-1.5">
        {{ $absence->circuit_label }}
    </span>
    @php $badge = $absence->statut_badge; @endphp
    <span class="badge badge-{{ $badge['color'] }} text-xs px-3 py-1.5">{{ $badge['label'] }}</span>
</div>

<div class="grid lg:grid-cols-3 gap-6 mb-6">
    {{-- Détail de l'absence --}}
    <div class="lg:col-span-2 card-white p-6">
        <div class="flex items-start justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-violet-500/15">{{ $absence->user->initiales }}</div>
                <div>
                    <p class="text-base font-semibold text-gray-800">{{ $absence->user->nom_complet }}</p>
                    <p class="text-xs text-gray-400">{{ $absence->user->matricule }} — {{ $absence->user->specialite ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-400">{{ $absence->etablissement->nom }}
                        <span class="inline-flex items-center ml-1 px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $absence->circuit_validation === 'primaire' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600' }}">{{ ucfirst($absence->circuit_validation ?? 'primaire') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-5">
            <div><p class="text-gray-400 text-xs">Type</p><p class="font-medium text-gray-700 flex items-center gap-1.5 mt-1"><span class="w-2.5 h-2.5 rounded-full" style="background:{{ $absence->typeAbsence->couleur }}"></span>{{ $absence->typeAbsence->libelle }}</p></div>
            <div><p class="text-gray-400 text-xs">Nombre de jours</p><p class="font-medium text-gray-700 mt-1">{{ $absence->nombre_jours }} jour(s)</p></div>
            <div><p class="text-gray-400 text-xs">Date début</p><p class="font-medium text-gray-700 mt-1">{{ $absence->date_debut->format('d/m/Y') }}</p></div>
            <div><p class="text-gray-400 text-xs">Date fin</p><p class="font-medium text-gray-700 mt-1">{{ $absence->date_fin->format('d/m/Y') }}</p></div>
            <div><p class="text-gray-400 text-xs">Heures impactées</p><p class="font-medium text-orange-500 mt-1">{{ $absence->heures_cours_perdu }}h</p></div>
            <div><p class="text-gray-400 text-xs">Prochain valideur</p><p class="font-medium text-violet-600 mt-1">{{ $absence->prochain_valideur }}</p></div>
        </div>

        <div class="border-t border-violet-50 pt-4">
            <p class="text-xs text-gray-400 mb-1">Motif</p>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $absence->motif }}</p>
            @if($absence->commentaire_agent)
            <p class="text-xs text-gray-400 mt-3 mb-1">Commentaire</p>
            <p class="text-sm text-gray-500 italic">{{ $absence->commentaire_agent }}</p>
            @endif
        </div>

        @if($absence->justificatifs->count() > 0)
        <div class="border-t border-violet-50 pt-4 mt-4">
            <p class="text-xs text-gray-400 mb-2">Justificatifs ({{ $absence->justificatifs->count() }})</p>
            <div class="space-y-2">
                @foreach($absence->justificatifs as $j)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-violet-50/60 border border-violet-100">
                    <svg class="w-5 h-5 text-violet-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <div class="flex-1"><p class="text-sm font-medium text-gray-700">{{ $j->nom_original }}</p><p class="text-xs text-gray-400">{{ $j->taille_formatee }}</p></div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Workflow visuel --}}
    <div class="card-white p-6">
        <h4 class="text-sm font-semibold text-gray-700 mb-4">Circuit de validation</h4>

        {{-- Étapes du workflow dynamique --}}
        @php
            $etapes = $absence->getEtapesWorkflow();
            $validationsMap = $absence->validations->keyBy('niveau');
            $statutsOrdre = [
                \App\Models\Absence::STATUT_EN_VALIDATION_CHEF => 1,
                \App\Models\Absence::STATUT_EN_VALIDATION_INSPECTEUR => 2,
                \App\Models\Absence::STATUT_EN_VALIDATION_DRENA => $absence->passe_par_inspecteur ? 3 : 2,
            ];
            $statutActuelOrdre = $statutsOrdre[$absence->statut] ?? 99;
        @endphp

        <div class="space-y-4">
            {{-- Soumission initiale --}}
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="w-0.5 flex-1 bg-violet-100 mt-1"></div>
                </div>
                <div class="pb-4">
                    <p class="text-sm font-medium text-gray-700">Soumise</p>
                    <p class="text-xs text-gray-400">{{ $absence->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            {{-- Validations enregistrées --}}
            @foreach($absence->validations as $v)
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full {{ $v->decision==='approuvee'?'bg-emerald-50':($v->decision==='refusee'?'bg-red-50':'bg-blue-50') }} flex items-center justify-center">
                        @if($v->decision === 'approuvee')
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($v->decision === 'refusee')
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                        @endif
                    </div>
                    <div class="w-0.5 flex-1 bg-violet-100 mt-1"></div>
                </div>
                <div class="pb-4">
                    <p class="text-sm font-medium text-gray-700">
                        {{ $v->valideur->nom_complet }}
                        <span class="text-xs text-gray-400 ml-1">({{ $v->valideur->getRoleNames()->first() }})</span>
                    </p>
                    <p class="text-xs text-gray-400">{{ $v->date_validation->format('d/m/Y H:i') }}</p>
                    @if($v->commentaire)
                    <p class="text-xs text-gray-500 mt-1 italic bg-gray-50 rounded-lg p-2">"{{ $v->commentaire }}"</p>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Étape en cours (pulsation) --}}
            @if(in_array($absence->statut, [
                \App\Models\Absence::STATUT_EN_VALIDATION_CHEF,
                \App\Models\Absence::STATUT_EN_VALIDATION_INSPECTEUR,
                \App\Models\Absence::STATUT_EN_VALIDATION_DRENA,
            ]))
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center animate-pulse">
                        <div class="w-3 h-3 rounded-full bg-violet-500"></div>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-violet-700">En attente — {{ $absence->prochain_valideur }}</p>
                    <p class="text-xs text-gray-400">Escalade auto 48h</p>
                </div>
            </div>
            @endif

            {{-- Résultat final --}}
            @if($absence->statut === \App\Models\Absence::STATUT_APPROUVEE)
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-semibold text-emerald-700 pt-1">Approuvée</p>
            </div>
            @elseif($absence->statut === \App\Models\Absence::STATUT_REFUSEE)
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-semibold text-red-700 pt-1">Refusée</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Formulaire de validation --}}
@if($absence->peutEtreValideePar(auth()->user()))
<div class="card-white p-6 mb-6 border-l-4 border-l-violet-500">
    <h3 class="text-sm font-semibold text-violet-700 mb-1">Validation requise</h3>
    <p class="text-xs text-gray-400 mb-4">Vous êtes le {{ $absence->prochain_valideur }} pour cette absence ({{ ucfirst($absence->circuit_validation) }}).</p>
    <form method="POST" action="{{ route('absences.valider', $absence) }}">
        @csrf
        <div class="mb-4">
            <label for="commentaire" class="label">Commentaire</label>
            <textarea name="commentaire" id="commentaire" rows="2" class="glass-input" placeholder="Votre justification..."></textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" name="decision" value="approuvee" class="btn-success">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Approuver
            </button>
            <button type="submit" name="decision" value="complement_requis" class="btn-secondary">Complément</button>
            <button type="submit" name="decision" value="refusee" class="btn-danger">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Refuser
            </button>
        </div>
    </form>
</div>
@endif

{{-- Suppléance --}}
@if($absence->statut === \App\Models\Absence::STATUT_APPROUVEE && !$absence->suppleance && $suppleantsPossibles->count() > 0)
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-4">Assigner un suppléant</h3>
    <form method="POST" action="{{ route('absences.assigner-suppleant', $absence) }}">
        @csrf
        <div class="mb-4">
            <label class="label">Suppléant disponible</label>
            <select name="suppleant_id" class="glass-input" required>
                @foreach($suppleantsPossibles as $s)
                <option value="{{ $s->id }}">{{ $s->nom_complet }} — {{ $s->specialite ?? 'N/A' }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Assigner</button>
    </form>
</div>
@endif

{{-- Annulation --}}
@if($absence->peutEtreAnnulee() && (auth()->id() === $absence->user_id || auth()->id() === $absence->declaree_par))
<form method="POST" action="{{ route('absences.annuler', $absence) }}" onsubmit="return confirm('Confirmer l\'annulation ?')">
    @csrf
    <button type="submit" class="btn-danger">Annuler cette absence</button>
</form>
@endif

</div>
@endsection
