<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemIssue;
use App\Models\SatelliteItemIssue;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getItemReportView()
    {

         $liststores = Store::all();
        return view('report.ItemReport',['liststores'=>$liststores]);
    }

      public function searchStockReport(Request $request)
        {
        $request->validate([
            'department' => 'required',
        ]);

       
        // All stores
        $liststores = Store::all();

        // Search Item
     $liststock = Item::with(['approveStock'])
    ->where('status', 'Active')
    ->where('store_id', $request->department)
    ->get();

        if ($liststock->count() > 0) {

            return view(
                'report.ItemReport',
                compact('liststock', 'liststores'  )
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.ItemReport',
                compact('liststock', 'liststores')
            )->with(
                'message_error',
                'No Items found'
            );
        }
    }

     public function getReceivedStocksView()
    {
        $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get();  
         
        return view('report.ReceivedStocks',['liststores'=>$liststores]);
    }

    public function searchReceivedStockReport(Request $request)
    
         {
        $request->validate([
            'department' => 'required',
        ]);

       
        // All stores
        $liststores = Store::all();

       $liststock = Stock::where('store_id', $request->department)
    ->where(function($query) {
        $query->where('status', 'pending')
              ->orWhere('status', 'approved');
    })
    ->orderBy('id', 'DESC')
    ->get();
        if ($liststock->count() > 0) {

            return view(
                'report.ReceivedStocks',
                compact('liststock', 'liststores'  )
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.ReceivedStocks',
                compact('liststock', 'liststores')
            )->with(
                'message_error',
                'No Items found'
            );
        }
    }

    public function printReceivedStockReport($department)
{

    $liststock = Stock::with(['itemname.unitname', 'supname', 'staffname'])
        ->where('store_id', $department)
        ->where(function ($query) {
            $query->where('status', 'pending')
                ->orWhere('status', 'approved');
        })
        ->orderBy('id', 'DESC')
        ->get();

    $store = Store::find($department);

    return view(
        'report.printReceivedStocks',
        compact('liststock', 'store')
    );
    }

    public function getReceivedStockByDateView()
    {
        $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
        return view('report.ReceivedStockByDate',['liststores'=>$liststores]);
    }
  
    public function stockreceivedDateInter(Request $request)
    {
       $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'department' => 'required',
    ]);

       
        // All stores
        $liststores = Store::all();

       $liststock = Stock::where('store_id', $request->department)
    ->where(function($query) {
        $query->where('status', 'pending')
              ->orWhere('status', 'approved');
    }) ->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ])
 
    ->orderBy('id', 'DESC')
    ->get();
        if ($liststock->count() > 0) {

            return view(
                'report.ReceivedStockByDate',
                compact('liststock', 'liststores'  )
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.ReceivedStockByDate',
                compact('liststock', 'liststores')
            )->with(
                'message_error',
                'No Items found'
            );
        }
    }

     public function printReceivedStockDateReport(Request $request)
    {

    $liststock = Stock::where('store_id', $request->department)
        ->where(function($query) {

            $query->where('status', 'pending')
                  ->orWhere('status', 'approved');

        })->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ])
 
    ->orderBy('id', 'DESC')
    ->get();
         

    $store = Store::find($request->department);

    return view(
        'report.printReceivedStockDate',
        compact('liststock', 'store')
    );
    }
    
    public function getsearchByItemView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
        $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

        return view('report.searchByItem',['liststores'=>$liststores,'getItemid'=>$getItemid]);
    }

    public function searchReceivedStockByItemReport(Request $request)
    {
          
        $request->validate([
            'department' => 'required',
             'item' => 'required',
        ]);

       
        // All stores
         $liststores = Store::all();
        
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

       $liststock = Stock::where('store_id', $request->department)
       ->where('item_id',$request->item)
    ->where(function($query) {
        $query->where('status', 'pending')
              ->orWhere('status', 'approved');
    })
    ->orderBy('id', 'DESC')
    ->get();
        if ($liststock->count() > 0) {

            return view(
                'report.searchByItem',
                compact('liststock', 'liststores' ,  'getItemid')
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.searchByItem',
                compact('liststock', 'liststores','getItemid')
            )->with(
                'message_error',
                'No Items found'
            );
        }
    }

      public function printReceivedStockByItemReport(Request $request)
    {

    $liststock = Stock::where('store_id', $request->department)
        ->where(function($query) {

            $query->where('status', 'pending')
                  ->orWhere('status', 'approved');

        })->where('item_id', $request->item )
 
    ->orderBy('id', 'DESC')
    ->get();
    $store = Store::find($request->department);

    return view(
        'report.printReceivedStockByItem',
        compact('liststock', 'store')
    );
    }

    public function getsearchByItemDateView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
        $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

        return view('report.searchByItemDate',['liststores'=>$liststores,'getItemid'=>$getItemid]);
    }

    
    public function searchReceivedStockByItemDateReport(Request $request)
    {
          
         $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'item' => 'required',
    ]);

       
        // All stores
        $liststores = Store::all();
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()
       $liststock = Stock::where('item_id', $request->item)
    ->where(function($query) {
        $query->where('status', 'pending')
              ->orWhere('status', 'approved');
    }) ->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ])
 
    ->orderBy('id', 'DESC')
    ->get();
        if ($liststock->count() > 0) {

            return view(
                'report.searchByItemDate',
                compact('liststock', 'liststores' ,'getItemid' )
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.searchByItemDate',
                compact('liststock', 'liststores','getItemid')
            )->with(
                'message_error',
                'No Items found'
            );
        }
    }
     

      public function printReceivedStockByItemDateReport(Request $request)
    {

    $liststock = Stock::where('item_id', $request->item)
        ->where(function($query) {

            $query->where('status', 'pending')
                  ->orWhere('status', 'approved');

        })->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ])
 
    ->orderBy('id', 'DESC')
    ->get();
         

    $store = Store::find(Auth::user()->department_id);

    return view(
        'report.printReceivedStockByItemDate',
        compact('liststock', 'store')
    );
    }

     public function getReorderLevelReportView()
    {
        $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get();  
         
        return view('report.ReOrderLevelReport',['liststores'=>$liststores]);
    }

    
   

