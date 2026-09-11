<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ApproveStock;
use App\Models\ItemIssue;
use App\Models\ItemRequest;
use App\Models\Staff;
use App\Models\Stock;
use App\Models\Store;
use App\Models\UsrUserLog;
use App\Services\StockApprovalService;
use App\Services\StoreContext;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index(StoreContext $storeContext, StockApprovalService $stockApproval)
    {
        $user = Staff::where('staff_id', Auth::user()->staff_id)->first();
        $scopedStoreIds = $storeContext->getScopedStoreIds();

        $stockScope = function ($query) use ($scopedStoreIds) {
            if (!empty($scopedStoreIds)) {
                $query->whereIn('store_id', $scopedStoreIds);
            }

            return $query;
        };

        $pendingStockCount = $stockScope(Stock::where('status', 'pending'))->count();
        $approvedStockLines = $stockScope(ApproveStock::where('status', 'approved'))->count();
        $totalStockQty = (int) $stockScope(ApproveStock::where('status', 'approved'))->sum('qty');

        $activeStore = $storeContext->getActiveStoreId()
            ? Store::find($storeContext->getActiveStoreId())
            : null;

        $isCentralStore = false;
        $fulfillStoreIds = [];
        $incomingRequisitions = collect();
        $awaitingHodRequisitions = collect();
        $pendingRequisitionCount = 0;
        $awaitingHodCount = 0;

        $centralStoreIds = Store::where('store_group', 'central')
            ->whereIn('id', $scopedStoreIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($activeStore?->store_group === 'central') {
            $isCentralStore = true;
            $fulfillStoreIds = [(int) $activeStore->id];
        } elseif (!empty($centralStoreIds)) {
            $isCentralStore = true;
            $fulfillStoreIds = $centralStoreIds;
        } elseif ($activeStore && ItemRequest::where('item_store_id', $activeStore->id)
            ->whereIn('status', ['request approved', 'pending request'])
            ->exists()) {
            $isCentralStore = true;
            $fulfillStoreIds = [(int) $activeStore->id];
        }

        $pendingIssueApprovals = collect();
        $pendingIssueApprovalCount = 0;
        $pendingIssueApprovalLines = 0;

        if ($isCentralStore && !empty($fulfillStoreIds)) {
            $approvedRequests = ItemRequest::with(['storename', 'staffname'])
                ->whereIn('item_store_id', $fulfillStoreIds)
                ->where('status', 'request approved')
                ->orderByDesc('created_at')
                ->get();

            $incomingRequisitions = $approvedRequests->groupBy('requisition_no')->map(function ($lines) {
                $first = $lines->first();

                return (object) [
                    'requisition_no'   => $first->requisition_no,
                    'requesting_store' => $first->storename,
                    'requested_by'     => $first->staffname,
                    'line_count'       => $lines->count(),
                    'total_qty'        => (int) $lines->sum(fn ($line) => $line->qty ?? $line->qty_requested),
                    'submitted_at'     => $lines->min('created_at'),
                ];
            })->values();

            $pendingRequisitionCount = $incomingRequisitions->count();

            $awaitingHodRequests = ItemRequest::with(['storename'])
                ->whereIn('item_store_id', $fulfillStoreIds)
                ->where('status', 'pending request')
                ->orderByDesc('created_at')
                ->get();

            $awaitingHodRequisitions = $awaitingHodRequests->groupBy('requisition_no')->map(function ($lines) {
                $first = $lines->first();

                return (object) [
                    'requisition_no'   => $first->requisition_no,
                    'requesting_store' => $first->storename,
                    'line_count'       => $lines->count(),
                    'total_qty'        => (int) $lines->sum('qty_requested'),
                    'submitted_at'     => $lines->min('created_at'),
                ];
            })->values();

            $awaitingHodCount = $awaitingHodRequisitions->count();

            $pendingIssueQuery = ItemIssue::with(['storename', 'staffname'])
                ->whereIn('store_id', $fulfillStoreIds)
                ->submittedForHodApproval()
                ->orderByDesc('created_at');

            $pendingIssueApprovalLines = (clone $pendingIssueQuery)->count();

            $pendingIssueApprovals = (clone $pendingIssueQuery)->get()
                ->groupBy('requisition_no')
                ->map(function ($lines) {
                    $first = $lines->first();

                    return (object) [
                        'requisition_no'   => $first->requisition_no,
                        'requesting_store' => $first->storename,
                        'issued_by'        => $first->staffname,
                        'line_count'       => $lines->count(),
                        'total_qty'        => (int) $lines->sum('qty'),
                        'submitted_at'     => $lines->min('created_at'),
                    ];
                })
                ->values();

            $pendingIssueApprovalCount = $pendingIssueApprovals->count();
        }

        $authUser = Auth::user();
        $canIssueStock = $authUser?->canAccessLinkRoute('IssueItem') ?? false;
        $canApproveIssues = $authUser?->canAccessLinkRoute('IssueApproval') ?? false;
        $canApproveRequisitions = $authUser?->canAccessLinkRoute('ApproveRequest') ?? false;
        $canStockEntry = $authUser?->canAccessLinkRoute('stockEntry') ?? false;
        $canPendingStock = $authUser?->canAccessLinkRoute('pendingStock') ?? false;
        $canStockApprovalMenu = $authUser?->canAccessLinkRoute('stockApproval') ?? false;
        $canManageItems = $authUser?->canAccessLinkRoute('Item') ?? false;
        $canReorder = $authUser?->canAccessLinkRoute('reOrder') ?? false;

        $isSatelliteStore = $activeStore?->store_group === 'satellite';
        $incomingTransfers = collect();
        $pendingReceiptCount = 0;
        $pendingReceiptQty = 0;

        if ($isSatelliteStore && $activeStore) {
            $pendingReceiptIssues = ItemIssue::with(['issuefrom'])
                ->where('issue_to', $activeStore->id)
                ->awaitingReceipt()
                ->orderByDesc('updated_at')
                ->get();

            $pendingReceiptCount = $pendingReceiptIssues->count();
            $pendingReceiptQty = (int) $pendingReceiptIssues->sum('qty');

            $incomingTransfers = $pendingReceiptIssues->groupBy('requisition_no')->map(function ($lines) {
                $first = $lines->first();

                return (object) [
                    'requisition_no'  => $first->requisition_no,
                    'invoice_number'  => $first->invoice_number,
                    'central_store'   => $first->issuefrom,
                    'line_count'      => $lines->count(),
                    'total_qty'       => (int) $lines->sum('qty'),
                    'issued_at'       => $lines->max('updated_at'),
                ];
            })->values();
        }

        return view('dashboard', [
            'user' => $user,
            'pendingStockCount' => $pendingStockCount,
            'approvedStockLines' => $approvedStockLines,
            'totalStockQty' => $totalStockQty,
            'activeStore' => $activeStore,
            'canApproveStock' => $stockApproval->canApproveStock(),
            'userName' => Auth::user()->name,
            'userRole' => optional(Auth::user()->categoryname)->cat_name ?? 'User',
            'isCentralStore' => $isCentralStore,
            'incomingRequisitions' => $incomingRequisitions,
            'awaitingHodRequisitions' => $awaitingHodRequisitions,
            'pendingRequisitionCount' => $pendingRequisitionCount,
            'awaitingHodCount' => $awaitingHodCount,
            'pendingIssueApprovals' => $pendingIssueApprovals,
            'pendingIssueApprovalCount' => $pendingIssueApprovalCount,
            'pendingIssueApprovalLines' => $pendingIssueApprovalLines,
            'canIssueStock' => $canIssueStock,
            'canApproveIssues' => $canApproveIssues,
            'canApproveRequisitions' => $canApproveRequisitions,
            'canStockEntry' => $canStockEntry,
            'canPendingStock' => $canPendingStock,
            'canStockApprovalMenu' => $canStockApprovalMenu,
            'canManageItems' => $canManageItems,
            'canReorder' => $canReorder,
            'isSatelliteStore' => $isSatelliteStore,
            'incomingTransfers' => $incomingTransfers,
            'pendingReceiptCount' => $pendingReceiptCount,
            'pendingReceiptQty' => $pendingReceiptQty,
        ]);
    }

    public function logoutAuthenticationProcess()
    {
        Auth::logout();

        $updateLogs = UsrUserLog::find((int) session('userLogId'));
        if ($updateLogs) {
            $updateLogs->logout_date = Carbon::now();
            $updateLogs->update();
        }

        session()->forget('userLogId');

        return redirect('/')->with('message_success', 'Logged out successfully');
    }

    public function themeSettings()
    {
        return view('layouts.theme-settings');
    }
}
