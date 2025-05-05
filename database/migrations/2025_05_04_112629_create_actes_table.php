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
        Schema::create('actes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_acte')->unique();
            $table->date('date_etablissement');
            $table->foreignId('citoyen_id')->constrained('citoyens')->onDelete('cascade');
            $table->foreignId('type_acte_id')->constrained('type_actes');
            $table->foreignId('commune_id')->constrained('communes');
            $table->text('fichier_pdf')->nullable(); // lien vers le PDF
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actes');
    }
};
