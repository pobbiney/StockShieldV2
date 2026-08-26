<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('stocks', 'rejection_reason')) {
                $table->longText('rejection_reason')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('stocks', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('stocks', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            if (Schema::hasColumn('stocks', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
            if (Schema::hasColumn('stocks', 'rejected_by')) {
                $table->dropColumn('rejected_by');
            }
            if (Schema::hasColumn('stocks', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
