<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['newStockEntry', 'satelliteStockApproval'] as $url) {
            $linkId = DB::table('user_links')->where('link_url', $url)->value('link_id');

            if (!$linkId) {
                continue;
            }

            DB::table('user_cat_links')->where('link_id', $linkId)->delete();
            DB::table('user_links')->where('link_id', $linkId)->delete();
        }
    }

    public function down(): void
    {
        // Menu seed is restored by 2026_08_20_100003 if needed.
    }
};
