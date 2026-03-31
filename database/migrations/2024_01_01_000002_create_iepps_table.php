<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iepps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drena_id')->constrained()->cascadeOnDelete();
            $table->string('code', 10)->unique();
            $table->string('nom');
            $table->string('localite')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('drena_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iepps');
    }
};
