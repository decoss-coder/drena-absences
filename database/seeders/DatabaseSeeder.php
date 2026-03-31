<?php

namespace Database\Seeders;

use App\Models\Absence;
use App\Models\AnneeScolaire;
use App\Models\CongeSolde;
use App\Models\Drena;
use App\Models\Etablissement;
use App\Models\Iepp;
use App\Models\TypeAbsence;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ═══════════════ ROLES & PERMISSIONS ═══════════════
        $permissions = [
            'absences.view', 'absences.create', 'absences.validate_n1',
            'absences.validate_n2', 'absences.validate_n3', 'absences.cancel',
            'absences.export', 'personnel.view', 'personnel.create',
            'personnel.edit', 'personnel.delete', 'etablissements.manage',
            'rapports.view', 'rapports.export', 'admin.drena.manage',
            'admin.config', 'admin.audit', 'suppleance.assign',
        ];
        foreach ($permissions as $p) Permission::firstOrCreate(['name' => $p]);

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        $adminDrena = Role::firstOrCreate(['name' => 'admin_drena']);
        $adminDrena->syncPermissions(['absences.view', 'absences.validate_n3', 'absences.export', 'personnel.view', 'personnel.create', 'personnel.edit', 'etablissements.manage', 'rapports.view', 'rapports.export', 'suppleance.assign']);

        $inspecteur = Role::firstOrCreate(['name' => 'inspecteur']);
        $inspecteur->syncPermissions(['absences.view', 'absences.validate_n2', 'personnel.view', 'rapports.view']);

        $gestionnaireRh = Role::firstOrCreate(['name' => 'gestionnaire_rh']);
        $gestionnaireRh->syncPermissions(['absences.view', 'absences.export', 'personnel.view', 'personnel.create', 'personnel.edit', 'rapports.view', 'rapports.export']);

        $chefEtab = Role::firstOrCreate(['name' => 'chef_etablissement']);
        $chefEtab->syncPermissions(['absences.view', 'absences.create', 'absences.validate_n1', 'absences.cancel', 'personnel.view', 'suppleance.assign']);

        $enseignant = Role::firstOrCreate(['name' => 'enseignant']);
        $enseignant->syncPermissions(['absences.view', 'absences.create', 'absences.cancel']);

        // ═══════════════ TYPES D'ABSENCES ═══════════════
        $types = [
            ['code' => 'MALADIE', 'libelle' => 'Maladie', 'justificatif_obligatoire' => true, 'niveau_validation_requis' => 1, 'couleur' => '#E74C3C', 'deductible_conge' => false, 'ordre' => 1],
            ['code' => 'CONGE', 'libelle' => 'Congé annuel', 'justificatif_obligatoire' => false, 'niveau_validation_requis' => 1, 'couleur' => '#3498DB', 'deductible_conge' => true, 'duree_max_jours' => 30, 'ordre' => 2],
            ['code' => 'MATERNITE', 'libelle' => 'Congé maternité', 'justificatif_obligatoire' => true, 'niveau_validation_requis' => 2, 'couleur' => '#E91E8C', 'deductible_conge' => false, 'duree_max_jours' => 98, 'ordre' => 3],
            ['code' => 'PATERNITE', 'libelle' => 'Congé paternité', 'justificatif_obligatoire' => true, 'niveau_validation_requis' => 1, 'couleur' => '#8E44AD', 'deductible_conge' => false, 'duree_max_jours' => 3, 'ordre' => 4],
            ['code' => 'MISSION', 'libelle' => 'Mission/Formation', 'justificatif_obligatoire' => true, 'niveau_validation_requis' => 1, 'couleur' => '#2ECC71', 'deductible_conge' => false, 'ordre' => 5],
            ['code' => 'CONVEN', 'libelle' => 'Convenance personnelle', 'justificatif_obligatoire' => false, 'niveau_validation_requis' => 1, 'couleur' => '#F39C12', 'deductible_conge' => true, 'duree_max_jours' => 5, 'ordre' => 6],
            ['code' => 'DECES', 'libelle' => 'Décès d\'un proche', 'justificatif_obligatoire' => true, 'niveau_validation_requis' => 1, 'couleur' => '#2C3E50', 'deductible_conge' => false, 'duree_max_jours' => 5, 'ordre' => 7],
            ['code' => 'GREVE', 'libelle' => 'Grève', 'justificatif_obligatoire' => false, 'niveau_validation_requis' => 1, 'couleur' => '#95A5A6', 'deductible_conge' => false, 'ordre' => 8],
            ['code' => 'ABANDON', 'libelle' => 'Abandon de poste', 'justificatif_obligatoire' => false, 'niveau_validation_requis' => 3, 'couleur' => '#C0392B', 'deductible_conge' => false, 'ordre' => 9],
            ['code' => 'FAMILLE', 'libelle' => 'Événement familial', 'justificatif_obligatoire' => false, 'niveau_validation_requis' => 1, 'couleur' => '#1ABC9C', 'deductible_conge' => true, 'duree_max_jours' => 3, 'ordre' => 10],
        ];
        foreach ($types as $t) TypeAbsence::firstOrCreate(['code' => $t['code']], $t);

        // ═══════════════ ANNÉE SCOLAIRE ═══════════════
        $annee = AnneeScolaire::firstOrCreate(
            ['libelle' => '2025-2026'],
            ['date_debut' => '2025-09-08', 'date_fin' => '2026-07-03', 'trimestre1_debut' => '2025-09-08', 'trimestre1_fin' => '2025-12-19',
             'trimestre2_debut' => '2026-01-05', 'trimestre2_fin' => '2026-03-27', 'trimestre3_debut' => '2026-04-13', 'trimestre3_fin' => '2026-07-03', 'en_cours' => true]
        );

        // ═══════════════ DRENA + IEPP + ÉTABLISSEMENTS ═══════════════
        $drenasData = [
            ['code' => 'ABJ1', 'nom' => 'DRENA Abidjan 1', 'region' => 'Abidjan', 'chef_lieu' => 'Plateau',
             'iepps' => [
                 ['code' => 'PLA', 'nom' => 'IEPP Plateau', 'etabs' => [
                     ['code' => 'ABJ1-PLA-001', 'nom' => 'Lycée Classique d\'Abidjan', 'type' => 'secondaire_general'],
                     ['code' => 'ABJ1-PLA-002', 'nom' => 'Collège Moderne du Plateau', 'type' => 'secondaire_general'],
                 ]],
                 ['code' => 'ADJ', 'nom' => 'IEPP Adjamé', 'etabs' => [
                     ['code' => 'ABJ1-ADJ-001', 'nom' => 'Groupe Scolaire Adjamé Nord', 'type' => 'primaire'],
                     ['code' => 'ABJ1-ADJ-002', 'nom' => 'EPP Adjamé 12', 'type' => 'primaire'],
                 ]],
             ]],
            ['code' => 'ABJ2', 'nom' => 'DRENA Abidjan 2', 'region' => 'Abidjan', 'chef_lieu' => 'Cocody',
             'iepps' => [
                 ['code' => 'COC', 'nom' => 'IEPP Cocody', 'etabs' => [
                     ['code' => 'ABJ2-COC-001', 'nom' => 'Lycée Scientifique de Cocody', 'type' => 'secondaire_general'],
                     ['code' => 'ABJ2-COC-002', 'nom' => 'Collège Moderne de Cocody', 'type' => 'secondaire_general'],
                 ]],
             ]],
            ['code' => 'ABJ3', 'nom' => 'DRENA Abidjan 3', 'region' => 'Abidjan', 'chef_lieu' => 'Yopougon',
             'iepps' => [
                 ['code' => 'YOP', 'nom' => 'IEPP Yopougon', 'etabs' => [
                     ['code' => 'ABJ3-YOP-001', 'nom' => 'Lycée Municipal de Yopougon', 'type' => 'secondaire_general'],
                 ]],
             ]],
            ['code' => 'BKE1', 'nom' => 'DRENA Bouaké 1', 'region' => 'Gbêkê', 'chef_lieu' => 'Bouaké',
             'iepps' => [
                 ['code' => 'BKE', 'nom' => 'IEPP Bouaké Centre', 'etabs' => [
                     ['code' => 'BKE1-BKE-001', 'nom' => 'Lycée Moderne de Bouaké', 'type' => 'secondaire_general'],
                 ]],
             ]],
            ['code' => 'SPI', 'nom' => 'DRENA San-Pédro', 'region' => 'San-Pédro', 'chef_lieu' => 'San-Pédro',
             'iepps' => [
                 ['code' => 'SPD', 'nom' => 'IEPP San-Pédro', 'etabs' => [
                     ['code' => 'SPI-SPD-001', 'nom' => 'EPP San-Pédro 1', 'type' => 'primaire'],
                 ]],
             ]],
        ];

        $specialites = ['Mathématiques', 'Français', 'Anglais', 'SVT', 'Physique-Chimie', 'Histoire-Géographie', 'EPS', 'Philosophie', 'Espagnol', 'Allemand'];
        $grades = ['Instituteur', 'Instituteur Adjoint', 'Professeur de collège', 'Professeur de lycée', 'Professeur certifié'];
        $prenomsFemme = ['Aminata', 'Fatoumata', 'Affoué', 'Adjoua', 'Aïcha', 'Marie', 'Marguerite', 'Bintou', 'Rokia', 'Awa'];
        $prenomsHomme = ['Kouadio', 'Sékou', 'Ibrahim', 'Yao', 'Kouassi', 'Moussa', 'Dramane', 'Ouattara', 'Bakary', 'Félix'];
        $noms = ['KOUADIO', 'BAMBA', 'COULIBALY', 'KONÉ', 'DIALLO', 'TOURÉ', 'YAO', 'OUATTARA', 'N\'GUESSAN', 'DIABATÉ', 'KOFFI', 'TRAORÉ', 'KONATÉ', 'DEMBÉLÉ', 'SANOGO'];

        $userIndex = 1;

        // Super Admin MENA
        $mena = User::firstOrCreate(['email' => 'admin@education.gouv.ci'], [
            'matricule' => 'MENA-001', 'nom' => 'KONÉ', 'prenoms' => 'Mariatou',
            'email' => 'admin@education.gouv.ci', 'password' => Hash::make('Mena@2026'),
            'genre' => 'F', 'statut' => 'actif', 'actif' => true, 'email_verified_at' => now(),
        ]);
        $mena->assignRole('super_admin');

        foreach ($drenasData as $drenaData) {
            $drena = Drena::firstOrCreate(['code' => $drenaData['code']], [
                'nom' => $drenaData['nom'], 'region' => $drenaData['region'], 'chef_lieu' => $drenaData['chef_lieu'], 'actif' => true,
            ]);

            // Admin DRENA
            $adminUser = User::firstOrCreate(['matricule' => 'ADM-' . $drenaData['code']], [
                'nom' => $noms[array_rand($noms)], 'prenoms' => $prenomsHomme[array_rand($prenomsHomme)],
                'email' => strtolower($drenaData['code']) . '.admin@education.gouv.ci',
                'password' => Hash::make('Drena@2026'), 'genre' => 'M', 'drena_id' => $drena->id,
                'statut' => 'actif', 'actif' => true, 'email_verified_at' => now(),
            ]);
            $adminUser->assignRole('admin_drena');

            // Gestionnaire RH
            $rhUser = User::firstOrCreate(['matricule' => 'RH-' . $drenaData['code']], [
                'nom' => $noms[array_rand($noms)], 'prenoms' => $prenomsFemme[array_rand($prenomsFemme)],
                'email' => strtolower($drenaData['code']) . '.rh@education.gouv.ci',
                'password' => Hash::make('Drena@2026'), 'genre' => 'F', 'drena_id' => $drena->id,
                'statut' => 'actif', 'actif' => true, 'email_verified_at' => now(),
            ]);
            $rhUser->assignRole('gestionnaire_rh');

            foreach ($drenaData['iepps'] as $ieppData) {
                $iepp = Iepp::firstOrCreate(['code' => $ieppData['code']], [
                    'drena_id' => $drena->id, 'nom' => $ieppData['nom'], 'actif' => true,
                ]);

                // Inspecteur
                $inspUser = User::firstOrCreate(['matricule' => 'INS-' . $ieppData['code']], [
                    'nom' => $noms[array_rand($noms)], 'prenoms' => $prenomsHomme[array_rand($prenomsHomme)],
                    'email' => strtolower($ieppData['code']) . '.insp@education.gouv.ci',
                    'password' => Hash::make('Drena@2026'), 'genre' => 'M',
                    'drena_id' => $drena->id, 'iepp_id' => $iepp->id,
                    'statut' => 'actif', 'actif' => true, 'email_verified_at' => now(),
                ]);
                $inspUser->assignRole('inspecteur');

                foreach ($ieppData['etabs'] as $etabData) {
                    $etab = Etablissement::firstOrCreate(['code' => $etabData['code']], [
                        'drena_id' => $drena->id, 'iepp_id' => $iepp->id,
                        'nom' => $etabData['nom'], 'type' => $etabData['type'],
                        'statut_juridique' => 'public', 'actif' => true,
                    ]);

                    // Chef d'établissement
                    $chefUser = User::firstOrCreate(['matricule' => 'CHF-' . $etabData['code']], [
                        'nom' => $noms[array_rand($noms)], 'prenoms' => $prenomsHomme[array_rand($prenomsHomme)],
                        'email' => 'chef.' . strtolower(str_replace('-', '', $etabData['code'])) . '@education.gouv.ci',
                        'password' => Hash::make('Drena@2026'), 'genre' => 'M',
                        'drena_id' => $drena->id, 'iepp_id' => $iepp->id, 'etablissement_id' => $etab->id,
                        'statut' => 'actif', 'actif' => true, 'email_verified_at' => now(),
                    ]);
                    $chefUser->assignRole('chef_etablissement');

                    // 5-8 enseignants par établissement
                    $nbEnseignants = rand(5, 8);
                    for ($i = 0; $i < $nbEnseignants; $i++) {
                        $genre = rand(0, 1) ? 'M' : 'F';
                        $prenoms = $genre === 'M' ? $prenomsHomme : $prenomsFemme;
                        $nom = $noms[array_rand($noms)];
                        $prenom = $prenoms[array_rand($prenoms)];

                        $ensUser = User::create([
                            'matricule' => sprintf('ENS-%s-%04d', date('Y'), $userIndex++),
                            'nom' => $nom, 'prenoms' => $prenom,
                            'email' => strtolower($prenom . '.' . $nom . $userIndex) . '@education.gouv.ci',
                            'password' => Hash::make('Drena@2026'), 'genre' => $genre,
                            'telephone' => '07' . rand(10000000, 99999999),
                            'grade' => $grades[array_rand($grades)],
                            'specialite' => $specialites[array_rand($specialites)],
                            'date_integration' => now()->subYears(rand(1, 20))->subMonths(rand(0, 11)),
                            'volume_horaire_hebdo' => rand(18, 24),
                            'drena_id' => $drena->id, 'iepp_id' => $iepp->id, 'etablissement_id' => $etab->id,
                            'statut' => 'actif', 'actif' => true, 'email_verified_at' => now(),
                        ]);
                        $ensUser->assignRole('enseignant');

                        // Solde congé
                        CongeSolde::create([
                            'user_id' => $ensUser->id, 'annee_scolaire_id' => $annee->id,
                            'jours_acquis' => 30, 'jours_consommes' => rand(0, 10),
                            'jours_restants' => 30 - rand(0, 10),
                        ]);

                        // Créer quelques absences
                        if (rand(1, 3) === 1) {
                            $typeAbsence = TypeAbsence::inRandomOrder()->first();
                            $dateDebut = now()->subDays(rand(1, 60));
                            $nombreJours = rand(1, 5);
                            $dateFin = (clone $dateDebut)->addWeekdays($nombreJours);
                            $statuts = ['approuvee', 'approuvee', 'approuvee', 'en_validation_n1', 'refusee'];
                            $statut = $statuts[array_rand($statuts)];

                            $absence = Absence::create([
                                'reference' => Absence::genererReference(),
                                'user_id' => $ensUser->id, 'type_absence_id' => $typeAbsence->id,
                                'etablissement_id' => $etab->id, 'drena_id' => $drena->id, 'iepp_id' => $iepp->id,
                                'date_debut' => $dateDebut, 'date_fin' => $dateFin,
                                'nombre_jours' => $nombreJours, 'motif' => 'Motif de test — ' . $typeAbsence->libelle,
                                'statut' => $statut, 'niveau_validation_actuel' => $statut === 'approuvee' ? 1 : ($statut === 'en_validation_n1' ? 1 : 1),
                                'declaree_par' => $ensUser->id,
                            ]);

                            if ($statut === 'approuvee') {
                                Validation::create([
                                    'absence_id' => $absence->id, 'valideur_id' => $chefUser->id,
                                    'niveau' => 1, 'decision' => 'approuvee',
                                    'commentaire' => 'Validé.', 'date_validation' => $dateDebut->addHours(rand(2, 48)),
                                ]);
                            }
                        }
                    }

                    $etab->update(['effectif_enseignants' => $nbEnseignants]);
                }
            }
        }

        $this->command->info('Seeder terminé avec succès !');
        $this->command->info('Comptes de démonstration :');
        $this->command->info('  Super Admin MENA : admin@education.gouv.ci / Mena@2026');
        $this->command->info('  Admin DRENA      : abj1.admin@education.gouv.ci / Drena@2026');
        $this->command->info('  Enseignant       : (voir la base de données)');
    }
}
