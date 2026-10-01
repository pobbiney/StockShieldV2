<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('satellite_item_issues', function (Blueprint $table) {
            if (! Schema::hasColumn('satellite_item_issues', 'requisition_unit')) {
                $table->string('requisition_unit', 100)->nullable()->after('unit_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('satellite_item_issues', function (Blueprint $table) {
            if (Schema::hasColumn('satellite_item_issues', 'requisition_unit')) {
                $table->dropColumn('requisition_unit');
            }
        });
    }
};
