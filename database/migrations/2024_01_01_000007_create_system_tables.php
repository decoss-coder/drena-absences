<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annee_scolaires', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 20);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->date('trimestre1_debut')->nullable();
            $table->date('trimestre1_fin')->nullable();
            $table->date('trimestre2_debut')->nullable();
            $table->date('trimestre2_fin')->nullable();
            $table->date('trimestre3_debut')->nullable();
            $table->date('trimestre3_fin')->nullable();
            $table->boolean('en_cours')->default(false);
            $table->timestamps();
        });

        Schema::create('jours_feries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annee_scolaire_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('libelle');
            $table->enum('type', ['ferie', 'vacances_debut', 'vacances_fin']);
            $table->timestamps();

            $table->index(['annee_scolaire_id', 'date']);
        });

        Schema::create('conge_soldes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annee_scolaire_id')->constrained()->cascadeOnDelete();
            $table->integer('jours_acquis')->default(30);
            $table->integer('jours_consommes')->default(0);
            $table->integer('jours_restants')->default(30);
            $table->timestamps();

            $table->unique(['user_id', 'annee_scolaire_id']);
        });

        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->boolean('succes')->default(true);
            $table->string('raison_echec')->nullable();
            $table->string('pays', 5)->nullable();
            $table->string('ville')->nullable();
            $table->timestamp('date_connexion');

            $table->index(['user_id', 'date_connexion']);
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type_evenement');
            $table->boolean('sms')->default(true);
            $table->boolean('email')->default(true);
            $table->boolean('push')->default(true);
            $table->boolean('in_app')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'type_evenement']);
        });

        Schema::create('seuil_alertes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drena_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->integer('valeur');
            $table->string('unite', 20)->default('jours');
            $table->string('action', 50)->default('notification');
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index('drena_id');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_id', 'notifiable_type', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('seuil_alertes');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('conge_soldes');
        Schema::dropIfExists('jours_feries');
        Schema::dropIfExists('annee_scolaires');
    }
};
