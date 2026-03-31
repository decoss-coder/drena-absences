<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration DRENA Absences
    |--------------------------------------------------------------------------
    |
    | Paramètres métier de l'application de gestion des absences.
    |
    */

    // Escalade automatique (en heures) si pas de validation
    'escalation_hours' => env('ABSENCE_AUTO_ESCALATION_HOURS', 48),

    // Seuils de validation par nombre de jours
    'max_jours_niveau1' => env('ABSENCE_MAX_DAYS_LEVEL1', 3),
    'max_jours_niveau2' => env('ABSENCE_MAX_DAYS_LEVEL2', 10),

    // Congés annuels par défaut (jours)
    'conge_annuel_jours' => 30,

    // Taille max des justificatifs (Ko)
    'justificatif_max_size' => 5120,

    // Formats acceptés pour les justificatifs
    'justificatif_mimes' => ['pdf', 'jpg', 'jpeg', 'png'],

    // Nombre max de fichiers par absence
    'justificatif_max_files' => 5,

    // Seuil d'alerte absentéisme (%)
    'seuil_alerte_taux' => 10,

    // Seuil d'alerte individuel (jours/an)
    'seuil_alerte_individuel' => 15,

    // Politique de mot de passe
    'password_min_length' => 8,
    'password_require_uppercase' => true,
    'password_require_number' => true,
    'password_require_special' => true,

    // Verrouillage de compte
    'max_login_attempts' => 5,
    'lockout_minutes' => 30,

    // Session
    'session_lifetime_minutes' => 30,

    // SMS
    'sms_driver' => env('SMS_DRIVER', 'orange'),
    'sms_sender' => env('SMS_ORANGE_SENDER', 'DRENA'),

    // Pagination par défaut
    'pagination' => 20,

    // Année scolaire
    'annee_scolaire_debut_mois' => 9,  // Septembre
    'annee_scolaire_fin_mois' => 7,     // Juillet
];
