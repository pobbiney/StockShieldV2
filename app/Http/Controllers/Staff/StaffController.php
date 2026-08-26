<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ApproveStock;
use App\Models\Department;
use App\Models\Staff;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class StaffController extends Controller
{
    public function addStaffView()
    {
        $list = Department::all();
        $liststaff = Staff::orderByDesc('staff_id')->get();
        $totalStaff = Staff::count();
        $maleCount = Staff::where('gender', 'Male')->count();
        $femaleCount = Staff::where('gender', 'Female')->count();
        $staffWithAccounts = User::whereNotNull('staff_id')->pluck('staff_id')->map(fn ($id) => (int) $id)->all();

        return view('staff-management.create-staff', [
            'list' => $list,
            'liststaff' => $liststaff,
            'totalStaff' => $totalStaff,
            'maleCount' => $maleCount,
            'femaleCount' => $femaleCount,
            'staffWithAccounts' => $staffWithAccounts,
        ]);
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
            ? redirect()->route('create-staff')->with('message_success', 'Staff added successfully')
            : back()->with('message_error', 'Something went wrong, please try again.')->withInput();
    

    }

    public function getSupplierView()
    {
        $list = Supplier::orderByDesc('id')->get();
        $totalSuppliers = Supplier::count();
        $activeCount = Supplier::where('status', 'Active')->count();
        $inactiveCount = Supplier::where('status', 'Inactive')->count();

        $suppliersWithStock = Stock::whereNotNull('supplier_id')
            ->pluck('supplier_id')
            ->merge(ApproveStock::whereNotNull('supplier_id')->pluck('supplier_id'))
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->all();

        $lastItem = Supplier::latest('id')->first();
        $number = $lastItem ? $lastItem->id + 1 : 1;
        $supCode = 'SUP-' . str_pad($number, 5, '0', STR_PAD_LEFT);

        return view('staff-management.Supplier', [
            'list' => $list,
            'supCode' => $supCode,
            'totalSuppliers' => $totalSuppliers,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
            'suppliersWithStock' => $suppliersWithStock,
        ]);
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

            return redirect()->route('Supplier')->with('message_error', 'Supplier already exist')->withInput();

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

            return $status
                ? redirect()->route('Supplier')->with('message_success', 'Supplier added successfully')
                : redirect()->route('Supplier')->with('message_error', 'Something went wrong, please try again.')->withInput();


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

    

        $supplier = Supplier::find($request->supplier_id);

        if (!$supplier) {
            return redirect()->route('Supplier')->with('message_error', 'Supplier record not found.');
        }

        $supplier->code = trim($request->code);
        $supplier->supplier = trim($request->supplier);
        $supplier->phone = $request->phone;
        $supplier->email = $request->email;
        $supplier->company = $request->company;
        $supplier->city = $request->city;
        $supplier->tin_number = $request->tin_number;
        $supplier->registration_number = $request->registration_number;
        $supplier->address = $request->address;
        $supplier->status = $request->status;
        $supplier->updated_by = Auth::user()->id;

        $status = $supplier->save();

        return $status
            ? redirect()->route('Supplier')->with('message_success', 'Supplier updated successfully')
            : redirect()->route('Supplier')->with('message_error', 'Something went wrong, please try again.')->withInput();
    }

    public function deleteSupplier($id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return redirect()->route('Supplier')->with('message_error', 'Supplier record not found.');
        }

        $hasStock = Stock::where('supplier_id', $id)->exists()
            || ApproveStock::where('supplier_id', $id)->exists();

        if ($hasStock) {
            return redirect()->route('Supplier')->with(
                'message_error',
                'Cannot delete this supplier because they are linked to stock records.'
            );
        }

        $supplier->delete();

        return redirect()->route('Supplier')->with('message_success', 'Supplier deleted successfully.');
    }

    public function getStaffListView()
    {
        return redirect()->route('create-staff');
    }

    public function getEditStaffView($staff_id)
    {
        return redirect()->route('create-staff')->with('open_edit_staff', $staff_id);
    }

    public function getStaffID($id)
    {
        $decodeID = Crypt::decrypt($id);
        $data = Staff::where('staff_id', $decodeID)->firstOrFail();

        return response()->json([
            'staff_id'        => $data->staff_id,
            'title'           => $data->title,
            'surname'         => $data->surname,
            'firstname'       => $data->firstname,
            'othername'       => $data->othername,
            'gender'          => $data->gender,
            'email'           => $data->personal_email,
            'phone'           => $data->contact_num,
            'address'         => $data->digital_address,
            'staff_number'    => $data->employee_id,
            'position'        => $data->position,
            'department'      => $data->department_id,
            'picture'         => $data->picture ? asset($data->picture) : asset('backend/assets/img/user.png'),
            'encrypted_id'    => $id,
        ]);
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
          $insertstaff = Staff::where('staff_id', $decodeId)->first();

        if (!$insertstaff) {
            return redirect()->route('create-staff')->with('message_error', 'Staff record not found.');
        }

        
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
            ? redirect()->route('create-staff')->with('message_success', 'Staff updated successfully')
            : redirect()->route('create-staff')->with('message_error', 'Something went wrong, please try again.')->withInput();

    }

    public function deleteStaff($staff_id)
    {
        $decodeID = Crypt::decrypt($staff_id);
        $staff = Staff::where('staff_id', $decodeID)->first();

        if (!$staff) {
            return redirect()->route('create-staff')->with('message_error', 'Staff record not found.');
        }

        if (User::where('staff_id', $staff->staff_id)->exists()) {
            return redirect()->route('create-staff')->with(
                'message_error',
                'Cannot delete this staff member because they have a user account. Remove the account first.'
            );
        }

        if ($staff->picture && file_exists(public_path($staff->picture))) {
            @unlink(public_path($staff->picture));
        }

        $staff->delete();

        return redirect()->route('create-staff')->with('message_success', 'Staff deleted successfully.');
    }
}
