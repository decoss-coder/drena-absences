<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ajouter le type d'établissement (primaire / secondaire)
        Schema::table('etablissements', function (Blueprint $table) {
            $table->enum('ordre_enseignement', ['primaire', 'secondaire'])
                  ->default('primaire')
                  ->after('type');
        });

        // 2. Modifier la colonne statut des absences pour utiliser les nouveaux statuts
        Schema::table('absences', function (Blueprint $table) {
            $table->string('statut', 30)->change();
        });

        // 3. Migrer les anciens statuts vers les nouveaux
        DB::table('absences')->where('statut', 'en_validation_n1')->update(['statut' => 'en_validation_chef']);
        DB::table('absences')->where('statut', 'en_validation_n2')->update(['statut' => 'en_validation_inspecteur']);
        DB::table('absences')->where('statut', 'en_validation_n3')->update(['statut' => 'en_validation_drena']);

        // 4. Ajouter un champ pour stocker le circuit de validation
        Schema::table('absences', function (Blueprint $table) {
            $table->enum('circuit_validation', ['primaire', 'secondaire'])
                  ->nullable()
                  ->after('niveau_validation_actuel');
        });
    }

    public function down(): void
    {
        DB::table('absences')->where('statut', 'en_validation_chef')->update(['statut' => 'en_validation_n1']);
        DB::table('absences')->where('statut', 'en_validation_inspecteur')->update(['statut' => 'en_validation_n2']);
        DB::table('absences')->where('statut', 'en_validation_drena')->update(['statut' => 'en_validation_n3']);

        Schema::table('absences', function (Blueprint $table) {
            $table->dropColumn('circuit_validation');
        });

        Schema::table('etablissements', function (Blueprint $table) {
            $table->dropColumn('ordre_enseignement');
        });
    }
};
