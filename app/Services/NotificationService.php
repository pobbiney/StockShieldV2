<?php

namespace App\Services;

use App\Models\ItemIssue;
use App\Models\ItemRequest;
use App\Models\ReturnItem;
use App\Models\StockReversal;
use App\Models\SatelliteIssueRequest;
use App\Models\SatelliteStockEntry;
use App\Models\Stock;
use App\Models\SystemNotifications;
use App\Models\User;
use App\Models\UserCat;
use App\Models\UserExtraLink;
use App\Models\UserLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class NotificationService
{
    public const TYPE_REQUISITION_SUBMITTED = 'requisition_submitted';
    public const TYPE_REQUISITION_APPROVED = 'requisition_approved';
    public const TYPE_ISSUE_PENDING_APPROVAL = 'issue_pending_approval';
    public const TYPE_ISSUE_APPROVED = 'issue_approved';
    public const TYPE_STOCK_PENDING_APPROVAL = 'stock_pending_approval';
    public const TYPE_SATELLITE_ISSUE_SUBMITTED = 'satellite_issue_submitted';
    public const TYPE_RETURN_PENDING_APPROVAL = 'return_pending_approval';
    public const TYPE_REVERSE_ENTRY_PENDING_APPROVAL = 'reverse_entry_pending_approval';

    public function __construct(
        protected StoreContext $storeContext
    ) {}

    public function notifyUsersForAction(
        string $type,
        string $title,
        string $message,
        string $actionRoute,
        ?string $referenceId = null,
        ?int $storeId = null,
        array $excludeUserIds = [],
        ?int $createdBy = null
    ): int {
        $recipients = $this->resolveUsersForRoute($actionRoute, $storeId)
            ->reject(fn (User $user) => in_array((int) $user->id, array_map('intval', $excludeUserIds), true));

        if ($recipients->isEmpty()) {
            return 0;
        }

        $actionUrl = $this->resolveActionUrl($actionRoute);
        $createdBy = $createdBy ?? auth()->id();
        $rows = [];

        foreach ($recipients as $recipient) {
            $rows[] = [
                'user_id'       => $recipient->id,
                'title'         => $title,
                'message'       => $message,
                'link'          => $actionUrl,
                'type'          => $type,
                'reference_id'  => $referenceId,
                'read_at'       => null,
                'action_route'  => $actionRoute,
                'action_url'    => $actionUrl,
                'store_id'      => $storeId,
                'created_by'    => $createdBy,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        SystemNotifications::insert($rows);

        return count($rows);
    }

    public function resolveUsersForRoute(string $routeName, ?int $storeId = null): Collection
    {
        $linkIds = UserLink::where('status', 'Active')
            ->where('link_url', $routeName)
            ->pluck('link_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($linkIds)) {
            return collect();
        }

        $roleUserIds = User::where('status', 'Active')
            ->whereIn('user_cat', function ($query) use ($linkIds) {
                $query->select('cat_id')
                    ->from('user_cat_links')
                    ->whereIn('link_id', $linkIds);
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $extraUserIds = UserExtraLink::whereIn('link_id', $linkIds)
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $userIds = array_values(array_unique(array_merge($roleUserIds, $extraUserIds)));

        if (empty($userIds)) {
            return collect();
        }

        return User::whereIn('id', $userIds)
            ->where('status', 'Active')
            ->get()
            ->filter(function (User $user) use ($storeId) {
                if (!$storeId) {
                    return true;
                }

                if ($this->userHasGlobalStoreAccess($user)) {
                    return true;
                }

                return $this->storeContext->canAccessStore($user, $storeId);
            })
            ->values();
    }

    public function markRead(int $notificationId, User $user): bool
    {
        $notification = SystemNotifications::where('id', $notificationId)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return false;
        }

        return $notification->markRead();
    }

    public function markAllRead(User $user): int
    {
        return SystemNotifications::unreadForUser($user->id)
            ->update(['read_at' => now()]);
    }

    public function markReadByTypeAndReference(User $user, string $type, ?string $referenceId = null): int
    {
        $query = SystemNotifications::unreadForUser($user->id)->where('type', $type);

        if ($referenceId !== null) {
            $query->where('reference_id', $referenceId);
        }

        return $query->update(['read_at' => now()]);
    }

    public function markResolvedByTypeAndReference(string $type, ?string $referenceId = null): int
    {
        $query = SystemNotifications::query()
            ->whereNull('read_at')
            ->where('type', $type);

        if ($referenceId !== null) {
            $query->where('reference_id', $referenceId);
        }

        return $query->update(['read_at' => now()]);
    }

    public function resolveIfComplete(string $type, ?string $referenceId): int
    {
        if ($referenceId === null || !$this->isWorkflowComplete($type, $referenceId)) {
            return 0;
        }

        return $this->markResolvedByTypeAndReference($type, $referenceId);
    }

    public function markRequisitionSubmittedReadIfComplete(User $user, string $requisitionNo): int
    {
        unset($user);

        return $this->resolveIfComplete(self::TYPE_REQUISITION_SUBMITTED, $requisitionNo);
    }

    protected function isWorkflowComplete(string $type, string $referenceId): bool
    {
        return match ($type) {
            self::TYPE_REQUISITION_SUBMITTED => !ItemRequest::where('requisition_no', $referenceId)
                ->where('status', 'pending request')
                ->exists(),

            self::TYPE_REQUISITION_APPROVED => !ItemRequest::where('requisition_no', $referenceId)
                ->where('status', 'request approved')
                ->exists(),

            self::TYPE_ISSUE_PENDING_APPROVAL => !ItemIssue::where('requisition_no', $referenceId)
                ->where('status', 'pending')
                ->exists(),

            self::TYPE_ISSUE_APPROVED => !ItemIssue::where('requisition_no', $referenceId)
                ->where('status', 'issued')
                ->where('status_two', 'issued')
                ->exists(),

            self::TYPE_STOCK_PENDING_APPROVAL => !Stock::where('batch_number', $referenceId)
                    ->where('status', 'pending')
                    ->exists()
                && !SatelliteStockEntry::where('batch_number', $referenceId)
                    ->where('status', 'pending')
                    ->exists(),

            self::TYPE_SATELLITE_ISSUE_SUBMITTED => !SatelliteIssueRequest::where('issue_no', $referenceId)
                ->whereIn('status', ['submitted', 'partial'])
                ->exists(),

            self::TYPE_RETURN_PENDING_APPROVAL => !ReturnItem::where('id', $referenceId)
                ->where('status', 'return initiated')
                ->exists(),

            self::TYPE_REVERSE_ENTRY_PENDING_APPROVAL => !StockReversal::where('id', $referenceId)
                ->where('status', StockReversal::STATUS_PENDING)
                ->exists(),

            default => false,
        };
    }

    public function syncStaleNotificationsForUser(User $user): int
    {
        $resolved = 0;

        SystemNotifications::unreadForUser($user->id)
            ->orderBy('id')
            ->get()
            ->each(function (SystemNotifications $notification) use (&$resolved) {
                if (!$notification->type || $notification->reference_id === null) {
                    return;
                }

                if ($this->isWorkflowComplete($notification->type, (string) $notification->reference_id)) {
                    if ($notification->markRead()) {
                        $resolved++;
                    }
                }
            });

        return $resolved;
    }

    public function unreadForUser(User $user, int $limit = 50): Collection
    {
        $this->syncStaleNotificationsForUser($user);

        return SystemNotifications::unreadForUser($user->id)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function unreadCount(User $user): int
    {
        $this->syncStaleNotificationsForUser($user);

        return SystemNotifications::unreadForUser($user->id)->count();
    }

    public function recentForUser(User $user, int $limit = 20): Collection
    {
        return SystemNotifications::forUser($user->id)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function newSince(User $user, int $lastId): Collection
    {
        return SystemNotifications::unreadForUser($user->id)
            ->where('id', '>', $lastId)
            ->orderBy('id')
            ->get();
    }

    protected function resolveActionUrl(string $actionRoute): string
    {
        if (Route::has($actionRoute)) {
            return route($actionRoute);
        }

        return url('/' . ltrim($actionRoute, '/'));
    }

    protected function userHasGlobalStoreAccess(User $user): bool
    {
        $category = UserCat::find($user->user_cat);

        return $category && (bool) $category->access_all_stores;
    }
}
