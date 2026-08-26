<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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

        $this->seedLink(
            'newStockEntry',
            'New Stock Entry',
            'new-stock-entry',
            'bi bi-box-arrow-in-down',
            $parentId,
            $this->roleIdsFor('stockEntry', [
                'Store Keeper',
                'Storekeeper',
                'Satellite Store Keeper',
                'Administrator (Satellite)',
            ])
        );

        $this->seedLink(
            'satelliteStockApproval',
            'Satellite Stock Approval',
            'satellite-stock-approval',
            'bi bi-check2-square',
            $parentId,
            $this->roleIdsFor('stockApproval', [
                'Administrator (Satellite)',
                'Satellite Store Keeper',
                'Head of Department',
            ])
        );
    }

    protected function roleIdsFor(string $linkUrl, array $fallbackRoleNames)
    {
        $linkId = DB::table('user_links')->where('link_url', $linkUrl)->value('link_id');

        if ($linkId) {
            $roleIds = DB::table('user_cat_links')->where('link_id', $linkId)->pluck('cat_id');
            if ($roleIds->isNotEmpty()) {
                return $roleIds;
            }
        }

        return DB::table('user_cat')->whereIn('cat_name', $fallbackRoleNames)->pluck('cat_id');
    }

    protected function seedLink(string $url, string $name, string $pageIdSub, string $icon, int $parentId, $roleIds): void
    {
        $exists = DB::table('user_links')->where('link_url', $url)->exists();

        if ($exists) {
            return;
        }

        $linkId = DB::table('user_links')->insertGetId([
            'link_url'     => $url,
            'link_name'    => $name,
            'link_target'  => null,
            'link_image'   => $icon,
            'link_parent'  => $parentId,
            'page_id'      => 'stock',
            'page_id_sub'  => $pageIdSub,
            'status'       => 'Active',
        ]);

        foreach (collect($roleIds)->unique() as $catId) {
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
        foreach (['newStockEntry', 'satelliteStockApproval'] as $url) {
            $linkId = DB::table('user_links')->where('link_url', $url)->value('link_id');

            if (!$linkId) {
                continue;
            }

            DB::table('user_cat_links')->where('link_id', $linkId)->delete();
            DB::table('user_links')->where('link_id', $linkId)->delete();
        }
    }
};
