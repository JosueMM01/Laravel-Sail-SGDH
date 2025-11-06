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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('descripcion');
            $table->string('presentacion');
            $table->boolean('cuadro_basico')->default(false);
            $table->integer('stock_min')->default(0);
            $table->integer('stock_max')->default(0);
            $table->integer('stock_optimo')->default(0);
            $table->foreignId('last_modified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
