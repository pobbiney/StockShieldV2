<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Models\ApproveStock;
use App\Models\Item;
use App\Models\ItemIssue;
use App\Models\ItemRequest;
use App\Models\SatelliteItemIssue;
use App\Models\SatelliteStockReceipt;
use App\Models\ReturnItem;
use App\Models\SatelliteIssueRequest;
use App\Models\Stock;
use App\Models\Store;
use App\Models\UnitOfMeasure;
use App\Services\NotificationService;
use App\Services\RequisitionFulfillmentService;
use App\Services\StoreContext;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RequisitionController extends Controller
{
    public function __construct(
        protected StoreContext $storeContext,
        protected NotificationService $notifications,
        protected RequisitionFulfillmentService $requisitionFulfillment
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

    protected function centralStoreIds(): array
    {
        $ids = Store::where('store_group', 'central')->pluck('id')->map(fn ($id) => (int) $id)->all();

        return $ids;
    }

    protected function resolveActiveStore(): ?Store
    {
        $activeStoreId = $this->resolveActiveStoreId();

        return $activeStoreId ? Store::find($activeStoreId) : null;
    }

    /**
     * Central pick lists cover stock issued from this store and stock issued to it.
     */
    protected function scopePickListStores($query, array $storeIds)
    {
        return $query->where(function ($query) use ($storeIds) {
            $query->whereIn('store_id', $storeIds)
                ->orWhereIn('issue_to', $storeIds);
        });
    }

    /**
     * Include mapped satellite stores when the user has both central and satellite access.
     */
    protected function pickListStoreIds(): array
    {
        $user = Auth::user();
        $scoped = $this->storeContext->getScopedStoreIds($user);

        if (!$user || $this->storeContext->hasGlobalStoreAccess($user)) {
            return $scoped;
        }

        $mapped = $this->storeContext->getMappedStoreIds($user);

        if (count($mapped) < 2) {
            return $scoped;
        }

        $groups = Store::whereIn('id', $mapped)->pluck('store_group')->unique()->filter();

        if ($groups->contains('central') && $groups->contains('satellite')) {
            return array_values(array_unique(array_merge($scoped, $mapped)));
        }

        return $scoped;
    }

    protected function mapSatelliteIssueGroupToPickRow($lines): object
    {
        $first = $lines->first();

        return (object) [
            'pick_type'       => 'satellite_issue',
            'reference_no'    => $first->requisition_no,
            'issue_no'        => null,
            'requisition_no'  => $first->requisition_no,
            'invoice_number'  => $first->invoice_number,
            'issuefrom'       => $first->issuefrom,
            'storename'       => $first->storename,
            'central_store'   => $first->issuefrom,
            'to_store'        => $first->storename,
            'ward_label'      => null,
            'issued_by'       => $first->authorised,
            'status'          => $first->status ?? 'issued',
            'updated_at'      => $lines->max('updated_at'),
            'line_count'      => $lines->count(),
            'unique_items'    => $lines->pluck('item_id')->unique()->count(),
            'total_qty'       => (int) $lines->sum('qty'),
            'issued_at'       => $lines->max('updated_at'),
            'requisition_unit'=> $this->pickListRequisitionUnit($lines),
        ];
    }

    protected function pickListRequisitionUnit($lines): ?string
    {
        $units = collect($lines)
            ->pluck('requisition_unit')
            ->map(fn ($unit) => is_string($unit) ? trim($unit) : '')
            ->filter()
            ->unique()
            ->values();

        if ($units->isEmpty()) {
            return null;
        }

        return $units->join(', ');
    }

    protected function satellitePickListRows(array $storeIds)
    {
        if (empty($storeIds)) {
            return collect();
        }

        $lines = SatelliteItemIssue::with(['issuefrom', 'storename', 'authorised'])
            ->awaitingReceipt();
        $this->scopePickListStores($lines, $storeIds);

        return $lines
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('requisition_no')
            ->map(fn ($group) => $this->mapSatelliteIssueGroupToPickRow($group))
            ->values();
    }

    protected function isSatellitePickListContext(): bool
    {
        $activeStore = $this->resolveActiveStore();

        return $activeStore && $activeStore->store_group === 'satellite';
    }

    /**
     * @return array{0: int, 1: Store}|null
     */
    protected function assertSatellitePickListContext(): ?array
    {
        $activeStore = $this->resolveActiveStore();

        if (!$activeStore) {
            return null;
        }

        if ($activeStore->store_group !== 'satellite') {
            return null;
        }

        if (!$this->storeContext->canAccessStore(Auth::user(), (int) $activeStore->id)) {
            return null;
        }

        return [(int) $activeStore->id, $activeStore];
    }

    protected function buildSatellitePickListGroups(int $activeStoreId): \Illuminate\Support\Collection
    {
        $wardIssues = SatelliteIssueRequest::with(['ward', 'issueToStore', 'issuedByUser', 'storename'])
            ->where('store_id', $activeStoreId)
            ->whereNotNull('issue_no')
            ->where('qty_issued', '>', 0)
            ->whereIn('status', ['issued', 'partial'])
            ->orderByDesc('issued_at')
            ->get()
            ->groupBy('issue_no')
            ->map(function ($lines) {
                $first = $lines->first();
                $destinationNames = $lines->map(fn ($line) => $line->destinationLabel())
                    ->filter(fn ($name) => $name !== '—')
                    ->unique()
                    ->values();

                return (object) [
                    'pick_type'       => 'ward_issue',
                    'reference_no'    => $first->issue_no,
                    'issue_no'        => $first->issue_no,
                    'requisition_no'  => $first->issue_no,
                    'invoice_number'  => null,
                    'central_store'   => null,
                    'ward_label'      => $destinationNames->count() > 1
                        ? $destinationNames->join(', ')
                        : ($destinationNames->first() ?? '—'),
                    'issued_by'       => $first->issuedByUser,
                    'line_count'      => $lines->count(),
                    'unique_items'    => $lines->pluck('item_id')->unique()->count(),
                    'total_qty'       => (int) $lines->sum('qty_issued'),
                    'issued_at'       => $lines->max('issued_at'),
                    'requisition_unit'=> $this->pickListRequisitionUnit($lines),
                ];
            });

        $centralTransferLines = ItemIssue::with(['issuefrom', 'staffname', 'authorised'])
            ->where('issue_to', $activeStoreId)
            ->awaitingReceipt()
            ->orderByDesc('updated_at')
            ->get();

        $satelliteTransferLines = SatelliteItemIssue::with(['issuefrom', 'staffname', 'authorised', 'storename'])
            ->where('issue_to', $activeStoreId)
            ->awaitingReceipt()
            ->orderByDesc('updated_at')
            ->get();

        $centralTransfers = $centralTransferLines->concat($satelliteTransferLines)
            ->groupBy('requisition_no')
            ->map(function ($lines) {
                $first = $lines->first();

                return (object) [
                    'pick_type'       => 'central_transfer',
                    'reference_no'    => $first->requisition_no,
                    'issue_no'        => null,
                    'requisition_no'  => $first->requisition_no,
                    'invoice_number'  => $first->invoice_number,
                    'central_store'   => $first->issuefrom,
                    'to_store'        => $first->storename ?? null,
                    'ward_label'      => null,
                    'issued_by'       => $first->authorised,
                    'line_count'      => $lines->count(),
                    'unique_items'    => $lines->pluck('item_id')->unique()->count(),
                    'total_qty'       => (int) $lines->sum('qty'),
                    'issued_at'       => $lines->max('updated_at'),
                    'requisition_unit'=> $this->pickListRequisitionUnit($lines),
                ];
            });

        $outboundSatelliteIssues = SatelliteItemIssue::with(['issuefrom', 'storename', 'authorised'])
            ->where('store_id', $activeStoreId)
            ->where('issue_to', '!=', $activeStoreId)
            ->awaitingReceipt()
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('requisition_no')
            ->map(fn ($lines) => $this->mapSatelliteIssueGroupToPickRow($lines));

        return $wardIssues
            ->concat($centralTransfers)
            ->concat($outboundSatelliteIssues)
            ->sortByDesc(fn ($group) => optional($group->issued_at)?->timestamp ?? 0)
            ->values();
    }

    public function getRequisitionView()
    {
        $activeStoreId = $this->resolveActiveStoreId();

        if (!$activeStoreId) {
            return redirect()->route('choose-store');
        }

        $activeStore = Store::find($activeStoreId);

        $getItemid = Item::with('unitname')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $cartItems = ItemRequest::with(['itemcode', 'itemname.unitname', 'sourceStore'])
            ->where('store_id', $activeStoreId)
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->get();

        $pendingCount = $cartItems->count();
        $totalQtyRequested = (int) $cartItems->sum('qty_requested');
        $uniqueItems = $cartItems->pluck('item_id')->unique()->count();

        $requestFromStores = Store::requestFromStores();
        $centralStores = $requestFromStores;
        $fulfillmentLabel = $requestFromStores->pluck('name')->join(', ') ?: 'Central Stores';
        $routingMode = $activeStore
            ? $this->requisitionFulfillment->routingMode($activeStore)
            : 'central';
        $routesToHub = in_array($routingMode, ['hub', 'both'], true);

        return view('requisition.Requisition', [
            'getItemid'          => $getItemid,
            'listitemissue'      => $cartItems,
            'activeStore'        => $activeStore,
            'centralStores'      => $centralStores,
            'requestFromStores'  => $requestFromStores,
            'fulfillmentLabel'   => $fulfillmentLabel,
            'routingMode'        => $routingMode,
            'routesToHub'        => $routesToHub,
            'requisitionHub'     => $this->requisitionFulfillment->hubStore(),
            'pendingCount'       => $pendingCount,
            'totalQtyRequested'  => $totalQtyRequested,
            'uniqueItems'        => $uniqueItems,
        ]);
    }

    public function addRequest(Request $request)
    {
        $allowedStoreIds = Store::requestFromStores()->pluck('id')->map(fn ($id) => (int) $id)->all();

        $request->validate([
            'item'           => 'required|integer|exists:items,id',
            'quantity'       => 'required|numeric|min:1',
            'item_store_id'  => ['required', 'integer', 'in:' . implode(',', $allowedStoreIds ?: [0])],
        ]);

        $activeStoreId = $this->resolveActiveStoreId();

        if (!$activeStoreId) {
            return redirect()->route('choose-store');
        }

        $item = Item::find((int) $request->item);

        if (!$item || $item->status !== 'Active') {
            return redirect()->route('Requisition')->with(
                'message_error',
                'Selected item is not available for requisition.'
            );
        }

        $fulfillmentStore = Store::find((int) $request->item_store_id);

        if (!$fulfillmentStore) {
            return redirect()->route('Requisition')->with(
                'message_error',
                'Select the store you are requesting from.'
            );
        }

        try {
            $lineMeta = $this->requisitionFulfillment->resolveLineMetadataForFulfillmentStore($fulfillmentStore, $item);
        } catch (RuntimeException $e) {
            return redirect()->route('Requisition')->with('message_error', $e->getMessage());
        }

        try {
            ItemRequest::create([
                'stock_id'      => $lineMeta['stock_id'],
                'item_id'       => $item->id,
                'batch_number'  => $lineMeta['batch_number'],
                'qty_requested' => (int) $request->quantity,
                'qty_issued'    => 0,
                'amount'        => $lineMeta['amount'],
                'item_store_id' => $lineMeta['item_store_id'],
                'store_id'      => $activeStoreId,
                'created_by'    => Auth::id(),
            ]);
        } catch (\Exception $e) {
            return redirect()->route('Requisition')->with('message_error', 'Something went wrong: ' . $e->getMessage());
        }

        return redirect()->route('Requisition')->with('message_success', 'Item added to your requisition draft.');
    }

    public function deleteitemRequest(string $id)
    {
        $activeStoreId = $this->resolveActiveStoreId();

        if (!$activeStoreId) {
            return redirect()->route('choose-store');
        }

        ItemRequest::where('id', $id)
            ->where('store_id', $activeStoreId)
            ->where('status', 'pending')
            ->delete();

        return redirect()->route('Requisition')->with('message_success', 'Item removed from draft.');
    }

    public function submitRequest()
    {
        $activeStoreId = $this->resolveActiveStoreId();

        if (!$activeStoreId) {
            return redirect()->route('choose-store');
        }

        $date = now()->format('Ymd');

        $lastRecord = ItemRequest::whereDate('created_at', today())
            ->whereNotNull('requisition_no')
            ->latest('id')
            ->first();

        $lastNumber = $lastRecord ? (int) substr($lastRecord->requisition_no, -4) : 0;
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $requestNo  = 'REQ-' . $date . '-' . $nextNumber;

        $requests = ItemRequest::where('status', 'pending')
            ->where('store_id', $activeStoreId)
            ->get();

        if ($requests->isEmpty()) {
            return back()->with('message_error', 'No draft items to submit.');
        }

        foreach ($requests as $item) {
            $item->update([
                'status'         => 'pending request',
                'requisition_no' => $requestNo,
            ]);
        }

        $itemCount = $requests->count();
        $storeName = Store::find($activeStoreId)?->name ?? 'Store';
        $fulfillStoreIds = $requests->pluck('item_store_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($fulfillStoreIds->isEmpty()) {
            $fulfillStoreIds = collect([null]);
        }

        foreach ($fulfillStoreIds as $fulfillStoreId) {
            $this->notifications->notifyUsersForAction(
                NotificationService::TYPE_REQUISITION_SUBMITTED,
                'New Requisition Submitted',
                "{$storeName} submitted {$requestNo} with {$itemCount} item(s) awaiting approval.",
                'ApproveRequest',
                $requestNo,
                $fulfillStoreId ?: null,
                [Auth::id()]
            );
        }

        return redirect()->route('Requisition')->with(
            'message_success',
            "Requisition {$requestNo} submitted successfully. You can start a new request anytime."
        );
    }

    public function getMyRequestView()
    {
        $storeIds = $this->storeContext->getScopedStoreIds();
        $activeStore = $this->storeContext->getActiveStore();

        $listrequest = ItemRequest::with(['itemcode', 'itemname.unitname', 'sourceStore', 'issues'])
            ->whereIn('store_id', $storeIds)
            ->whereNotNull('requisition_no')
            ->orderByDesc('created_at')
            ->get();

        $totalCount = $listrequest->count();
        $totalQtyRequested = (int) $listrequest->sum(fn (ItemRequest $row) => $row->effectiveRequestedQuantity());
        $totalQtyIssued = (int) $listrequest->sum(fn (ItemRequest $row) => $row->effectiveIssuedQuantity());
        $fulfilledCount = $listrequest->filter(fn (ItemRequest $row) => $row->fulfillmentStatus() === 'fulfilled')->count();
        $pendingCount = $listrequest->filter(fn (ItemRequest $row) => in_array($row->fulfillmentStatus(), ['pending', 'partial'], true))->count();
        $rejectedCount = $listrequest->filter(fn (ItemRequest $row) => $row->status === 'rejected')->count();

        return view('requisition.MyRequest', [
            'listrequest'       => $listrequest,
            'activeStore'       => $activeStore,
            'totalCount'        => $totalCount,
            'totalQtyRequested' => $totalQtyRequested,
            'totalQtyIssued'    => $totalQtyIssued,
            'fulfilledCount'    => $fulfilledCount,
            'pendingCount'      => $pendingCount,
            'rejectedCount'     => $rejectedCount,
        ]);
    }

    public function getApproveRequestView()
    {
        $storeIds = $this->storeContext->getScopedStoreIds();

        $pendingRequests = ItemRequest::with(['storename', 'staffname', 'sourceStore'])
            ->where('status', 'pending request')
            ->when(
                !$this->storeContext->hasGlobalStoreAccess(),
                fn ($query) => $query->where(function ($scoped) use ($storeIds) {
                    $scoped->whereIn('store_id', $storeIds)
                        ->orWhereIn('item_store_id', $storeIds);
                })
            )
            ->orderByDesc('created_at')
            ->get();

        $requisitions = $pendingRequests->groupBy('requisition_no')->map(function ($lines) {
            $first = $lines->first();

            return (object) [
                'requisition_no'      => $first->requisition_no,
                'requesting_store'    => $first->storename,
                'requested_by'        => $first->staffname,
                'source_store'        => $first->sourceStore,
                'line_count'          => $lines->count(),
                'total_qty_requested' => (int) $lines->sum('qty_requested'),
                'submitted_at'        => $lines->min('created_at'),
            ];
        })->values();

        return view('requisition.ApproveRequest', [
            'requisitions'      => $requisitions,
            'totalRequisitions' => $requisitions->count(),
            'totalLineItems'    => $pendingRequests->count(),
            'totalQty'          => (int) $pendingRequests->sum('qty_requested'),
        ]);
    }

    public function getviewRequest($requisition_no)
    {
        $storeIds = $this->storeContext->getScopedStoreIds();
        $decodeID = Crypt::decrypt($requisition_no);

        $listrequest = ItemRequest::with(['itemcode', 'itemname.unitname', 'storename', 'staffname', 'sourceStore'])
            ->where('requisition_no', $decodeID)
            ->where('status', 'pending request')
            ->when(
                !$this->storeContext->hasGlobalStoreAccess(),
                fn ($query) => $query->where(function ($scoped) use ($storeIds) {
                    $scoped->whereIn('store_id', $storeIds)
                        ->orWhereIn('item_store_id', $storeIds);
                })
            )
            ->orderByDesc('id')
            ->get();

        return view('requisition.viewRequest', [
            'listrequest'   => $listrequest,
            'requisitionNo' => $decodeID,
        ]);
    }

    public function getrequesttemID($id)
    {
        $data = ItemRequest::with('itemname')->findOrFail($id);

        return response()->json([
            'id'   => $data->id,
            'name' => $data->itemname->name ?? 'Item',
        ]);
    }

    public function addItemRejectRequest(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'reason'  => 'required',
        ]);

        $insertCat = ItemRequest::find($request->item_id);
        $insertCat->status = 'rejected';
        $insertCat->reason = trim($request->reason);
        $insertCat->approved_by = Auth::id();

        $status = $insertCat->save();

        if ($status && $insertCat->requisition_no) {
            $this->notifications->resolveIfComplete(
                NotificationService::TYPE_REQUISITION_SUBMITTED,
                $insertCat->requisition_no
            );
            $this->notifications->resolveIfComplete(
                NotificationService::TYPE_REQUISITION_APPROVED,
                $insertCat->requisition_no
            );
        }

        return $status
            ? back()->with('message_success', 'Request has been rejected successfully')
            : back()->with('message_error', 'Something went wrong, please try again.');
    }

    public function addApproveRequest(Request $request)
    {
        $approvedRequisitions = [];

        foreach ($request->request_id as $requestId) {
            $itemRequest = ItemRequest::where('id', $requestId)
                ->where('status', 'pending request')
                ->first();

            if (!$itemRequest) {
                continue;
            }

            $approvedQty = $request->qty[$requestId] ?? $itemRequest->qty_requested;

            $itemRequest->qty         = $approvedQty;
            $itemRequest->status      = 'request approved';
            $itemRequest->approved_by = Auth::id();
            $itemRequest->save();

            $approvedRequisitions[$itemRequest->requisition_no] = (int) $itemRequest->item_store_id;
        }

        foreach (array_keys($approvedRequisitions) as $requisitionNo) {
            $this->notifications->resolveIfComplete(
                NotificationService::TYPE_REQUISITION_SUBMITTED,
                $requisitionNo
            );
        }

        foreach ($approvedRequisitions as $requisitionNo => $centralStoreId) {
            $this->notifications->notifyUsersForAction(
                NotificationService::TYPE_REQUISITION_APPROVED,
                'Requisition Approved',
                "Requisition {$requisitionNo} has been approved and is ready for issuing.",
                'IssueItem',
                $requisitionNo,
                $centralStoreId ?: null,
                [Auth::id()]
            );
        }

        return redirect()->route('ApproveRequest')
            ->with('message_success', 'Request approved successfully');
    }

    public function getPickListView()
    {
        if ($context = $this->assertSatellitePickListContext()) {
            [$activeStoreId, $activeStore] = $context;
            $pickLists = $this->buildSatellitePickListGroups($activeStoreId);

            return view('stock.PickListSatellite', [
                'pickLists'      => $pickLists,
                'activeStore'    => $activeStore,
                'totalPickLists' => $pickLists->count(),
                'totalLineItems' => $pickLists->sum('line_count'),
                'totalQty'       => $pickLists->sum('total_qty'),
            ]);
        }

        $listdept = $this->pickListStoreIds();

        $listrequest = ItemIssue::with(['issuefrom', 'storename'])
            ->whereIn('id', function ($query) use ($listdept) {
                $query->selectRaw('MAX(id)')
                    ->from('item_issues')
                    ->where('status', 'issued');
                $this->scopePickListStores($query, $listdept)
                    ->groupBy('requisition_no');
            })
            ->orderByDesc('id')
            ->get();

        $existingReqNos = $listrequest->pluck('requisition_no')->filter()->all();
        $satelliteRows = $this->satellitePickListRows($listdept)
            ->reject(fn ($row) => in_array($row->requisition_no, $existingReqNos, true))
            ->values();

        $listrequest = $listrequest
            ->concat($satelliteRows)
            ->sortByDesc(fn ($row) => optional($row->issued_at ?? $row->updated_at)?->timestamp ?? 0)
            ->values();

        return view('requisition.PickList', [
            'listrequest' => $listrequest,
            'activeStore' => $this->resolveActiveStore(),
        ]);
    }

    public function getviewPickList($requisition_no)
    {
        try {
            $decodeID = Crypt::decrypt($requisition_no);
        } catch (\Exception $e) {
            return redirect()->route('PickList')->with('message_error', 'Invalid pick list reference.');
        }

        if ($context = $this->assertSatellitePickListContext()) {
            [$activeStoreId, $activeStore] = $context;

            $satelliteLines = SatelliteIssueRequest::with([
                'itemcode',
                'itemname.unitname',
                'ward',
                'issueToStore',
                'storename',
                'staffname',
                'issuedByUser',
            ])
                ->where('store_id', $activeStoreId)
                ->where('issue_no', $decodeID)
                ->where('qty_issued', '>', 0)
                ->orderBy('batch_number')
                ->orderBy('id')
                ->get();

            if ($satelliteLines->isNotEmpty()) {
                $first = $satelliteLines->first();
                $destinationNames = $satelliteLines->map(fn ($line) => $line->destinationLabel())
                    ->filter(fn ($name) => $name !== '—')
                    ->unique()
                    ->values();
                $usesStoreDestinations = $activeStore->isGeneralAdminSatelliteHub();

                return view('stock.viewPickUpSatellite', [
                    'listrequest'   => $satelliteLines,
                    'activeStore'   => $activeStore,
                    'pickType'      => 'ward_issue',
                    'requisitionNo' => $decodeID,
                    'issueNo'       => $decodeID,
                    'invoiceNumber' => null,
                    'centralStore'  => null,
                    'wardLabel'     => $destinationNames->count() > 1
                        ? $destinationNames->join(', ')
                        : ($destinationNames->first() ?? '—'),
                    'destinationLabel' => $usesStoreDestinations ? 'Store' : 'Ward',
                    'issuedBy'      => $first->issuedByUser,
                ]);
            }

            $satelliteIssueLines = SatelliteItemIssue::with([
                'itemcode',
                'itemname.unitname',
                'unitname',
                'issuefrom',
                'storename',
                'staffname',
                'authorised',
            ])
                ->where('requisition_no', $decodeID)
                ->awaitingReceipt()
                ->where(function ($query) use ($activeStoreId) {
                    $query->where('store_id', $activeStoreId)
                        ->orWhere('issue_to', $activeStoreId);
                })
                ->orderBy('batch_number')
                ->orderBy('id')
                ->get();

            if ($satelliteIssueLines->isNotEmpty()) {
                $first = $satelliteIssueLines->first();
                $isOutbound = $satelliteIssueLines->contains(
                    fn ($line) => (int) $line->store_id === $activeStoreId
                );

                return view('stock.viewPickUpSatellite', [
                    'listrequest'   => $satelliteIssueLines,
                    'activeStore'   => $activeStore,
                    'pickType'      => $isOutbound ? 'satellite_issue' : 'central_transfer',
                    'requisitionNo' => $decodeID,
                    'issueNo'       => null,
                    'invoiceNumber' => $first->invoice_number,
                    'centralStore'  => $first->issuefrom,
                    'toStore'       => $first->storename,
                    'wardLabel'     => null,
                    'issuedBy'      => $first->authorised,
                ]);
            }

            $listrequest = ItemIssue::with(['itemcode', 'itemname.unitname', 'issuefrom', 'staffname', 'authorised'])
                ->where('issue_to', $activeStoreId)
                ->where('requisition_no', $decodeID)
                ->awaitingReceipt()
                ->orderBy('batch_number')
                ->orderBy('id')
                ->get();

            if ($listrequest->isEmpty()) {
                return redirect()->route('PickList')
                    ->with('message_error', 'Pick list not found or stock has already been received.');
            }

            $first = $listrequest->first();

            return view('stock.viewPickUpSatellite', [
                'listrequest'   => $listrequest,
                'activeStore'   => $activeStore,
                'pickType'      => 'central_transfer',
                'requisitionNo' => $decodeID,
                'issueNo'       => null,
                'invoiceNumber' => $first->invoice_number,
                'centralStore'  => $first->issuefrom,
                'wardLabel'     => null,
                'issuedBy'      => $first->authorised,
            ]);
        }

        $listdept = $this->pickListStoreIds();

        $centralLines = ItemIssue::with([
            'itemcode',
            'itemname.unitname',
            'issuefrom',
            'storename',
            'staffname',
            'authorised',
        ]);
        $this->scopePickListStores($centralLines, $listdept)
            ->where('requisition_no', $decodeID)
            ->where('status', 'issued')
            ->orderBy('batch_number')
            ->orderBy('id');
        $centralLines = $centralLines->get();

        $satelliteLines = SatelliteItemIssue::with([
            'itemcode',
            'itemname.unitname',
            'unitname',
            'issuefrom',
            'storename',
            'staffname',
            'authorised',
        ]);
        $this->scopePickListStores($satelliteLines, $listdept)
            ->where('requisition_no', $decodeID)
            ->awaitingReceipt()
            ->orderBy('batch_number')
            ->orderBy('id');
        $satelliteLines = $satelliteLines->get();

        $listrequest = $centralLines->concat($satelliteLines)->values();

        if ($listrequest->isEmpty()) {
            return redirect()->route('PickList')->with('message_error', 'Pick list not found.');
        }

        $first = $listrequest->first();
        $fromNames = $listrequest->map(fn ($line) => $line->issuefrom?->name)->filter()->unique()->values();
        $toNames = $listrequest->map(fn ($line) => $line->storename?->name)->filter()->unique()->values();

        return view('requisition.viewPickUp', [
            'listrequest'   => $listrequest,
            'activeStore'   => $this->resolveActiveStore(),
            'requisitionNo' => $decodeID,
            'invoiceNumber' => $listrequest->first(fn ($line) => $line->invoice_number)?->invoice_number ?? $first->invoice_number,
            'fromStore'     => $first->issuefrom,
            'toStore'       => $first->storename,
            'fromStoreLabel'=> $fromNames->isNotEmpty() ? $fromNames->join(', ') : ($first->issuefrom?->name ?? 'store'),
            'toStoreLabel'  => $toNames->isNotEmpty() ? $toNames->join(', ') : ($first->storename?->name ?? 'store'),
            'issuedBy'      => $first->authorised,
            'createdBy'     => $first->staffname,
            'issuedAt'      => $listrequest->sortByDesc('updated_at')->first()?->updated_at,
            'totalQty'      => (int) $listrequest->sum('qty'),
            'uniqueItems'   => $listrequest->pluck('item_id')->unique()->count(),
        ]);
    }

    public function printPickList($invoice)
    {
        try {
            $decodeID = Crypt::decrypt($invoice);
        } catch (\Exception $e) {
            return redirect()->back()->with('message_error', 'Invalid invoice reference.');
        }

        $activeStoreId = $this->resolveActiveStoreId();

        if (!$activeStoreId) {
            $scoped = $this->storeContext->getScopedStoreIds();
            $activeStoreId = $scoped[0] ?? null;
        }

        if (!$activeStoreId) {
            return redirect()->back()->with('message_error', 'Please select your active store.');
        }

        $storeIds = $this->pickListStoreIds();

        if (empty($storeIds) && $activeStoreId) {
            $storeIds = [(int) $activeStoreId];
        }

        if (empty($storeIds)) {
            return redirect()->back()->with('message_error', 'Please select your active store.');
        }

        $issueRelations = ['staffname', 'authorised', 'itemcode', 'itemname.unitname', 'itemRequest.staffname'];
        $satelliteIssueRelations = array_merge($issueRelations, ['unitname']);

        $issues = $this->scopePickListStores(
            ItemIssue::with($issueRelations)->where('invoice_number', $decodeID),
            $storeIds
        )->first();

        $listissues = collect();

        if ($issues) {
            $listissues = $this->scopePickListStores(
                ItemIssue::with($issueRelations)->where('invoice_number', $decodeID),
                $storeIds
            )->get();
        } else {
            $issues = $this->scopePickListStores(
                SatelliteItemIssue::with($satelliteIssueRelations)->where('invoice_number', $decodeID),
                $storeIds
            )->first();

            if (!$issues) {
                return redirect()->back()->with('message_error', 'Invoice not found.');
            }

            $listissues = $this->scopePickListStores(
                SatelliteItemIssue::with($satelliteIssueRelations)->where('invoice_number', $decodeID),
                $storeIds
            )->get();
        }

        $store   = Store::find($issues->store_id);
        $issueto = Store::find($issues->issue_to);
        $requestedBy = optional(optional($issues->itemRequest)->staffname)->name;

        if (! $requestedBy && $issues->requisition_no) {
            $requestLine = ItemRequest::with('staffname')
                ->where('requisition_no', $issues->requisition_no)
                ->first();
            $requestedBy = optional(optional($requestLine)->staffname)->name;
        }

        return view('requisition.print', compact('issues', 'decodeID', 'store', 'issueto', 'listissues', 'requestedBy'));
    }

    public function getReturnView()
    {
        $scopedStoreIds = $this->storeContext->getScopedStoreIds();

        $liststock = ApproveStock::with(['itemcode', 'itemname', 'supname', 'storename'])
            ->whereIn('store_id', $scopedStoreIds)
            ->where('status', 'approved')
            ->where('qty', '>', 0)
            ->orderByDesc('id')
            ->get();

        return view('requisition.Return', [
            'liststock'    => $liststock,
            'activeStore'  => $this->storeContext->getActiveStore(),
            'totalBatches' => $liststock->count(),
            'uniqueItems'  => $liststock->pluck('item_id')->unique()->count(),
            'totalQty'     => (int) $liststock->sum('qty'),
        ]);
    }

    public function getreturnItemID($id)
    {
        $data = ApproveStock::with(['itemcode', 'itemname'])->findOrFail($id);

        return response()->json([
            'id'            => $data->id,
            'item_id'       => $data->item_id,
            'item_name'     => $data->itemname->name ?? 'Item',
            'item_code'     => $data->itemcode->item_code ?? '—',
            'batch_number'  => $data->batch_number,
            'qty'           => $data->qty,
            'expiry_date'   => $data->expiry_date,
        ]);
    }

    public function addReturn(Request $request)
    {
        $request->validate([
            'quantity'      => 'required',
            'comment'       => 'required',
            'return_status' => 'required',
        ]);

        $insertCat = new ReturnItem();
        $insertCat->item_id = trim($request->item_id);
        $insertCat->batch_number = trim($request->batch_number);
        $insertCat->quantity = trim($request->quantity);
        $insertCat->manager_comment = trim($request->comment);
        $insertCat->return_status = trim($request->return_status);
        $insertCat->returned_by = Auth::id();
        $status = $insertCat->save();

        ApproveStock::where('batch_number', $request->batch_number)
            ->update(['status' => 'return initiated']);

        if ($status) {
            $stock = ApproveStock::where('batch_number', $request->batch_number)->first();
            $item = Item::find($insertCat->item_id);
            $itemName = $item?->name ?? 'Item';

            $this->notifications->notifyUsersForAction(
                NotificationService::TYPE_RETURN_PENDING_APPROVAL,
                'Return Awaiting Approval',
                "Return of {$itemName} (batch {$request->batch_number}) requires approval.",
                'ReturnApproval',
                (string) $insertCat->id,
                $stock ? (int) $stock->store_id : null,
                [Auth::id()]
            );
        }

        return $status
            ? back()->with('message_success', 'Process initiated successfully, Please wait for approval')
            : back()->with('message_error', 'Something went wrong, please try again.');
    }

    public function getReturnApprovalView()
    {
        $scopedStoreIds = $this->storeContext->getScopedStoreIds();

        $listItem = ReturnItem::with([
                'itemcode',
                'itemname',
                'staffname',
                'stockdetails.supname',
                'stockdetails.storename',
            ])
            ->where('status', 'return initiated')
            ->whereHas('stockdetails', function ($query) use ($scopedStoreIds) {
                $query->whereIn('store_id', $scopedStoreIds);
            })
            ->orderByDesc('id')
            ->get();

        return view('requisition.ReturnApproval', [
            'listItem'     => $listItem,
            'activeStore'  => $this->storeContext->getActiveStore(),
            'pendingCount' => $listItem->count(),
            'totalQty'     => (int) $listItem->sum('quantity'),
            'uniqueItems'  => $listItem->pluck('item_id')->unique()->count(),
        ]);
    }

    public function getreturnItemApprovalID($id)
    {
        $data = ReturnItem::with(['itemcode', 'itemname', 'staffname', 'stockdetails'])
            ->findOrFail($id);

        return response()->json([
            'id'              => $data->id,
            'quantity'        => $data->quantity,
            'batch_number'    => $data->batch_number,
            'return_status'   => $data->return_status,
            'manager_comment' => $data->manager_comment,
            'item_name'       => $data->itemname->name ?? $data->itemcode->name ?? 'Item',
            'item_code'       => $data->itemcode->item_code ?? '—',
            'returned_by'     => $data->staffname->name ?? '—',
            'expiry_date'     => $data->stockdetails->expiry_date ?? null,
            'available_qty'   => $data->stockdetails->qty ?? null,
            'store_name'      => $data->stockdetails->storename->name ?? null,
        ]);
    }

    public function addReturnApproval(Request $request)
    {
        $request->validate([
            'comment'       => 'required|string',
            'status'        => 'required|string',
            'item_id'       => 'required|exists:return_items,id',
            'batch_number'  => 'required|exists:approve_stocks,batch_number',
            'qty'           => 'required|numeric|min:0.01',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $insertCat = ReturnItem::findOrFail($request->item_id);
                $insertCat->status = 'approved';
                $insertCat->hod_comment = trim($request->comment);
                $insertCat->approved_by_hod = Auth::id();
                $insertCat->save();

                $data = ApproveStock::where('batch_number', $request->batch_number)->firstOrFail();

                if ($request->qty < $data->qty) {
                    $data->update([
                        'qty'    => $data->qty - $request->qty,
                        'status' => 'returned',
                    ]);
                } else {
                    $data->update(['status' => 'returned']);
                }
            });
        } catch (\Exception $e) {
            return back()->with('message_error', 'Something went wrong, please try again.');
        }

        $this->notifications->resolveIfComplete(
            NotificationService::TYPE_RETURN_PENDING_APPROVAL,
            (string) $request->item_id
        );

        return back()->with('message_success', 'Process approved successfully');
    }

    public function getIssuedItems($requisition_no)
    {
        $listdept = $this->storeContext->getScopedStoreIds();
        $decodeID = Crypt::decrypt($requisition_no);

        $centralIssues = ItemIssue::with(['staffname', 'storename', 'itemcode', 'itemname.unitname', 'issuefrom'])
            ->whereIn('store_id', $listdept)
            ->where('requisition_no', $decodeID)
            ->submittedForHodApproval()
            ->orderBy('batch_number')
            ->orderBy('id')
            ->get();

        $satelliteIssues = SatelliteItemIssue::with(['staffname', 'storename', 'itemcode', 'itemname.unitname', 'unitname', 'issuefrom', 'satelliteStockReceipt'])
            ->whereIn('store_id', $listdept)
            ->where('requisition_no', $decodeID)
            ->submittedForHodApproval()
            ->orderBy('batch_number')
            ->orderBy('id')
            ->get();

        $listissues = $centralIssues->concat($satelliteIssues);

        $batchNumbers = $centralIssues->pluck('batch_number')->unique();

        $itembalance = ApproveStock::whereIn('batch_number', $batchNumbers)
            ->get()
            ->keyBy('batch_number');

        $groupedIssues = $listissues->groupBy('item_id')->map(function ($lines) use ($itembalance) {
            $first = $lines->first();

            $batchLines = $lines->map(function ($issue) use ($itembalance) {
                $issueKey = $issue instanceof SatelliteItemIssue ? 's'.$issue->id : (string) $issue->id;
                $balance = 0;

                if ($issue instanceof SatelliteItemIssue) {
                    $balance = (int) ($issue->satelliteStockReceipt->qty ?? 0);
                } else {
                    $balance = (int) ($itembalance[$issue->batch_number]->qty ?? 0);
                }

                return (object) [
                    'issue_id'     => $issueKey,
                    'batch_number' => $issue->batch_number,
                    'prepared_qty' => (int) $issue->qty,
                    'balance'      => $balance,
                    'amount'       => (float) ($issue->amount ?? 0),
                ];
            })->values();

            $requestedQty = $lines->first(fn ($row) => $row->qty_requested !== null)?->qty_requested;

            return (object) [
                'item_id'       => $first->item_id,
                'itemcode'      => $first->itemcode,
                'itemname'      => $first->itemname,
                'requested_qty' => $requestedQty !== null ? (int) $requestedQty : (int) $batchLines->sum('prepared_qty'),
                'prepared_qty'  => (int) $batchLines->sum('prepared_qty'),
                'total_balance' => (int) $batchLines->sum('balance'),
                'lines'         => $batchLines,
            ];
        })->values();

        return view('requisition.viewIssues', [
            'listissues'    => $listissues,
            'groupedIssues' => $groupedIssues,
            'itembalance'   => $itembalance,
            'requisitionNo' => $decodeID,
        ]);
    }
}
