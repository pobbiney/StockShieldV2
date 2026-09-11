<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\ApproveStock;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemIssue;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Imports\ItemsImport;
use App\Models\ItemRequest;
use App\Models\SatelliteStockEntry;
use App\Models\SatelliteStockReceipt;
use App\Models\ReturnItem;
use App\Services\StoreContext;
use App\Services\StockApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Exceptions\NoTypeDetectedException;
use Maatwebsite\Excel\Facades\Excel;
 

 
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;


class StockController extends Controller
{
    public function __construct(
        protected StoreContext $storeContext,
        protected StockApprovalService $stockApproval,
        protected NotificationService $notifications,
    ) {
    }

    protected function resolveStoreIdForStockEntry(Request $request, ?Stock $existingStock = null): int
    {
        if ($this->storeContext->hasGlobalStoreAccess()) {
            $storeId = (int) ($request->store ?? $request->store_id);

            if (!Store::where('id', $storeId)->exists()) {
                abort(422, 'Invalid store selected.');
            }

            return $storeId;
        }

        $activeStoreId = $this->storeContext->getActiveStoreId();

        if (!$activeStoreId) {
            abort(403, 'No active store selected.');
        }

        if ($existingStock && (int) $existingStock->store_id !== $activeStoreId) {
            abort(403, 'Unauthorized access to this stock entry.');
        }

        return $activeStoreId;
    }

    protected function assertCanApproveStock(): void
    {
        if (!$this->stockApproval->canApproveStock()) {
            abort(403, 'You are not authorized to approve stock entries.');
        }
    }

    protected function assertPendingStockInScope(Stock $stock): void
    {
        if ($stock->status !== 'pending') {
            abort(403, 'Only pending stock entries can be modified.');
        }

        if (!in_array((int) $stock->store_id, $this->storeContext->getScopedStoreIds(), true)) {
            abort(403, 'Unauthorized access to this stock entry.');
        }
    }

    protected function itemBelongsToScopedStores(Item $item): bool
    {
        return in_array((int) $item->store_id, $this->storeContext->getScopedStoreIds(), true);
    }

    protected function resolveStoreIdForItem(Request $request, ?Item $existingItem = null): int
    {
        if ($this->storeContext->hasGlobalStoreAccess()) {
            return (int) $request->store_id;
        }

        $activeStoreId = $this->storeContext->getActiveStoreId();

        if (!$activeStoreId) {
            abort(403, 'No active store selected.');
        }

        if ($existingItem && (int) $existingItem->store_id !== $activeStoreId) {
            abort(403, 'Unauthorized access to this item.');
        }

        return $activeStoreId;
    }

