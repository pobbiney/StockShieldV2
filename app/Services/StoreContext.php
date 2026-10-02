<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use App\Models\UserCat;

class StoreContext
{
    public function getMappedStoreIds(User $user): array
    {
        if (empty($user->department_id)) {
            return [];
        }

        return array_values(array_filter(array_map(
            'intval',
            explode('~', $user->department_id)
        )));
    }

    public function hasGlobalStoreAccess(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        $category = UserCat::find($user->user_cat);

        return $category && (bool) $category->access_all_stores;
    }

    /**
     * Store IDs for the logged-in session: the chosen active store,
     * otherwise the stores mapped on the user record.
     */
    public function getLoginStoreIds(?User $user = null): array
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return [];
        }

        $activeStoreId = $this->getActiveStoreId();

        if ($activeStoreId && $this->canAccessStore($user, $activeStoreId)) {
            return [$activeStoreId];
        }

        return $this->getMappedStoreIds($user);
    }

    public function getActiveStoreId(): ?int
    {
        $activeStoreId = session('active_store_id');

        return $activeStoreId ? (int) $activeStoreId : null;
    }

    public function getScopedStoreIds(?User $user = null): array
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return [];
        }

        if ($this->hasGlobalStoreAccess($user)) {
            return Store::pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        $activeStoreId = $this->getActiveStoreId();

        if ($activeStoreId) {
            return [$activeStoreId];
        }

        return $this->getMappedStoreIds($user);
    }

    public function requiresStoreSelection(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (!$user || $this->hasGlobalStoreAccess($user)) {
            return false;
        }

        $mappedStoreIds = $this->getMappedStoreIds($user);

        return count($mappedStoreIds) > 1 && !$this->getActiveStoreId();
    }

    public function canAccessStore(User $user, int $storeId): bool
    {
        if ($this->hasGlobalStoreAccess($user)) {
            return Store::where('id', $storeId)->exists();
        }

        return in_array($storeId, $this->getMappedStoreIds($user), true);
    }

    public function setActiveStore(int $storeId): void
    {
        session(['active_store_id' => $storeId]);
    }

    public function clearActiveStore(): void
    {
        session()->forget('active_store_id');
    }

    public function resolvePostLoginRedirect(?User $user = null): string
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return route('admin-login');
        }

        if ($this->hasGlobalStoreAccess($user)) {
            $this->clearActiveStore();

            return route('dashboard');
        }

        $mappedStoreIds = $this->getMappedStoreIds($user);

        if (empty($mappedStoreIds)) {
            auth()->logout();

            return route('admin-login');
        }

        if (count($mappedStoreIds) === 1) {
            $this->setActiveStore($mappedStoreIds[0]);

            return route('dashboard');
        }

        if ($this->requiresStoreSelection($user)) {
            return route('choose-store');
        }

        return route('dashboard');
    }

    public function getActiveStore(?User $user = null): ?Store
    {
        $activeStoreId = $this->getActiveStoreId();

        if (!$activeStoreId) {
            return null;
        }

        if (!$this->canAccessStore($user ?? auth()->user(), $activeStoreId)) {
            return null;
        }

        return Store::find($activeStoreId);
    }

    public function getMappedStores(?User $user = null): \Illuminate\Support\Collection
    {
        $user = $user ?? auth()->user();
        $storeIds = $this->getMappedStoreIds($user);

        if (empty($storeIds)) {
            return collect();
        }

        return Store::whereIn('id', $storeIds)->orderBy('name')->get();
    }
}
