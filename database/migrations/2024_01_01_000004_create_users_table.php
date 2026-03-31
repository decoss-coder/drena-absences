<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 20)->unique();
            $table->string('nom');
            $table->string('prenoms');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('telephone', 20)->nullable();
            $table->string('telephone_secondary', 20)->nullable();
            $table->enum('genre', ['M', 'F']);
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('photo')->nullable();

            // Professional info
            $table->string('grade')->nullable();
            $table->string('echelon')->nullable();
            $table->string('specialite')->nullable();
            $table->date('date_integration')->nullable();
            $table->date('date_prise_service')->nullable();
            $table->integer('volume_horaire_hebdo')->default(0);

            // Affectation
            $table->foreignId('drena_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('iepp_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('etablissement_id')->nullable()->constrained()->nullOnDelete();

            // Status
            $table->enum('statut', ['actif', 'conge', 'suspendu', 'radie', 'retraite', 'decede'])->default('actif');
            $table->boolean('actif')->default(true);
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            // Delegation
            $table->foreignId('delegue_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('delegation_debut')->nullable();
            $table->timestamp('delegation_fin')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['drena_id', 'iepp_id', 'etablissement_id']);
            $table->index('statut');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
