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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('no_proveedor')->unique();
            $table->string('rfc', 13)->unique();
            $table->string('razon_social');
            $table->text('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->string('pagina_web')->nullable();
            $table->string('representante')->nullable();
            $table->boolean('estatus')->default(true);
            $table->foreignId('last_modified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
