@extends('layouts.app')
@section('title', 'Absence ' . $absence->reference)
@section('page-title', 'Absence ' . $absence->reference)
@section('page-subtitle', $absence->user->nom_complet . ' — ' . $absence->typeAbsence->libelle)

@section('content')
<div class="max-w-4xl">
    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        {{-- Info principale --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold">
                        {{ $absence->user->initiales }}
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-900">{{ $absence->user->nom_complet }}</p>
                        <p class="text-xs text-gray-500">{{ $absence->user->matricule }} — {{ $absence->user->specialite ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $absence->etablissement->nom }}</p>
                    </div>
                </div>
                @php $badge = $absence->statut_badge; @endphp
                <span class="badge badge-{{ $badge['color'] }} text-sm px-3 py-1">{{ $badge['label'] }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm mb-5">
                <div><p class="text-gray-500 text-xs">Type d'absence</p><p class="font-medium flex items-center gap-1.5 mt-0.5"><span class="w-2.5 h-2.5 rounded-full" style="background:{{ $absence->typeAbsence->couleur }}"></span>{{ $absence->typeAbsence->libelle }}</p></div>
                <div><p class="text-gray-500 text-xs">Nombre de jours</p><p class="font-medium mt-0.5">{{ $absence->nombre_jours }} jour(s) ouvré(s)</p></div>
                <div><p class="text-gray-500 text-xs">Date de début</p><p class="font-medium mt-0.5">{{ $absence->date_debut->format('d/m/Y') }}{{ $absence->demi_journee_debut ? ' (½ journée)' : '' }}</p></div>
                <div><p class="text-gray-500 text-xs">Date de fin</p><p class="font-medium mt-0.5">{{ $absence->date_fin->format('d/m/Y') }}{{ $absence->demi_journee_fin ? ' (½ journée)' : '' }}</p></div>
                <div><p class="text-gray-500 text-xs">Heures de cours impactées</p><p class="font-medium mt-0.5 text-orange-600">{{ $absence->heures_cours_perdu }}h</p></div>
                <div><p class="text-gray-500 text-xs">Déclarée par</p><p class="font-medium mt-0.5">{{ $absence->declarant?->nom_complet ?? $absence->user->nom_complet }}</p></div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500 mb-1">Motif</p>
                <p class="text-sm text-gray-800 leading-relaxed">{{ $absence->motif }}</p>
                @if($absence->commentaire_agent)
                <p class="text-xs text-gray-500 mt-3 mb-1">Commentaire</p>
                <p class="text-sm text-gray-600 italic">{{ $absence->commentaire_agent }}</p>
                @endif
            </div>

            {{-- Justificatifs --}}
            @if($absence->justificatifs->count() > 0)
            <div class="border-t border-gray-100 pt-4 mt-4">
                <p class="text-xs text-gray-500 mb-2">Pièces justificatives ({{ $absence->justificatifs->count() }})</p>
                <div class="space-y-2">
                    @foreach($absence->justificatifs as $j)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <div class="flex-1"><p class="text-sm font-medium text-gray-900">{{ $j->nom_original }}</p><p class="text-xs text-gray-500">{{ $j->taille_formatee }}</p></div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Timeline workflow --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h4 class="text-sm font-semibold text-gray-900 mb-4">Workflow de validation</h4>
            <div class="space-y-4">
                {{-- Création --}}
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                        <div class="w-0.5 flex-1 bg-gray-200 mt-1"></div>
                    </div>
                    <div class="pb-4"><p class="text-sm font-medium text-gray-900">Déclaration soumise</p><p class="text-xs text-gray-500">{{ $absence->created_at->format('d/m/Y H:i') }}</p></div>
                </div>

                {{-- Validations --}}
                @foreach($absence->validations as $v)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full {{ $v->decision === 'approuvee' ? 'bg-emerald-100' : ($v->decision === 'refusee' ? 'bg-red-100' : 'bg-blue-100') }} flex items-center justify-center">
                            @if($v->decision === 'approuvee')<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @elseif($v->decision === 'refusee')<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @else<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>@endif
                        </div>
                        <div class="w-0.5 flex-1 bg-gray-200 mt-1"></div>
                    </div>
                    <div class="pb-4">
                        <p class="text-sm font-medium text-gray-900">Niveau {{ $v->niveau }} — @php $vb = $v->decision_badge; @endphp <span class="badge badge-{{ $vb['color'] }}">{{ $vb['label'] }}</span></p>
                        <p class="text-xs text-gray-500">{{ $v->valideur->nom_complet }} — {{ $v->date_validation->format('d/m/Y H:i') }}</p>
                        @if($v->commentaire)<p class="text-xs text-gray-600 mt-1 italic">"{{ $v->commentaire }}"</p>@endif
                    </div>
                </div>
                @endforeach

                {{-- Étapes restantes --}}
                @if(in_array($absence->statut, ['en_validation_n1', 'en_validation_n2', 'en_validation_n3']))
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center animate-pulse"><div class="w-3 h-3 rounded-full bg-amber-500"></div></div>
                    </div>
                    <div><p class="text-sm font-medium text-amber-700">En attente — Niveau {{ $absence->niveau_validation_actuel }}</p><p class="text-xs text-gray-500">Escalade auto dans 48h si pas de réponse</p></div>
                </div>
                @endif
            </div>

            {{-- Suppléance --}}
            @if($absence->suppleance)
            <div class="border-t border-gray-100 pt-4 mt-4">
                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Suppléance</h4>
                <p class="text-sm"><span class="font-medium">{{ $absence->suppleance->suppleant->nom_complet }}</span></p>
                <p class="text-xs text-gray-500">{{ ucfirst($absence->suppleance->statut) }} — {{ $absence->suppleance->heures_effectuees }}h effectuées</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Actions de validation --}}
    @if(in_array($absence->statut, ['en_validation_n1', 'en_validation_n2', 'en_validation_n3']))
        @php
            $canValidate = match($absence->statut) {
                'en_validation_n1' => auth()->user()->hasRole('chef_etablissement') && auth()->user()->etablissement_id === $absence->etablissement_id,
                'en_validation_n2' => auth()->user()->hasRole('inspecteur') && auth()->user()->iepp_id === $absence->iepp_id,
                'en_validation_n3' => auth()->user()->hasRole('admin_drena') && auth()->user()->drena_id === $absence->drena_id,
                default => false,
            };
        @endphp
        @if($canValidate)
        <div class="bg-white rounded-xl border-2 border-amber-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-amber-800 mb-4">Action de validation — Niveau {{ $absence->niveau_validation_actuel }}</h3>
            <form method="POST" action="{{ route('absences.valider', $absence) }}">
                @csrf
                <div class="mb-4">
                    <label for="commentaire" class="label">Commentaire (optionnel)</label>
                    <textarea name="commentaire" id="commentaire" rows="2" class="input" placeholder="Justification de votre décision..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" name="decision" value="approuvee" class="btn btn-success">Approuver</button>
                    <button type="submit" name="decision" value="complement_requis" class="btn btn-secondary">Demander un complément</button>
                    <button type="submit" name="decision" value="refusee" class="btn btn-danger">Refuser</button>
                </div>
            </form>
        </div>
        @endif
    @endif

    {{-- Assigner suppléant --}}
    @if($absence->statut === 'approuvee' && !$absence->suppleance && $suppleantsPossibles->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Assigner un suppléant</h3>
        <form method="POST" action="{{ route('absences.assigner-suppleant', $absence) }}">
            @csrf
            <div class="mb-4">
                <label for="suppleant_id" class="label">Suppléant disponible</label>
                <select name="suppleant_id" id="suppleant_id" class="input" required>
                    @foreach($suppleantsPossibles as $s)
                        <option value="{{ $s->id }}">{{ $s->nom_complet }} — {{ $s->specialite ?? 'N/A' }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Assigner</button>
        </form>
    </div>
    @endif

    {{-- Annuler --}}
    @if($absence->peutEtreAnnulee() && (auth()->id() === $absence->user_id || auth()->id() === $absence->declaree_par))
    <form method="POST" action="{{ route('absences.annuler', $absence) }}" onsubmit="return confirm('Confirmer l\'annulation ?')">
        @csrf
        <button type="submit" class="btn btn-danger">Annuler cette absence</button>
    </form>
    @endif
</div>
@endsection
