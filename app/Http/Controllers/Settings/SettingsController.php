<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Store;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function getDepartmentView()
    {
        $list = Department::all();
        return view('settings.department',['list'=>$list]);
    }
     public function addDepartment(Request $request)
    {
         $request->validate([
            'name' => 'required',
            'status' => 'required',
            
            
        ]);
            $insertCat = new Department();
            $insertCat->name = trim($request->name);
            $insertCat->status = $request->status;
          
           
            $insertCat = $insertCat->save();
            return $insertCat ? back()->with('message_success','Department   added successfully') : back()->with('message_error','Something went wrong, please try again.');
    }
   
    public function getdepartmentID($id)
    {
         $data = Department::findOrFail($id);
          return response()->json($data);
    }

    public function updateDepartment(Request $request)
    {
          $request->validate([
            'name' => 'required',
            'status' => 'required',
            
            
        ]);
            $insertCat = Department::find($request->loan_id);
            $insertCat->name = trim($request->name);
            $insertCat->status = $request->status;
            
            $insertCat = $insertCat->save();
            return $insertCat ? back()->with('message_success','Department updated successfully') : back()->with('message_error','Something went wrong, please try again.');
    }

    public function getStoreView()
    {  
        $list = Store::all();
        return view('settings.store',['list'=>$list]);
    }
     public function getstoreID($id)
    {
         $data = Store::findOrFail($id);
          return response()->json($data);
    }

     public function addStore(Request $request)
    {
         $request->validate([
            'name' => 'required',
            'status' => 'required',
            
            
        ]);
            $insertCat = new Store();
            $insertCat->name = trim($request->name);
            $insertCat->status = $request->status;
          
           
            $insertCat = $insertCat->save();
            return $insertCat ? back()->with('message_success','Store   added successfully') : back()->with('message_error','Something went wrong, please try again.');
    }

     public function updateStore(Request $request)
    {
          $request->validate([
             'store_id' => 'required|exists:stores,id',
            'name' => 'required',
            'status' => 'required',
            
            
        ]);
            $insertCat = Store::find($request->store_id);
            $insertCat->name = trim($request->name);
            $insertCat->status = $request->status;
            
            $insertCat = $insertCat->save();
            return $insertCat ? back()->with('message_success','Store updated successfully') : back()->with('message_error','Something went wrong, please try again.');
    }
}
