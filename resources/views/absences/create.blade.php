@extends('layouts.app')
@section('title', 'Déclarer une absence')
@section('page-title', 'Déclarer une absence')
@section('page-subtitle', 'Remplissez le formulaire pour soumettre votre demande')

@section('content')
<form method="POST" action="{{ route('absences.store') }}" enctype="multipart/form-data" class="max-w-3xl">
    @csrf

    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Informations de l'absence</h3>

        @hasanyrole('chef_etablissement|admin_drena')
        <div class="mb-5">
            <label for="user_id" class="label">Déclarer pour</label>
            <select name="user_id" id="user_id" class="input">
                <option value="">— Moi-même —</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" {{ old('user_id') == $agent->id ? 'selected' : '' }}>
                        {{ $agent->matricule }} — {{ $agent->nom_complet }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Laissez vide pour déclarer votre propre absence.</p>
        </div>
        @endhasanyrole

        <div class="mb-5">
            <label for="type_absence_id" class="label">Type d'absence <span class="text-red-500">*</span></label>
            <select name="type_absence_id" id="type_absence_id" class="input" required>
                <option value="">Sélectionnez un type</option>
                @foreach($typesAbsence as $type)
                    <option value="{{ $type->id }}" {{ old('type_absence_id') == $type->id ? 'selected' : '' }}
                            data-justificatif="{{ $type->justificatif_obligatoire ? '1' : '0' }}"
                            data-max-jours="{{ $type->duree_max_jours }}">
                        {{ $type->libelle }}
                        @if($type->justificatif_obligatoire) (justificatif obligatoire) @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-5">
            <div>
                <label for="date_debut" class="label">Date de début <span class="text-red-500">*</span></label>
                <input type="date" name="date_debut" id="date_debut" class="input" value="{{ old('date_debut') }}" required min="{{ date('Y-m-d') }}">
            </div>
            <div>
                <label for="date_fin" class="label">Date de fin <span class="text-red-500">*</span></label>
                <input type="date" name="date_fin" id="date_fin" class="input" value="{{ old('date_fin') }}" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-5">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="demi_journee_debut" value="1" {{ old('demi_journee_debut') ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Demi-journée (début)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="demi_journee_fin" value="1" {{ old('demi_journee_fin') ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Demi-journée (fin)</span>
            </label>
        </div>

        {{-- Calcul automatique --}}
        <div id="calcul-jours" class="mb-5 px-4 py-3 rounded-lg bg-blue-50 border border-blue-200 text-sm text-blue-800 hidden">
            Durée calculée : <strong id="nombre-jours">0</strong> jour(s) ouvré(s)
        </div>

        <div class="mb-5">
            <label for="motif" class="label">Motif détaillé <span class="text-red-500">*</span></label>
            <textarea name="motif" id="motif" rows="3" class="input" required placeholder="Décrivez la raison de votre absence...">{{ old('motif') }}</textarea>
        </div>

        <div>
            <label for="commentaire_agent" class="label">Commentaire (optionnel)</label>
            <textarea name="commentaire_agent" id="commentaire_agent" rows="2" class="input" placeholder="Information complémentaire...">{{ old('commentaire_agent') }}</textarea>
        </div>
    </div>

    {{-- Justificatifs --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-1">Pièces justificatives</h3>
        <p class="text-xs text-gray-500 mb-4" id="justificatif-info">Ajoutez vos justificatifs si nécessaire.</p>

        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition cursor-pointer" id="dropzone" onclick="document.getElementById('justificatifs').click()">
            <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            <p class="text-sm text-gray-600 font-medium">Cliquez ou glissez vos fichiers ici</p>
            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG — max 5 Mo par fichier</p>
        </div>
        <input type="file" name="justificatifs[]" id="justificatifs" multiple accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="previewFiles(this)">

        <div id="file-list" class="space-y-2 mt-4"></div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        <button type="submit" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            Soumettre la demande
        </button>
        <a href="{{ route('absences.index') }}" class="btn btn-secondary">Annuler</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Calcul automatique des jours
const dateDebut = document.getElementById('date_debut');
const dateFin = document.getElementById('date_fin');
const calculDiv = document.getElementById('calcul-jours');
const nombreJoursEl = document.getElementById('nombre-jours');

function calculerJours() {
    if (!dateDebut.value || !dateFin.value) { calculDiv.classList.add('hidden'); return; }
    const d1 = new Date(dateDebut.value), d2 = new Date(dateFin.value);
    if (d2 < d1) { calculDiv.classList.add('hidden'); return; }
    let jours = 0, current = new Date(d1);
    while (current <= d2) {
        const day = current.getDay();
        if (day !== 0 && day !== 6) jours++;
        current.setDate(current.getDate() + 1);
    }
    const demiDebut = document.querySelector('[name="demi_journee_debut"]').checked;
    const demiFin = document.querySelector('[name="demi_journee_fin"]').checked;
    if (demiDebut) jours -= 0.5;
    if (demiFin) jours -= 0.5;
    nombreJoursEl.textContent = jours;
    calculDiv.classList.remove('hidden');
}

dateDebut.addEventListener('change', function() { dateFin.min = this.value; calculerJours(); });
dateFin.addEventListener('change', calculerJours);
document.querySelectorAll('[name^="demi_journee"]').forEach(el => el.addEventListener('change', calculerJours));

// Justificatif obligatoire
document.getElementById('type_absence_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const info = document.getElementById('justificatif-info');
    if (opt.dataset.justificatif === '1') {
        info.textContent = 'Un justificatif est OBLIGATOIRE pour ce type d\'absence.';
        info.className = 'text-xs text-red-600 mb-4 font-medium';
    } else {
        info.textContent = 'Ajoutez vos justificatifs si nécessaire.';
        info.className = 'text-xs text-gray-500 mb-4';
    }
});

// Preview fichiers
function previewFiles(input) {
    const list = document.getElementById('file-list');
    list.innerHTML = '';
    Array.from(input.files).forEach((file, i) => {
        const size = file.size > 1048576 ? (file.size/1048576).toFixed(1)+' Mo' : (file.size/1024).toFixed(0)+' Ko';
        list.innerHTML += `<div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
            <svg class="w-8 h-8 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            <div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 truncate">${file.name}</p><p class="text-xs text-gray-500">${size}</p></div>
            <span class="text-xs font-medium text-emerald-600">Prêt</span>
        </div>`;
    });
}
</script>
@endpush
