<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Models\ApproveStock;
use App\Models\Item;
use App\Models\ItemIssue;
use App\Models\ItemRequest;
use App\Models\Store;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class RequisitionController extends Controller
{
    public function getRequisitionView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $getItemid = Item::where('status', 'Active')
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

             $storeId = Auth::user()->department_id;

            ItemRequest::create([
                'stock_id'       => $stock->stock_id,
                'item_id'        => $stock->item_id,
                'batch_number'   => $request->batch_number,
                'qty'            => $request->quantity,
                'amount'         => $stock->amount,
                'item_store_id'  => $stock->store_id,
                'store_id'  => $storeId,
                'created_by'     => Auth::id(),
            ]);

        }

        return back()->with(
            'message_success',
            'Item successfully added'
        );
    }

       public function deleteitemRequest(string $id)
    {
        ItemRequest ::where('id',$id)->delete();
        

        return redirect('Requisition')->with('message_success','Item deleted successfully!');
    }

    public function submitRequest()
    {
        $storeId = Auth::user()->department_id;
        $date = now()->format('Ymd');

        // Get the last requisition number today
        $lastRecord = ItemRequest::whereDate('created_at', today())
            ->whereNotNull('requisition_no')
            ->latest('id')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->requisition_no, -4);
        } else {
            $lastNumber = 0;
        }

        $requests = ItemRequest::where('status', 'pending')
            ->where('store_id', $storeId)
            ->get();

        foreach ($requests as $item) {  // ← don't reuse $request variable name
            $lastNumber++;  // ← increment INSIDE the loop

            $nextNumber = str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
            $requestNo  = 'REQ-' . $date . '-' . $nextNumber;

            ItemRequest::where('id', $item->id)
                ->update([
                    'status'        => 'pending request',
                    'requisition_no' => $requestNo,
                ]);
        }

        return back()->with('message_success', 'Request submitted successfully');
    }

   public function getMyRequestView()
{
    $listdept = array_map('intval', explode('~', Auth::user()->department_id));

    $listrequest = ItemRequest::where('store_id', $listdept)
        ->orderBy('id', 'DESC')
        ->get();

 

    return view('requisition.MyRequest', ['listrequest' => $listrequest]);
}

public function getApproveRequestView()
{
    $listdept = array_map('intval', explode('~', Auth::user()->department_id));

  $listrequest = ItemRequest::where('store_id', $listdept)
    ->whereIn('id', function ($query) use ($listdept) {
        $query->selectRaw('MAX(id)')
            ->from('item_requests')
            ->where('store_id', $listdept)
              ->where('status','pending request')
            ->groupBy('requisition_no');
    })
    ->orderBy('id', 'DESC')
    ->get();
 

    return view('requisition.ApproveRequest', ['listrequest' => $listrequest]);
}

    public function getviewRequest($requisition_no)
    {
        $listdept = array_map('intval', explode('~', Auth::user()->department_id));

    $decodeID = Crypt::decrypt($requisition_no);

        $listrequest = ItemRequest::where('store_id', $listdept)
        ->where('requisition_no',$decodeID)
        ->where('status','pending request')
            ->orderBy('id', 'DESC')
            ->get();
    

        return view('requisition.viewRequest', ['listrequest' => $listrequest]);
    }

    public function getrequesttemID($id)
    {
         $data = ItemRequest::findOrFail($id);
          return response()->json($data);
    }

    public function addItemRejectRequest(Request $request)
    {
         $request->validate([
            'item_id' =>'required',
            'reason' =>'required'
           
        ]);

       
            $insertCat = ItemRequest::find($request->item_id);
            $insertCat->status = "rejected";
            $insertCat->reason = trim($request->reason);
            $insertCat->approved_by = Auth::User()->id;
             
             
            $status = $insertCat->save();

            return $status ? back()->with('message_success','Request has been rejected successfully') : back()->with('message_error','Something went wrong, please try again.');
    }

       public function addApproveRequest(Request $request)
    {
        foreach ($request->request_id as $requestId) {

        $itemRequest = ItemRequest::where('id', $requestId)
            ->where('status', 'pending request')
            ->first();

        if (!$itemRequest) {
            continue;
        }

        // Get qty from form input array using the request ID as key
        $approvedQty = $request->qty[$requestId] ?? $itemRequest->qty;

        $itemRequest->qty         = $approvedQty;
        $itemRequest->status      = 'request approved';
        $itemRequest->approved_by = Auth::user()->id;
        $itemRequest->save();
    }

    return back()->with('message_success', 'Requisition approved successfully');
    }


    public function getPickListView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id));

  $listrequest = ItemIssue::where('store_id', $listdept)
    ->whereIn('id', function ($query) use ($listdept) {
        $query->selectRaw('MAX(id)')
            ->from('item_issues')
            ->where('store_id', $listdept)
              ->where('status','issued')
            ->groupBy('requisition_no');
    })
    ->orderBy('id', 'DESC')
    ->get();
 

    return view('requisition.PickList', ['listrequest' => $listrequest]);
    }

     public function getviewPickList($requisition_no)
    {
        $listdept = array_map('intval', explode('~', Auth::user()->department_id));

    $decodeID = Crypt::decrypt($requisition_no);

        $listrequest = ItemIssue::where('store_id', $listdept)
        ->where('requisition_no',$decodeID)
        ->where('status','issued')
            ->orderBy('id', 'DESC')
            ->get();
    

        return view('requisition.viewPickUp', ['listrequest' => $listrequest]);
    }

      public function printPickList($invoice)
{
    // Decrypt if you encrypted it when passing via URL
    try {
        $decodeID = Crypt::decrypt($invoice);
    } catch (\Exception $e) {
        return redirect()->back()->with('message_error', 'Invalid invoice reference.');
    }

    $issues = ItemIssue::where('invoice_number', $decodeID)->first();

    if (!$issues) {
        return redirect()->back()->with('message_error', 'Invoice not found.');
    }

    $store   = Store::find($issues->store_id);
    $issueto = Store::find($issues->issue_to);

    $listissues = ItemIssue::where('invoice_number', $decodeID)->get();

    return view('requisition.print', compact('issues', 'decodeID', 'store', 'issueto', 'listissues'));
}
    
}
