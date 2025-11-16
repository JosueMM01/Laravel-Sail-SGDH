<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE solicitudes MODIFY estatus ENUM('pendiente','pendiente_jefe','pendiente_farmacia','aprobada','rechazada','surtida') NOT NULL DEFAULT 'pendiente_jefe'");
        DB::statement("UPDATE solicitudes SET estatus = 'pendiente_jefe' WHERE estatus = 'pendiente'");
        DB::statement("ALTER TABLE solicitudes MODIFY estatus ENUM('pendiente_jefe','pendiente_farmacia','aprobada','rechazada','surtida') NOT NULL DEFAULT 'pendiente_jefe'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE solicitudes MODIFY estatus ENUM('pendiente','pendiente_jefe','pendiente_farmacia','aprobada','rechazada','surtida') NOT NULL DEFAULT 'pendiente'");
        DB::statement("UPDATE solicitudes SET estatus = 'pendiente' WHERE estatus IN ('pendiente_jefe','pendiente_farmacia')");
        DB::statement("ALTER TABLE solicitudes MODIFY estatus ENUM('pendiente','aprobada','rechazada','surtida') NOT NULL DEFAULT 'pendiente'");
    }
};
