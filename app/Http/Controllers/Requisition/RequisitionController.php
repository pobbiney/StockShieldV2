<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Models\ApproveStock;
use App\Models\Item;
use App\Models\ItemIssue;
use App\Models\ItemRequest;
use App\Models\ReturnItem;
use App\Models\Stock;
use App\Models\Store;
use App\Models\SystemNotifications;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RequisitionController extends Controller
{
    public function getRequisitionView()
    {
         $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
        $getItemid = Item::where('status', 'Active')
        ->where('status','Active')
        ->get(); // fix: whereIn + get()
        $liststore = Store::all();

        
          $listitemissue = ItemRequest::where('store_id',$listdept)
        ->where('status','pending')
        ->get();
         
        return view('requisition.Requisition', ['getItemid'=>$getItemid,'liststore'=>$liststore,'listitemissue'=>$listitemissue]);
    }

         public function addRequest(Request $request)
        {
            $request->validate([
                'item'     => 'required',
                'quantity' => 'required|numeric|min:1',
                'stock_id' => 'required',
            ]);

            $stock = ApproveStock::where('status', 'approved')
                        ->where('stock_id', $request->stock_id)
                        ->first();

            if (!$stock) {
                return back()->with('message_error', 'Selected item has no Quantity.');
            }

            // New check: requested quantity vs available stock
            if ($request->quantity > $stock->qty) {
                return back()->with(
                    'message_error',
                    'Requested quantity (' . $request->quantity . ') exceeds available stock (' . $stock->qty . ').'
                );
            }

            try {
                ItemRequest::create([
                    'stock_id'      => $stock->stock_id,
                    'item_id'       => $stock->item_id,
                    'batch_number'  => $request->batch_number,
                    'qty_requested' => $request->quantity,
                    'amount'        => $stock->amount,
                    'item_store_id' => $stock->store_id,
                    'store_id'      => Auth::user()->department_id,
                    'created_by'    => Auth::user()->id,
                ]);

            } catch (\Exception $e) {
                return back()->with('message_error', 'Something went wrong: ' . $e->getMessage());
            }

            return back()->with('message_success', 'Item successfully added');
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

    $lastRecord = ItemRequest::whereDate('created_at', today())
        ->whereNotNull('requisition_no')
        ->latest('id')
        ->first();

    $lastNumber = $lastRecord ? (int) substr($lastRecord->requisition_no, -4) : 0;

    $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    $requestNo  = 'REQ-' . $date . '-' . $nextNumber;

    $requests = ItemRequest::where('status', 'pending')
        ->where('store_id', $storeId)
        ->get();

    if ($requests->isEmpty()) {
        return back()->with('message_error', 'No pending items to submit.');
    }

    foreach ($requests as $item) {
        $item->update([
            'status'         => 'pending request',
            'requisition_no' => $requestNo,
        ]);
    }

    // ONE notification for the whole requisition, not per item
    $itemCount = $requests->count();

    SystemNotifications::create([
        'title'        => 'New Item Request',
        'message'      => "Requisition {$requestNo} submitted with {$itemCount} item(s), needs approval.",
        //'link'         => route('pending-requests', ['requisition_no' => $requestNo]),
        'type'         => 'new_request',
        'reference_id' => $requestNo,
    ]);

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

    $listrequest = ItemRequest::whereIn('store_id', $listdept)
        ->whereIn('id', function ($query) use ($listdept) {
            $query->selectRaw('MAX(id)')
                ->from('item_requests')
                ->whereIn('store_id', $listdept)
                ->where('status', 'pending request')
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

        $listrequest = ItemRequest::whereIn('store_id', $listdept)
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

    return redirect()->route('ApproveRequest')
            ->with('message_success', 'Request approved successfully');
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

    $issues = ItemIssue::where('invoice_number', $decodeID)->
    where('store_id', Auth::user()->department_id)->

    first();

    if (!$issues) {
        return redirect()->back()->with('message_error', 'Invoice not found.');
    }

    $store   = Store::find($issues->store_id);
    $issueto = Store::find($issues->issue_to);

    $listissues = ItemIssue::where('invoice_number', $decodeID)->where('store_id', Auth::user()->department_id)->get();

    return view('requisition.print', compact('issues', 'decodeID', 'store', 'issueto', 'listissues'));
}
    

    public function getReturnView()
    {

      $listdept = array_map('intval', explode('~', Auth::user()->department_id)); // cast to int
       
         
        $liststock = ApproveStock::whereIn('store_id',$listdept)
        ->where('status','approved')
        ->orderBy('id','DESC')
        ->get();
    return view('requisition.Return',['liststock'=>$liststock]);
    }

     public function getreturnItemID($id)
    {
         $data = ApproveStock::findOrFail($id);
          return response()->json($data);
    }

     public function addReturn(Request $request)
    {
         $request->validate([
            'quantity' =>'required',
            'comment' =>'required',
            'return_status' =>'required'
           
        ]);

       
            $insertCat = new ReturnItem();
            
            $insertCat->item_id = trim($request->item_id);
            $insertCat->batch_number = trim($request->batch_number);
            $insertCat->quantity = trim($request->quantity);
            $insertCat->manager_comment = trim($request->comment);
            $insertCat->return_status = trim($request->return_status);
            $insertCat->returned_by = Auth::User()->id;
            $status = $insertCat->save();

              // Update original stock table
           ApproveStock::where('batch_number', $request->batch_number)
            ->update([
                'status' => 'return initiated'
            ]);

            return $status ? back()->with('message_success','Process initiated successfully, Please wait for approval') : back()->with('message_error','Something went wrong, please try again.');
    }

    public function getReturnApprovalView()
    {
          
        $listItem = ReturnItem::where('status','return initiated')
        
        ->orderBy('id','DESC')
        ->get();

        
    return view('requisition.ReturnApproval',['listItem'=>$listItem]);
    }

     

     public function getreturnItemApprovalID($id)
    {
         $data = ReturnItem::findOrFail($id);
          return response()->json($data);
    }

    public function addReturnApproval(Request $request)
{
    $request->validate([
        'comment' => 'required|string',
        'status' => 'required|string',
        'item_id' => 'required|exists:return_items,id',
        'batch_number' => 'required|exists:approve_stocks,batch_number',
        'qty' => 'required|numeric|min:0.01',
    ]);

    try {
        DB::transaction(function () use ($request) {
            $insertCat = ReturnItem::findOrFail($request->item_id);
            $insertCat->status = 'approved';
            $insertCat->hod_comment = trim($request->comment);
            $insertCat->approved_by_hod = Auth::id();
            $insertCat->save();

            $data = ApproveStock::where('batch_number', $request->batch_number)->firstOrFail();

            if ($request->qty < $data->qty) {
                $data->update([
                    'qty' => $data->qty - $request->qty,
                    'status' => 'returned',
                ]);
            } else {
                $data->update([
                     'status' => 'returned',
                ]);
            }
        });
    } catch (\Exception $e) {
        return back()->with('message_error', 'Something went wrong, please try again.');
    }

    return back()->with('message_success', 'Process approved successfully');
}

 public function getIssuedItems($requisition_no)
    {
        $listdept = array_map('intval', explode('~', Auth::user()->department_id));

    $decodeID = Crypt::decrypt($requisition_no);

        $listissues = ItemIssue::whereIn('store_id', $listdept)
        ->where('requisition_no',$decodeID)
        ->where('status','pending')
            ->orderBy('id', 'DESC')
            ->get();

           $batchNumbers = $listissues->pluck('batch_number')->unique();

    $itembalance = ApproveStock::whereIn('batch_number', $batchNumbers)
        ->get()
        ->keyBy('batch_number');

        return view('requisition.viewIssues', ['listissues' => $listissues,'itembalance'=>$itembalance]);
    }
}
