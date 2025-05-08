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
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();

            // Références
            $table->foreignId('citoyen_id')->constrained('citoyens');
            $table->foreignId('acte_id')->constrained('actes');
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();

            // Détails de la demande
            $table->timestamp('date_demande')->useCurrent();
            $table->enum('statut', ['en_attente', 'validée', 'rejetée', 'traitée'])->default('en_attente');
            $table->string('moyen_retrait')->nullable(); // en ligne / en personne
            $table->string('purpose'); // motif de la demande
            $table->integer('copies')->default(1); // nombre de copies

            // Fichiers joints
            $table->string('id_front'); // chemin du fichier pièce recto
            $table->string('id_back');  // chemin du fichier pièce verso
            $table->string('birth_copy'); // chemin de la photo de l'extrait

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
