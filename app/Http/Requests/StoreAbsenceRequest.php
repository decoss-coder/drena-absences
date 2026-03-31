<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbsenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'type_absence_id' => 'required|exists:type_absences,id',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'demi_journee_debut' => 'boolean',
            'demi_journee_fin' => 'boolean',
            'motif' => 'required|string|min:10|max:1000',
            'commentaire_agent' => 'nullable|string|max:500',
            'justificatifs' => 'nullable|array|max:5',
            'justificatifs.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'type_absence_id.required' => 'Veuillez sélectionner un type d\'absence.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.after_or_equal' => 'La date de début doit être aujourd\'hui ou ultérieure.',
            'date_fin.after_or_equal' => 'La date de fin doit être après ou égale à la date de début.',
            'motif.required' => 'Le motif est obligatoire.',
            'motif.min' => 'Le motif doit contenir au moins 10 caractères.',
            'justificatifs.max' => 'Vous ne pouvez pas joindre plus de 5 fichiers.',
            'justificatifs.*.max' => 'Chaque fichier ne doit pas dépasser 5 Mo.',
            'justificatifs.*.mimes' => 'Les fichiers doivent être au format PDF, JPG ou PNG.',
        ];
    }
}
