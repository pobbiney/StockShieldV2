<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    $liststock = Stock::where('store_id', $department)
        ->where(function($query) {

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

}
