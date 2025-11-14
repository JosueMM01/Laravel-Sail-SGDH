<?php

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('rol', 'Administrador')->update([
            'rol' => UserRole::ADMIN_FARMACIA->value,
        ]);

        DB::table('users')->where('rol', 'Super Administrador')->update([
            'rol' => UserRole::SUPER_ADMIN->value,
        ]);

        DB::table('users')->where('rol', 'Jefe de Área')->orWhere('rol', 'Jefe de area')->orWhere('rol', 'Jefe area')->update([
            'rol' => UserRole::JEFE_AREA->value,
        ]);

        DB::table('users')->where('rol', 'Personal')->orWhere('rol', 'Personal de área')->orWhere('rol', 'Personal de area')->update([
            'rol' => UserRole::PERSONAL_AREA->value,
        ]);
    }

    public function down(): void
    {
        DB::table('users')->where('rol', UserRole::ADMIN_FARMACIA->value)->update([
            'rol' => 'Administrador',
        ]);

        DB::table('users')->where('rol', UserRole::SUPER_ADMIN->value)->update([
            'rol' => 'Super Administrador',
        ]);

        DB::table('users')->where('rol', UserRole::JEFE_AREA->value)->update([
            'rol' => 'Jefe de Área',
        ]);

        DB::table('users')->where('rol', UserRole::PERSONAL_AREA->value)->update([
            'rol' => 'Personal de área',
        ]);
    }
};
