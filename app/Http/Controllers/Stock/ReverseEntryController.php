<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\ApproveStock;
use App\Models\StockReversal;
use App\Services\StockReversalService;
use App\Services\StoreContext;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ReverseEntryController extends Controller
{
    public function __construct(
        protected StoreContext $storeContext,
        protected StockReversalService $reversalService,
    ) {}

    public function index()
    {
        $scopedStoreIds = $this->storeContext->getScopedStoreIds();

        if (empty($scopedStoreIds)) {
            return redirect()->route('choose-store')
                ->with('message_error', 'Select a store to manage reverse entries.');
        }

        $liststock = ApproveStock::with(['itemcode', 'itemname', 'supname', 'storename'])
            ->whereIn('store_id', $scopedStoreIds)
            ->where('status', 'approved')
            ->where('qty', '>', 0)
            ->orderByDesc('id')
            ->get();

        $pendingReversals = StockReversal::whereIn('store_id', $scopedStoreIds)
            ->where('status', StockReversal::STATUS_PENDING)
            ->count();

        return view('stock.reverseEntry', [
            'liststock'        => $liststock,
            'activeStore'      => $this->storeContext->getActiveStore(),
            'totalBatches'     => $liststock->count(),
            'uniqueItems'      => $liststock->pluck('item_id')->unique()->count(),
            'totalQty'         => (int) $liststock->sum('qty'),
            'pendingReversals' => $pendingReversals,
        ]);
    }

    public function batchLookup(Request $request)
    {
        $batchNumber = trim((string) $request->query('batch_number', ''));

        if ($batchNumber === '') {
            return response()->json(['error' => 'Batch number is required.'], 422);
        }

        $batch = $this->reversalService->findReversibleBatch($batchNumber);

        if (!$batch) {
            return response()->json(['error' => 'Batch not found or not available for reversal.'], 404);
        }

        if (StockReversal::where('batch_number', $batch->batch_number)
            ->where('store_id', $batch->store_id)
            ->where('status', StockReversal::STATUS_PENDING)
            ->exists()) {
            return response()->json(['error' => 'A pending reversal already exists for this batch.'], 422);
        }

        return response()->json([
            'id'           => $batch->id,
            'item_id'      => $batch->item_id,
            'item_name'    => $batch->itemname->name ?? 'Item',
            'item_code'    => $batch->itemcode->item_code ?? '—',
            'batch_number' => $batch->batch_number,
            'qty'          => (int) $batch->qty,
            'expiry_date'  => $batch->expiry_date,
            'store_name'   => $batch->storename->name ?? null,
            'purchase_order' => $batch->purchase_order,
        ]);
    }

    public function getBatchById($id)
    {
        $scopedStoreIds = $this->storeContext->getScopedStoreIds();

        $batch = ApproveStock::with(['itemcode', 'itemname', 'storename'])
            ->whereIn('store_id', $scopedStoreIds)
            ->where('status', 'approved')
            ->where('qty', '>', 0)
            ->findOrFail($id);

        return response()->json([
            'id'           => $batch->id,
            'item_id'      => $batch->item_id,
            'item_name'    => $batch->itemname->name ?? 'Item',
            'item_code'    => $batch->itemcode->item_code ?? '—',
            'batch_number' => $batch->batch_number,
            'qty'          => (int) $batch->qty,
            'expiry_date'  => $batch->expiry_date,
            'store_name'   => $batch->storename->name ?? null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'batch_number'  => 'required|string',
            'reversal_type' => 'required|in:partial,full,delete',
            'qty'           => 'nullable|integer|min:1',
            'reason'        => 'required|string|min:3',
        ]);

        if ($request->reversal_type === StockReversal::TYPE_PARTIAL && !$request->filled('qty')) {
            return back()->withInput()->with('message_error', 'Quantity is required for partial reversal.');
        }

        try {
            $this->reversalService->submit(
                $request->batch_number,
                $request->reversal_type,
                $request->qty ? (int) $request->qty : null,
                $request->reason
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('message_error', $e->getMessage());
        }

        return back()->with('message_success', 'Reverse entry submitted successfully. Awaiting approval.');
    }

    public function approvalIndex()
    {
        if (!$this->reversalService->canApproveReversal()) {
            abort(403, 'You are not authorized to approve reverse entries.');
        }

        $scopedStoreIds = $this->storeContext->getScopedStoreIds();

        $listReversals = StockReversal::with([
                'itemcode',
                'itemname',
                'store',
                'requestedByUser',
                'approveStock.supname',
            ])
            ->whereIn('store_id', $scopedStoreIds)
            ->where('status', StockReversal::STATUS_PENDING)
            ->orderByDesc('id')
            ->get();

        return view('stock.reverseEntryApproval', [
            'listReversals' => $listReversals,
            'activeStore'   => $this->storeContext->getActiveStore(),
            'pendingCount'  => $listReversals->count(),
            'totalQty'      => (int) $listReversals->sum('qty_to_reverse'),
            'uniqueItems'   => $listReversals->pluck('item_id')->unique()->count(),
        ]);
    }

    public function approve(Request $request, StockReversal $reversal)
    {
        $request->validate([
            'comment' => 'required|string|min:3',
        ]);

        if (!$this->reversalService->approve($reversal, $request->comment)) {
            return back()->with('message_error', 'Unable to approve this reversal. It may no longer be pending or you lack permission.');
        }

        return back()->with('message_success', 'Reverse entry approved. Inventory has been updated.');
    }

    public function reject(Request $request, StockReversal $reversal)
    {
        $request->validate([
            'comment' => 'required|string|min:3',
        ]);

        if (!$this->reversalService->reject($reversal, $request->comment)) {
            return back()->with('message_error', 'Unable to reject this reversal. It may no longer be pending or you lack permission.');
        }

        return back()->with('message_success', 'Reverse entry rejected.');
    }
}
