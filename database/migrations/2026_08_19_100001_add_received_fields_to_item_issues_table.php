<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_issues', function (Blueprint $table) {
            if (!Schema::hasColumn('item_issues', 'received_at')) {
                $table->timestamp('received_at')->nullable()->after('issued_by');
            }
            if (!Schema::hasColumn('item_issues', 'received_by')) {
                $table->unsignedInteger('received_by')->nullable()->after('received_at');
            }
        });

        Schema::table('approve_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('approve_stocks', 'source_issue_id')) {
                $table->unsignedBigInteger('source_issue_id')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('item_issues', function (Blueprint $table) {
            if (Schema::hasColumn('item_issues', 'received_by')) {
                $table->dropColumn('received_by');
            }
            if (Schema::hasColumn('item_issues', 'received_at')) {
                $table->dropColumn('received_at');
            }
        });

        Schema::table('approve_stocks', function (Blueprint $table) {
            if (Schema::hasColumn('approve_stocks', 'source_issue_id')) {
                $table->dropColumn('source_issue_id');
            }
        });
    }
};
