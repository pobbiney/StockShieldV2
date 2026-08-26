<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\SatelliteIssueRequest;
use App\Models\SatelliteStockReceipt;
use App\Models\Store;
use App\Models\Ward;
use App\Services\SatelliteIssueService;
use App\Services\NotificationService;
use App\Services\StoreContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class SatelliteIssueController extends Controller
{
    public function __construct(
        protected StoreContext $storeContext,
        protected SatelliteIssueService $satelliteIssue,
        protected NotificationService $notifications
    ) {}

    protected function resolveActiveStoreId(): ?int
    {
        $activeStoreId = $this->storeContext->getActiveStoreId();

        if ($activeStoreId) {
            return $activeStoreId;
        }

        $mapped = $this->storeContext->getMappedStoreIds(Auth::user());

        if (count($mapped) === 1) {
            $this->storeContext->setActiveStore($mapped[0]);

            return $mapped[0];
        }

        return null;
    }

    protected function assertSatelliteStore(): Store
    {
        $activeStoreId = $this->resolveActiveStoreId();

        if (!$activeStoreId) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->route('choose-store')
            );
        }

        $activeStore = Store::find($activeStoreId);

        if (!$activeStore || $activeStore->store_group !== 'satellite') {
            abort(403, 'Issue Item (Satellite) is only available for satellite stores.');
        }

        return $activeStore;
    }

    protected function usesStoreDestinations(Store $store): bool
    {
        return $store->isGeneralAdminSatelliteHub();
    }

    public function index()
    {
        $activeStore = $this->assertSatelliteStore();
        $activeStoreId = (int) $activeStore->id;
        $usesStoreDestinations = $this->usesStoreDestinations($activeStore);

        $itemIdsWithStock = SatelliteStockReceipt::where('store_id', $activeStoreId)
            ->where('qty', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', now());
            })
            ->pluck('item_id')
            ->unique();

        $getItemid = Item::whereIn('id', $itemIdsWithStock)
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $wards = $usesStoreDestinations
            ? collect()
            : Ward::where('store_id', $activeStoreId)
                ->where('status', 'Active')
                ->orderBy('name')
                ->get();

        $destinationStores = $usesStoreDestinations
            ? Store::where('status', 'Active')
                ->where('id', '!=', $activeStoreId)
                ->orderBy('name')
                ->get()
            : collect();

        $cartItems = SatelliteIssueRequest::with(['itemcode', 'itemname.unitname', 'ward', 'issueToStore'])
            ->where('store_id', $activeStoreId)
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->get();

        $submittedItems = SatelliteIssueRequest::with(['itemcode', 'itemname.unitname', 'ward', 'issueToStore'])
            ->where('store_id', $activeStoreId)
            ->whereNotNull('issue_no')
            ->where('status', '!=', 'pending')
            ->orderByDesc('created_at')
            ->get();

        $submittedGroups = $submittedItems->groupBy('issue_no')->map(function ($lines) use ($usesStoreDestinations) {
            $first = $lines->first();
            $wardNames = $lines->map(fn ($line) => $line->destinationLabel())
                ->filter(fn ($name) => $name !== '—')
                ->unique()
                ->values();

            return (object) [
                'issue_no'            => $first->issue_no,
                'ward'                => $first->ward,
                'ward_label'          => $wardNames->count() > 1 ? $wardNames->join(', ') : ($wardNames->first() ?? ($usesStoreDestinations ? 'Store' : 'Ward')),
                'line_count'          => $lines->count(),
                'total_qty_requested' => (int) $lines->sum('qty_requested'),
                'total_qty_issued'    => (int) $lines->sum('qty_issued'),
                'submitted_at'        => $lines->min('created_at'),
                'lines'               => $lines,
                'can_issue'           => $lines->contains(fn ($line) => in_array($line->status, ['submitted', 'partial'], true)
                    && (int) $line->qty_issued < (int) $line->qty_requested),
            ];
        })->values();

        $pendingCount = $cartItems->count();
        $totalQtyRequested = (int) $cartItems->sum('qty_requested');
        $uniqueItems = $cartItems->pluck('item_id')->unique()->count();

        return view('stock.IssueItemSatellite', [
            'getItemid'               => $getItemid,
            'wards'                   => $wards,
            'destinationStores'       => $destinationStores,
            'usesStoreDestinations'   => $usesStoreDestinations,
            'destinationLabel'        => $usesStoreDestinations ? 'Store' : 'Ward',
            'listitemissue'           => $cartItems,
            'submittedGroups'         => $submittedGroups,
            'activeStore'             => $activeStore,
            'pendingCount'            => $pendingCount,
            'totalQtyRequested'       => $totalQtyRequested,
            'uniqueItems'             => $uniqueItems,
        ]);
    }

    public function add(Request $request)
    {
        $activeStore = $this->assertSatelliteStore();
        $activeStoreId = (int) $activeStore->id;
        $usesStoreDestinations = $this->usesStoreDestinations($activeStore);

        if ($usesStoreDestinations) {
            $request->validate([
                'item'            => 'required|integer',
                'issue_to_store'  => 'required|integer|exists:stores,id',
                'quantity'        => 'required|numeric|min:1',
            ]);
        } else {
            $request->validate([
                'item'     => 'required|integer',
                'ward'     => 'required|integer|exists:wards,id',
                'quantity' => 'required|numeric|min:1',
            ]);
        }

        $receipt = $this->satelliteIssue->findEarliestReceipt((int) $request->item, $activeStoreId);

        if (!$receipt) {
            return redirect()->route('IssueItemSatellite')->with(
                'message_error',
                'Selected item has no available stock at this satellite store.'
            );
        }

        $available = $this->satelliteIssue->availableQty((int) $request->item, $activeStoreId);

        if ((int) $request->quantity > $available) {
            return redirect()->route('IssueItemSatellite')->with(
                'message_error',
                "Requested quantity exceeds available stock ({$available} units)."
            );
        }

        $wardId = null;
        $issueToStoreId = null;

        if ($usesStoreDestinations) {
            $destinationStore = Store::where('id', (int) $request->issue_to_store)
                ->where('status', 'Active')
                ->where('id', '!=', $activeStoreId)
                ->first();

            if (!$destinationStore) {
                return redirect()->route('IssueItemSatellite')->with(
                    'message_error',
                    'Selected store is not available for issue.'
                );
            }

            $issueToStoreId = $destinationStore->id;
        } else {
            $ward = Ward::where('store_id', $activeStoreId)
                ->where('id', (int) $request->ward)
                ->where('status', 'Active')
                ->first();

            if (!$ward) {
                return redirect()->route('IssueItemSatellite')->with(
                    'message_error',
                    'Selected ward is not available for this satellite store.'
                );
            }

            $wardId = $ward->id;
        }

        try {
            SatelliteIssueRequest::create([
                'satellite_stock_receipt_id' => $receipt->id,
                'item_id'                    => $receipt->item_id,
                'batch_number'               => $receipt->batch_number,
                'qty_requested'              => (int) $request->quantity,
                'qty_issued'                 => 0,
                'amount'                     => $receipt->amount ?? 0,
                'store_id'                   => $activeStoreId,
                'issue_to_store_id'          => $issueToStoreId,
                'ward_id'                    => $wardId,
                'status'                     => 'pending',
                'created_by'                 => Auth::id(),
            ]);
        } catch (\Exception $e) {
            return redirect()->route('IssueItemSatellite')->with(
                'message_error',
                'Something went wrong: ' . $e->getMessage()
            );
        }

        return redirect()->route('IssueItemSatellite')->with(
            'message_success',
            'Item added to your issue draft.'
        );
    }

    public function destroy(string $id)
    {
        $activeStore = $this->assertSatelliteStore();

        SatelliteIssueRequest::where('id', $id)
            ->where('store_id', $activeStore->id)
            ->where('status', 'pending')
            ->delete();

        return redirect()->route('IssueItemSatellite')->with(
            'message_success',
            'Item removed from draft.'
        );
    }

    public function submit()
    {
        $activeStore = $this->assertSatelliteStore();
        $activeStoreId = (int) $activeStore->id;

        $requests = SatelliteIssueRequest::where('status', 'pending')
            ->where('store_id', $activeStoreId)
            ->get();

        if ($requests->isEmpty()) {
            return back()->with('message_error', 'No draft items to submit.');
        }

        $date = now()->format('Ymd');

        $lastRecord = SatelliteIssueRequest::whereDate('created_at', today())
            ->whereNotNull('issue_no')
            ->latest('id')
            ->first();

        $lastNumber = $lastRecord ? (int) substr($lastRecord->issue_no, -4) : 0;
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $issueNo    = 'SIS-' . $date . '-' . $nextNumber;

        foreach ($requests as $item) {
            $item->update([
                'status'   => 'submitted',
                'issue_no' => $issueNo,
            ]);
        }

        $lineCount = $requests->count();
        $this->notifications->notifyUsersForAction(
            NotificationService::TYPE_SATELLITE_ISSUE_SUBMITTED,
            'Satellite Issue Slip Submitted',
            "Issue slip {$issueNo} with {$lineCount} line(s) is ready to be issued from stock.",
            'IssueItemSatellite',
            $issueNo,
            $activeStoreId,
            [Auth::id()]
        );

        return redirect()->route('IssueItemSatellite')->with(
            'message_success',
            "Issue slip {$issueNo} submitted successfully. You can issue stock when ready."
        );
    }

    public function issueBatch(string $issueNo)
    {
        $activeStore = $this->assertSatelliteStore();

        try {
            $decoded = Crypt::decrypt($issueNo);
        } catch (\Exception $e) {
            $decoded = $issueNo;
        }

        $issued = $this->satelliteIssue->issueBatch($decoded, (int) $activeStore->id);

        if ($issued === 0) {
            return back()->with('message_error', 'Unable to issue items. Check stock availability.');
        }

        $this->notifications->resolveIfComplete(
            NotificationService::TYPE_SATELLITE_ISSUE_SUBMITTED,
            $decoded
        );

        return redirect()
            ->route('satellite-issue.print', Crypt::encrypt($decoded))
            ->with(
                'message_success',
                "{$issued} line(s) issued successfully from satellite inventory."
            );
    }

    public function printIssueSlip(string $issueNo)
    {
        $activeStore = $this->assertSatelliteStore();

        try {
            $decoded = Crypt::decrypt($issueNo);
        } catch (\Exception $e) {
            $decoded = $issueNo;
        }

        $lines = SatelliteIssueRequest::with([
            'itemcode',
            'itemname.unitname',
            'ward',
            'issueToStore',
            'storename',
            'issuedByUser',
        ])
            ->where('issue_no', $decoded)
            ->where('store_id', $activeStore->id)
            ->where('qty_issued', '>', 0)
            ->orderBy('id')
            ->get();

        if ($lines->isEmpty()) {
            return redirect()->route('IssueItemSatellite')
                ->with('message_error', 'Issue slip not found or not yet issued.');
        }

        $first = $lines->first();
        $destinationNames = $lines->map(fn ($line) => $line->destinationLabel())
            ->filter(fn ($name) => $name !== '—')
            ->unique()
            ->values();

        return view('stock.printSatelliteIssue', [
            'issueNo'   => $decoded,
            'lines'     => $lines,
            'store'     => $first->storename,
            'issuedBy'  => $first->issuedByUser,
            'issuedAt'  => $lines->max('issued_at'),
            'wardLabel' => $destinationNames->count() > 1
                ? $destinationNames->join(', ')
                : ($destinationNames->first() ?? '—'),
            'destinationLabel' => $this->usesStoreDestinations($activeStore) ? 'Store' : 'Ward',
        ]);
    }

    public function getBatchNumber(Request $request)
    {
        $activeStore = $this->assertSatelliteStore();
        $itemId = (int) $request->getID;

        $item = Item::with('unitname')->find($itemId);

        if (!$item) {
            return response()->json([
                'batch_number'  => null,
                'message_error' => 'Item not found',
            ]);
        }

        $receipt = $this->satelliteIssue->findEarliestReceipt($itemId, (int) $activeStore->id);

        if (!$receipt) {
            return response()->json([
                'batch_number'  => null,
                'item_name'     => $item->name,
                'message_error' => 'No quantity for ' . $item->name,
            ]);
        }

        $available = $this->satelliteIssue->availableQty($itemId, (int) $activeStore->id);

        return response()->json([
            'batch_number' => $receipt->batch_number,
            'item_name'    => $item->name,
            'store_id'     => $receipt->store_id,
            'receipt_id'   => $receipt->id,
            'qty'          => $available,
            'expiry_date'  => $receipt->expiry_date,
            'uom_name'     => optional($item->unitname)->name,
        ]);
    }
}
