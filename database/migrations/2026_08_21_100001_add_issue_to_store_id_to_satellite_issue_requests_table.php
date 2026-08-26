<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('satellite_issue_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('satellite_issue_requests', 'issue_to_store_id')) {
                $table->unsignedBigInteger('issue_to_store_id')->nullable()->after('store_id');
            }
        });

        Schema::table('satellite_issue_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('ward_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('satellite_issue_requests', function (Blueprint $table) {
            if (Schema::hasColumn('satellite_issue_requests', 'issue_to_store_id')) {
                $table->dropColumn('issue_to_store_id');
            }
        });
    }
};
