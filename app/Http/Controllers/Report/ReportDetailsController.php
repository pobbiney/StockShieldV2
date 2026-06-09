<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportDetailsController extends Controller
{
    public function getCommodityReportView()
    {
        

        return view('report.CommodityReport' );
    }


    public function searchCommodityReport(Request $request)
    {
             // Get filter inputs (only date filters)
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        
        // Get ALL stores from stores table
        $stores = DB::table('stores')
            ->where('status', 'Active')
            ->select('id', 'name')
            ->orderBy('id')
            ->get();
        
        $reportData = [];
        
        foreach ($stores as $store) {
            // 1. OPENING BALANCE VALUE (from approve_stocks BEFORE start date for this store)
            $openingBalance = DB::table('approve_stocks')
                ->where('store_id', $store->id)
                ->where('status', 'approved')
                ->when($start_date, function($q) use ($start_date) {
                    return $q->where('created_at', '<', $start_date);
                })
                ->select(DB::raw('COALESCE(SUM(qty * amount), 0) as total_value'))
                ->first();
            
            // 2. RECEIPTS VALUE (from stocks table DURING period for this store)
            $receipts = DB::table('stocks')
                ->where('store_id', $store->id)
                ->when($start_date, function($q) use ($start_date) {
                    return $q->where('created_at', '>=', $start_date);
                })
                ->when($end_date, function($q) use ($end_date) {
                    return $q->where('created_at', '<=', $end_date . ' 23:59:59');
                })
                ->select(DB::raw('COALESCE(SUM(qty * amount), 0) as total_value'))
                ->first();
            
            // 3. ISSUES VALUE (from item_issues during period for this store)
            $issues = DB::table('item_issues')
                ->where('store_id', $store->id)
                ->where('status', 'issued')
                ->when($start_date, function($q) use ($start_date) {
                    return $q->where('created_at', '>=', $start_date);
                })
                ->when($end_date, function($q) use ($end_date) {
                    return $q->where('created_at', '<=', $end_date . ' 23:59:59');
                })
                ->select(DB::raw('COALESCE(SUM(qty * amount), 0) as total_value'))
                ->first();
            
            // 4. CLOSING BALANCE VALUE (from approve_stocks UP TO end date for this store)
            $closingBalance = DB::table('approve_stocks')
                ->where('store_id', $store->id)
                ->where('status', 'approved')
                ->when($end_date, function($q) use ($end_date) {
                    return $q->where('created_at', '<=', $end_date . ' 23:59:59');
                })
                ->select(DB::raw('COALESCE(SUM(qty * amount), 0) as total_value'))
                ->first();
            
            // Calculate values
            $balanceBfValue = $openingBalance->total_value;
            $receiptsValue = $receipts->total_value;
            $totalStockValue = $balanceBfValue + $receiptsValue;
            $issuedValue = $issues->total_value;
            $closingValue = $closingBalance->total_value;
            
            // Store all stores (even with zero values)
            $reportData[] = [
                'id' => $store->id,
                'store_name' => $store->name,
                'balance_bf_value' => number_format($balanceBfValue, 2),
                'receipts_value' => number_format($receiptsValue, 2),
                'total_stock_value' => number_format($totalStockValue, 2),
                'issued_value' => number_format($issuedValue, 2),
                'closing_balance_value' => number_format($closingValue, 2),
            ];

            $totalBF = array_sum(array_map(function($item)  { return floatval(str_replace(',','',$item['balance_bf_value'] ?? 0));}, $reportData));
            $totalREc = array_sum(array_map(function($item)  { return floatval(str_replace(',','',$item['receipts_value'] ?? 0));}, $reportData));
            $totalStockval = array_sum(array_map(function($item)  { return floatval(str_replace(',','',$item['total_stock_value'] ?? 0));}, $reportData));
            $totalIssVal = array_sum(array_map(function($item)  { return floatval(str_replace(',','',$item['issued_value'] ?? 0));}, $reportData));
            $totalClVal = array_sum(array_map(function($item)  { return floatval(str_replace(',','',$item['closing_balance_value'] ?? 0));}, $reportData));
        }
        
        return view('report.CommodityReport', compact('reportData', 'start_date', 'end_date','totalBF','totalREc','totalStockval','totalIssVal','totalClVal'));
    
    }

    public function printCommoditySummaryReport(Request $request)
    {
      
              // Get filter inputs (only date filters)
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        
        // Get ALL stores from stores table
        $stores = DB::table('stores')
            ->where('status', 'Active')
            ->select('id', 'name')
            ->orderBy('id')
            ->get();
        
        $reportData = [];
        
        foreach ($stores as $store) {
            // 1. OPENING BALANCE VALUE (from approve_stocks BEFORE start date for this store)
            $openingBalance = DB::table('approve_stocks')
                ->where('store_id', $store->id)
                ->where('status', 'approved')
                ->when($start_date, function($q) use ($start_date) {
                    return $q->where('created_at', '<', $start_date);
                })
                ->select(DB::raw('COALESCE(SUM(qty * amount), 0) as total_value'))
                ->first();
            
            // 2. RECEIPTS VALUE (from stocks table DURING period for this store)
            $receipts = DB::table('stocks')
                ->where('store_id', $store->id)
                ->when($start_date, function($q) use ($start_date) {
                    return $q->where('created_at', '>=', $start_date);
                })
                ->when($end_date, function($q) use ($end_date) {
                    return $q->where('created_at', '<=', $end_date . ' 23:59:59');
                })
                ->select(DB::raw('COALESCE(SUM(qty * amount), 0) as total_value'))
                ->first();
            
            // 3. ISSUES VALUE (from item_issues during period for this store)
            $issues = DB::table('item_issues')
                ->where('store_id', $store->id)
                ->where('status', 'issued')
                ->when($start_date, function($q) use ($start_date) {
                    return $q->where('created_at', '>=', $start_date);
                })
                ->when($end_date, function($q) use ($end_date) {
                    return $q->where('created_at', '<=', $end_date . ' 23:59:59');
                })
                ->select(DB::raw('COALESCE(SUM(qty * amount), 0) as total_value'))
                ->first();
            
            // 4. CLOSING BALANCE VALUE (from approve_stocks UP TO end date for this store)
                $closingBalance = DB::table('approve_stocks')
                ->where('store_id', $store->id)
                ->where('status', 'approved')
                ->when($end_date, function($q) use ($end_date) {
                    return $q->where('created_at', '<=', $end_date . ' 23:59:59');
                })
                ->select(DB::raw('COALESCE(SUM(qty * amount), 0) as total_value'))
                ->first();
            
            // Calculate values
            $balanceBfValue = $openingBalance->total_value;
            $receiptsValue = $receipts->total_value;
            $totalStockValue = $balanceBfValue + $receiptsValue;
            $issuedValue = $issues->total_value;
            $closingValue = $closingBalance->total_value;
            
            // Store all stores (even with zero values)
            $reportData[] = [
                'id' => $store->id,
                'store_name' => $store->name,
                'balance_bf_value' => number_format($balanceBfValue, 2),
                'receipts_value' => number_format($receiptsValue, 2),
                'total_stock_value' => number_format($totalStockValue, 2),
                'issued_value' => number_format($issuedValue, 2),
                'closing_balance_value' => number_format($closingValue, 2),
            ];

            $totalBF = array_sum(array_map(function($item)  { return floatval(str_replace(',','',$item['balance_bf_value'] ?? 0));}, $reportData));
            $totalREc = array_sum(array_map(function($item)  { return floatval(str_replace(',','',$item['receipts_value'] ?? 0));}, $reportData));
            $totalStockval = array_sum(array_map(function($item)  { return floatval(str_replace(',','',$item['total_stock_value'] ?? 0));}, $reportData));
            $totalIssVal = array_sum(array_map(function($item)  { return floatval(str_replace(',','',$item['issued_value'] ?? 0));}, $reportData));
            $totalClVal = array_sum(array_map(function($item)  { return floatval(str_replace(',','',$item['closing_balance_value'] ?? 0));}, $reportData));
        }

        return view('report.printCommoditySummaryReport', compact('reportData', 'start_date', 'end_date','totalBF','totalREc','totalStockval','totalIssVal','totalClVal'));
    }
    
    public function getDetailedCommodityReportView()
    {
        $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $liststores = Store::whereIn('id', $listdept)->get(); 
        $getItemid = Item::whereIn('store_id', $listdept)->get(); // fix: whereIn + get()

        return view('report.DetailedCommodityReport', ['liststores'=>$liststores,'getItemid'=>$getItemid]);
    }

   
     

    public function searchCommodityDetailReport(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    $store_id = $request->department;

    $liststores = Store::where('status', 'Active')->get();

    $items = DB::table('items as i')
        ->leftJoin('unit_of_measures as u', 'i.unit_id', '=', 'u.id')
        ->where('i.status', 'Active')
        ->when($store_id, fn($q) => $q->where('i.store_id', $store_id))
        ->select('i.id', 'i.name', 'i.store_id', 'u.name as uom')
        ->orderBy('i.id')
        ->get();

    // 🔥 reusable function (IMPORTANT)
    $sumStock = function ($table, $itemId, $storeId, $start = null, $end = null, $approved = false) {

        return DB::table($table)
            ->where('item_id', $itemId)
            ->where('store_id', $storeId)
            ->when($approved, fn($q) => $q->where('status', 'approved'))
            ->when($start, fn($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn($q) => $q->where('created_at', '<=', $end . ' 23:59:59'))
            ->selectRaw('COALESCE(SUM(qty),0) as qty, COALESCE(SUM(qty * amount),0) as value')
            ->first();
    };

    $reportData = [];

    foreach ($items as $item) {

        // 🟡 Opening balance
        $opening = $sumStock('approve_stocks', $item->id, $item->store_id, null, $start_date, true);

        // 🟢 Receipts (stocks table as you insisted)
        $receipts = $sumStock('stocks', $item->id, $item->store_id, $start_date, $end_date);

        // 🔴 Issues
        $issues = $sumStock('item_issues', $item->id, $item->store_id, $start_date, $end_date);

        // 📦 Closing balance (approved stock up to end date)
        $closing = $sumStock('approve_stocks', $item->id, $item->store_id, null, $end_date, true);

        // 💰 Price (simple average)
        $avgPrice = DB::table('stocks')
            ->where('item_id', $item->id)
            ->where('store_id', $item->store_id)
            ->selectRaw('COALESCE(SUM(qty * amount) / NULLIF(SUM(qty),0),0) as price')
            ->first();

        $reportData[] = [
            'id' => $item->id,
            'item_description' => $item->name,
            'uom' => $item->uom ?? 'N/A',

            'price' => number_format($avgPrice->price, 2),

            'balance_bf_qty' => $opening->qty,
            'balance_bf_value' => $opening->value,

            'receipts_qty' => $receipts->qty,
            'receipts_value' => $receipts->value,

            'total_stock_qty' => $opening->qty + $receipts->qty,
            'total_stock_value' => $opening->value + $receipts->value,

            'issued_qty' => $issues->qty,
            'issued_value' => $issues->value,

            'closing_balance_qty' => $closing->qty,
            'closing_balance_value' => $closing->value,
        ];
    }

    return view('report.DetailedCommodityReport', compact(
        'reportData',
        'liststores',
        'start_date',
        'end_date',
        'store_id'
    ));
}

    public function printCommodityDetailReport(Request $request)
{
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    $store_id = $request->department;

    $liststores = Store::where('status', 'Active')->get();

    $items = DB::table('items as i')
        ->leftJoin('unit_of_measures as u', 'i.unit_id', '=', 'u.id')
        ->where('i.status', 'Active')
        ->when($store_id, fn($q) => $q->where('i.store_id', $store_id))
        ->select('i.id', 'i.name', 'i.store_id', 'u.name as uom')
        ->orderBy('i.id')
        ->get();

    // 🔥 reusable function (IMPORTANT)
    $sumStock = function ($table, $itemId, $storeId, $start = null, $end = null, $approved = false) {

        return DB::table($table)
            ->where('item_id', $itemId)
            ->where('store_id', $storeId)
            ->when($approved, fn($q) => $q->where('status', 'approved'))
            ->when($start, fn($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn($q) => $q->where('created_at', '<=', $end . ' 23:59:59'))
            ->selectRaw('COALESCE(SUM(qty),0) as qty, COALESCE(SUM(qty * amount),0) as value')
            ->first();
    };

    $reportData = [];

    foreach ($items as $item) {

        // 🟡 Opening balance
        $opening = $sumStock('approve_stocks', $item->id, $item->store_id, null, $start_date, true);

        // 🟢 Receipts (stocks table as you insisted)
        $receipts = $sumStock('stocks', $item->id, $item->store_id, $start_date, $end_date);

        // 🔴 Issues
        $issues = $sumStock('item_issues', $item->id, $item->store_id, $start_date, $end_date);

        // 📦 Closing balance (approved stock up to end date)
        $closing = $sumStock('approve_stocks', $item->id, $item->store_id, null, $end_date, true);

        // 💰 Price (simple average)
        $avgPrice = DB::table('stocks')
            ->where('item_id', $item->id)
            ->where('store_id', $item->store_id)
            ->selectRaw('COALESCE(SUM(qty * amount) / NULLIF(SUM(qty),0),0) as price')
            ->first();

        $reportData[] = [
            'id' => $item->id,
            'item_description' => $item->name,
            'uom' => $item->uom ?? 'N/A',

            'price' => number_format($avgPrice->price, 2),

            'balance_bf_qty' => $opening->qty,
            'balance_bf_value' => $opening->value,

            'receipts_qty' => $receipts->qty,
            'receipts_value' => $receipts->value,

            'total_stock_qty' => $opening->qty + $receipts->qty,
            'total_stock_value' => $opening->value + $receipts->value,

            'issued_qty' => $issues->qty,
            'issued_value' => $issues->value,

            'closing_balance_qty' => $closing->qty,
            'closing_balance_value' => $closing->value,
        ];
    }

    return view('report.printCommodityDetailedReport', compact(
        'reportData',
        'liststores',
        'start_date',
        'end_date',
        'store_id'
    ));
}
}

