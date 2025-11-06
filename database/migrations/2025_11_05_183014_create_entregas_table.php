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
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_entrega', ['surtido_diario', 'extraordinaria']);
            $table->foreignId('area_id')->constrained('areas')->restrictOnDelete();
            $table->foreignId('usuario_entrega_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('fecha_entrega')->useCurrent();
            $table->foreignId('solicitud_id')->nullable()->constrained('solicitudes')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
