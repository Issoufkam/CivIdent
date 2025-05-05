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
            $table->foreignId('citoyen_id')->constrained('citoyens');
            $table->foreignId('acte_id')->constrained('actes');
            $table->timestamp('date_demande')->useCurrent();
            $table->enum('statut', ['en_attente', 'validée', 'rejetée', 'traitée'])->default('en_attente');
            $table->string('moyen_retrait')->nullable(); // en ligne / en personne
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
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
