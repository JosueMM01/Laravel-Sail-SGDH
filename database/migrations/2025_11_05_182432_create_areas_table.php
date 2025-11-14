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
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('responsable')->nullable();
            // Auditoría
            $table->foreignId('last_modified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // AHORA que ya existe 'areas', agregamos la columna a 'users' sin causar error
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('area_id')->nullable()->after('rol')->constrained('areas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'area_id')) {
                return;
            }

            // Drop FK before removing the column to keep rollbacks clean.
            $table->dropForeign(['area_id']);
            $table->dropColumn('area_id');
        });

        Schema::dropIfExists('areas');
    }
};
