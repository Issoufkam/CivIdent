<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['naissance', 'mariage', 'deces', 'vie', 'revenue', 'entretien']);
            $table->enum('status', ['actif', 'inactif', 'en_attente'])->default('en_attente');
            $table->string('registry_number', 50)->unique();
            $table->string('registry_page', 20)->nullable();
            $table->string('registry_volume', 20)->nullable();
            $table->json('metadata');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('commune_id')->constrained('communes')->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('justificatif_path')->nullable();
            $table->timestamp('decision_date')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
