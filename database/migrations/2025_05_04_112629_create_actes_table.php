<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Renommé depuis numero_acte
            $table->string('type'); // Remplacer le type_acte_id par un enum
            $table->string('nom');
            $table->string('prenoms');
            $table->string('email');
            $table->string('telephone', 20);
            $table->date('date_naissance');
            $table->string('lieu_naissance');
            $table->string('sous_prefecture');
            $table->enum('genre', ['M', 'F']);
            $table->string('nom_pere');
            $table->string('nom_mere');
            $table->integer('numero_registre');
            $table->text('motif_demande');
            $table->integer('nombre_copies');
            $table->string('fichier_id_recto');
            $table->string('fichier_id_verso');
            $table->string('copie_extrait');
            $table->foreignId('citoyen_id')->constrained('citoyens')->onDelete('cascade');
            $table->foreignId('commune_id')->constrained('communes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actes');
    }
};
