<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('user_links')
            ->where('link_url', 'IssueItemSatellite')
            ->exists();

        if ($exists) {
            return;
        }

        $parentId = DB::table('user_links')
            ->where('link_parent', 0)
            ->where(function ($q) {
                $q->where('link_name', 'like', '%Stock Management%')
                    ->orWhere('page_id', 'stock');
            })
            ->orderBy('link_id')
            ->value('link_id');

        if (!$parentId) {
            $parentId = DB::table('user_links')
                ->where('link_parent', 0)
                ->where('link_name', 'like', '%Stock%')
                ->orderBy('link_id')
                ->value('link_id');
        }

        if (!$parentId) {
            return;
        }

        $linkId = DB::table('user_links')->insertGetId([
            'link_url'     => 'IssueItemSatellite',
            'link_name'    => 'Issue Item (Satellite)',
            'link_target'  => null,
            'link_image'   => 'bi bi-box-arrow-right',
            'link_parent'  => $parentId,
            'page_id'      => 'stock',
            'page_id_sub'  => 'issue-item-satellite',
            'status'       => 'Active',
        ]);

        $referenceLinkId = DB::table('user_links')
            ->where('link_url', 'ReceiveStock')
            ->value('link_id');

        if (!$referenceLinkId) {
            $referenceLinkId = DB::table('user_links')
                ->where('link_url', 'Requisition')
                ->value('link_id');
        }

        $roleIds = collect();

        if ($referenceLinkId) {
            $roleIds = DB::table('user_cat_links')
                ->where('link_id', $referenceLinkId)
                ->pluck('cat_id');
        }

        if ($roleIds->isEmpty()) {
            $roleIds = DB::table('user_cat')
                ->whereIn('cat_name', [
                    'Store Keeper',
                    'Storekeeper',
                    'Satellite Store Keeper',
                    'Administrator (Satellite)',
                ])
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
        $linkId = DB::table('user_links')
            ->where('link_url', 'IssueItemSatellite')
            ->value('link_id');

        if (!$linkId) {
            return;
        }

        DB::table('user_cat_links')->where('link_id', $linkId)->delete();
        DB::table('user_links')->where('link_id', $linkId)->delete();
    }
};
