<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $globalRoleNames = [
            'Administrator',
            'Administrator (Satellite)',
            'HOD',
            'DHOD',
        ];

        DB::table('user_cat')
            ->whereIn('cat_name', $globalRoleNames)
            ->update(['access_all_stores' => true]);
    }

    public function down(): void
    {
        $globalRoleNames = [
            'Administrator',
            'Administrator (Satellite)',
            'HOD',
            'DHOD',
        ];

        DB::table('user_cat')
            ->whereIn('cat_name', $globalRoleNames)
            ->update(['access_all_stores' => false]);
    }
};
