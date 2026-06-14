<?php

namespace App\Http\Controllers\Issues;

use App\Http\Controllers\Controller;
use App\Models\ApproveStock;
use App\Models\Item;
use App\Models\ItemIssue;
use App\Models\ItemRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class IssueController extends Controller
{
    

   public function getviewStoreRequest($requisition_no)
    {
        $listdept = array_map('intval', explode('~', Auth::user()->department_id));

    $decodeID = Crypt::decrypt($requisition_no);

        $listrequest = ItemRequest::where('item_store_id', $listdept)
        ->where('requisition_no',$decodeID)
        ->where('status','request approved')
            ->orderBy('id', 'DESC')
            ->get();
    

        return view('stock.viewStoreRequest', ['listrequest' => $listrequest]);
    }

    
    public function addIssueRequest(Request $request)
        {
          // VALIDATION PHASE
    foreach ($request->qty as $requestId => $issueQty) {

        $itemRequest = ItemRequest::find($requestId);

        $stock = ApproveStock::where('item_id', $itemRequest->item_id)
                    ->where('batch_number', $itemRequest->batch_number)
                    ->first();

        if (!$stock) {
            return redirect()->back()
                ->withInput()
                ->with('message_error',
                    "No stock found for {$itemRequest->itemname->name}");
        }

        // Check expiry
        if ($stock->expiry_date &&
            Carbon::parse($stock->expiry_date)->lte(Carbon::today())) {

            return redirect()->back()
                ->withInput()
                ->with('message_error',
                    "{$itemRequest->itemname->name} has expired.");
        }

        // Check stock balance
        if ($issueQty > $stock->qty) {

            return redirect()->back()
                ->withInput()
                ->with('message_error',
                    "{$itemRequest->itemname->name}: Requested {$issueQty} but only {$stock->qty} available.");
        }
    }

    // SAVE PHASE
    DB::beginTransaction();

    try {

        foreach ($request->qty as $requestId => $issueQty) {

            $itemRequest = ItemRequest::find($requestId);

            $stock = ApproveStock::where('item_id', $itemRequest->item_id)
                        ->where('batch_number', $itemRequest->batch_number)
                        ->first();

            // Save issue record
            ItemIssue::create([
            'stock_id'       => $itemRequest->stock_id,
            'item_id'        => $itemRequest->item_id,
            'batch_number'   => $itemRequest->batch_number,
            'qty'            => $issueQty,
            'amount'         => $itemRequest->amount,
            'requisition_no' => $itemRequest->requisition_no,
            'issue_to'       => $itemRequest->store_id,
            'store_id'       => $itemRequest->item_store_id,
            'created_by'     => Auth::id(),
            ]);

            
             $itemRequests = ItemRequest::find($requestId)
          
            ->update([
                'status' => 'pending issues'
            ]);
        }

        DB::commit();

        return redirect()->back()
            ->with('message_success', 'Items issued successfully.');

    } catch (\Exception $e) {

        DB::rollBack();

        return redirect()->back()
            ->with('message_error', $e->getMessage());
    }
        }
}
