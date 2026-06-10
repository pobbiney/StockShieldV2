<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Models\ApproveStock;
use App\Models\Item;
use App\Models\ItemIssue;
use App\Models\ItemRequest;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequisitionController extends Controller
{
    public function getRequisitionView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $getItemid = Item::whereIn('store_id', $listdept)
        ->where('status','Active')
        ->get(); // fix: whereIn + get()
        $liststore = Store::all();

        
          $listitemissue = ItemRequest::where('item_store_id',$listdept)
        ->where('status','pending')
        ->get();
        return view('requisition.Requisition', ['getItemid'=>$getItemid,'liststore'=>$liststore,'listitemissue'=>$listitemissue]);
    }

        public function addRequest(Request $request)
    {
        $request->validate([
            'item'      => 'required',
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
                'Selected item has no Quantity.'
            );
        }

          foreach ($stocks as $stock) {

           

            ItemRequest::create([
                'stock_id'       => $stock->stock_id,
                'item_id'        => $stock->item_id,
                'batch_number'   => $request->batch_number,
                'qty'            => $request->quantity,
                'amount'         => $stock->amount,
                'item_store_id'  => $stock->store_id,
                'created_by'     => Auth::id(),
            ]);

        }

        return back()->with(
            'message_success',
            'Item successfully added'
        );
    }
}
