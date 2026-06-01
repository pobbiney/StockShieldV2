<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\Store;
use App\Models\User;
use App\Models\UserCat;
use App\Models\UserCatLink;
use App\Models\UserLink;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
 

class UserManagementController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth'];
    }

    public function userCategoryView (){

        $listCategory = UserCat::orderBy('cat_name')->get();

        return view ('user-management.user-category',[
            'listCategory' => $listCategory
        ]);
    }

    public function userCategoryProcess (Request $request){

        $request->validate(['category_name' => 'required']);

        if(UserCat::where('cat_name',trim($request->category_name))->get()->count() > 0){

            return back()->with('message_error','Record already exist');

        }else{

            $insertCat = new UserCat();
            $insertCat->cat_name = trim($request->category_name);
            $insertCat->status = 'Active';
            $status = $insertCat->save();

            return $status ? back()->with('success_message','Category added successfully') : back()->with('error_message','Something went wrong, please try again.');

            
        }
    }

    public function editUserCategoryView ($id){

      $decodeID = Crypt::decrypt($id);
      
      $categoryData = UserCat::find($decodeID);
      $listCategory = UserCat::all();

        return view ('user-management.edit-user-category',[
            'categoryData' => $categoryData,'listCategory'=>$listCategory,
            'id' => $id
        ]);


    }

    public function editUserCategoryProcess (Request $request,$id){

        $decodeID = Crypt::decrypt($id);

        $request->validate(['category_name' => 'required']);

        $insertCat =  UserCat::find($decodeID);
        $insertCat->cat_name = trim($request->category_name);
        $insertCat->status = $request->status;
        $status = $insertCat->update();

        return $status ? back()->with('success_message','Category updated successfully') : back()->with('error_message','Something went wrong, please try again.');
    }

    public function assignUserPrivilegesView (){

        $userCatList = UserCat::orderBy('cat_name')->get();

        $parents = array();
        $child = array();

        $activeLinks = UserLink::where('status','Active')->get();


        foreach ($activeLinks as $r_links) {
            if ($r_links->link_parent == 0) {

                $parents[] = $r_links;
               
            } else {

                $child[] = $r_links;
            }
        }

        return view ('user-management.assign-user-privileges',[
            'userCatList' => $userCatList,
            'activeLinks' => $activeLinks,
            'parents' => $parents,
            'child' => $child
        ]);
    }

    public function getUserPrivileges(Request $request){

        $userCat = $request->category;
        $links = DB::select('SELECT user_links.link_id, user_links.page_id, user_links.link_url, user_links.link_name, user_links.link_image, user_links.link_parent FROM user_cat_links INNER JOIN user_links ON user_cat_links.link_id = user_links.link_id WHERE user_cat_links.cat_id = :id ORDER BY user_links.link_name ASC',['id' => $userCat]);

        $child = array();

        if(!empty($links)){

            foreach($links as $row_links){
    
                if($row_links->link_parent > 0){
    
                    $child[] = $row_links;
    
                }
            }
    
         }

         $t_link = UserCatLink::where('cat_id',$userCat)->get();

         $mylinks = [];

         foreach($t_link as $tt){

            $mylinks[] = $tt->link_id;

        }

         $all_links = UserLink::where('status','Active')->get();

        $main = array();
        $children = array();

         foreach($all_links as $r_links){

            if($r_links->link_parent == 0){
                $main[] = $r_links;
            }else{
                $children[] = $r_links;
            }
        }

    
        $privlist = "";

        $privlist .= "<table class='table table-bordered' style='width:100%'>";
        foreach($main as $mainlink){
            $privlist .= "<tr>
                <td colspan='2'><small><b>".$mainlink->link_name."</b></small></td>
            </tr>";
            foreach($children as $subs){ 
                if($mainlink->link_id == $subs->link_parent){

                $privlist .="<tr>
                   <td style=\"width: 60px;\"><input type=\"checkbox\" class=\"form-check-input\" name=\"priv_check[]\" id=\"priv_check\" value=\"".$subs->link_id."\" "; if(in_array($subs->link_id,$mylinks)){ $privlist .="checked";}
                $privlist .="></td>
                   <td><small>". $subs->link_name."</small></td>
               </tr>";
               }
            }
             
         }
        $privlist .="</table>";

        return $privlist;

    }

    public function saveUserPrivileges (Request $request){

        if(!empty($request->priv_check)){

            $usercategory = $request->category;

            UserCatLink::where('cat_id', $usercategory)->delete();

            foreach ($request->priv_check as $r) {

                $sql = DB::insert('insert into user_cat_links (link_id, cat_id) values (?, ?)', [$r, $usercategory]);

            }

            $link = DB::select('SELECT DISTINCT(user_links.link_parent) FROM user_links
            JOIN user_cat_links ON user_links.link_id = user_cat_links.link_id
            WHERE user_cat_links.cat_id =:id',['id' => $usercategory]);

            foreach ($link as $r_two) {

                $sql_two = DB::insert('insert into user_cat_links (link_id, cat_id) values (?, ?)', [$r_two->link_parent, $usercategory]);

            }

            if($sql==true && $sql_two==true){

                return "ok";

            }else{

                return "fail";

            }

            
        }else{

            return "unchecked";
        }

           

    }


    public function createAccountUser(){

        $userCategoryList = UserCat::orderBy('cat_name')->get();
        $listStaff = Staff::get();

        return view ('user-management.create-account',[
            'listStaff' => $listStaff,
            'userCategoryList' => $userCategoryList
        ]);
    }

    public function getAccountEmail(Request $request){

        $user = Staff::find($request->users);

    if (!$user) {
        // Return a safe response if user not found
        return response()->json([
            'error' => 'User not found'
        ], 404);
    }

    // Return both email and phone
    return response()->json([
        'personal_email' => $user->personal_email,
        'contact_num' => $user->contact_num, // replace 'phone_number' with your actual column name
    ]);

    }

    public function createAccount(Request $request){

        $request->validate([
            'users' => 'required',
            'category' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

         $checkExist = User::where('email',$request->email)->count();

        if($checkExist > 0){
            
            return back()->with('error_message','User Already Exist');

        }

        $staffDetails = Staff::find($request->users);

        User::create([
            'name' => $staffDetails->firstname.' '.$staffDetails->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_cat' => $request->category,
            'phone' => $request->phone,
            'staff_id' => $staffDetails->staff_id,
            'status' => 'Active'
        ]);

        return back()->with('success_message','User Account Created Successfully');

    }

    public function listAccountsView (){

        $listCategory = UserCat::get();
        $userList = User::get();

        return view ('user-management.list-accounts',[
            'listCategory' => $listCategory,
            'userList' => $userList
        ]);
    }

    public function getUserAccountList(Request $request){

        $request->validate([
            'category' => 'required'
        ]);

        $userCatList = UserCat::get();

        $userList = User::where('user_cat',$request->category)->get();

        if($userList->count() > 0){
            

            return view('user-management.list-accounts',['listCategory' => $userCatList,'userList' => $userList]);
            
        }

        return view('user-management.list-accounts',['listCategory' => $userCatList,'userList' => []]);

    }

    public function editUserAccountView ($id){

        $decodeId = Crypt::decrypt($id);

        $userData = User::find($decodeId);
        $userCategoryList = UserCat::where('status','Active')->get();

        return view ('user-management.edit-user-account',[
            'userData' => $userData,
            'userCategoryList' => $userCategoryList,
            'id' => $id
        ]);
    }

    public function editUserAccountProcess (Request $request,$id){

        $decodeId = Crypt::decrypt($id);

        $request->validate([
            'user' => 'required',
            'category' => 'required',
            'email' => 'required|email',
            'status' => 'required'
        ]);


        $updateAccount = User::find($decodeId);
        $updateAccount->name = $request->user;
        $updateAccount->user_cat = $request->category;
        $updateAccount->status = $request->status;
        if(isset($request->password)){$updateAccount->password = Hash::make($request->password);}
        $status = $updateAccount->update();


        return $status ?  back()->with('success_message','Account Details Updated Successfully') : back()->with('error_message','Something went wrong, Please try again.');


    }

    // public function getStaffMapping()
    // {
    //     $liststaff = Staff::all();
    //     $listdept = Department::all();
    //     return view('user-management.staffMapping',['liststaff'=>$liststaff,'listdept'=>$listdept]);
    // }

    public function mapStore(Request $request)
    {
         $request->validate([
        'staff_id' => 'required',
        'department_id' => 'required|array'
    ]);

    // Convert array to comma-separated string
         $departments = implode('~', $request->department_id);

           // Get the user
        $user = User::where('staff_id', $request->staff_id)->first();

        if (!$user) {
            return back()->with('message_error', 'Staff not found.');
        }

        // Update
        $user->department_id = $departments;
        $saved = $user->save();

        return $saved 
            ? back()->with([
            'message_success' => 'Staff mapped successfully',
            'mapped_staff_id' => $user->staff_id,                        // pass staff id back
            'mapped_departments' => $request->department_id              // pass selected dept ids back
          ])
            : back()->with('message_error','Something went wrong, please try again.');
     
    }

    
    public function getSubmenuItems($id)
    {
      $decodeID = Crypt::decrypt($id);
      $list = UserLink::where('link_parent',$decodeID)->get();
        $parent = UserLink::where('link_id',$decodeID)->first();
      return view('layouts.submenu',['list'=>$list,'parent'=>$parent]);
    }

    public function getstoremappingvoew()
    {
        $liststaff = Staff::all();
        $liststore = Store::all();

        return view('user-management.storemapping',['liststaff'=>$liststaff,'liststore'=>$liststore]);
    }

    public function getStaffStores($staff_id)
{
     $user = DB::table('users')
        ->where('staff_id', $staff_id)
        ->value('department_id'); // gets the single column value

    $mappedIds = $user ? explode('~', $user) : [];
    
    return response()->json($mappedIds);
}


   
}
