@extends('layouts.app')
@section('title', 'Déclarer une absence')
@section('page-title', 'Déclarer une absence')
@section('page-subtitle', 'Remplissez le formulaire')
@section('content')
<form method="POST" action="{{ route('absences.store') }}" enctype="multipart/form-data" class="max-w-3xl">@csrf
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-5">Informations de l'absence</h3>
    @hasanyrole('chef_etablissement|admin_drena')
    <div class="mb-5"><label class="label">Déclarer pour</label><select name="user_id" class="glass-input"><option value="">— Moi-même —</option>@foreach($agents as $a)<option value="{{ $a->id }}" {{ old('user_id')==$a->id?'selected':'' }}>{{ $a->matricule }} — {{ $a->nom_complet }}</option>@endforeach</select></div>
    @endhasanyrole
    <div class="mb-5"><label class="label">Type d'absence <span class="text-red-400">*</span></label><select name="type_absence_id" id="type_absence_id" class="glass-input" required><option value="">Sélectionnez</option>@foreach($typesAbsence as $t)<option value="{{ $t->id }}" {{ old('type_absence_id')==$t->id?'selected':'' }} data-justificatif="{{ $t->justificatif_obligatoire?'1':'0' }}">{{ $t->libelle }}@if($t->justificatif_obligatoire) (justif. obligatoire)@endif</option>@endforeach</select></div>
    <div class="grid grid-cols-2 gap-4 mb-5"><div><label class="label">Date début <span class="text-red-400">*</span></label><input type="date" name="date_debut" id="date_debut" class="glass-input" value="{{ old('date_debut') }}" required min="{{ date('Y-m-d') }}"></div><div><label class="label">Date fin <span class="text-red-400">*</span></label><input type="date" name="date_fin" id="date_fin" class="glass-input" value="{{ old('date_fin') }}" required></div></div>
    <div class="grid grid-cols-2 gap-4 mb-5">
        <label class="flex items-center gap-2.5 cursor-pointer"><input type="checkbox" name="demi_journee_debut" value="1" {{ old('demi_journee_debut')?'checked':'' }} class="w-4 h-4 rounded-md border-gray-300 text-violet-600 focus:ring-violet-500"><span class="text-sm text-gray-500">Demi-journée (début)</span></label>
        <label class="flex items-center gap-2.5 cursor-pointer"><input type="checkbox" name="demi_journee_fin" value="1" {{ old('demi_journee_fin')?'checked':'' }} class="w-4 h-4 rounded-md border-gray-300 text-violet-600 focus:ring-violet-500"><span class="text-sm text-gray-500">Demi-journée (fin)</span></label>
    </div>
    <div id="calcul-jours" class="mb-5 px-4 py-3 rounded-xl bg-violet-50 border border-violet-100 text-sm text-violet-700 hidden">Durée : <strong id="nombre-jours">0</strong> jour(s) ouvré(s)</div>
    <div class="mb-5"><label class="label">Motif <span class="text-red-400">*</span></label><textarea name="motif" rows="3" class="glass-input" required placeholder="Décrivez la raison...">{{ old('motif') }}</textarea></div>
    <div><label class="label">Commentaire (optionnel)</label><textarea name="commentaire_agent" rows="2" class="glass-input" placeholder="Info complémentaire...">{{ old('commentaire_agent') }}</textarea></div>
</div>
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-1">Justificatifs</h3>
    <p class="text-xs text-gray-400 mb-4" id="justificatif-info">Ajoutez vos fichiers si nécessaire.</p>
    <div class="border-2 border-dashed border-violet-200 rounded-2xl p-8 text-center hover:border-violet-400 transition cursor-pointer bg-violet-50/30" onclick="document.getElementById('justificatifs').click()">
        <svg class="w-10 h-10 text-violet-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
        <p class="text-sm text-gray-500 font-medium">Cliquez ou glissez</p><p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG — max 5 Mo</p>
    </div>
    <input type="file" name="justificatifs[]" id="justificatifs" multiple accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="pvF(this)">
    <div id="file-list" class="space-y-2 mt-4"></div>
</div>
<div class="flex gap-3"><button type="submit" class="btn-primary"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>Soumettre</button><a href="{{ route('absences.index') }}" class="btn-secondary">Annuler</a></div>
</form>
@endsection
@push('scripts')
<script>
const dD=document.getElementById('date_debut'),dF=document.getElementById('date_fin'),cD=document.getElementById('calcul-jours'),nJ=document.getElementById('nombre-jours');function calc(){if(!dD.value||!dF.value){cD.classList.add('hidden');return}const d1=new Date(dD.value),d2=new Date(dF.value);if(d2<d1){cD.classList.add('hidden');return}let j=0,c=new Date(d1);while(c<=d2){if(c.getDay()!==0&&c.getDay()!==6)j++;c.setDate(c.getDate()+1)}if(document.querySelector('[name="demi_journee_debut"]')?.checked)j-=.5;if(document.querySelector('[name="demi_journee_fin"]')?.checked)j-=.5;nJ.textContent=j;cD.classList.remove('hidden')}dD.addEventListener('change',function(){dF.min=this.value;calc()});dF.addEventListener('change',calc);document.querySelectorAll('[name^="demi_journee"]').forEach(e=>e.addEventListener('change',calc));
document.getElementById('type_absence_id').addEventListener('change',function(){const o=this.options[this.selectedIndex],i=document.getElementById('justificatif-info');if(o.dataset.justificatif==='1'){i.textContent='Justificatif OBLIGATOIRE';i.className='text-xs text-red-500 mb-4 font-semibold'}else{i.textContent='Ajoutez si nécessaire.';i.className='text-xs text-gray-400 mb-4'}});
function pvF(input){const l=document.getElementById('file-list');l.innerHTML='';Array.from(input.files).forEach(f=>{const s=f.size>1048576?(f.size/1048576).toFixed(1)+' Mo':(f.size/1024).toFixed(0)+' Ko';l.innerHTML+=`<div class="flex items-center gap-3 p-3 rounded-xl bg-violet-50 border border-violet-100"><svg class="w-5 h-5 text-violet-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg><div class="flex-1"><p class="text-sm font-medium text-gray-700">${f.name}</p><p class="text-xs text-gray-400">${s}</p></div><span class="text-xs font-semibold text-emerald-600">Prêt</span></div>`})}
</script>
@endpush
