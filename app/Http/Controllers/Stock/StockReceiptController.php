<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\ItemIssue;
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

        $pendingIssues = ItemIssue::with(['issuefrom', 'staffname', 'authorised', 'itemname'])
            ->whereIn('issue_to', $storeIds)
            ->awaitingReceipt()
            ->orderByDesc('updated_at')
            ->get();

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

        $listissues = ItemIssue::with(['itemcode', 'itemname.unitname', 'issuefrom', 'staffname', 'authorised'])
            ->whereIn('issue_to', $storeIds)
            ->where('requisition_no', $decodeID)
            ->awaitingReceipt()
            ->orderBy('batch_number')
            ->orderBy('id')
            ->get();

        if ($listissues->isEmpty()) {
            return redirect()->route('ReceiveStock')
                ->with('message_error', 'No pending items to receive for this transfer.');
        }

        $groupedIssues = $listissues->groupBy('item_id')->map(function ($lines) {
            $first = $lines->first();

            $batchLines = $lines->map(fn ($issue) => (object) [
                'issue_id'     => $issue->id,
                'batch_number' => $issue->batch_number,
                'qty'          => (int) $issue->qty,
                'amount'       => (float) ($issue->amount ?? 0),
            ])->values();

            return (object) [
                'item_id'      => $first->item_id,
                'itemcode'     => $first->itemcode,
                'itemname'     => $first->itemname,
                'lines'        => $batchLines,
                'total_qty'    => (int) $batchLines->sum('qty'),
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

        $issues = ItemIssue::whereIn('issue_to', $storeIds)
            ->where('requisition_no', $decodeID)
            ->awaitingReceipt()
            ->get();

        if ($issues->isEmpty()) {
            return redirect()->route('ReceiveStock')
                ->with('message_error', 'No pending items to receive for this transfer.');
        }

        try {
            $accepted = $this->stockReceipt->acceptIssues($issues);
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
}
