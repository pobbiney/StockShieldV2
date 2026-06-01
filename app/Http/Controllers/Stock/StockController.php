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
use Illuminate\Support\Str;
use App\Imports\ItemsImport;
use Maatwebsite\Excel\Facades\Excel;
 

 
use Illuminate\Support\Facades\File;


class StockController extends Controller
{
    public function getItemCatView()
    {
        $list = ItemCategory::all();
        return view('stock.ItemCategory',['list'=>$list]);
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
            $insertCat = new ItemCategory();
            $insertCat->name = trim($request->name);
            $insertCat->status = $request->status;
          
           
            $insertCat = $insertCat->save();
            return $insertCat ? back()->with('message_success','Item Category   added successfully') : back()->with('message_error','Something went wrong, please try again.');
    }

     public function updateItemCategory(Request $request)
    {
          $request->validate([
            'name' => 'required',
            'status' => 'required',
            
            
        ]);
            $insertCat = ItemCategory::find($request->cat_id);
            $insertCat->name = trim($request->name);
            $insertCat->status = $request->status;
            
            $insertCat = $insertCat->save();
            return $insertCat ? back()->with('message_success','Item Category updated successfully') : back()->with('message_error','Something went wrong, please try again.');
    }

    public function getunitOfmeasureView()
    {
         $list = UnitOfMeasure::all();
        return view('stock.unitOfmeasure',['list'=>$list]);
    }

    public function addUnitOfMeasure(Request $request)
    {
          $request->validate([
            'name' => 'required',
            'status' => 'required',
            
            
        ]);
            $insertCat = new UnitOfMeasure();
            $insertCat->name = trim($request->name);
            $insertCat->status = $request->status;
          
           
            $insertCat = $insertCat->save();
            return $insertCat ? back()->with('message_success','Unit Of Issue  added successfully') : back()->with('message_error','Something went wrong, please try again.');
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
            $insertCat = UnitOfMeasure::find($request->cat_id);
            $insertCat->name = trim($request->name);
            $insertCat->status = $request->status;
            
            $insertCat = $insertCat->save();
            return $insertCat ? back()->with('message_success','Unit of Issue updated successfully') : back()->with('message_error','Something went wrong, please try again.');
    }

    public function getItemView()
    {
       
        $listcat = ItemCategory::all();
        $listunit = UnitOfMeasure::all();
        $listdept = array_map('intval', explode('~', Auth::user()->department_id));  
        $getstoreid = Store::whereIn('id', $listdept)->get();  

         $list = Item::whereIn('store_id',$listdept)->get();
       
        return view('stock.Item',['list'=>$list,'listcat'=>$listcat,'listunit'=>$listunit,'getstoreid'=>$getstoreid]);
    }

    public function addItem(Request $request)
    {
         $request->validate([
            'name' =>'required',
            'unit_of_measure_id' =>'required',
            'category_id' => 'required',
            'status'=>'required',
            'store_id' => 'required',
            're_order_level' => 'required'
        ]);

        if(Item::where('name',$request->name)->get()->count() > 0){

            return back()->with('message_error','Item already exist');

        }else{

          // Generate Item Code
        $lastItem = Item::latest('id')->first();

        if($lastItem){
            $number = $lastItem->id + 1;
        } else {
            $number = 1;
        }

        $itemCode = 'ITM-' . str_pad($number, 5, '0', STR_PAD_LEFT);

            $insertCat = new Item();
             $insertCat->item_code = $itemCode;
            $insertCat->name = trim($request->name);
            $insertCat->cat_id = $request->category_id;
            $insertCat->unit_id = $request->unit_of_measure_id;
            $insertCat->store_id = $request->store_id;
            $insertCat->reorder_level = $request->re_order_level ;
             $insertCat->status = $request->status;
             
            $insertCat->created_by = Auth::User()->id;
             
            $status = $insertCat->save();

            return $status ? back()->with('message_success','Item added successfully') : back()->with('message_error','Something went wrong, please try again.');


        }

    }

