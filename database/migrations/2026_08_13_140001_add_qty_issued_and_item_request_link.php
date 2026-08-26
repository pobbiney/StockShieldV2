<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('item_requests', 'qty_issued')) {
                $table->unsignedInteger('qty_issued')->default(0)->after('qty_requested');
            }
        });

        Schema::table('item_issues', function (Blueprint $table) {
            if (!Schema::hasColumn('item_issues', 'item_request_id')) {
                $table->unsignedBigInteger('item_request_id')->nullable()->after('requisition_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            if (Schema::hasColumn('item_requests', 'qty_issued')) {
                $table->dropColumn('qty_issued');
            }
        });

        Schema::table('item_issues', function (Blueprint $table) {
            if (Schema::hasColumn('item_issues', 'item_request_id')) {
                $table->dropColumn('item_request_id');
            }
        });
    }
};
