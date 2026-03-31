<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PersonnelExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(private ?int $drenaId = null) {}

    public function query(): Builder
    {
        $q = User::query()->with(['drena', 'iepp', 'etablissement'])->actifs()->orderBy('nom');
        if ($this->drenaId) $q->where('drena_id', $this->drenaId);
        return $q;
    }

    public function headings(): array
    {
        return ['Matricule', 'Nom', 'Prénoms', 'Genre', 'Email', 'Téléphone', 'Grade', 'Échelon', 'Spécialité', 'DRENA', 'IEPP', 'Établissement', 'Date intégration', 'Volume horaire', 'Statut'];
    }

    public function map($user): array
    {
        return [
            $user->matricule, $user->nom, $user->prenoms, $user->genre,
            $user->email, $user->telephone ?? '', $user->grade ?? '',
            $user->echelon ?? '', $user->specialite ?? '',
            $user->drena?->nom ?? '', $user->iepp?->nom ?? '',
            $user->etablissement?->nom ?? '',
            $user->date_integration?->format('d/m/Y') ?? '',
            $user->volume_horaire_hebdo . 'h',
            ucfirst($user->statut),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1B4F72']]]];
    }
}