      public function getItemID($id)
    {
         $data = Item::findOrFail($id);
          return response()->json($data);
    }

    public function updateItem(Request $request)
    {
         $request->validate([
            'name' =>'required',
            'unit_of_measure_id' =>'required',
            'category_id' => 'required',
            'status'=>'required',
            'store_id' => 'required',
            're_order_level' => 'required'
        ]);

       
            $insertCat = Item::find($request->item_id);
            $insertCat->name = trim($request->name);
            $insertCat->cat_id = $request->category_id;
            $insertCat->unit_id = $request->unit_of_measure_id;
            $insertCat->store_id = $request->store_id;
             $insertCat->status = $request->status;
             $insertCat->reorder_level = $request->re_order_level ;
              $insertCat->updated_by = Auth::User()->id;
             
             
            $status = $insertCat->save();

            return $status ? back()->with('message_success','Item updated successfully') : back()->with('message_error','Something went wrong, please try again.');

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
    

    public function getstockEntryView()
    {
        $listsup = Supplier::all();
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

       

         $getstoreId = Store::whereIn('id', $listdept)->get(); // fix: whereIn + get()
        $liststock = Stock::whereIn('store_id',$listdept)
        ->where('status','pending')->get();

  
     
        return view('stock.stockEntry',['getItemid'=>$getItemid,'listsup'=>$listsup,'getstoreId'=>$getstoreId,'liststock'=>$liststock]);
    }



   public function addStock(Request $request)
{
    $request->validate([
        'item' => 'required',
        'batch_number' => 'nullable',
        'expiry_date' => 'required',
        'supplier' => 'required',
        'waybill' => 'required',
        'award_letter' => 'required',
        'amount' => 'required',
        'store' => 'required',
        'quantity' => 'required',
        'bar_code' => 'nullable',
    ]);

    // ✅ Batch number: use user input OR generate
    $batchNumber = $request->batch_number 
        ? $request->batch_number 
        : 'BN' . rand(10000000, 99999999);

    // ✅ Barcode: use user input OR generate
    $barcode = $request->bar_code 
        ? $request->bar_code 
        : 'SS' . rand(10000000, 99999999);

    // Generate barcode image
    $barcodeImage = DNS1D::getBarcodePNG($barcode, 'C128');

    // Folder path
    $folderPath = public_path('barcodes');

    if (!File::exists($folderPath)) {
        File::makeDirectory($folderPath, 0755, true);
    }

    $imageName = $barcode . '.png';

    file_put_contents(
        $folderPath . '/' . $imageName,
        base64_decode($barcodeImage)
    );

    // Save stock
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
    $insertCat->store_id = $request->store;
    $insertCat->qty = $request->quantity;

    $insertCat->barcode = $barcode;
    $insertCat->barcode_path = 'barcodes/' . $imageName;

    $insertCat->created_by = Auth::id();

    $status = $insertCat->save();

    return $status
        ? back()->with('message_success', 'Stock added successfully')
        : back()->with('message_error', 'Something went wrong, please try again.');
}

  public function deleteStockItem(string $id)
    {
        Stock ::where('id',$id)->delete();
        

        return redirect('stockEntry')->with('message_success','Item deleted successfully!');
    }

    //get stock details
      public function getStockID($id)
    {
         $data = Stock::findOrFail($id);
          return response()->json($data);
    }

    public function updateStock(Request $request)
    {
       $request->validate([
        'item' => 'required',
        'batch_number' => 'nullable',
        'expiry_date' => 'required',
        'supplier' => 'required',
        'waybill' => 'required',
        'award_letter' => 'required',
        'amount' => 'required',
        'store' => 'required',
        'quantity' => 'required',
        'bar_code' => 'nullable',
    ]);

    // ✅ Batch number: use user input OR generate
    $batchNumber = $request->batch_number 
        ? $request->batch_number 
        : 'BN' . rand(10000000, 99999999);

    // ✅ Barcode: use user input OR generate
    $barcode = $request->bar_code 
        ? $request->bar_code 
        : 'SS' . rand(10000000, 99999999);

    // Generate barcode image
    $barcodeImage = DNS1D::getBarcodePNG($barcode, 'C128');

    // Folder path
    $folderPath = public_path('barcodes');

    if (!File::exists($folderPath)) {
        File::makeDirectory($folderPath, 0755, true);
    }

    $imageName = $barcode . '.png';

    file_put_contents(
        $folderPath . '/' . $imageName,
        base64_decode($barcodeImage)
    );

    // Save stock
     $insertCat = Stock::find($request->stock_id);
    $insertCat->item_id = $request->item;
    $insertCat->batch_number = $batchNumber;
    $insertCat->manufacturing_date = $request->manufacturing_date;
    $insertCat->expiry_date = $request->expiry_date;
    $insertCat->supplier_id = $request->supplier;
    $insertCat->purchase_order = $request->purchase_order;
    $insertCat->waybill = $request->waybill;
    $insertCat->award_letter = $request->award_letter;
    $insertCat->amount = $request->amount;
    $insertCat->store_id = $request->store;
    $insertCat->qty = $request->quantity;

    $insertCat->barcode = $barcode;
    $insertCat->barcode_path = 'barcodes/' . $imageName;

    $insertCat->created_by = Auth::id();

    $status = $insertCat->save();

    return $status
        ? back()->with('message_success', 'Stock updated successfully')
        : back()->with('message_error', 'Something went wrong, please try again.');
   }

   public function getstockApprovalView()
   {

    
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
       
         
        $liststock = Stock::whereIn('store_id',$listdept)
        ->where('status','pending')->get();
    return view('stock.stockApproval',['liststock'=>$liststock]);
   }

    public function ApproveStock($id)
    {
        

      // Logged in user's store
    $storeId = Auth::user()->department_id;

    // Get selected pending stock
    $stocks = Stock::where('id', $id)
                    ->where('status', 'pending')
                    ->where('store_id', $storeId)
                    ->get();
    foreach ($stocks as $stock) {

        ApproveStock::create([
            'stock_id'        => $stock->id,
            'item_id'         => $stock->item_id,
            'batch_number'    => $stock->batch_number,
            'expiry_date'     =>  $stock->expiry_date,
            'qty'             => $stock->qty,
            'amount'          => $stock->amount,
            'purchase_order'  => $stock->purchase_order,
            'supplier_id'     => $stock->supplier_id,
            'store_id'        => $stock->store_id,
            'created_by'      =>  Auth::id(),
            'status'          => 'approved',
        ]);

        // Update original stock table
        Stock::where('id', $stock->id)
            ->update([
                'status' => 'approved'
            ]);
    }

   
        

        return back()->with('message_success', 'Stock Approved Successfully');
    }

      public function approveAll()
    {
    // Get logged in user's store
    $storeId = Auth::user()->department_id;

    // Get only pending stock for that store
    $stocks = Stock::where('status', 'pending')
                    ->where('store_id', $storeId)
                    ->get();

    foreach ($stocks as $stock) {

        ApproveStock::create([
            'stock_id'        => $stock->id,
            'item_id'         => $stock->item_id,
            'batch_number'    => $stock->batch_number,
            'expiry_date'     => $stock->expiry_date,
            'qty'             => $stock->qty,
            'amount'          => $stock->amount,
            'purchase_order'  => $stock->purchase_order,
            'supplier_id'     => $stock->supplier_id,
            'store_id'        => $stock->store_id,
            'created_by'      =>  Auth::id(),
            'status'          => 'approved',
        ]);

        // Update original stock table
        Stock::where('id', $stock->id)
            ->update([
                'status' => 'approved'
            ]);
    }

    return back()->with('message_success', 'All pending stock approved successfully');
   }

   public function getpendingStockView()
    {
        $listsup = Supplier::all();
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $getItemid = Item::whereIn('store_id', $listdept)
         ->where('status','Active')
        ->get(); // fix: whereIn + get()

       

         $getstoreId = Store::whereIn('id', $listdept)->get(); // fix: whereIn + get()
        $liststock = Stock::whereIn('store_id',$listdept)
        ->where('status','pending')->get();

  
     
        return view('stock.pendingStock',['getItemid'=>$getItemid,'listsup'=>$listsup,'getstoreId'=>$getstoreId,'liststock'=>$liststock]);
    }

    public function getapprovedStockView()
    {
        
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststock = Stock::whereIn('store_id',$listdept)
        ->where('status','approved')->get();
        return view('stock.approvedStock',[ 'liststock'=>$liststock]);
    }

    public function getIssueItemView()
    {
        $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $getItemid = Item::whereIn('store_id', $listdept)
        ->where('status','Active')
        ->get(); // fix: whereIn + get()
        $liststore = Store::all();
        

        $listitemissue = ItemIssue::where('store_id',$listdept)
        ->where('status','pending')
        ->get();
        return view('stock.IssueItem', ['getItemid'=>$getItemid,'liststore'=>$liststore,'listitemissue'=>$listitemissue]);
    }

    public function getBatchNumber(Request $request)
{
    $getID = $request->getID;

    $stock = DB::table('approve_stocks')
        ->where('item_id', $getID)
        ->where('qty', '>', 0)
        ->where('status', 'approved')

        // Skip expired items
        ->whereDate('expiry_date', '>=', now())

        // Pick nearest expiry first (FEFO)
        ->orderBy('expiry_date', 'ASC')

        ->first();

    if ($stock) {

        return response()->json([
            'batch_number' => $stock->batch_number,
            'stock_id'     => $stock->id,
            'qty'          => $stock->qty,
            'expiry_date'  => $stock->expiry_date
        ]);
    }

    return response()->json([
        'batch_number' => null,
        'message' => 'No valid non-expired stock available'
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
     $liststores = Store::all();
        return view('stock.IssueApproval', [  'liststores'=>$liststores]);
   }

        public function searchIssues(Request $request)
        {
        $request->validate([
            'department' => 'required',
        ]);

       
        // All stores
        $liststores = Store::all();

        // Search issues
        $listissues = ItemIssue::where('status', 'pending_issues')
                       
                        ->where('issue_to', $request->department)
                        ->get();

        if ($listissues->count() > 0) {

            return view(
                'stock.IssueApproval',
                compact('listissues', 'liststores'  )
            )->with(
                'message_success',
                $listissues->count().' issue(s) found'
            );

        } else {

            return view(
                'stock.IssueApproval',
                compact('listissues', 'liststores')
            )->with(
                'message_error',
                'No issues found'
            );
        }
    }
 

    public function ApproveIssueIndv(Request $request)
    {
        $request->validate([
            'issue_id' => 'required|array'
        ]);
    $invoiceNo = 'INV-' . date('YmdHis') . '-' . strtoupper(Str::random(6));
        $lastInvoice = null;
        

        foreach ($request->issue_id as $issueId) {

            $issue = ItemIssue::where('id', $issueId)
                ->where('status', 'pending_issues')
                ->first();

            if (!$issue) continue;

            $approvedQty = $request->qty[$issueId];

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
            $issue->save();

            $lastInvoice = $invoiceNo;
        }

        return redirect()->route('stock.print', $lastInvoice)
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
        'file' => 'required'
    ]);

    $import = new ItemsImport();

    $import->import($request->file('file'));

    return back()->with(
        'message_success',
        'Items imported successfully'
    );
    }

     public function getIssueItemID($id)
    {
         $data = ItemIssue::findOrFail($id);
          return response()->json($data);
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

            return $status ? back()->with('message_success','Item has been rejected successfully') : back()->with('message_error','Something went wrong, please try again.');
    }
    
}
