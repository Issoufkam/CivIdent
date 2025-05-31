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
        Schema::table('documents', function (Blueprint $table) {
            // Modifie la colonne 'justificatif_path' pour la rendre nullable
            // Assurez-vous que la colonne existe avant de la modifier
            if (Schema::hasColumn('documents', 'justificatif_path')) {
                $table->string('justificatif_path')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Si vous annulez la migration, vous pouvez la rendre à nouveau non nullable
            // ou la laisser telle quelle si vous ne savez pas son état initial
            if (Schema::hasColumn('documents', 'justificatif_path')) {
                $table->string('justificatif_path')->nullable(false)->change();
            }
        });
    }
};
