<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('type_absences', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->boolean('justificatif_obligatoire')->default(false);
            $table->integer('duree_max_jours')->nullable();
            $table->integer('niveau_validation_requis')->default(1);
            $table->string('couleur', 7)->default('#6B7280');
            $table->string('icone', 50)->default('calendar-x');
            $table->boolean('deductible_conge')->default(false);
            $table->boolean('actif')->default(true);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('type_absence_id')->constrained()->restrictOnDelete();
            $table->foreignId('etablissement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('drena_id')->constrained()->cascadeOnDelete();
            $table->foreignId('iepp_id')->constrained()->cascadeOnDelete();

            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('nombre_jours');
            $table->boolean('demi_journee_debut')->default(false);
            $table->boolean('demi_journee_fin')->default(false);
            $table->text('motif');
            $table->text('commentaire_agent')->nullable();

            $table->enum('statut', [
                'brouillon',
                'soumise',
                'en_validation_n1',
                'en_validation_n2',
                'en_validation_n3',
                'approuvee',
                'refusee',
                'annulee',
                'completement_requis'
            ])->default('brouillon');

            $table->integer('niveau_validation_actuel')->default(0);
            $table->foreignId('declaree_par')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('est_recurrente')->default(false);
            $table->string('pattern_recurrence')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'statut']);
            $table->index(['etablissement_id', 'date_debut', 'date_fin']);
            $table->index(['drena_id', 'statut']);
            $table->index(['date_debut', 'date_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
        Schema::dropIfExists('type_absences');
    }
};
