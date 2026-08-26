<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('user_links')
            ->where('link_url', 'ward')
            ->exists();

        if ($exists) {
            return;
        }

        $parentId = DB::table('user_links')
            ->where('link_url', 'department')
            ->value('link_parent');

        if (!$parentId) {
            $parentId = DB::table('user_links')
                ->where('link_name', 'Settings')
                ->where('link_parent', 0)
                ->value('link_id');
        }

        if (!$parentId) {
            return;
        }

        $linkId = DB::table('user_links')->insertGetId([
            'link_url'     => 'ward',
            'link_name'    => 'Ward',
            'link_target'  => null,
            'link_image'   => 'bi bi-hospital',
            'link_parent'  => $parentId,
            'page_id'      => 'settings',
            'page_id_sub'  => 'ward',
            'status'       => 'Active',
        ]);

        $departmentLinkId = DB::table('user_links')
            ->where('link_url', 'department')
            ->value('link_id');

        $roleIds = collect();

        if ($departmentLinkId) {
            $roleIds = DB::table('user_cat_links')
                ->where('link_id', $departmentLinkId)
                ->pluck('cat_id');
        }

        if ($roleIds->isEmpty()) {
            $roleIds = DB::table('user_cat')
                ->whereIn('cat_name', [
                    'Satellite Store Keeper',
                    'Administrator (Satellite)',
                    'Store Keeper',
                    'Storekeeper',
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
            ->where('link_url', 'ward')
            ->value('link_id');

        if (!$linkId) {
            return;
        }

        DB::table('user_cat_links')->where('link_id', $linkId)->delete();
        DB::table('user_links')->where('link_id', $linkId)->delete();
    }
};
