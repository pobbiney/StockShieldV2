<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_receipts', 'source_type')) {
                $table->string('source_type')->default('issue_transfer')->after('id');
            }
            if (!Schema::hasColumn('stock_receipts', 'new_stock_entry_id')) {
                $table->unsignedBigInteger('new_stock_entry_id')->nullable()->unique()->after('item_issue_id');
            }
        });

        if (Schema::hasColumn('stock_receipts', 'item_issue_id')) {
            try {
                Schema::table('stock_receipts', function (Blueprint $table) {
                    $table->dropUnique(['item_issue_id']);
                });
            } catch (\Throwable $e) {
                // Index may not exist or already dropped.
            }

            DB::statement('ALTER TABLE stock_receipts MODIFY item_issue_id BIGINT UNSIGNED NULL');
        }

        DB::table('stock_receipts')->whereNull('source_type')->update(['source_type' => 'issue_transfer']);
    }

    public function down(): void
    {
        Schema::table('stock_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('stock_receipts', 'new_stock_entry_id')) {
                $table->dropColumn('new_stock_entry_id');
            }
            if (Schema::hasColumn('stock_receipts', 'source_type')) {
                $table->dropColumn('source_type');
            }
        });
    }
};
