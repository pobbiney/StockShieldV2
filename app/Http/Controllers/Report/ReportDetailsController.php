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
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $report = collect();

        $stores = Store::all();

        foreach ($stores as $store) {

            $receivedBefore = DB::table('approve_stocks')
                ->where('store_id', $store->id)
                 ->where('status', 'approved')
                ->where('created_at', '<', $request->start_date)
                ->sum(DB::raw('qty * amount'));

            $issuedBefore = DB::table('item_issues')
                ->where('store_id', $store->id)
                ->where('status', 'issued')
                ->where('created_at', '<', $request->start_date)
                ->sum(DB::raw('qty * amount'));

            $balanceBF = $receivedBefore - $issuedBefore;

            $receiptValue = DB::table('approve_stocks')
                ->where('store_id', $store->id)
                ->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ])
                ->sum(DB::raw('qty * amount'));

            $issuedValue = DB::table('item_issues')
                ->where('store_id', $store->id)
                ->where('status', 'issued')
                ->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ])
                ->sum(DB::raw('qty * amount'));

            $totalStockValue = $balanceBF + $receiptValue;

            $closingBalance = $totalStockValue - $issuedValue;

            $report->push([
                'id' => $store->id,
                'store_name' => $store->name, // change if your field is location_name
                'balance_bf' => $balanceBF,
                'receipt_value' => $receiptValue,
                'total_stock_value' => $totalStockValue,
                'issued_value' => $issuedValue,
                'closing_balance' => $closingBalance,
            ]);
        }

        return view('report.CommodityReport', compact('report'));
    }

    public function printCommoditySummaryReport(Request $request)
    {
      
        $report = collect();

        $stores = Store::all();

        foreach ($stores as $store) {

            $receivedBefore = DB::table('approve_stocks')
                ->where('store_id', $store->id)
                 ->where('status', 'approved')
                ->where('created_at', '<', $request->start_date)
                ->sum(DB::raw('qty * amount'));

            $issuedBefore = DB::table('item_issues')
                ->where('store_id', $store->id)
                ->where('status', 'issued')
                ->where('created_at', '<', $request->start_date)
                ->sum(DB::raw('qty * amount'));

            $balanceBF = $receivedBefore - $issuedBefore;

            $receiptValue = DB::table('approve_stocks')
                ->where('store_id', $store->id)
                ->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ])
                ->sum(DB::raw('qty * amount'));

            $issuedValue = DB::table('item_issues')
                ->where('store_id', $store->id)
                ->where('status', 'issued')
                ->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ])
                ->sum(DB::raw('qty * amount'));

            $totalStockValue = $balanceBF + $receiptValue;

            $closingBalance = $totalStockValue - $issuedValue;

            $report->push([
                'id' => $store->id,
                'store_name' => $store->name, // change if your field is location_name
                'balance_bf' => $balanceBF,
                'receipt_value' => $receiptValue,
                'total_stock_value' => $totalStockValue,
                'issued_value' => $issuedValue,
                'closing_balance' => $closingBalance,
            ]);
        }

        return view('report.printCommoditySummaryReport', compact('report'));
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
}

