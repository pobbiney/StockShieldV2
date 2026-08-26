<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('satellite_issue_requests', function (Blueprint $table) {
            if (Schema::hasColumn('satellite_issue_requests', 'department_id')
                && !Schema::hasColumn('satellite_issue_requests', 'ward_id')) {
                $table->unsignedBigInteger('ward_id')->nullable()->after('store_id');
            }
        });

        if (Schema::hasColumn('satellite_issue_requests', 'department_id')) {
            Schema::table('satellite_issue_requests', function (Blueprint $table) {
                $table->dropColumn('department_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('satellite_issue_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('satellite_issue_requests', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('store_id');
            }

            if (Schema::hasColumn('satellite_issue_requests', 'ward_id')) {
                $table->dropColumn('ward_id');
            }
        });
    }
};
