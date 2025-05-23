<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['birth', 'marriage', 'death']);
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');

            $table->integer('year');
            $table->integer('month');
            $table->integer('count')->default(0);

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->unique(['type', 'commune_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