    public function getItemCatView()
    {
        $list = ItemCategory::orderByDesc('id')->get();
        $totalCategories = ItemCategory::count();
        $activeCount = ItemCategory::where('status', 'Active')->count();
        $inactiveCount = ItemCategory::where('status', 'Inactive')->count();
        $categoriesWithItems = Item::whereNotNull('cat_id')
            ->pluck('cat_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->all();

        return view('stock.ItemCategory', [
            'list' => $list,
            'totalCategories' => $totalCategories,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
            'categoriesWithItems' => $categoriesWithItems,
        ]);
    }

     public function getitemCatID($id)
    {
         $data = ItemCategory::findOrFail($id);
          return response()->json($data);
    }

    public function addItemCategory(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $category = new ItemCategory();
        $category->name = trim($request->name);
        $category->status = $request->status;

        $status = $category->save();

        return $status
            ? redirect()->route('ItemCategory')->with('message_success', 'Item category added successfully')
            : redirect()->route('ItemCategory')->with('message_error', 'Something went wrong, please try again.')->withInput();
    }

     public function updateItemCategory(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $category = ItemCategory::find($request->cat_id);

        if (!$category) {
            return redirect()->route('ItemCategory')->with('message_error', 'Item category not found.');
        }

        $category->name = trim($request->name);
        $category->status = $request->status;

        $status = $category->save();

        return $status
            ? redirect()->route('ItemCategory')->with('message_success', 'Item category updated successfully')
            : redirect()->route('ItemCategory')->with('message_error', 'Something went wrong, please try again.')->withInput();
    }

    public function deleteItemCategory($id)
    {
        $category = ItemCategory::find($id);

        if (!$category) {
            return redirect()->route('ItemCategory')->with('message_error', 'Item category not found.');
        }

        if (Item::where('cat_id', $id)->exists()) {
            return redirect()->route('ItemCategory')->with(
                'message_error',
                'Cannot delete this category because it is linked to one or more items.'
            );
        }

        $category->delete();

        return redirect()->route('ItemCategory')->with('message_success', 'Item category deleted successfully.');
    }

    public function getunitOfmeasureView()
    {
        $list = UnitOfMeasure::orderByDesc('id')->get();
        $totalUnits = UnitOfMeasure::count();
        $activeCount = UnitOfMeasure::where('status', 'Active')->count();
        $inactiveCount = UnitOfMeasure::where('status', 'Inactive')->count();
        $unitsWithItems = Item::whereNotNull('unit_id')
            ->pluck('unit_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->all();

        return view('stock.unitOfmeasure', [
            'list' => $list,
            'totalUnits' => $totalUnits,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
            'unitsWithItems' => $unitsWithItems,
        ]);
    }

    public function addUnitOfMeasure(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $unit = new UnitOfMeasure();
        $unit->name = trim($request->name);
        $unit->status = $request->status;

        $status = $unit->save();

        return $status
            ? redirect()->route('unitOfmeasure')->with('message_success', 'Unit of issue added successfully')
            : redirect()->route('unitOfmeasure')->with('message_error', 'Something went wrong, please try again.')->withInput();
    }

     public function getUnitofMeasureID($id)
    {
         $data = UnitOfMeasure::findOrFail($id);
          return response()->json($data);
    }

     public function updateUnitOfMeasure(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $unit = UnitOfMeasure::find($request->cat_id);

        if (!$unit) {
            return redirect()->route('unitOfmeasure')->with('message_error', 'Unit of issue not found.');
        }

        $unit->name = trim($request->name);
        $unit->status = $request->status;

        $status = $unit->save();

        return $status
            ? redirect()->route('unitOfmeasure')->with('message_success', 'Unit of issue updated successfully')
            : redirect()->route('unitOfmeasure')->with('message_error', 'Something went wrong, please try again.')->withInput();
    }

    public function deleteUnitOfMeasure($id)
    {
        $unit = UnitOfMeasure::find($id);

        if (!$unit) {
            return redirect()->route('unitOfmeasure')->with('message_error', 'Unit of issue not found.');
        }

        if (Item::where('unit_id', $id)->exists()) {
            return redirect()->route('unitOfmeasure')->with(
                'message_error',
                'Cannot delete this unit because it is linked to one or more items.'
            );
        }

        $unit->delete();

        return redirect()->route('unitOfmeasure')->with('message_success', 'Unit of issue deleted successfully.');
    }

    public function getItemView()
    {
        $listcat = ItemCategory::orderBy('name')->get();
        $listunit = UnitOfMeasure::orderBy('name')->get();
        $scopedStoreIds = $this->storeContext->getScopedStoreIds();
        $isGlobalAccess = $this->storeContext->hasGlobalStoreAccess();
        $activeStore = $this->storeContext->getActiveStore();
        $getstoreid = Store::whereIn('id', $scopedStoreIds)->orderBy('name')->get();

        $baseQuery = Item::whereIn('store_id', $scopedStoreIds);
        $list = (clone $baseQuery)
            ->with(['categoryname', 'unitname', 'storename'])
            ->orderByDesc('id')
            ->get();

        $totalItems = (clone $baseQuery)->count();
        $categoriesUsed = (clone $baseQuery)->whereNotNull('cat_id')->distinct()->count('cat_id');

        $categoryStats = (clone $baseQuery)
            ->select('cat_id', DB::raw('COUNT(*) as item_count'))
            ->whereNotNull('cat_id')
            ->groupBy('cat_id')
            ->orderByDesc('item_count')
            ->get()
            ->map(function ($row) use ($listcat, $totalItems) {
                $cat = $listcat->firstWhere('id', $row->cat_id);
                $count = (int) $row->item_count;

                return [
                    'id' => (int) $row->cat_id,
                    'name' => $cat->name ?? 'Uncategorized',
                    'count' => $count,
                    'pct' => $totalItems > 0 ? round(($count / $totalItems) * 100) : 0,
                ];
            })
            ->values();

        $topCategory = $categoryStats->first();

        $itemsWithUsage = Stock::whereNotNull('item_id')
            ->pluck('item_id')
            ->merge(ApproveStock::whereNotNull('item_id')->pluck('item_id'))
            ->merge(ItemIssue::whereNotNull('item_id')->pluck('item_id'))
            ->merge(ItemRequest::whereNotNull('item_id')->pluck('item_id'));

        if (Schema::hasTable('return_items')) {
            $itemsWithUsage = $itemsWithUsage->merge(
                ReturnItem::whereNotNull('item_id')->pluck('item_id')
            );
        }

        $itemsWithUsage = $itemsWithUsage
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->all();

        $lastItem = Item::latest('id')->first();
        $number = $lastItem ? $lastItem->id + 1 : 1;
        $itemCodePreview = 'ITM-' . str_pad($number, 5, '0', STR_PAD_LEFT);

        return view('stock.Item', [
            'list' => $list,
            'listcat' => $listcat,
            'listunit' => $listunit,
            'getstoreid' => $getstoreid,
            'totalItems' => $totalItems,
            'categoriesUsed' => $categoriesUsed,
            'categoryStats' => $categoryStats,
            'topCategory' => $topCategory,
            'itemsWithUsage' => $itemsWithUsage,
            'itemCodePreview' => $itemCodePreview,
            'isGlobalAccess' => $isGlobalAccess,
            'activeStore' => $activeStore,
        ]);
    }

    public function addItem(Request $request)
    {
         $request->validate([
            'name' =>'required',
            'unit_of_measure_id' =>'required',
            'category_id' => 'required',
            'status'=>'required',
            'store_id' => $this->storeContext->hasGlobalStoreAccess() ? 'required' : 'nullable',
            're_order_level' => 'required'
        ]);

        $storeId = $this->resolveStoreIdForItem($request);

        if (!$this->storeContext->canAccessStore(Auth::user(), $storeId)) {
            return redirect()->route('Item')->with('message_error', 'You are not authorized to add items to this store.')->withInput();
        }

        if (Item::where('name', $request->name)->where('store_id', $storeId)->exists()) {
            return redirect()->route('Item')->with('message_error', 'Item already exist in this store')->withInput();
        }

        $lastItem = Item::latest('id')->first();
        $number = $lastItem ? $lastItem->id + 1 : 1;
        $itemCode = 'ITM-' . str_pad($number, 5, '0', STR_PAD_LEFT);

        $item = new Item();
        $item->item_code = $itemCode;
        $item->name = trim($request->name);
        $item->cat_id = $request->category_id;
        $item->unit_id = $request->unit_of_measure_id;
        $item->store_id = $storeId;
        $item->reorder_level = $request->re_order_level;
        $item->status = $request->status;
        $item->created_by = Auth::user()->id;

        $status = $item->save();

        return $status
            ? redirect()->route('Item')->with('message_success', 'Item added successfully')
            : redirect()->route('Item')->with('message_error', 'Something went wrong, please try again.')->withInput();
    }

      public function getItemID($id)
    {
         $data = Item::findOrFail($id);

         if (!$this->itemBelongsToScopedStores($data)) {
             abort(403, 'Unauthorized access to this item.');
         }

          return response()->json($data);
    }

    public function updateItem(Request $request)
    {
         $request->validate([
            'name' =>'required',
            'unit_of_measure_id' =>'required',
            'category_id' => 'required',
            'status'=>'required',
            'store_id' => $this->storeContext->hasGlobalStoreAccess() ? 'required' : 'nullable',
            're_order_level' => 'required'
        ]);

       
        $item = Item::find($request->item_id);

        if (!$item) {
            return redirect()->route('Item')->with('message_error', 'Item not found.');
        }

        if (!$this->itemBelongsToScopedStores($item)) {
            abort(403, 'Unauthorized access to this item.');
        }

        $storeId = $this->resolveStoreIdForItem($request, $item);

        if (Item::where('name', $request->name)
            ->where('store_id', $storeId)
            ->where('id', '!=', $item->id)
            ->exists()) {
            return redirect()->route('Item')->with('message_error', 'Item already exist in this store')->withInput();
        }

        $item->name = trim($request->name);
        $item->cat_id = $request->category_id;
        $item->unit_id = $request->unit_of_measure_id;
        $item->store_id = $storeId;
        $item->status = $request->status;
        $item->reorder_level = $request->re_order_level;
        $item->updated_by = Auth::user()->id;

        $status = $item->save();

        return $status
            ? redirect()->route('Item')->with('message_success', 'Item updated successfully')
            : redirect()->route('Item')->with('message_error', 'Something went wrong, please try again.')->withInput();
    }

    public function deleteItem($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return redirect()->route('Item')->with('message_error', 'Item not found.');
        }

        if (!$this->itemBelongsToScopedStores($item)) {
            abort(403, 'Unauthorized access to this item.');
        }

        $hasUsage = Stock::where('item_id', $id)->exists()
            || ApproveStock::where('item_id', $id)->exists()
            || ItemIssue::where('item_id', $id)->exists()
            || ItemRequest::where('item_id', $id)->exists()
            || (Schema::hasTable('return_items') && ReturnItem::where('item_id', $id)->exists());

        if ($hasUsage) {
            return redirect()->route('Item')->with(
                'message_error',
                'Cannot delete this item because it is linked to stock or transaction records.'
            );
        }

        $item->delete();

        return redirect()->route('Item')->with('message_success', 'Item deleted successfully.');
    }
    

    public function getreOrderView()
    {
        $list = Item::where('status',"Active")->get();
        return view('stock.reOrder',['list'=>$list]);

    }

    public function addreorderlevel(Request $request)
    {
         $request->validate([
            'quantity' =>'required',
           
        ]);

       
            $insertCat = Item::find($request->item_id);
            $insertCat->reorder_level = trim($request->quantity);
            $insertCat->updated_by = Auth::User()->id;
             
             
            $status = $insertCat->save();

            return $status ? back()->with('message_success','ReOrder Level Set successfully') : back()->with('message_error','Something went wrong, please try again.');
    }
    

    protected function isSatelliteStockContext(): bool
    {
        return $this->storeContext->getActiveStore()?->store_group === 'satellite';
    }

    protected function assertPendingSatelliteStockEntryInScope(SatelliteStockEntry $entry): void
    {
        if ($entry->status !== 'pending') {
            abort(403, 'Only pending stock entries can be modified.');
        }

        if (!in_array((int) $entry->store_id, $this->storeContext->getScopedStoreIds(), true)) {
            abort(403, 'Unauthorized access to this stock entry.');
        }
    }

    protected function pendingStockEntryUsesSatellite(?string $source): bool
    {
        if ($source === 'satellite') {
            return true;
        }

        if ($source === 'central') {
            return false;
        }

        return $this->isSatelliteStockContext();
    }

    /**
     * @return \Illuminate\Support\Collection<int, Stock|SatelliteStockEntry>
     */
    protected function collectPendingStockEntriesForScope(array $scopedStoreIds)
    {
        $stores = Store::whereIn('id', $scopedStoreIds)->get();

        $centralStoreIds = $stores->where('store_group', '!=', 'satellite')->pluck('id');
        $satelliteStoreIds = $stores->where('store_group', 'satellite')->pluck('id');

        $centralPending = Stock::with(['itemcode', 'itemname', 'supname', 'storename'])
            ->where('status', 'pending')
            ->when(
                $centralStoreIds->isNotEmpty(),
                fn ($query) => $query->whereIn('store_id', $centralStoreIds),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->get()
            ->each(fn (Stock $row) => $row->setAttribute('entry_source', 'central'));

        $satellitePending = SatelliteStockEntry::with(['itemcode', 'itemname', 'supname', 'storename'])
            ->where('status', 'pending')
            ->when(
                $satelliteStoreIds->isNotEmpty(),
                fn ($query) => $query->whereIn('store_id', $satelliteStoreIds),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->get()
            ->each(fn (SatelliteStockEntry $row) => $row->setAttribute('entry_source', 'satellite'));

        return $centralPending
            ->concat($satellitePending)
            ->sortByDesc(fn ($row) => $row->created_at ?? $row->id)
            ->values();
    }

    protected function storeUsesSatelliteStockEntry(?Store $store = null): bool
    {
        $store = $store ?? $this->storeContext->getActiveStore();

        return $store?->store_group === 'satellite';
    }

    /**
     * Items available in the stock-entry modal (must match the target store).
     */
    protected function stockEntryItemsQuery(array $scopedStoreIds, bool $isSatelliteStore)
    {
        if ($isSatelliteStore) {
            return Item::with('storename')
                ->orderBy('name');
        }

        $storeIdsForItems = $scopedStoreIds;

        if (!$this->storeContext->hasGlobalStoreAccess()) {
            $activeStoreId = $this->storeContext->getActiveStoreId();
            if ($activeStoreId) {
                $storeIdsForItems = [(int) $activeStoreId];
            }
        }

        return Item::with('storename')
            ->whereIn('store_id', $storeIdsForItems)
            ->orderBy('name');
    }

    protected function generateBarcodeAssets(?string $barCode = null): array
    {
        $barcode = $barCode ?: 'SS' . rand(10000000, 99999999);
        $barcodeImage = DNS1D::getBarcodePNG($barcode, 'C128');
        $folderPath = public_path('barcodes');

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $imageName = $barcode . '.png';
        file_put_contents($folderPath . '/' . $imageName, base64_decode($barcodeImage));

        return [
            'barcode' => $barcode,
            'barcode_path' => 'barcodes/' . $imageName,
        ];
    }

    protected function buildStockEntryView()
    {
        $scopedStoreIds = $this->storeContext->getScopedStoreIds();

        if (empty($scopedStoreIds)) {
            return redirect()->route('choose-store')
                ->with('message_error', 'Select a store before recording stock entries.');
        }

        $isSatelliteStore = $this->isSatelliteStockContext();

        $listsup = Supplier::where('status', 'Active')->orderBy('company')->get();
        $getItemid = $this->stockEntryItemsQuery($scopedStoreIds, $isSatelliteStore)->get();
        $getstoreId = Store::whereIn('id', $scopedStoreIds)->orderBy('name')->get();

        if ($isSatelliteStore) {
            $liststock = SatelliteStockEntry::with(['itemcode', 'itemname', 'supname', 'storename'])
                ->whereIn('store_id', $scopedStoreIds)
                ->where('status', 'pending')
                ->orderByDesc('id')
                ->get();
        } else {
            $liststock = Stock::with(['itemcode', 'itemname', 'supname', 'storename'])
                ->whereIn('store_id', $scopedStoreIds)
                ->where('status', 'pending')
                ->orderByDesc('id')
                ->get();
        }

        $pendingCount = $liststock->count();
        $totalQty = (int) $liststock->sum('qty');
        $totalValue = $liststock->sum(fn ($s) => (float) ($s->qty ?? 0) * (float) ($s->amount ?? 0));
        $uniqueItems = $liststock->pluck('item_id')->unique()->count();
        $uniquePct = $pendingCount > 0 ? min(100, (int) round(($uniqueItems / $pendingCount) * 100)) : 0;

        $storeBreakdown = $liststock->groupBy('store_id')->map(function ($rows, $storeId) {
            return [
                'name' => optional($rows->first()->storename)->name ?? 'Store #' . $storeId,
                'count' => $rows->count(),
            ];
        });

        return view('stock.stockEntry', [
            'getItemid' => $getItemid,
            'listsup' => $listsup,
            'getstoreId' => $getstoreId,
            'liststock' => $liststock,
            'pendingCount' => $pendingCount,
            'totalQty' => $totalQty,
            'totalValue' => $totalValue,
            'uniqueItems' => $uniqueItems,
            'uniquePct' => $uniquePct,
            'storeBreakdown' => $storeBreakdown,
            'requiresApproval' => $this->stockApproval->requiresApproval(),
            'canApproveStock' => $isSatelliteStore ? false : $this->stockApproval->canApproveStock(),
            'activeStoreId' => $this->storeContext->getActiveStoreId(),
            'isSatelliteStore' => $isSatelliteStore,
        ]);
    }

    public function getstockEntryView()
    {
        return $this->buildStockEntryView();
    }



   public function addStock(Request $request)
{
    $rules = [
        'item' => 'required|exists:items,id',
        'batch_number' => 'nullable|string|max:255',
        'supplier' => 'required|exists:suppliers,id',
        'waybill' => 'required|string|max:255',
        'award_letter' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:1',
        'bar_code' => 'nullable|string|max:255',
        'comment' => 'nullable|string',
        'expiry_date' => ['required_unless:store,2', 'nullable', 'date'],
    ];

    if ($this->storeContext->hasGlobalStoreAccess()) {
        $rules['store'] = 'required|exists:stores,id';
    }

    $request->validate($rules);

    $storeId = $this->resolveStoreIdForStockEntry($request);

    if ($this->isSatelliteStockContext()) {
        $batchNumber = $request->batch_number ?: 'BN' . rand(10000000, 99999999);
        $barcodeAssets = $this->generateBarcodeAssets($request->bar_code);

        SatelliteStockEntry::create([
            'item_id'            => $request->item,
            'batch_number'       => $batchNumber,
            'manufacturing_date' => $request->manufacturing_date,
            'expiry_date'        => $request->expiry_date,
            'supplier_id'        => $request->supplier,
            'purchase_order'     => $request->purchase_order,
            'waybill'            => $request->waybill,
            'award_letter'       => $request->award_letter,
            'amount'             => $request->amount,
            'store_id'           => $storeId,
            'comment'            => $request->comment,
            'qty'                => $request->quantity,
            'barcode'            => $barcodeAssets['barcode'],
            'barcode_path'       => $barcodeAssets['barcode_path'],
            'created_by'         => Auth::id(),
            'status'             => 'pending',
        ]);

        $itemName = Item::find($request->item)?->name ?? 'Item';
        $this->notifications->notifyUsersForAction(
            NotificationService::TYPE_STOCK_PENDING_APPROVAL,
            'Stock Entry Pending Approval',
            "Satellite stock entry for {$itemName} (batch {$batchNumber}) requires approval.",
            'stockApproval',
            $batchNumber,
            (int) $storeId,
            [Auth::id()]
        );

        return redirect()->route('stockEntry')
            ->with('message_success', 'Stock entry submitted for approval. It will be recorded once approved via Stock Approval.');
    }

    $item = Item::findOrFail($request->item);
    if ((int) $item->store_id !== $storeId) {
        return back()->with('message_error', 'Selected item does not belong to this store.')->withInput();
    }

    $batchNumber = $request->batch_number ?: 'BN' . rand(10000000, 99999999);
    $barcodeAssets = $this->generateBarcodeAssets($request->bar_code);

    $insertCat = new Stock();
    $insertCat->item_id = $request->item;
    $insertCat->batch_number = $batchNumber;
    $insertCat->manufacturing_date = $request->manufacturing_date;
    $insertCat->expiry_date = $request->expiry_date;
    $insertCat->supplier_id = $request->supplier;
    $insertCat->purchase_order = $request->purchase_order;
    $insertCat->waybill = $request->waybill;
    $insertCat->award_letter = $request->award_letter;
    $insertCat->amount = $request->amount;
    $insertCat->store_id = $storeId;
    $insertCat->comment = $request->comment;
    $insertCat->qty = $request->quantity;
    $insertCat->barcode = $barcodeAssets['barcode'];
    $insertCat->barcode_path = $barcodeAssets['barcode_path'];
    $insertCat->created_by = Auth::id();
    $insertCat->status = 'pending';

    if (!$insertCat->save()) {
        return redirect()->route('stockEntry')->with('message_error', 'Something went wrong, please try again.')->withInput();
    }

    if ($this->stockApproval->canApproveStock()) {
        $this->stockApproval->approve($insertCat->fresh());

        return redirect()->route('stockEntry')->with('message_success', 'Stock entry approved and added to inventory.');
    }

    $itemName = $item->name ?? 'Item';
    $this->notifications->notifyUsersForAction(
        NotificationService::TYPE_STOCK_PENDING_APPROVAL,
        'Stock Entry Pending Approval',
        "Stock entry for {$itemName} (batch {$batchNumber}) requires approval.",
        'stockApproval',
        $batchNumber,
        (int) $storeId,
        [Auth::id()]
    );

    return redirect()->route('stockEntry')->with('message_success', 'Stock entry submitted for approval. It will be added to inventory once approved by an authorized user.');
}

  public function deleteStockItem(Request $request, string $id)
    {
        if ($this->pendingStockEntryUsesSatellite($request->query('source'))) {
            $entry = SatelliteStockEntry::findOrFail($id);
            $this->assertPendingSatelliteStockEntryInScope($entry);
            $entry->delete();
        } else {
            $stock = Stock::findOrFail($id);
            $this->assertPendingStockInScope($stock);
            $stock->delete();
        }

        return redirect()->back()->with('message_success', 'Pending stock entry deleted successfully.');
    }

    //get stock details
      public function getStockID(Request $request, $id)
    {
        if ($this->pendingStockEntryUsesSatellite($request->query('source'))) {
            $data = SatelliteStockEntry::findOrFail($id);
            $this->assertPendingSatelliteStockEntryInScope($data);
        } else {
            $data = Stock::findOrFail($id);
            if ($data->status === 'pending') {
                $this->assertPendingStockInScope($data);
            }
        }

        return response()->json($data);
    }

    public function updateStock(Request $request)
    {
       $request->validate([
        'stock_id' => 'required|integer',
        'item' => 'required|exists:items,id',
        'batch_number' => 'nullable|string|max:255',
        'expiry_date' => 'nullable|date',
        'supplier' => 'required|exists:suppliers,id',
        'waybill' => 'required|string|max:255',
        'award_letter' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'store' => 'nullable|exists:stores,id',
        'quantity' => 'required|integer|min:1',
        'bar_code' => 'nullable|string|max:255',
        'comment' => 'nullable|string',
    ]);

    if ($this->isSatelliteStockContext()) {
        $entry = SatelliteStockEntry::findOrFail($request->stock_id);
        $this->assertPendingSatelliteStockEntryInScope($entry);

        $storeId = $this->resolveStoreIdForStockEntry($request);
        $batchNumber = $request->batch_number ?: 'BN' . rand(10000000, 99999999);
        $barcodeAssets = $this->generateBarcodeAssets($request->bar_code);

        $entry->item_id = $request->item;
        $entry->batch_number = $batchNumber;
        $entry->manufacturing_date = $request->manufacturing_date;
        $entry->expiry_date = $request->expiry_date;
        $entry->supplier_id = $request->supplier;
        $entry->purchase_order = $request->purchase_order;
        $entry->waybill = $request->waybill;
        $entry->award_letter = $request->award_letter;
        $entry->amount = $request->amount;
        $entry->store_id = $storeId;
        $entry->qty = $request->quantity;
        $entry->comment = $request->comment;
        $entry->barcode = $barcodeAssets['barcode'];
        $entry->barcode_path = $barcodeAssets['barcode_path'];
        $entry->updated_by = Auth::id();
        $entry->status = 'pending';

        $status = $entry->save();

        return $status
            ? redirect()->route('stockEntry')->with('message_success', 'Pending stock entry updated. It still requires approval before inventory is updated.')
            : redirect()->route('stockEntry')->with('message_error', 'Something went wrong, please try again.')->withInput();
    }

    $insertCat = Stock::findOrFail($request->stock_id);
    $this->assertPendingStockInScope($insertCat);

    $storeId = $this->resolveStoreIdForStockEntry($request, $insertCat);

    $batchNumber = $request->batch_number ?: 'BN' . rand(10000000, 99999999);
    $barcodeAssets = $this->generateBarcodeAssets($request->bar_code);

    $insertCat->item_id = $request->item;
    $insertCat->batch_number = $batchNumber;
    $insertCat->manufacturing_date = $request->manufacturing_date;
    $insertCat->expiry_date = $request->expiry_date;
    $insertCat->supplier_id = $request->supplier;
    $insertCat->purchase_order = $request->purchase_order;
    $insertCat->waybill = $request->waybill;
    $insertCat->award_letter = $request->award_letter;
    $insertCat->amount = $request->amount;
    $insertCat->store_id = $storeId;
    $insertCat->qty = $request->quantity;
    $insertCat->comment = $request->comment;
    $insertCat->barcode = $barcodeAssets['barcode'];
    $insertCat->barcode_path = $barcodeAssets['barcode_path'];
    $insertCat->updated_by = Auth::id();
    $insertCat->status = 'pending';

    $status = $insertCat->save();

    $message = $this->stockApproval->canApproveStock()
        ? 'Pending stock entry updated successfully.'
        : 'Pending stock entry updated. It still requires approval before inventory is updated.';

    return $status
        ? redirect()->route('stockEntry')->with('message_success', $message)
        : redirect()->route('stockEntry')->with('message_error', 'Something went wrong, please try again.')->withInput();
   }

   public function getstockApprovalView()
   {
    $this->assertCanApproveStock();

    $scopedStoreIds = $this->storeContext->getScopedStoreIds();

    $satelliteStoreIds = Store::whereIn('id', $scopedStoreIds)
        ->where('store_group', 'satellite')
        ->pluck('id');
    $centralStoreIds = Store::whereIn('id', $scopedStoreIds)
        ->where('store_group', '!=', 'satellite')
        ->pluck('id');

    $pendingStocks = Stock::where('status', 'pending')
        ->when($centralStoreIds->isNotEmpty(), fn ($q) => $q->whereIn('store_id', $centralStoreIds), fn ($q) => $q->whereRaw('1 = 0'))
        ->get();

    $pendingSatelliteEntries = SatelliteStockEntry::where('status', 'pending')
        ->when($satelliteStoreIds->isNotEmpty(), fn ($q) => $q->whereIn('store_id', $satelliteStoreIds), fn ($q) => $q->whereRaw('1 = 0'))
        ->get();

    $pendingStoreIds = $pendingStocks->pluck('store_id')
        ->merge($pendingSatelliteEntries->pluck('store_id'))
        ->unique()
        ->values();

    $liststock = Store::whereIn('id', $pendingStoreIds)
        ->orderBy('name')
        ->get()
        ->map(function (Store $store) {
            if ($store->store_group === 'satellite') {
                $rows = SatelliteStockEntry::where('store_id', $store->id)->where('status', 'pending')->get();
            } else {
                $rows = Stock::where('store_id', $store->id)->where('status', 'pending')->get();
            }

            $store->pending_count = $rows->count();
            $store->pending_qty = (int) $rows->sum('qty');
            $store->pending_value = $rows->sum(fn ($s) => (float) ($s->qty ?? 0) * (float) ($s->amount ?? 0));
            $store->pending_avg_price = $store->pending_qty > 0
                ? round($store->pending_value / $store->pending_qty, 2)
                : 0;

            return $store;
        });

    $pendingCount = $pendingStocks->count() + $pendingSatelliteEntries->count();
    $storeCount = $liststock->count();
    $totalQty = (int) $pendingStocks->sum('qty') + (int) $pendingSatelliteEntries->sum('qty');
    $totalValue = $pendingStocks->sum(fn ($s) => (float) ($s->qty ?? 0) * (float) ($s->amount ?? 0))
        + $pendingSatelliteEntries->sum(fn ($s) => (float) ($s->qty ?? 0) * (float) ($s->amount ?? 0));
    $topStore = $liststock->sortByDesc('pending_count')->first();
    $topStorePct = ($pendingCount > 0 && $topStore)
        ? round(($topStore->pending_count / $pendingCount) * 100)
        : 0;

    return view('stock.stockApproval', compact(
        'liststock',
        'pendingCount',
        'storeCount',
        'totalQty',
        'totalValue',
        'topStore',
        'topStorePct',
    ));
   }

    public function ApproveStock($id)
    {
    $this->assertCanApproveStock();

    if (request()->query('source') === 'satellite') {
        $entry = SatelliteStockEntry::where('id', $id)->where('status', 'pending')->firstOrFail();

        if (!in_array((int) $entry->store_id, $this->storeContext->getScopedStoreIds(), true)) {
            abort(403, 'You cannot approve stock for this store.');
        }

        if (!$this->stockApproval->approveSatelliteEntry($entry)) {
            return back()->with('message_error', 'Unable to approve this stock entry.');
        }

        return back()->with('message_success', 'Stock approved and recorded in satellite inventory.');
    }

    $stock = Stock::where('id', $id)->where('status', 'pending')->firstOrFail();

    if (!in_array((int) $stock->store_id, $this->storeContext->getScopedStoreIds(), true)) {
        abort(403, 'You cannot approve stock for this store.');
    }

    if (!$this->stockApproval->approve($stock)) {
        return back()->with('message_error', 'Unable to approve this stock entry.');
    }

        return back()->with('message_success', 'Stock approved and added to inventory.');
    }

    public function rejectStock(Request $request, $id)
    {
        $this->assertCanApproveStock();

        $request->validate([
            'reason' => 'required|string|min:3|max:2000',
        ]);

        if ($request->query('source') === 'satellite') {
            $entry = SatelliteStockEntry::where('id', $id)->where('status', 'pending')->firstOrFail();

            if (!in_array((int) $entry->store_id, $this->storeContext->getScopedStoreIds(), true)) {
                abort(403, 'You cannot reject stock for this store.');
            }

            if (!$this->stockApproval->rejectSatelliteEntry($entry, $request->reason)) {
                return back()->with('message_error', 'Unable to reject this stock entry.');
            }

            return back()->with('message_success', 'Stock entry rejected successfully.');
        }

        $stock = Stock::where('id', $id)->where('status', 'pending')->firstOrFail();

        if (!in_array((int) $stock->store_id, $this->storeContext->getScopedStoreIds(), true)) {
            abort(403, 'You cannot reject stock for this store.');
        }

        if (!$this->stockApproval->reject($stock, $request->reason)) {
            return back()->with('message_error', 'Unable to reject this stock entry.');
        }

        return back()->with('message_success', 'Stock entry rejected successfully.');
    }

    public function rejectAll(Request $request, $store_id)
    {
        $this->assertCanApproveStock();

        $request->validate([
            'reason' => 'required|string|min:3|max:2000',
        ]);

        $storeId = (int) $store_id;

        if (!in_array($storeId, $this->storeContext->getScopedStoreIds(), true)) {
            abort(403, 'You cannot reject stock for this store.');
        }

        $store = Store::findOrFail($storeId);

        if ($store->store_group === 'satellite') {
            $entries = SatelliteStockEntry::where('status', 'pending')->where('store_id', $storeId)->get();
            $rejected = $this->stockApproval->rejectManySatelliteEntries($entries, $request->reason);
        } else {
            $stocks = Stock::where('status', 'pending')->where('store_id', $storeId)->get();
            $rejected = $this->stockApproval->rejectMany($stocks, $request->reason);
        }

        if ($rejected === 0) {
            return back()->with('message_error', 'No pending stock entries were rejected.');
        }

        return redirect()->route('stockApproval')->with(
            'message_success',
            "{$rejected} pending stock " . ($rejected === 1 ? 'entry' : 'entries') . ' rejected successfully.'
        );
    }

      public function approveAll($store_id)
    {
    $this->assertCanApproveStock();

    $storeId = (int) $store_id;

    if (!in_array($storeId, $this->storeContext->getScopedStoreIds(), true)) {
        abort(403, 'You cannot approve stock for this store.');
    }

    $store = Store::findOrFail($storeId);

    if ($store->store_group === 'satellite') {
        $entries = SatelliteStockEntry::where('status', 'pending')->where('store_id', $storeId)->get();
        $approved = $this->stockApproval->approveManySatelliteEntries($entries);
    } else {
        $stocks = Stock::where('status', 'pending')->where('store_id', $storeId)->get();
        $approved = $this->stockApproval->approveMany($stocks);
    }

    if ($approved === 0) {
        return redirect()->route('stockApproval')->with('message_error', 'No pending stock entries were approved.');
    }

    return redirect()->route('stockApproval')->with('message_success', "{$approved} pending stock " . ($approved === 1 ? 'entry' : 'entries') . ' approved successfully.');
   }

   public function getpendingStockView()
    {
        $scopedStoreIds = $this->storeContext->getScopedStoreIds();

        if (empty($scopedStoreIds)) {
            return redirect()->route('choose-store')
                ->with('message_error', 'Select a store to view pending stock.');
        }

        $listsup = Supplier::where('status', 'Active')->orderBy('company')->get();
        $getItemid = Item::whereIn('store_id', $scopedStoreIds)
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();
        $getstoreId = Store::whereIn('id', $scopedStoreIds)->orderBy('name')->get();

        $liststock = $this->collectPendingStockEntriesForScope($scopedStoreIds);

        $pendingCount = $liststock->count();
        $totalQty = (int) $liststock->sum('qty');
        $totalValue = $liststock->sum(fn ($s) => (float) ($s->qty ?? 0) * (float) ($s->amount ?? 0));
        $uniqueItems = $liststock->pluck('item_id')->unique()->count();
        $uniquePct = $pendingCount > 0 ? min(100, (int) round(($uniqueItems / $pendingCount) * 100)) : 0;

        $storeBreakdown = $liststock->groupBy('store_id')->map(function ($rows, $storeId) {
            return [
                'name' => optional($rows->first()->storename)->name ?? 'Store #' . $storeId,
                'count' => $rows->count(),
            ];
        });

        return view('stock.pendingStock', [
            'getItemid' => $getItemid,
            'listsup' => $listsup,
            'getstoreId' => $getstoreId,
            'liststock' => $liststock,
            'pendingCount' => $pendingCount,
            'totalQty' => $totalQty,
            'totalValue' => $totalValue,
            'uniqueItems' => $uniqueItems,
            'uniquePct' => $uniquePct,
            'storeBreakdown' => $storeBreakdown,
            'requiresApproval' => $this->stockApproval->requiresApproval(),
        ]);
    }

    public function getapprovedStockView()
    {
        $scopedStoreIds = $this->storeContext->getScopedStoreIds();

        if (empty($scopedStoreIds)) {
            return redirect()->route('choose-store')
                ->with('message_error', 'Select a store to view approved stock.');
        }

        $isSatelliteStore = $this->isSatelliteStockContext();

        if ($isSatelliteStore) {
            $liststock = SatelliteStockReceipt::with(['itemcode', 'itemname', 'supname', 'storename', 'staffname'])
                ->whereIn('store_id', $scopedStoreIds)
                ->orderByDesc('received_at')
                ->get();
        } else {
            $liststock = ApproveStock::with(['itemcode', 'itemname', 'supname', 'storename', 'staffname'])
                ->whereIn('store_id', $scopedStoreIds)
                ->where('status', 'approved')
                ->orderByDesc('created_at')
                ->get();
        }

        $approvedCount = $liststock->count();
        $totalQty = (int) $liststock->sum('qty');
        $totalValue = $liststock->sum(fn ($s) => (float) ($s->qty ?? 0) * (float) ($s->amount ?? 0));
        $uniqueItems = $liststock->pluck('item_id')->unique()->count();
        $uniquePct = $approvedCount > 0 ? min(100, (int) round(($uniqueItems / $approvedCount) * 100)) : 0;

        $storeBreakdown = $liststock->groupBy('store_id')->map(function ($rows, $storeId) {
            return [
                'name' => optional($rows->first()->storename)->name ?? 'Store #' . $storeId,
                'count' => $rows->count(),
            ];
        });

        $expiringSoon = $liststock->filter(function ($row) {
            if (empty($row->expiry_date)) {
                return false;
            }

            return Carbon::parse($row->expiry_date)->lte(now()->addMonths(3));
        })->count();

        return view('stock.approvedStock', [
            'liststock' => $liststock,
            'approvedCount' => $approvedCount,
            'totalQty' => $totalQty,
            'totalValue' => $totalValue,
            'uniqueItems' => $uniqueItems,
            'uniquePct' => $uniquePct,
            'storeBreakdown' => $storeBreakdown,
            'expiringSoon' => $expiringSoon,
            'isSatelliteStore' => $isSatelliteStore,
        ]);
    }

    public function getIssueItemView()
    {
        $storeIds = $this->storeContext->getScopedStoreIds();

        if (empty($storeIds)) {
            return redirect()->route('choose-store');
        }

        if ($user = auth()->user()) {
            $this->notifications->syncStaleNotificationsForUser($user);
        }

        $approvedRequests = ItemRequest::with(['storename', 'staffname', 'itemname'])
            ->whereIn('item_store_id', $storeIds)
            ->where('status', 'request approved')
            ->orderByDesc('created_at')
            ->get();

        $requisitions = $approvedRequests->groupBy('requisition_no')->map(function ($lines) {
            $first = $lines->first();

            return (object) [
                'requisition_no'      => $first->requisition_no,
                'requesting_store'    => $first->storename,
                'requested_by'        => $first->staffname,
                'line_count'          => $lines->count(),
                'total_qty_requested' => (int) $lines->sum('qty_requested'),
                'total_qty_approved'  => (int) $lines->sum(fn ($line) => $line->qty ?? $line->qty_requested),
                'submitted_at'        => $lines->min('created_at'),
            ];
        })->values();

        $activeStore = $this->storeContext->getActiveStore();

        return view('stock.IssueItem', [
            'requisitions'      => $requisitions,
            'activeStore'       => $activeStore,
            'totalRequisitions' => $requisitions->count(),
            'totalLineItems'    => $approvedRequests->count(),
            'totalQty'          => (int) $approvedRequests->sum(fn ($row) => $row->qty ?? $row->qty_requested),
        ]);
    }

    
    public function getBatchNumber(Request $request)
    {
        $getID = $request->getID;

        $item = DB::table('items')
            ->leftJoin('unit_of_measures', 'items.unit_id', '=', 'unit_of_measures.id')
            ->where('items.id', $getID)
            ->select('items.*', 'unit_of_measures.name as uom_name')
            ->first();

        if (!$item) {
            return response()->json([
                'batch_number'  => null,
                'message_error' => 'Item not found',
            ]);
        }

        $stock = DB::table('approve_stocks')
            ->where('item_id', $getID)
            ->where('qty', '>', 0)
            ->where('status', 'approved')
            ->whereDate('expiry_date', '>=', now())
            ->orderBy('expiry_date', 'ASC')
            ->first();

        if ($stock) {
            return response()->json([
                'batch_number' => $stock->batch_number,
                'item_name'    => $item->name,
                'store_id'     => $stock->store_id,
                'stock_id'     => $stock->stock_id,
                'qty'          => $stock->qty,
                'expiry_date'  => $stock->expiry_date,
                'uom_name'     => $item->uom_name,
            ]);
        }

        return response()->json([
            'batch_number'  => null,
            'item_name'     => $item->name,
            'uom_name'      => $item->uom_name,
            'qty'           => 0,
            'message_error' => 'No stock available for ' . $item->name,
        ]);
    }

    public function addItemIssue(Request $request)
{
    $request->validate([
        'item'      => 'required',
        'book_no'   => 'required',
        'store'     => 'required',
        'quantity'  => 'required|numeric|min:1',
    ]);

    // Check if item exists in approved stock table
    $stocks = ApproveStock::where('status', 'approved')
                ->where('stock_id', $request->stock_id)
                ->get();

    // If no stock found
    if ($stocks->isEmpty()) {
        return back()->with(
            'message_error',
            'Selected item has not quantity please stock before issuing.'
        );
    }

    foreach ($stocks as $stock) {

        // Check if requested quantity is greater than available quantity
        if ($request->quantity > $stock->qty) {
            return back()->with(
                'message_error',
                'Requested quantity is greater than available stock quantity.'
            );
        }

        // Check expiry date
        if ($stock->expiry_date && $stock->expiry_date < now()) {
            return back()->with(
                'message_error',
                'This item batch has expired and cannot be issued.'
            );
        }

        ItemIssue::create([
            'stock_id'       => $stock->stock_id,
            'item_id'        => $stock->item_id,
            'batch_number'   => $request->batch_number,
            'qty'            => $request->quantity,
            'amount'         => $stock->amount,
            'requisition_no' => $request->book_no,
            'issue_to'       => $request->store,
            'store_id'       => $stock->store_id,
            'created_by'     => Auth::id(),
            'status'         => 'pending',
            'status_two'     => 'pending',
        ]);

    }

    return back()->with(
        'message_success',
        'Item issued successfully. Kindly wait for approval.'
    );
}

     public function deleteStockIssue(string $id)
    {
        ItemIssue ::where('id',$id)->delete();
        

        return redirect('IssueItem')->with('message_success','Item deleted successfully!');
    }

      public function approveAllIssues()
    {
    // Get logged in user's store
    $storeId = Auth::user()->department_id;

    // Get only pending stock for that store
    $stocks = ItemIssue::where('status', 'pending')
                    ->where('store_id', $storeId)
                    ->get();

    foreach ($stocks as $stock) {

    

         ItemIssue::where('id', $stock->id)
          
            ->update([
                'status' => 'pending_issues'
            ]);
    }

    return back()->with('message_success', 'All pending Issues saved successfully');
   }

   public function getIssueApproval()
   {
        $storeIds = $this->storeContext->getScopedStoreIds();

        if (empty($storeIds)) {
            return redirect()->route('choose-store');
        }

        $pendingIssues = ItemIssue::with(['staffname', 'storename', 'itemname'])
            ->whereIn('store_id', $storeIds)
            ->submittedForHodApproval()
            ->orderByDesc('created_at')
            ->get();

        $requisitions = $pendingIssues->groupBy('requisition_no')->map(function ($lines) {
            $first = $lines->first();

            return (object) [
                'requisition_no'   => $first->requisition_no,
                'requesting_store' => $first->storename,
                'issued_by'        => $first->staffname,
                'line_count'       => $lines->count(),
                'total_qty'        => (int) $lines->sum('qty'),
                'submitted_at'     => $lines->min('created_at'),
            ];
        })->values();

        return view('stock.IssueApproval', [
            'requisitions'      => $requisitions,
            'activeStore'       => $this->storeContext->getActiveStore(),
            'totalRequisitions' => $requisitions->count(),
            'totalLineItems'    => $pendingIssues->count(),
            'totalQty'          => (int) $pendingIssues->sum('qty'),
        ]);
   }

        public function searchIssues(Request $request)
        {
        $request->validate([
            'department' => 'required',
        ]);

        $liststores = Store::all();

        $pendingIssues = ItemIssue::with(['staffname', 'storename', 'itemname'])
            ->submittedForHodApproval()
            ->where('issue_to', $request->department)
            ->orderByDesc('created_at')
            ->get();

        $requisitions = $pendingIssues->groupBy('requisition_no')->map(function ($lines) {
            $first = $lines->first();

            return (object) [
                'requisition_no'   => $first->requisition_no,
                'requesting_store' => $first->storename,
                'issued_by'        => $first->staffname,
                'line_count'       => $lines->count(),
                'total_qty'        => (int) $lines->sum('qty'),
                'submitted_at'     => $lines->min('created_at'),
            ];
        })->values();

        $viewData = [
            'requisitions'      => $requisitions,
            'activeStore'       => $this->storeContext->getActiveStore(),
            'totalRequisitions' => $requisitions->count(),
            'totalLineItems'    => $pendingIssues->count(),
            'totalQty'          => (int) $pendingIssues->sum('qty'),
            'liststores'        => $liststores,
        ];

        if ($pendingIssues->count() > 0) {
            return view('stock.IssueApproval', $viewData)
                ->with('message_success', $pendingIssues->count() . ' issue(s) found');
        }

        return view('stock.IssueApproval', $viewData)
            ->with('message_error', 'No issues found');
        }
 

    public function ApproveIssueIndv(Request $request)
    {
        $request->validate([
            'issue_id' => 'required|array'
        ]);
    $invoiceNo = 'INV-' . date('YmdHis') . '-' . strtoupper(Str::random(6));
        $lastInvoice = null;
        $notifiedStores = [];
        $processedRequisitions = [];
        

        foreach ($request->issue_id as $issueId) {

            $issue = ItemIssue::where('id', $issueId)
                ->submittedForHodApproval()
                ->first();

            if (!$issue) continue;

            $processedRequisitions[$issue->requisition_no] = true;

            $approvedQty = (int) ($request->qty[$issueId] ?? 0);

            if ($approvedQty <= 0) {
                continue;
            }

            $stock = ApproveStock::where('item_id', $issue->item_id)
                ->where('batch_number', $issue->batch_number)
                ->where('store_id', $issue->store_id)
                ->first();

            if (!$stock) {
                return back()->with('message_error', 'Stock not found for '.$issue->itemname->name);
            }

            if ($approvedQty > $stock->qty) {
                return back()->with('message_error', 'Insufficient stock for '.$issue->itemname->name);
            }

            // Deduct stock
            $stock->qty -= $approvedQty;
            $stock->save();

        
            $issue->qty = $approvedQty;
            $issue->invoice_number = $invoiceNo;
            $issue->issued_by = Auth::id();
            $issue->status = 'issued';
            $issue->status_two = 'issued';
            $issue->save();

            if ($issue->item_request_id) {
                $totalIssued = (int) ItemIssue::where('item_request_id', $issue->item_request_id)
                    ->where('status', 'issued')
                    ->sum('qty');

                ItemRequest::where('id', $issue->item_request_id)->update([
                    'qty_issued' => $totalIssued,
                    'status'     => 'issued',
                ]);
            } else {
                ItemRequest::where('requisition_no', $issue->requisition_no)
                    ->where('item_id', $issue->item_id)
                    ->where('store_id', $issue->issue_to)
                    ->update(['status' => 'issued']);
            }

            $lastInvoice = $invoiceNo;

            $satelliteStoreId = (int) $issue->issue_to;
            if ($satelliteStoreId && !isset($notifiedStores[$satelliteStoreId])) {
                $storeName = Store::find($satelliteStoreId)?->name ?? 'satellite store';
                $this->notifications->notifyUsersForAction(
                    NotificationService::TYPE_ISSUE_APPROVED,
                    'Stock Issued — Receive Required',
                    "Items have been issued to {$storeName}. Please receive stock into inventory.",
                    'ReceiveStock',
                    $issue->requisition_no,
                    $satelliteStoreId,
                    [Auth::id()]
                );
                $notifiedStores[$satelliteStoreId] = true;
            }
        }

        foreach (array_keys($processedRequisitions) as $requisitionNo) {
            $this->notifications->resolveIfComplete(
                NotificationService::TYPE_ISSUE_PENDING_APPROVAL,
                $requisitionNo
            );
        }

        return redirect()->route('IssueApproval')
            ->with('message_success', 'Issues approved successfully');
    }
    

    public function printIssue($invoice)
    {
        $issues = ItemIssue::where('invoice_number', $invoice)->first();

        $store = Store::find($issues->store_id);
        $issueto = Store::find($issues->issue_to);

        $listissues = ItemIssue::where('invoice_number', $invoice)
                    ->get();

        return view('stock.print', compact('issues', 'invoice', 'store','issueto','listissues'));
    }

    public function addBulkupload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        $uploadedFile = $request->file('file');
        $readerType = $this->resolveImportReaderType($uploadedFile);

        if (!$readerType) {
            return back()->with(
                'message_error',
                'Could not detect the file type. Please upload a valid .xlsx, .xls, or .csv spreadsheet.'
            );
        }

        $import = new ItemsImport(
            defaultStoreId: $this->resolveBulkUploadStoreId(),
            createdBy: Auth::id(),
        );

        try {
            Excel::import($import, $uploadedFile, null, $readerType);
        } catch (NoTypeDetectedException $e) {
            return back()->with(
                'message_error',
                'Unable to read the uploaded file. Save it as .xlsx, .xls, or .csv and try again.'
            );
        }

        if ($import->importedCount === 0) {
            $details = collect($import->skipReasons)
                ->map(fn ($count, $reason) => $count . ' ' . $reason)
                ->implode('; ');

            return back()->with(
                'message_error',
                'No items were imported.'
                . ($details ? ' Reasons: ' . $details . '.' : '')
                . ' Headers: name, category_id (or category name), unit_id (or unit name), status.'
                . ' Add store_id if you are a global admin without an active store.'
            );
        }

        $message = $import->importedCount . ' item(s) imported successfully';

        if ($import->skippedCount > 0) {
            $message .= ' (' . $import->skippedCount . ' row(s) skipped as empty, duplicate, or missing store)';
        }

        return redirect()->route('Item')->with(
            'message_success',
            $message
        );
    }

    protected function resolveBulkUploadStoreId(): ?int
    {
        if ($this->storeContext->hasGlobalStoreAccess()) {
            return $this->storeContext->getActiveStoreId();
        }

        $activeStoreId = $this->storeContext->getActiveStoreId();

        if ($activeStoreId) {
            return $activeStoreId;
        }

        $scopedStoreIds = $this->storeContext->getScopedStoreIds();

        return $scopedStoreIds[0] ?? null;
    }

    protected function resolveImportReaderType(UploadedFile $file): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: '');

        $byExtension = [
            'xlsx' => ExcelFormat::XLSX,
            'xlsm' => ExcelFormat::XLSX,
            'xltx' => ExcelFormat::XLSX,
            'xls'  => ExcelFormat::XLS,
            'xlt'  => ExcelFormat::XLS,
            'csv'  => ExcelFormat::CSV,
            'txt'  => ExcelFormat::CSV,
        ];

        if (isset($byExtension[$extension])) {
            return $byExtension[$extension];
        }

        $byMime = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ExcelFormat::XLSX,
            'application/vnd.ms-excel' => ExcelFormat::XLS,
            'text/csv' => ExcelFormat::CSV,
            'text/plain' => ExcelFormat::CSV,
            'application/csv' => ExcelFormat::CSV,
            'application/octet-stream' => ExcelFormat::XLSX,
        ];

