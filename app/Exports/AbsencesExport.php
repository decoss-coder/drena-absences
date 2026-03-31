<?php

namespace App\Exports;

use App\Models\Absence;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsencesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    use Exportable;

    public function __construct(
        private ?int $drenaId = null,
        private ?string $dateDebut = null,
        private ?string $dateFin = null,
        private ?string $statut = null,
    ) {}

    public function query(): Builder
    {
        $query = Absence::query()
            ->with(['user', 'typeAbsence', 'etablissement', 'drena', 'iepp'])
            ->orderBy('date_debut', 'desc');

        if ($this->drenaId) $query->where('drena_id', $this->drenaId);
        if ($this->statut) $query->where('statut', $this->statut);
        if ($this->dateDebut) $query->where('date_debut', '>=', $this->dateDebut);
        if ($this->dateFin) $query->where('date_fin', '<=', $this->dateFin);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Matricule',
            'Nom',
            'Prénoms',
            'DRENA',
            'IEPP',
            'Établissement',
            'Spécialité',
            'Type d\'absence',
            'Date début',
            'Date fin',
            'Nombre de jours',
            'Demi-journée début',
            'Demi-journée fin',
            'Motif',
            'Statut',
            'Heures cours perdues',
            'Date création',
        ];
    }

    public function map($absence): array
    {
        return [
            $absence->reference,
            $absence->user->matricule,
            $absence->user->nom,
            $absence->user->prenoms,
            $absence->drena->nom,
            $absence->iepp->nom,
            $absence->etablissement->nom,
            $absence->user->specialite ?? '—',
            $absence->typeAbsence->libelle,
            $absence->date_debut->format('d/m/Y'),
            $absence->date_fin->format('d/m/Y'),
            $absence->nombre_jours,
            $absence->demi_journee_debut ? 'Oui' : 'Non',
            $absence->demi_journee_fin ? 'Oui' : 'Non',
            $absence->motif,
            $absence->statut,
            $absence->heures_cours_perdu,
            $absence->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1B4F72']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Absences';
    }
}
