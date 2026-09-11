<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('user_links')->where('link_url', 'RejectedItems')->exists()) {
            return;
        }

        $parentId = DB::table('user_links')
            ->where('link_url', 'MyRequest')
            ->value('link_parent');

        if (!$parentId) {
            $parentId = DB::table('user_links')
                ->where('link_parent', 0)
                ->where('page_id', 'request')
                ->orderBy('link_id')
                ->value('link_id');
        }

        if (!$parentId) {
            return;
        }

        $linkId = DB::table('user_links')->insertGetId([
            'link_url'    => 'RejectedItems',
            'link_name'   => 'Rejected Items',
            'link_target' => null,
            'link_image'  => 'bi bi-x-circle',
            'link_parent' => $parentId,
            'page_id'     => 'request',
            'page_id_sub' => 'rejected-items',
            'status'      => 'Active',
        ]);

        $roleIds = collect();
        $copyFromUrls = ['MyRequest', 'ApproveRequest', 'stockApproval', 'IssueApproval', 'reverseEntryApproval'];

        foreach ($copyFromUrls as $sourceUrl) {
            $sourceLinkId = DB::table('user_links')->where('link_url', $sourceUrl)->value('link_id');

            if ($sourceLinkId) {
                $roleIds = $roleIds->merge(
                    DB::table('user_cat_links')->where('link_id', $sourceLinkId)->pluck('cat_id')
                );
            }
        }

        if ($roleIds->isEmpty()) {
            $roleIds = DB::table('user_cat')
                ->whereIn('cat_name', ['Store Keeper', 'Storekeeper', 'Administrator'])
                ->pluck('cat_id');
        }

        foreach ($roleIds->unique() as $catId) {
            $already = DB::table('user_cat_links')
                ->where('cat_id', $catId)
                ->where('link_id', $linkId)
                ->exists();

            if (!$already) {
                DB::table('user_cat_links')->insert([
                    'cat_id'  => $catId,
                    'link_id' => $linkId,
                ]);
            }
        }
    }

    public function down(): void
    {
        $linkId = DB::table('user_links')->where('link_url', 'RejectedItems')->value('link_id');

        if (!$linkId) {
            return;
        }

        DB::table('user_cat_links')->where('link_id', $linkId)->delete();
        DB::table('user_links')->where('link_id', $linkId)->delete();
    }
};
