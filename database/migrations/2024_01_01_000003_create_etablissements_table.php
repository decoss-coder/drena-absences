<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drena_id')->constrained()->cascadeOnDelete();
            $table->foreignId('iepp_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->unique();
            $table->string('nom');
            $table->enum('type', ['maternelle', 'primaire', 'secondaire_general', 'secondaire_technique']);
            $table->enum('statut_juridique', ['public', 'prive_laic', 'prive_confessionnel']);
            $table->string('localite')->nullable();
            $table->text('adresse')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('effectif_eleves')->default(0);
            $table->integer('effectif_enseignants')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['drena_id', 'iepp_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};
