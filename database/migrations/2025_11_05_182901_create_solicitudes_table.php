<?php

use App\Enums\SolicitudStatus;
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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->restrictOnDelete();
            $table->foreignId('usuario_solicitante_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->text('justificacion')->nullable();
            $table->enum('estatus', SolicitudStatus::values())
                ->default(SolicitudStatus::PENDIENTE_JEFE->value);
            $table->foreignId('last_modified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
