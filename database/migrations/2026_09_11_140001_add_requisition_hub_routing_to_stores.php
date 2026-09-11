<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'is_requisition_hub')) {
                $table->boolean('is_requisition_hub')->default(false)->after('store_group');
            }
            if (!Schema::hasColumn('stores', 'route_requisitions_to_hub')) {
                $table->boolean('route_requisitions_to_hub')->default(false)->after('is_requisition_hub');
            }
        });

        Schema::table('item_issues', function (Blueprint $table) {
            if (!Schema::hasColumn('item_issues', 'satellite_stock_receipt_id')) {
                $table->unsignedBigInteger('satellite_stock_receipt_id')->nullable()->after('stock_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('item_issues', function (Blueprint $table) {
            if (Schema::hasColumn('item_issues', 'satellite_stock_receipt_id')) {
                $table->dropColumn('satellite_stock_receipt_id');
            }
        });

        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'route_requisitions_to_hub')) {
                $table->dropColumn('route_requisitions_to_hub');
            }
            if (Schema::hasColumn('stores', 'is_requisition_hub')) {
                $table->dropColumn('is_requisition_hub');
            }
        });
    }
};
