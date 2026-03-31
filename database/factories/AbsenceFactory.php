<?php

namespace Database\Factories;

use App\Models\Absence;
use App\Models\Drena;
use App\Models\Etablissement;
use App\Models\Iepp;
use App\Models\TypeAbsence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsenceFactory extends Factory
{
    protected $model = Absence::class;

    public function definition(): array
    {
        $dateDebut = $this->faker->dateTimeBetween('now', '+30 days');
        $dateFin = (clone $dateDebut)->modify('+' . rand(1, 5) . ' weekdays');
        $nombreJours = rand(1, 5);

        return [
            'reference' => 'ABS-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'user_id' => User::role('enseignant')->inRandomOrder()->first()?->id ?? User::factory(),
            'type_absence_id' => TypeAbsence::inRandomOrder()->first()?->id ?? 1,
            'etablissement_id' => Etablissement::inRandomOrder()->first()?->id ?? 1,
            'drena_id' => Drena::inRandomOrder()->first()?->id ?? 1,
            'iepp_id' => Iepp::inRandomOrder()->first()?->id ?? 1,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nombre_jours' => $nombreJours,
            'demi_journee_debut' => false,
            'demi_journee_fin' => false,
            'motif' => $this->faker->sentence(10),
            'statut' => 'brouillon',
            'niveau_validation_actuel' => 0,
            'declaree_par' => null,
        ];
    }

    public function soumise(): static
    {
        return $this->state(['statut' => 'en_validation_n1', 'niveau_validation_actuel' => 1]);
    }

    public function approuvee(): static
    {
        return $this->state(['statut' => 'approuvee', 'niveau_validation_actuel' => 1]);
    }

    public function refusee(): static
    {
        return $this->state(['statut' => 'refusee', 'niveau_validation_actuel' => 1]);
    }
}
