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
            $table->string('performed_by_email')->nullable()->after('performed_by');
            $table->string('performed_by_name')->nullable()->after('performed_by_email');
            $table->string('target_type')->nullable()->after('target_user_id');
            $table->unsignedBigInteger('target_id')->nullable()->after('target_type');
            $table->string('target_description')->nullable()->after('target_id');
            $table->string('ip_address', 45)->nullable()->after('metadata');
            $table->text('user_agent')->nullable()->after('ip_address');

            $table->index('performed_by', 'admin_audit_logs_performed_by_index');
            $table->index('action', 'admin_audit_logs_action_index');
            $table->index(['target_type', 'target_id'], 'admin_audit_logs_target_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('admin_audit_logs')) {
            return;
        }

        Schema::table('admin_audit_logs', function (Blueprint $table): void {
            if (Schema::hasColumn('admin_audit_logs', 'performed_by')) {
                $table->dropForeign(['performed_by']);
            }
        });

        $this->dropIndexIfExists('admin_audit_logs', 'admin_audit_logs_performed_by_index');
        $this->dropIndexIfExists('admin_audit_logs', 'admin_audit_logs_action_index');
        $this->dropIndexIfExists('admin_audit_logs', 'admin_audit_logs_target_index');

        $columns = [
            'performed_by_email',
            'performed_by_name',
            'target_type',
            'target_id',
            'target_description',
            'ip_address',
            'user_agent',
        ];

        $columnsToDrop = array_filter($columns, static fn (string $column): bool => Schema::hasColumn('admin_audit_logs', $column));

        if ($columnsToDrop) {
            Schema::table('admin_audit_logs', function (Blueprint $table) use ($columnsToDrop): void {
                $table->dropColumn($columnsToDrop);
            });
        }

        Schema::table('admin_audit_logs', function (Blueprint $table): void {
            if (Schema::hasColumn('admin_audit_logs', 'performed_by')) {
                $table->foreign('performed_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        $exists = collect(DB::select('SHOW INDEX FROM `'.$table.'` WHERE Key_name = ?', [$index]))->isNotEmpty();

        if ($exists) {
            DB::statement('ALTER TABLE `'.$table.'` DROP INDEX `'.$index.'`');
        }
    }
};
