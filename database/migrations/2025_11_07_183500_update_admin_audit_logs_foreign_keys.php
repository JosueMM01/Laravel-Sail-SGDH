<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_audit_logs', function (Blueprint $table) {
            $table->dropForeign(['performed_by']);
            $table->dropForeign(['target_user_id']);
        });

        DB::statement('ALTER TABLE admin_audit_logs MODIFY performed_by BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE admin_audit_logs MODIFY target_user_id BIGINT UNSIGNED NULL');

        Schema::table('admin_audit_logs', function (Blueprint $table) {
            $table->foreign('performed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('target_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('admin_audit_logs', function (Blueprint $table) {
            $table->dropForeign(['performed_by']);
            $table->dropForeign(['target_user_id']);
        });

        DB::table('admin_audit_logs')->whereNull('performed_by')->delete();
        DB::table('admin_audit_logs')->whereNull('target_user_id')->delete();

        DB::statement('ALTER TABLE admin_audit_logs MODIFY performed_by BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE admin_audit_logs MODIFY target_user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('admin_audit_logs', function (Blueprint $table) {
            $table->foreign('performed_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('target_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