        return $byMime[$file->getMimeType()] ?? null;
    }

     public function getIssueItemID($id)
    {
        $data = ItemIssue::with('itemname')->findOrFail($id);

        return response()->json([
            'id'   => $data->id,
            'name' => $data->itemname->name ?? 'Item',
        ]);
    }

     public function addItemRejection(Request $request)
    {
         $request->validate([
            'item_id' =>'required',
           
        ]);

       
            $insertCat = ItemIssue::find($request->item_id);
            $insertCat->status = "rejected";
            $insertCat->reason = trim($request->reason);
            $insertCat->issued_by = Auth::User()->id;
             
             
            $status = $insertCat->save();

            if ($status && $insertCat->requisition_no) {
                $this->notifications->resolveIfComplete(
                    NotificationService::TYPE_ISSUE_PENDING_APPROVAL,
                    $insertCat->requisition_no
                );
            }

            return $status ? back()->with('message_success','Item has been rejected successfully') : back()->with('message_error','Something went wrong, please try again.');
    }

     public function getviewStockEntry($store_id)
   {
    $this->assertCanApproveStock();

    try {
        $storeId = (int) Crypt::decrypt($store_id);
    } catch (\Throwable $e) {
        abort(404);
    }

    if (!in_array($storeId, $this->storeContext->getScopedStoreIds(), true)) {
        abort(403, 'You cannot review stock for this store.');
    }

    $store = Store::findOrFail($storeId);
    $isSatelliteStore = $store->store_group === 'satellite';

    if ($isSatelliteStore) {
        $liststock = SatelliteStockEntry::with(['itemcode', 'itemname', 'supname', 'staffname'])
            ->where('store_id', $storeId)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();
    } else {
        $liststock = Stock::with(['itemcode', 'itemname', 'supname', 'staffname'])
            ->where('store_id', $storeId)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();
    }

    $pendingCount = $liststock->count();
    $totalQty = (int) $liststock->sum('qty');
    $totalValue = $liststock->sum(fn ($s) => (float) ($s->qty ?? 0) * (float) ($s->amount ?? 0));
    $uniqueItems = $liststock->pluck('item_id')->unique()->count();

    return view('stock.viewStockEntry', [
        'liststock' => $liststock,
        'store' => $store,
        'storeId' => $storeId,
        'isSatelliteStore' => $isSatelliteStore,
        'canApproveStock' => $this->stockApproval->canApproveStock(),
        'pendingCount' => $pendingCount,
        'totalQty' => $totalQty,
        'totalValue' => $totalValue,
        'uniqueItems' => $uniqueItems,
    ]);
   }

    public function getItemUom(Request $request)
    {
        $item = DB::table('items')
            ->join('unit_of_measures', 'items.unit_id', '=', 'unit_of_measures.id')
            ->where('items.id', $request->item_id)
            ->select('items.name as item_name', 'unit_of_measures.name as uom_name')
            ->first();

        if (!$item) {
            return response()->json(['uom_name' => null]);
        }

        return response()->json([
            'uom_name' => $item->uom_name,
        ]);
    }
}
