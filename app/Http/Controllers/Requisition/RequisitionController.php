<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Models\Item;
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
        

        
        return view('requisition.Requisition', ['getItemid'=>$getItemid,'liststore'=>$liststore]);
    }
}
