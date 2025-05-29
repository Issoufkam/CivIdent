<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['naissance', 'mariage', 'deces']);
            $table->enum('status', ['en_attente', 'approuvee', 'rejetee'])->default('en_attente');
            $table->string('registry_number')->unique(); // Ex: "2023-ABJ-0256"
            $table->integer('registry_page')->nullable();
            $table->string('registry_volume')->nullable();
            $table->json('metadata'); // Stocke les détails spécifiques (noms, dates, etc.)
            $table->string('justificatif_path'); // Chemin du fichier justificatif
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->timestamp('paid_at')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_downloaded')->default(false); // Indique si le document a été téléchargé
            $table->date('traitement_date')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null');

            // Champs pour gérer les duplicatas
            $table->boolean('is_duplicata')->default(false);
            $table->foreignId('original_document_id')->nullable()->constrained('documents')->onDelete('cascade');
            $table->string('pdf_path')->nullable(); // Chemin vers le duplicata PDF généré

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
