<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('valideur_id')->constrained('users')->cascadeOnDelete();
            $table->integer('niveau');
            $table->enum('decision', ['approuvee', 'refusee', 'complement_requis']);
            $table->text('commentaire')->nullable();
            $table->timestamp('date_validation');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['absence_id', 'niveau']);
        });

        Schema::create('justificatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absence_id')->constrained()->cascadeOnDelete();
            $table->string('nom_fichier');
            $table->string('nom_original');
            $table->string('chemin');
            $table->string('type_mime', 100);
            $table->integer('taille');
            $table->string('type_document')->nullable();
            $table->foreignId('uploade_par')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('absence_id');
        });

        Schema::create('suppleances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('titulaire_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('suppleant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('etablissement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigne_par')->constrained('users')->cascadeOnDelete();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('heures_effectuees', 5, 1)->default(0);
            $table->enum('statut', ['proposee', 'acceptee', 'refusee', 'en_cours', 'terminee'])->default('proposee');
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->index(['absence_id', 'suppleant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppleances');
        Schema::dropIfExists('justificatifs');
        Schema::dropIfExists('validations');
    }
};
