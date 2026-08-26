<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected function seedLink(string $linkUrl, string $linkName, string $pageIdSub, string $icon, array $copyFromUrls): void
    {
        if (DB::table('user_links')->where('link_url', $linkUrl)->exists()) {
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
            'link_url'    => $linkUrl,
            'link_name'   => $linkName,
            'link_target' => null,
            'link_image'  => $icon,
            'link_parent' => $parentId,
            'page_id'     => 'stock',
            'page_id_sub' => $pageIdSub,
            'status'      => 'Active',
        ]);

        $roleIds = collect();

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

    public function up(): void
    {
        $this->seedLink(
            'reverseEntry',
            'Reverse Entry',
            'reverse-entry',
            'bi bi-arrow-counterclockwise',
            ['Return', 'stockEntry']
        );

        $this->seedLink(
            'reverseEntryApproval',
            'Reverse Entry Approval',
            'reverse-entry-approval',
            'bi bi-shield-exclamation',
            ['ReturnApproval', 'stockApproval']
        );
    }

    public function down(): void
    {
        foreach (['reverseEntry', 'reverseEntryApproval'] as $linkUrl) {
            $linkId = DB::table('user_links')->where('link_url', $linkUrl)->value('link_id');

            if (!$linkId) {
                continue;
            }

            DB::table('user_cat_links')->where('link_id', $linkId)->delete();
            DB::table('user_links')->where('link_id', $linkId)->delete();
        }
    }
};
