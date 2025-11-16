<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('invited_by')->nullable()->after('remember_token')->constrained('users')->nullOnDelete();
            $table->string('invitation_token', 64)->nullable()->after('invited_by');
            $table->timestamp('invitation_sent_at')->nullable()->after('invitation_token');
            $table->timestamp('invitation_accepted_at')->nullable()->after('invitation_sent_at');

            $table->index('invitation_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_invitation_token_index');
            $table->dropConstrainedForeignId('invited_by');
            $table->dropColumn(['invitation_token', 'invitation_sent_at', 'invitation_accepted_at']);
        });
    }
};
