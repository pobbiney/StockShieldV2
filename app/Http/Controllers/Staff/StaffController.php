<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Staff;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class StaffController extends Controller
{
    public function addStaffView()
    {  
        $list = Department::all();
        return view('staff-management.create-staff',['list'=>$list]);
    }

    public function addStaff(Request $request)
    {
          $request->validate([
            'title' => 'required',
            'surname' => 'required',
            'firstname' => 'required',
            'gender' => 'required',
            'email' => 'required',
            'phone' => 'required',
             
            'staff_number' => 'required',
            'position' => 'required',
            'department' => 'required',
        ]);


        // if(Staff::where('employee_id',$request->staff_number)->get()->count() > 0){

        //     return back()->with('message_error','Record already exist');

        // }else{

        $insertstaff = new Staff();
        if($request->hasFile('image')){
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;

            $file->move(public_path('uploads/profile-photo'), $filename);

            $insertstaff->picture = 'uploads/profile-photo/'.$filename;
        }
        $insertstaff->title = trim($request->title);
        $insertstaff->surname = trim($request->surname);
        $insertstaff->firstname = trim($request->firstname);
        $insertstaff->othername = trim($request->othername);
        $insertstaff->gender = trim($request->gender);
        $insertstaff->personal_email = trim($request->email);
        $insertstaff->contact_num = trim($request->phone);
        $insertstaff->position = trim($request->position);
        $insertstaff->employee_id = trim($request->staff_number);
        $insertstaff->digital_address = trim($request->address);
        $insertstaff->department_id = trim($request->department);
        $insertstaff->created_by = Auth::user()->id;

        $status = $insertstaff->save();

        return $status 
            ? back()->with('message_success','Staff added successfully') 
            : back()->with('error_message','Something went wrong, please try again.');
    

    }

    public function getSupplierView()
    {
        $list =  Supplier::all();
          // Generate Item Code
        $lastItem = Supplier::latest('id')->first();

        if($lastItem){
            $number = $lastItem->id + 1;
        } else {
            $number = 1;
        }

        $supCode = 'SUP-' . str_pad($number, 5, '0', STR_PAD_LEFT);
         return view('staff-management.Supplier',['list'=>$list,'supCode'=>$supCode]);
    }

     //Adding supplier to database
    public function addSupplier(Request $request)
    {
         $request->validate([
        'code' => 'required',
        'supplier' => 'required',
        'phone' => 'required',
        // 'email' => 'required|email',
        'company' => 'required',
         
        'city' => 'required',
        // 'tin_number' => 'required',
        // 'registration_number' => 'required',
        'address' => 'required',
        'status' => 'required',
    ]);

    if(Supplier::where('code',$request->code)->get()->count() > 0){

            return back()->with('message_error','Supplier already exist');

        }else{

            $insertCat = new Supplier ();
             $insertCat->code = trim($request->code);
            $insertCat->supplier = trim($request->supplier);
            $insertCat->phone = $request->phone;
            $insertCat->email = $request->email;
            $insertCat->company = $request->company;
            $insertCat->city = $request->city;
            $insertCat->tin_number = $request->tin_number;
            $insertCat->registration_number = $request->registration_number;
            $insertCat->address = $request->address;
            $insertCat->status = $request->status;
             
            $insertCat->created_by = Auth::User()->id;
             
            $status = $insertCat->save();

            return $status ? back()->with('message_success','Supplier added successfully') : back()->with('message_error','Something went wrong, please try again.');


        }
    }

    //get supplier details
      public function getSupplierID($id)
    {
         $data = Supplier::findOrFail($id);
          return response()->json($data);
    }

     //update supplier to database
    public function updateSupplier(Request $request)
    {
         $request->validate([
        'code' => 'required',
        'supplier' => 'required',
        'phone' => 'required',
        'email' => 'required|email',
        'company' => 'required',
         
        'city' => 'required',
        'tin_number' => 'required',
        'registration_number' => 'required',
        'address' => 'required',
        'status' => 'required',
    ]);

    

             $insertCat = Supplier::find($request->supplier_id);
             $insertCat->code = trim($request->code);
            $insertCat->supplier = trim($request->supplier);
            $insertCat->phone = $request->phone;
            $insertCat->email = $request->email;
            $insertCat->company = $request->company;
            $insertCat->city = $request->city;
            $insertCat->tin_number = $request->tin_number;
            $insertCat->registration_number = $request->registration_number;
            $insertCat->address = $request->address;
            $insertCat->status = $request->status;
             
            $insertCat->updated_by = Auth::User()->id;
             
            $status = $insertCat->save();

            return $status ? back()->with('message_success','Supplier updated successfully') : back()->with('message_error','Something went wrong, please try again.');

 
    }

    public function getStaffListView()
    {
        $liststaff = Staff::all();
        return view('staff-management.list-staff',['liststaff'=>$liststaff]);

    }

    public function getEditStaffView($staff_id){
    $decodeID = Crypt::decrypt($staff_id);
     
    $data = Staff::where('staff_id',$decodeID)->first();
    $list = Department::all();
    
    return view ('staff-management.edit-staff',[ 'data'=>$data,'staff_id'=>$staff_id,'list'=>$list ]);
    }

    public function editStaff(Request $request, $staff_id)
    {
         $request->validate([
            'title' => 'required',
            'surname' => 'required',
            'firstname' => 'required',
            'gender' => 'required',
            'email' => 'required',
            'phone' => 'required',
             
            'staff_number' => 'required',
            'position' => 'required',
            'department' => 'required',
        ]);


          $decodeId = Crypt::decrypt($staff_id);
          $insertstaff =  Staff::find($decodeId);

        
        if($request->hasFile('image')){
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;

            $file->move(public_path('uploads/profile-photo'), $filename);

            $insertstaff->picture = 'uploads/profile-photo/'.$filename;
        }
        $insertstaff->title = trim($request->title);
        $insertstaff->surname = trim($request->surname);
        $insertstaff->firstname = trim($request->firstname);
        $insertstaff->othername = trim($request->othername);
        $insertstaff->gender = trim($request->gender);
        $insertstaff->personal_email = trim($request->email);
        $insertstaff->contact_num = trim($request->phone);
        $insertstaff->position = trim($request->position);
        $insertstaff->employee_id = trim($request->staff_number);
        $insertstaff->digital_address = trim($request->address);
        $insertstaff->department_id = trim($request->department);
        $insertstaff->updated_by = Auth::user()->id;

        $status = $insertstaff->update();

        return $status 
            ? back()->with('message_success','Staff updated successfully') 
            : back()->with('error_message','Something went wrong, please try again.');

    }
}