public function searchStockLevelReport(Request $request)
{
    $request->validate([
        'department' => 'required',
    ]);

    $liststores = Store::all();

    $liststock = Item::join('approve_stocks', function ($join) {
        $join->on('items.id', '=', 'approve_stocks.item_id')
             ->on('items.store_id', '=', 'approve_stocks.store_id')
             ->where('approve_stocks.status', '=', 'approved');
    })
    ->select(
        'items.*',
        DB::raw('SUM(approve_stocks.qty) as total_qty')
    )
    ->where('items.store_id', $request->department)
    ->where('items.status', 'Active')
    ->groupBy(
        'items.id',
        'items.item_code',
        'items.name',
        'items.cat_id',
        'items.unit_id',
        'items.store_id',
        'items.created_by',
        'items.created_at',
        'items.updated_at',
        'items.status',
        'items.reorder_level',
        'items.updated_by'
    )
    ->havingRaw('SUM(approve_stocks.qty) > 0')
    ->havingRaw('SUM(approve_stocks.qty) <= items.reorder_level')
    ->orderBy('items.name', 'ASC')
    ->get();

    if ($liststock->count() > 0) {
        return view(
            'report.ReOrderLevelReport',
            compact('liststock', 'liststores')
        )->with(
            'message_success',
            $liststock->count() . ' Item(s) found'
        );
    }

    return view(
        'report.ReOrderLevelReport',
        compact('liststock', 'liststores')
    )->with(
        'message_error',
        'No Items found'
    );
}
   

 public function printReoderlevelStockReport($department)
{

    $liststock = Item::join('approve_stocks', function ($join) {
        $join->on('items.id', '=', 'approve_stocks.item_id')
             ->on('items.store_id', '=', 'approve_stocks.store_id')
             ->where('approve_stocks.status', '=', 'approved');
    })
    ->select(
        'items.*',
        DB::raw('SUM(approve_stocks.qty) as total_qty')
    )
    ->where('items.store_id', $department)
    ->where('items.status', 'Active')
    ->groupBy(
        'items.id',
        'items.item_code',
        'items.name',
        'items.cat_id',
        'items.unit_id',
        'items.store_id',
        'items.created_by',
        'items.created_at',
        'items.updated_at',
        'items.status',
        'items.reorder_level',
        'items.updated_by'
    )
    ->havingRaw('SUM(approve_stocks.qty) > 0')
    ->havingRaw('SUM(approve_stocks.qty) <= items.reorder_level')
    ->orderBy('items.name', 'ASC')
    ->get();

    $store = Store::find($department);

    return view(
        'report.printReorderlevel',
        compact('liststock', 'store')
    );
    }

      public function getIssuedItemsReportView()
    {
        $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get();  
         
        return view('report.IssuedItemsReport',['liststores'=>$liststores]);
    }

    protected function storeIssuesFromSatelliteInventory(?Store $store): bool
    {
        return $store && ($store->isAdminStoreSatellite() || $store->isRequisitionHub());
    }

    protected function issuedItemsForStore(
        int $storeId,
        string $orderBy = 'id',
        string $direction = 'desc',
        array $filters = []
    ) {
        $store = Store::find($storeId);
        $relations = ['itemname.unitname', 'issuefrom', 'storename', 'staffname'];

        $query = $this->storeIssuesFromSatelliteInventory($store)
            ? SatelliteItemIssue::with($relations)
            : ItemIssue::with($relations);

        $query->where('store_id', $storeId)->where('status', 'issued');

        if (! empty($filters['item_id'])) {
            $query->where('item_id', $filters['item_id']);
        }

        if (! empty($filters['start_date']) && ! empty($filters['end_date'])) {
            $query->whereBetween('created_at', [
                $filters['start_date'] . ' 00:00:00',
                $filters['end_date'] . ' 23:59:59',
            ]);
        }

        return $query->orderBy($orderBy, $direction)->get();
    }

     public function searchIssuedStoreReport(Request $request)
    {
          
        $request->validate([
            'department' => 'required',
         
        ]);

       
        // All stores
         $liststores = Store::all();
        
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

       $liststock = $this->issuedItemsForStore((int) $request->department, 'id', 'desc');
        if ($liststock->count() > 0) {

            return view(
                'report.searchIssueItemByStore',
                compact('liststock', 'liststores' ,  'getItemid')
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.searchIssueItemByStore',
                compact('liststock', 'liststores','getItemid')
            )->with(
                'message_error',
                'No Items found'
            );
        }
    }


      public function printIssuedItemReport(Request $request)
    {
          
       
        // All stores
         $liststores = Store::all();
        
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()R

       $liststock = $this->issuedItemsForStore((int) $request->department, 'invoice_number', 'asc');
    $store = Store::find($request->department);
        return view(
        'report.printIssuedItemReport',
        compact('liststock', 'liststores','getItemid','store')
    );
    }

    public function getsearchIssueItemByStoreView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get();  
        return view('report.searchIssueItemByStore',['liststores'=>$liststores]);
    }

     public function getsearchByIssueItemView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
        $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

        return view('report.searchByIssueItem', ['liststores'=>$liststores,'getItemid'=>$getItemid]);
    }

    public function searchIssuedItemReport(Request $request)
    {
         $request->validate([
            'department' => 'required',
             'item' => 'required',
        ]);

       
        // All stores
         $liststores = Store::all();
        
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

       $liststock = $this->issuedItemsForStore((int) $request->department, 'invoice_number', 'asc', [
           'item_id' => $request->item,
       ]);
        if ($liststock->count() > 0) {

            return view(
                'report.searchByIssueItem',
                compact('liststock', 'liststores' ,  'getItemid')
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.searchByIssueItem',
                compact('liststock', 'liststores','getItemid')
            )->with(
                'message_error',
                'No Items found'
            );
    }
    }

    public function printIssuedByItemReport(Request $request)
    {
         

       
        // All stores
         $liststores = Store::all();
        
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

       $liststock = $this->issuedItemsForStore((int) $request->department, 'invoice_number', 'asc', [
           'item_id' => $request->item,
       ]);
     $store = Store::find($request->department);
      return view(
        'report.printIssuedByItemReport',
        compact('liststock', 'liststores','getItemid','store')
    );
    }

      public function getsearchByIssuedDateView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
        $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

        return view('report.searchByIssuedDate', ['liststores'=>$liststores,'getItemid'=>$getItemid]);
    }

     public function searchByIssuedDate(Request $request)
    {
       $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'department' => 'required',
    ]);

       
        // All stores
        $liststores = Store::all();

       $liststock = $this->issuedItemsForStore((int) $request->department, 'id', 'desc', [
           'start_date' => $request->start_date,
           'end_date' => $request->end_date,
       ]);
        if ($liststock->count() > 0) {

            return view(
                'report.searchByIssuedDate',
                compact('liststock', 'liststores'  )
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.searchByIssuedDate',
                compact('liststock', 'liststores')
            )->with(
                'message_error',
                'No Items found'
            );
        }
    }


      public function printIssuedItemDateReport(Request $request)
    {
      
       
        // All stores
        $liststores = Store::all();

           $startDate = $request->start_date;
          $endDate   = $request->end_date;

       $liststock = $this->issuedItemsForStore((int) $request->department, 'id', 'desc', [
           'start_date' => $request->start_date,
           'end_date' => $request->end_date,
       ]);
        $store = Store::find($request->department);

    return view(
        'report.printIssuedItemDate',
        compact('liststock', 'store','liststores', 'startDate',
            'endDate')
    );
    }


     public function getsearchIssuedItemByDateIntevView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
        $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

        return view('report.searchIssuedItemByDateIntev', ['liststores'=>$liststores,'getItemid'=>$getItemid]);
    }

     public function searchIssuedItemByDateIntev(Request $request)
    {
          
         $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'item' => 'required',
        'department' => 'required',
    ]);

       
        // All stores
        $liststores = Store::all();
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()
       $liststock = $this->issuedItemsForStore((int) $request->department, 'id', 'desc', [
           'item_id' => $request->item,
           'start_date' => $request->start_date,
           'end_date' => $request->end_date,
       ]);
        if ($liststock->count() > 0) {

            return view(
                'report.searchIssuedItemByDateIntev',
                compact('liststock', 'liststores' ,'getItemid' )
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.searchIssuedItemByDateIntev',
                compact('liststock', 'liststores','getItemid')
            )->with(
                'message_error',
                'No Items found'
            );
        }
    }


     public function printIssuedItemDateIntervalReport(Request $request)
    {
        

       
        // All stores
        $liststores = Store::all();
         $startDate = $request->start_date;
          $endDate   = $request->end_date;

         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()
       $liststock = $this->issuedItemsForStore((int) $request->department, 'id', 'desc', [
           'item_id' => $request->item,
           'start_date' => $request->start_date,
           'end_date' => $request->end_date,
       ]);
       $store = Store::find($request->department);

    return view(
        'report.printIssuedItemByDateInterval',
        compact('liststock', 'store','liststores', 'startDate',
            'endDate','getItemid')
    );
    }


     public function printSummaryReport(Request $request)
    {
        
      $liststores = Store::all();

        // Search Item
     $liststock = Item::with(['approveStock'])
    ->where('status', 'Active')
    ->where('store_id', $request->department)
    ->get();

     return view(
        'report.printSummaryReport',
        compact('liststock', 'store','liststores', 'startDate',
            'endDate','getItemid')
    );

        
        
    
    }

    //get Search Item By Department View

     public function searchIssueItemByDepartmentView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get();  
        return view('report.searchIssuedItemByDepartment',['liststores'=>$liststores]);
    }
    //Search Issued Item By Department
     public function searchIssuedItemByDeprtReport(Request $request)
    {
          
        $request->validate([
            'department' => 'required',
         
        ]);

       
        
        
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

         // All stores
         $liststores = Store::whereIn('id',$listdept)->get();

       $liststock = ItemIssue::where('issue_to', $request->department)
    ->where(function($query) {
        $query->where('status', 'issued');
            
    })
    ->orderBy('id', 'DESC')
    ->get();
        if ($liststock->count() > 0) {

            return view(
                'report.searchIssuedItemByDepartment',
                compact('liststock', 'liststores' ,  'getItemid')
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.searchIssuedItemByDepartment',
                compact('liststock', 'liststores','getItemid')
            )->with(
                'message_error',
                'No Items found'
            );
        }
    }

      public function printIssuedItemByDepartReport(Request $request)
    {
          
       
        
        
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()R

         // All stores
        $liststores = Store::whereIn('id',$listdept)->get();
      $liststock = ItemIssue::where('issue_to', $request->department)
    ->where(function($query) {
        $query->where('status', 'issued');
    })
   
    ->orderBy('created_at', 'desc')
    ->get();
      
    $store = Store::find($request->department);
        return view(
        'report.printIssuedItemByDepartmentReport',
        compact('liststock', 'liststores','getItemid','store')
    );
    }

    //Search By Issued Items to Department

      public function getsearchByIssueItemDepartmentView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
        $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

        return view('report.searchByIssueItemDepartment', ['liststores'=>$liststores,'getItemid'=>$getItemid]);
    }

    public function searchIssuedItemDepartReport(Request $request)
    {
         $request->validate([
            'department' => 'required',
             'item' => 'required',
        ]);

       
        
        
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

          $liststores = Store::whereIn('id',$listdept)->get();

       $liststock = ItemIssue::where('issue_to', $request->department)
       ->where('item_id',$request->item)
    ->where(function($query) {
        $query->where('status', 'issued');
              
    })
    ->orderBy('invoice_number')
    ->get();
        if ($liststock->count() > 0) {

            return view(
                'report.searchByIssueItemDepartment',
                compact('liststock', 'liststores' ,  'getItemid')
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.searchByIssueItemDepartment',
                compact('liststock', 'liststores','getItemid')
            )->with(
                'message_error',
                'No Items found'
            );
    }
    }

      public function printIssuedByItemDepartReport(Request $request)
    {
         
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

         $liststores = Store::whereIn('id',$listdept)->get();

       $liststock = ItemIssue::where('issue_to', $request->department)
       ->where('item_id',$request->item)
    ->where(function($query) {
        $query->where('status', 'issued');
              
    })
    ->orderBy('invoice_number')
    ->get();
     $store = Store::find($request->department);
      return view(
        'report.printIssuedByItemDepartmentReport',
        compact('liststock', 'liststores','getItemid','store')
    );
    }

     public function getsearchByIssuedDateDepartView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
        $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

        return view('report.searchByIssuedDepartmentDate', ['liststores'=>$liststores,'getItemid'=>$getItemid]);
    }

     public function searchByIssuedDepartDate(Request $request)
    {
       $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'department' => 'required',
    ]);

       
       
      $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
       $liststock = ItemIssue::where('issue_to', $request->department)
    ->where(function($query) {
        $query->where('status', 'issued');
            
    }) ->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ])
 
    ->orderBy('id', 'DESC')
    ->get();

 
        if ($liststock->count() > 0) {

            return view(
                'report.searchByIssuedDepartmentDate',
                compact('liststock', 'liststores'  )
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.searchByIssuedDepartmentDate',
                compact('liststock', 'liststores')
            )->with(
                'message_error',
                'No Items found'
            );
        }
    }

       public function printIssuedItemDateDepartReport(Request $request)
    {
      
        

           $startDate = $request->start_date;
          $endDate   = $request->end_date;
       $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
       $liststock = ItemIssue::where('issue_to', $request->department)
    ->where(function($query) {
        $query->where('status', 'issued');
            
    }) ->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ])
 
    ->orderBy('id', 'DESC')
    ->get();
        $store = Store::find($request->department);

    return view(
        'report.printIssuedItemDepartmentDate',
        compact('liststock', 'store','liststores', 'startDate',
            'endDate')
    );
    }

     public function getsearchIssuedItemByDepartDateIntevView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
        $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

        return view('report.searchIssuedItemByDepartmentDateIntev', ['liststores'=>$liststores,'getItemid'=>$getItemid]);
    }

     public function searchIssuedItemByDepartDateIntev(Request $request)
    {
          
         $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'item' => 'required',
    ]);

       
        // All stores
        
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()
         $liststores = Store::whereIn('id', $listdept)->get(); 
        $liststock = ItemIssue::where('issue_to', $request->department)
       ->where('item_id',$request->item)
    ->where(function($query) {
        $query->where('status', 'issued');
              
    }) ->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ])
 
    ->orderBy('id', 'DESC')
    ->get();
        if ($liststock->count() > 0) {

            return view(
                'report.searchIssuedItemByDepartmentDateIntev',
                compact('liststock', 'liststores' ,'getItemid' )
            )->with(
                'message_success',
                $liststock->count().' Items(s) found'
            );

        } else {

            return view(
                'report.searchIssuedItemByDepartmentDateIntev',
                compact('liststock', 'liststores','getItemid')
            )->with(
                'message_error',
                'No Items found'
            );
        }
    }
 

  public function printIssuedItemDateIntervalDepartReport(Request $request)
    {
        

       
        // All stores
        
         $startDate = $request->start_date;
          $endDate   = $request->end_date;

         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
         $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()
         $liststores = Store::whereIn('id', $listdept)->get(); 
       $liststock = ItemIssue::where('issue_to', $request->department)
       ->where('item_id',$request->item)
    ->where(function($query) {
        $query->where('status', 'issued');
              
    }) ->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ])
 
    ->orderBy('id', 'DESC')
    ->get();
       $store = Store::find(Auth::user()->department_id);

    return view(
        'report.printIssuedItemByDateDepartInterval',
        compact('liststock', 'store','liststores', 'startDate',
            'endDate','getItemid')
    );
    }


}
