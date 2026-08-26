<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('new_stock_entries') && !Schema::hasTable('satellite_stock_entries')) {
            Schema::rename('new_stock_entries', 'satellite_stock_entries');
        }

        if (Schema::hasTable('stock_receipts') && !Schema::hasTable('satellite_stock_receipts')) {
            Schema::rename('stock_receipts', 'satellite_stock_receipts');
        }

        if (Schema::hasTable('satellite_stock_receipts')
            && Schema::hasColumn('satellite_stock_receipts', 'new_stock_entry_id')
            && !Schema::hasColumn('satellite_stock_receipts', 'satellite_stock_entry_id')) {
            DB::statement('ALTER TABLE satellite_stock_receipts CHANGE new_stock_entry_id satellite_stock_entry_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('satellite_stock_receipts')
            && Schema::hasColumn('satellite_stock_receipts', 'satellite_stock_entry_id')
            && !Schema::hasColumn('satellite_stock_receipts', 'new_stock_entry_id')) {
            Schema::table('satellite_stock_receipts', function (Blueprint $table) {
                $table->renameColumn('satellite_stock_entry_id', 'new_stock_entry_id');
            });
        }

        if (Schema::hasTable('satellite_stock_receipts') && !Schema::hasTable('stock_receipts')) {
            Schema::rename('satellite_stock_receipts', 'stock_receipts');
        }

        if (Schema::hasTable('satellite_stock_entries') && !Schema::hasTable('new_stock_entries')) {
            Schema::rename('satellite_stock_entries', 'new_stock_entries');
        }
    }
};
