<?php

namespace App\Http\Controllers\Issues;

use App\Http\Controllers\Controller;
use App\Models\ApproveStock;
use App\Models\Item;
use App\Models\ItemIssue;
use App\Models\ItemRequest;
use App\Models\SystemNotifications;
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
        $requestIds = $request->input('request_id', []);
        $qtyInputs  = $request->input('qty', []);

        if (empty($requestIds)) {
            return back()->with('message_error', 'No items selected to issue.');
        }

        $allWarnings   = [];
        $hasHardFailure = false;

        foreach ($requestIds as $itemRequestId) {
            $itemRequest = ItemRequest::find($itemRequestId);

            if (!$itemRequest) {
                continue;
            }

            $qtyToIssue = $qtyInputs[$itemRequestId] ?? null;

            if (!$qtyToIssue || $qtyToIssue <= 0) {
                continue;
            }

            $remainingQty = $qtyToIssue;

            $batches = ApproveStock::where('item_id', $itemRequest->item_id)
                ->where('status', 'approved')
                ->where('qty', '>', 0)
                ->whereDate('expiry_date', '>=', now())
                ->orderBy('expiry_date', 'ASC')
                ->get();

            if ($batches->isEmpty()) {
                $allWarnings[] = "{$itemRequest->itemname->name}: no stock available.";
                $hasHardFailure = true;
                continue;
            }

            foreach ($batches as $stock) {
                if ($remainingQty <= 0) break;

                $qtyToTake = min($remainingQty, $stock->qty);

                ItemIssue::create([
                'stock_id'         => $stock->stock_id,
                    'item_id'          => $stock->item_id,
                    'batch_number'     => $stock->batch_number,
                    'qty_requested'     => $itemRequest->qty_requested,
                    'qty'       => $qtyToTake,
                    'amount'       => $stock->amount,
                    
                    'requisition_no' => $itemRequest->requisition_no,
                    'issue_to'       => $itemRequest->store_id,
                    'store_id'       => $itemRequest->item_store_id,
                    'created_by'       => Auth::id(),
                ]);

                $daysToExpiry = now()->diffInDays($stock->expiry_date, false);
                if ($daysToExpiry <= 30) {
                    $allWarnings[] = "Batch {$stock->batch_number} for {$itemRequest->itemname->name} expires in {$daysToExpiry} day(s).";
                }

                $remainingQty -= $qtyToTake;
            }

            if ($remainingQty > 0) {
                $fulfilled = $qtyToIssue - $remainingQty;
                $allWarnings[] = "{$itemRequest->itemname->name}: only {$fulfilled} of {$qtyToIssue} could be issued.";
            }

            $itemRequest->update(['status' => 'issued']);
        }

        $message = 'Item(s) issued and sent for HOD approval.';

        if (!empty($allWarnings)) {
            $message .= ' ' . implode(' ', $allWarnings);
        }

        if ($hasHardFailure) {
            return back()->with('message_error', $message);
        }

        return back()->with('message_success', $message);
    }
}
