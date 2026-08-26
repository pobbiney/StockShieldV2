<?php

namespace App\Services;

use App\Models\ApproveStock;
use App\Models\SatelliteStockEntry;
use App\Models\SatelliteStockReceipt;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockApprovalService
{
    /** Route names for the stock approval menu (user_links.link_url). */
    public const APPROVAL_ROUTE_NAMES = [
        'stockApproval',
    ];

    /** Screen identifiers for the stock approval menu (user_links.page_id_sub). */
    public const APPROVAL_PAGE_ID_SUBS = [
        'stock-approval',
    ];

    public function __construct(
        protected NotificationService $notifications
    ) {}

    public function canApproveStock(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        foreach (self::APPROVAL_ROUTE_NAMES as $routeName) {
            if ($user->canAccessLinkRoute($routeName)) {
                return true;
            }
        }

        foreach (self::APPROVAL_PAGE_ID_SUBS as $pageIdSub) {
            if ($user->canAccessScreen($pageIdSub)) {
                return true;
            }
        }

        return false;
    }

    public function requiresApproval(?User $user = null): bool
    {
        return !$this->canApproveStock($user);
    }

    public function approve(Stock $stock, ?User $approver = null): bool
    {
        $approver = $approver ?? auth()->user();

        if (!$approver || !$this->canApproveStock($approver)) {
            return false;
        }

        if ($stock->status !== 'pending') {
            return false;
        }

        if (!ApproveStock::where('stock_id', $stock->id)->exists()) {
            ApproveStock::create([
                'stock_id' => $stock->id,
                'item_id' => $stock->item_id,
                'batch_number' => $stock->batch_number,
                'expiry_date' => $stock->expiry_date,
                'qty' => $stock->qty,
                'amount' => $stock->amount,
                'purchase_order' => $stock->purchase_order,
                'supplier_id' => $stock->supplier_id,
                'store_id' => $stock->store_id,
                'created_by' => $approver->id,
                'status' => 'approved',
            ]);
        }

        $stock->update([
            'status' => 'approved',
            'updated_by' => $approver->id,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        $this->notifications->resolveIfComplete(
            NotificationService::TYPE_STOCK_PENDING_APPROVAL,
            $stock->batch_number
        );

        return true;
    }

    public function approveSatelliteEntry(SatelliteStockEntry $entry, ?User $approver = null): bool
    {
        $approver = $approver ?? auth()->user();

        if (!$approver || !$this->canApproveStock($approver)) {
            return false;
        }

        if ($entry->status !== 'pending') {
            return false;
        }

        if (SatelliteStockReceipt::where('satellite_stock_entry_id', $entry->id)->exists()) {
            return false;
        }

        DB::transaction(function () use ($entry, $approver) {
            SatelliteStockReceipt::create([
                'source_type'              => 'direct_entry',
                'satellite_stock_entry_id' => $entry->id,
                'item_issue_id'            => null,
                'item_request_id'          => null,
                'stock_id'                 => null,
                'item_id'                  => $entry->item_id,
                'batch_number'             => $entry->batch_number,
                'qty'                      => $entry->qty,
                'amount'                   => $entry->amount,
                'expiry_date'              => $entry->expiry_date,
                'purchase_order'           => $entry->purchase_order,
                'supplier_id'              => $entry->supplier_id,
                'store_id'                 => $entry->store_id,
                'central_store_id'         => null,
                'requisition_no'           => null,
                'invoice_number'           => null,
                'received_by'              => $approver->id,
                'received_at'              => now(),
            ]);

            $entry->update([
                'status'      => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'updated_by'  => $approver->id,
            ]);
        });

        $this->notifications->resolveIfComplete(
            NotificationService::TYPE_STOCK_PENDING_APPROVAL,
            $entry->batch_number
        );

        return true;
    }

    public function approveMany(iterable $stocks, ?User $approver = null): int
    {
        $count = 0;

        foreach ($stocks as $stock) {
            if ($this->approve($stock, $approver)) {
                $count++;
            }
        }

        return $count;
    }

    public function approveManySatelliteEntries(iterable $entries, ?User $approver = null): int
    {
        $count = 0;

        foreach ($entries as $entry) {
            if ($this->approveSatelliteEntry($entry, $approver)) {
                $count++;
            }
        }

        return $count;
    }

    public function reject(Stock $stock, string $reason, ?User $rejector = null): bool
    {
        $rejector = $rejector ?? auth()->user();

        if (!$rejector || !$this->canApproveStock($rejector)) {
            return false;
        }

        if ($stock->status !== 'pending') {
            return false;
        }

        $stock->update([
            'status' => 'rejected',
            'rejection_reason' => trim($reason),
            'rejected_by' => $rejector->id,
            'rejected_at' => now(),
            'updated_by' => $rejector->id,
        ]);

        $this->notifications->resolveIfComplete(
            NotificationService::TYPE_STOCK_PENDING_APPROVAL,
            $stock->batch_number
        );

        return true;
    }

    public function rejectSatelliteEntry(SatelliteStockEntry $entry, string $reason, ?User $rejector = null): bool
    {
        $rejector = $rejector ?? auth()->user();

        if (!$rejector || !$this->canApproveStock($rejector)) {
            return false;
        }

        if ($entry->status !== 'pending') {
            return false;
        }

        $entry->update([
            'status'           => 'rejected',
            'rejection_reason' => trim($reason),
            'rejected_by'      => $rejector->id,
            'rejected_at'      => now(),
            'updated_by'       => $rejector->id,
        ]);

        $this->notifications->resolveIfComplete(
            NotificationService::TYPE_STOCK_PENDING_APPROVAL,
            $entry->batch_number
        );

        return true;
    }

    public function rejectMany(iterable $stocks, string $reason, ?User $rejector = null): int
    {
        $count = 0;

        foreach ($stocks as $stock) {
            if ($this->reject($stock, $reason, $rejector)) {
                $count++;
            }
        }

        return $count;
    }

    public function rejectManySatelliteEntries(iterable $entries, string $reason, ?User $rejector = null): int
    {
        $count = 0;

        foreach ($entries as $entry) {
            if ($this->rejectSatelliteEntry($entry, $reason, $rejector)) {
                $count++;
            }
        }

        return $count;
    }
}
