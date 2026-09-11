<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\ItemIssue;
use App\Models\SatelliteItemIssue;
use App\Services\NotificationService;
use App\Services\StockReceiptService;
use App\Services\StoreContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use RuntimeException;

class StockReceiptController extends Controller
{
    public function __construct(
        protected StoreContext $storeContext,
        protected StockReceiptService $stockReceipt,
        protected NotificationService $notifications
    ) {}

    public function index()
    {
        try {
            $activeStore = $this->stockReceipt->assertSatelliteReceiptContext();
        } catch (RuntimeException $e) {
            return redirect()->route('dashboard')->with('message_error', $e->getMessage());
        }

        $storeIds = [(int) $activeStore->id];

        $pendingCentral = ItemIssue::with(['issuefrom', 'staffname', 'authorised', 'itemname'])
            ->whereIn('issue_to', $storeIds)
            ->awaitingReceipt()
            ->orderByDesc('updated_at')
            ->get();

        $pendingSatellite = SatelliteItemIssue::with(['issuefrom', 'staffname', 'authorised', 'itemname'])
            ->whereIn('issue_to', $storeIds)
            ->awaitingReceipt()
            ->orderByDesc('updated_at')
            ->get();

        $pendingIssues = $pendingCentral->concat($pendingSatellite)->sortByDesc('updated_at')->values();

        $transfers = $pendingIssues->groupBy('requisition_no')->map(function ($lines) {
            $first = $lines->first();

            return (object) [
                'requisition_no'  => $first->requisition_no,
                'invoice_number'  => $first->invoice_number,
                'central_store'   => $first->issuefrom,
                'issued_by'       => $first->authorised,
                'line_count'      => $lines->count(),
                'unique_items'    => $lines->pluck('item_id')->unique()->count(),
                'total_qty'       => (int) $lines->sum('qty'),
                'issued_at'       => $lines->max('updated_at'),
            ];
        })->values();

        return view('stock.ReceiveStock', [
            'transfers'           => $transfers,
            'activeStore'         => $activeStore,
            'totalTransfers'      => $transfers->count(),
            'totalLineItems'      => $pendingIssues->count(),
            'totalQty'            => (int) $pendingIssues->sum('qty'),
        ]);
    }

    public function show($requisition_no)
    {
        try {
            $activeStore = $this->stockReceipt->assertSatelliteReceiptContext();
        } catch (RuntimeException $e) {
            return redirect()->route('ReceiveStock')->with('message_error', $e->getMessage());
        }

        $decodeID = Crypt::decrypt($requisition_no);
        $storeIds = [(int) $activeStore->id];

        $centralIssues = ItemIssue::with(['itemcode', 'itemname.unitname', 'issuefrom', 'staffname', 'authorised'])
            ->whereIn('issue_to', $storeIds)
            ->where('requisition_no', $decodeID)
            ->awaitingReceipt()
            ->orderBy('batch_number')
            ->orderBy('id')
            ->get();

        $satelliteIssues = SatelliteItemIssue::with(['itemcode', 'itemname.unitname', 'issuefrom', 'staffname', 'authorised'])
            ->whereIn('issue_to', $storeIds)
            ->where('requisition_no', $decodeID)
            ->awaitingReceipt()
            ->orderBy('batch_number')
            ->orderBy('id')
            ->get();

        $listissues = $centralIssues->concat($satelliteIssues);

        if ($listissues->isEmpty()) {
            return redirect()->route('ReceiveStock')
                ->with('message_error', 'No pending items to receive for this transfer.');
        }

        $groupedIssues = $listissues->groupBy('item_id')->map(function ($lines) {
            $first = $lines->first();
            $multiplier = $this->receiptItemTotalQtyMultiplier($first->itemname);

            $batchLines = $lines->map(function ($issue) use ($multiplier) {
                $issuedQty = (int) $issue->qty;

                return (object) [
                    'issue_id'       => $issue->id,
                    'batch_number'   => $issue->batch_number,
                    'qty'            => $issuedQty,
                    'accept_qty'     => $this->receiptEffectiveQty($issuedQty, $multiplier),
                    'amount'         => (float) ($issue->amount ?? 0),
                    'issuing_store'  => $issue->issuefrom,
                ];
            })->values();

            $issuedTotal = (int) $batchLines->sum('qty');
            $acceptTotal = (int) $batchLines->sum('accept_qty');

            return (object) [
                'item_id'               => $first->item_id,
                'itemcode'              => $first->itemcode,
                'itemname'              => $first->itemname,
                'lines'                 => $batchLines,
                'issuing_store'         => $first->issuefrom,
                'total_qty_multiplier'  => $multiplier,
                'issued_qty'            => $issuedTotal,
                'total_qty'             => $acceptTotal,
            ];
        })->values();

        $first = $listissues->first();

        return view('stock.viewReceiveStock', [
            'listissues'    => $listissues,
            'groupedIssues' => $groupedIssues,
            'requisitionNo' => $decodeID,
            'invoiceNumber' => $first->invoice_number,
            'centralStore'  => $first->issuefrom,
            'activeStore'   => $activeStore,
        ]);
    }

    public function accept(Request $request, $requisition_no)
    {
        try {
            $activeStore = $this->stockReceipt->assertSatelliteReceiptContext();
        } catch (RuntimeException $e) {
            return back()->with('message_error', $e->getMessage());
        }

        $decodeID = Crypt::decrypt($requisition_no);
        $storeIds = [(int) $activeStore->id];

        $centralIssues = ItemIssue::with('itemname')
            ->whereIn('issue_to', $storeIds)
            ->where('requisition_no', $decodeID)
            ->awaitingReceipt()
            ->get();

        $satelliteIssues = SatelliteItemIssue::with('itemname')
            ->whereIn('issue_to', $storeIds)
            ->where('requisition_no', $decodeID)
            ->awaitingReceipt()
            ->get();

        if ($centralIssues->isEmpty() && $satelliteIssues->isEmpty()) {
            return redirect()->route('ReceiveStock')
                ->with('message_error', 'No pending items to receive for this transfer.');
        }

        try {
            $accepted = $this->stockReceipt->acceptIssues($centralIssues);
            $accepted += $this->stockReceipt->acceptSatelliteIssues($satelliteIssues);
        } catch (RuntimeException $e) {
            return back()->with('message_error', $e->getMessage());
        }

        if ($accepted === 0) {
            return back()->with('message_error', 'Nothing was received. Items may have already been accepted.');
        }

        $this->notifications->resolveIfComplete(
            NotificationService::TYPE_ISSUE_APPROVED,
            $decodeID
        );

        return redirect()->route('ReceiveStock')
            ->with('message_success', $accepted . ' item line(s) accepted into ' . $activeStore->name . ' inventory.');
    }

    private function receiptItemTotalQtyMultiplier($item): ?int
    {
        if (!$item) {
            return null;
        }

        $multiplier = $item->total_qty ?? null;

        if ($multiplier === null || $multiplier === '') {
            return null;
        }

        $multiplier = (int) $multiplier;

        return $multiplier > 0 ? $multiplier : null;
    }

    private function receiptEffectiveQty(int $issuedQty, ?int $totalQtyMultiplier): int
    {
        if ($totalQtyMultiplier === null) {
            return $issuedQty;
        }

        return $issuedQty * $totalQtyMultiplier;
    }
}
