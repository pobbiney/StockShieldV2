<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'route_requisitions_to_central')) {
                $table->boolean('route_requisitions_to_central')->default(true)->after('route_requisitions_to_hub');
            }
        });

        if (Schema::hasColumn('stores', 'route_requisitions_to_hub')
            && Schema::hasColumn('stores', 'route_requisitions_to_central')) {
            DB::table('stores')
                ->where('route_requisitions_to_hub', true)
                ->update(['route_requisitions_to_central' => false]);
        }
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'route_requisitions_to_central')) {
                $table->dropColumn('route_requisitions_to_central');
            }
        });
    }
};
