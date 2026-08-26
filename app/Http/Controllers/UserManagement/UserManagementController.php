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
use App\Models\UserExtraLink;
use App\Models\UserLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

 

class UserManagementController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth'];
    }

    public function userCategoryView (){

        $listCategory = UserCat::orderBy('cat_name')->get();
        $privilegeCounts = UserCatLink::select('cat_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('cat_id')
            ->pluck('cnt', 'cat_id');

        return view ('user-management.user-category',[
            'listCategory' => $listCategory,
            'totalRoles' => $listCategory->count(),
            'globalAccessCount' => $listCategory->where('access_all_stores', true)->count(),
            'activeCount' => $listCategory->where('status', 'Active')->count(),
            'privilegeCounts' => $privilegeCounts,
        ]);
    }

    public function userCategoryProcess (Request $request){

        $request->validate(['category_name' => 'required']);

        if(UserCat::where('cat_name',trim($request->category_name))->get()->count() > 0){

            return back()->with('error_message','Record already exist')->withInput();

        }else{

            $insertCat = new UserCat();
            $insertCat->cat_name = trim($request->category_name);
            $insertCat->status = 'Active';
            $insertCat->access_all_stores = $request->boolean('access_all_stores');
            $status = $insertCat->save();

            return $status ? back()->with('success_message','Category added successfully') : back()->with('error_message','Something went wrong, please try again.')->withInput();

            
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

        $request->validate([
            'category_name' => 'required',
            'status' => 'required|in:Active,Inactive',
        ]);

        $category = UserCat::find($decodeID);

        if (!$category) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Role not found.'], 404);
            }

            return back()->with('error_message', 'Role not found.');
        }

        if (UserCat::where('cat_name', trim($request->category_name))
            ->where('cat_id', '!=', $decodeID)
            ->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Record already exist.'], 422);
            }

            return back()->with('error_message', 'Record already exist.');
        }

        $category->cat_name = trim($request->category_name);
        $category->status = $request->status;
        $category->access_all_stores = $request->boolean('access_all_stores');
        $status = $category->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => (bool) $status,
                'message' => $status
                    ? 'Category updated successfully'
                    : 'Something went wrong, please try again.',
            ], $status ? 200 : 422);
        }

        return $status
            ? back()->with('success_message','Category updated successfully')
            : back()->with('error_message','Something went wrong, please try again.');
    }

    public function assignUserPrivilegesView (){

        $userCatList = UserCat::orderBy('cat_name')->get();
        $activeLinks = UserLink::where('status', 'Active')->get();
        $childLinks = $activeLinks->where('link_parent', '>', 0);

        $rolesWithPrivileges = UserCatLink::distinct('cat_id')->count('cat_id');
        $totalAssignedLinks = UserCatLink::count();

        $roleOptions = $userCatList->map(fn ($cat) => [
            'id' => $cat->cat_id,
            'name' => $cat->cat_name,
            'global' => (bool) $cat->access_all_stores,
        ])->values();

        return view ('user-management.assign-user-privileges',[
            'userCatList' => $userCatList,
            'totalRoles' => $userCatList->count(),
            'totalScreens' => $childLinks->count(),
            'rolesWithPrivileges' => $rolesWithPrivileges,
            'totalAssignedLinks' => $totalAssignedLinks,
            'roleOptions' => $roleOptions,
        ]);
    }

    public function getUserPrivileges(Request $request){

        $request->validate([
            'category' => 'required|integer',
        ]);

        $userCat = (int) $request->category;
        $assignedLinkIds = UserCatLink::where('cat_id', $userCat)
            ->pluck('link_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $allLinks = UserLink::where('status', 'Active')->orderBy('link_name')->get();
        $parents = $allLinks->where('link_parent', 0)->values();
        $children = $allLinks->where('link_parent', '>', 0);

        $groups = [];

        foreach ($parents as $parent) {
            $items = $children
                ->where('link_parent', $parent->link_id)
                ->map(fn ($link) => [
                    'id' => (int) $link->link_id,
                    'name' => $link->link_name,
                    'checked' => in_array((int) $link->link_id, $assignedLinkIds, true),
                ])
                ->values()
                ->all();

            if (!empty($items)) {
                $groups[] = [
                    'parent_id' => (int) $parent->link_id,
                    'parent_name' => $parent->link_name,
                    'items' => $items,
                ];
            }
        }

        $assignedScreenCount = collect($groups)
            ->flatMap(fn ($group) => $group['items'])
            ->where('checked', true)
            ->count();

        return response()->json([
            'groups' => $groups,
            'assigned_count' => $assignedScreenCount,
            'total_count' => $children->count(),
        ]);
    }

    public function saveUserPrivileges (Request $request){

        if (empty($request->category)) {
            return 'unselected';
        }

        if (empty($request->priv_check)) {
            return 'unchecked';
        }

        $usercategory = $request->category;

        UserCatLink::where('cat_id', $usercategory)->delete();

        foreach ($request->priv_check as $r) {
            DB::insert('insert into user_cat_links (link_id, cat_id) values (?, ?)', [$r, $usercategory]);
        }

        $link = DB::select('SELECT DISTINCT(user_links.link_parent) FROM user_links
            JOIN user_cat_links ON user_links.link_id = user_cat_links.link_id
            WHERE user_cat_links.cat_id =:id', ['id' => $usercategory]);

        foreach ($link as $r_two) {
            $exists = UserCatLink::where('cat_id', $usercategory)
                ->where('link_id', $r_two->link_parent)
                ->exists();

            if (!$exists) {
                DB::insert('insert into user_cat_links (link_id, cat_id) values (?, ?)', [$r_two->link_parent, $usercategory]);
            }
        }

        return 'ok';
    }


    protected function getRoleScreensByCategory($categoryIds): array
    {
        $categoryIds = collect($categoryIds)->filter()->values();

        if ($categoryIds->isEmpty()) {
            return [];
        }

        $rows = DB::table('user_cat_links')
            ->join('user_links', 'user_cat_links.link_id', '=', 'user_links.link_id')
            ->whereIn('user_cat_links.cat_id', $categoryIds)
            ->where('user_links.status', 'Active')
            ->orderBy('user_links.link_name')
            ->get(['user_cat_links.cat_id', 'user_links.link_id', 'user_links.link_name']);

        $grouped = [];

        foreach ($rows as $row) {
            $grouped[$row->cat_id][$row->link_id] = $row->link_name;
        }

        foreach ($grouped as $catId => $screens) {
            $items = [];

            foreach ($screens as $id => $name) {
                $items[] = ['id' => (int) $id, 'name' => $name];
            }

            usort($items, fn ($a, $b) => strcasecmp($a['name'], $b['name']));
            $grouped[$catId] = $items;
        }

        return $grouped;
    }

    protected function syncUserExtraLinks(User $user, array $extraLinkIds): int
    {
        UserExtraLink::where('user_id', $user->id)->delete();

        $roleLinkIds = UserCatLink::where('cat_id', $user->user_cat)
            ->pluck('link_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $saved = 0;
        $assigned = [];

        foreach (array_unique(array_map('intval', $extraLinkIds)) as $linkId) {
            if (in_array($linkId, $roleLinkIds, true) || in_array($linkId, $assigned, true)) {
                continue;
            }

            UserExtraLink::create([
                'user_id' => $user->id,
                'link_id' => $linkId,
            ]);
            $assigned[] = $linkId;
            $saved++;

            $link = UserLink::find($linkId);

            if ($link && $link->link_parent > 0 && !in_array($link->link_parent, $roleLinkIds, true)) {
                $parentId = (int) $link->link_parent;

                if (!in_array($parentId, $assigned, true)) {
                    UserExtraLink::create([
                        'user_id' => $user->id,
                        'link_id' => $parentId,
                    ]);
                    $assigned[] = $parentId;
                }
            }
        }

        return $saved;
    }

    protected function applyStoreMapping(User $user, ?array $storeIds = null): array
    {
        $category = UserCat::find($user->user_cat);

        if ($category && $category->access_all_stores) {
            $ids = Store::where('status', 'Active')->pluck('id')->all();
            $user->department_id = implode('~', $ids);
            $user->save();

            return [
                'count' => count($ids),
                'global' => true,
                'store_names' => Store::whereIn('id', $ids)->orderBy('name')->pluck('name')->all(),
            ];
        }

        if (empty($storeIds)) {
            return ['count' => 0, 'global' => false, 'store_names' => []];
        }

        $ids = array_values(array_map('intval', $storeIds));
        $user->department_id = implode('~', $ids);
        $user->save();

        return [
            'count' => count($ids),
            'global' => false,
            'store_names' => Store::whereIn('id', $ids)->orderBy('name')->pluck('name')->all(),
        ];
    }

    public function createAccountUser(){

        $userCategoryList = UserCat::orderBy('cat_name')->get();
        $existingStaffIds = User::whereNotNull('staff_id')->pluck('staff_id');
        $listStaff = Staff::whereNotIn('staff_id', $existingStaffIds)
            ->orderBy('surname')
            ->orderBy('firstname')
            ->get();
        $liststore = Store::where('status', 'Active')->orderBy('name')->get();

        $totalStaff = Staff::count();
        $totalAccounts = User::count();
        $staffWithoutAccounts = max($totalStaff - $existingStaffIds->unique()->count(), 0);
        $rolePrivilegeCounts = UserCatLink::select('cat_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('cat_id')
            ->pluck('cnt', 'cat_id');

        $roleScreensByCategory = $this->getRoleScreensByCategory(
            $userCategoryList->pluck('cat_id')
        );

        $roleData = $userCategoryList->mapWithKeys(function ($category) use ($roleScreensByCategory) {
            $screenList = $roleScreensByCategory[$category->cat_id] ?? [];

            return [
                $category->cat_id => [
                    'name' => $category->cat_name,
                    'global' => (bool) $category->access_all_stores,
                    'screens' => count($screenList),
                    'screen_names' => array_column($screenList, 'name'),
                    'role_link_ids' => array_column($screenList, 'id'),
                ],
            ];
        });

        $allScreens = UserLink::where('status', 'Active')
            ->where('link_parent', '>', 0)
            ->orderBy('link_name')
            ->get(['link_id', 'link_name'])
            ->map(fn ($link) => ['id' => (int) $link->link_id, 'name' => $link->link_name])
            ->values();

        $staffSearchList = $listStaff->map(function ($staff) {
            return [
                'id' => $staff->staff_id,
                'name' => trim(($staff->title ?? '') . ' ' . $staff->firstname . ' ' . $staff->surname),
            ];
        })->values();

        $selectedStaffName = '';
        if (old('users')) {
            $selectedStaff = $listStaff->firstWhere('staff_id', (int) old('users'));
            if ($selectedStaff) {
                $selectedStaffName = trim(($selectedStaff->title ?? '') . ' ' . $selectedStaff->firstname . ' ' . $selectedStaff->surname);
            }
        }

        return view ('user-management.create-account',[
            'listStaff' => $listStaff,
            'userCategoryList' => $userCategoryList,
            'liststore' => $liststore,
            'totalStaff' => $totalStaff,
            'totalAccounts' => $totalAccounts,
            'staffWithoutAccounts' => $staffWithoutAccounts,
            'totalRoles' => $userCategoryList->count(),
            'rolePrivilegeCounts' => $rolePrivilegeCounts,
            'roleData' => $roleData,
            'allScreens' => $allScreens,
            'staffSearchList' => $staffSearchList,
            'selectedStaffName' => $selectedStaffName,
        ]);
    }

    public function getAccountEmail(Request $request){

        $user = Staff::find($request->users);

    if (!$user) {
        return response()->json([
            'error' => 'User not found'
        ], 404);
    }

    return response()->json([
        'personal_email' => $user->personal_email,
        'contact_num' => $user->contact_num,
        'full_name' => trim(($user->title ?? '') . ' ' . $user->firstname . ' ' . $user->surname),
        'department' => $user->department,
        'gender' => $user->gender,
        'service_no' => $user->service_no,
        'picture' => $user->picture,
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

        $category = UserCat::find($request->category);

        if (!$category) {
            return back()->with('error_message', 'Invalid role selected.')->withInput();
        }

        if (!$category->access_all_stores) {
            $request->validate([
                'department_id' => 'required|array|min:1',
                'department_id.*' => 'integer|exists:stores,id',
            ], [
                'department_id.required' => 'Select at least one store for this role.',
            ]);
        }

        $staffDetails = Staff::find($request->users);

        if (!$staffDetails) {
            return back()->with('error_message', 'Staff member not found.')->withInput();
        }

        $user = User::create([
            'name' => $staffDetails->firstname.' '.$staffDetails->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_cat' => $request->category,
            'phone' => $request->phone,
            'staff_id' => $staffDetails->staff_id,
            'status' => 'Active'
        ]);

        if ($request->filled('extra_link_ids')) {
            $request->validate([
                'extra_link_ids' => 'array',
                'extra_link_ids.*' => 'integer|exists:user_links,link_id',
            ]);
        }

        $extraScreenCount = $this->syncUserExtraLinks(
            $user,
            $request->extra_link_ids ?? []
        );

        $mapping = $this->applyStoreMapping(
            $user,
            $request->department_id ?? []
        );

        $privilegeCount = UserCatLink::where('cat_id', $user->user_cat)->count();

        if ($mapping['global']) {
            $storeMessage = "All {$mapping['count']} active stores assigned automatically.";
        } elseif ($mapping['count'] > 0) {
            $storeMessage = $mapping['count'] . ' store(s) mapped: ' . implode(', ', $mapping['store_names']) . '.';
        } else {
            $storeMessage = 'No stores mapped — assign stores before this user can log in.';
        }

        $screenMessage = $privilegeCount > 0
            ? "{$privilegeCount} screen(s) available via the assigned role."
            : 'No screens assigned to this role yet — configure privileges in User Management.';

        if ($extraScreenCount > 0) {
            $screenMessage .= " {$extraScreenCount} additional user-only screen(s) granted.";
        }

        return redirect()
            ->route('user-management-create-account')
            ->with('success_message', "Account created for {$user->name}. {$storeMessage} {$screenMessage}");

    }

    public function listAccountsView (){

        $listCategory = UserCat::orderBy('cat_name')->get();
        $userList = User::orderBy('name')->get();
        $roleMeta = UserCat::get()->keyBy('cat_id');

        return view ('user-management.list-accounts',[
            'listCategory' => $listCategory,
            'userList' => $userList,
            'totalAccounts' => User::count(),
            'activeAccounts' => User::where('status', 'Active')->count(),
            'inactiveAccounts' => User::where('status', 'Inactive')->count(),
            'rolesInUse' => User::distinct('user_cat')->count('user_cat'),
            'selectedCategory' => null,
            'roleMeta' => $roleMeta,
        ]);
    }

    public function getUserAccountList(Request $request){

        $request->validate([
            'category' => 'required'
        ]);

        $userCatList = UserCat::orderBy('cat_name')->get();
        $roleMeta = UserCat::get()->keyBy('cat_id');

        $userList = User::where('user_cat', $request->category)->orderBy('name')->get();

        return view('user-management.list-accounts', [
            'listCategory' => $userCatList,
            'userList' => $userList,
            'totalAccounts' => User::count(),
            'activeAccounts' => User::where('status', 'Active')->count(),
            'inactiveAccounts' => User::where('status', 'Inactive')->count(),
            'rolesInUse' => User::distinct('user_cat')->count('user_cat'),
            'selectedCategory' => (int) $request->category,
            'roleMeta' => $roleMeta,
        ]);
    }

    public function editUserAccountView ($id){

        $decodeId = Crypt::decrypt($id);
        $userData = User::find($decodeId);

        if (!$userData) {
            return redirect()
                ->route('user-management-list-create-account')
                ->with('error_message', 'User account not found.');
        }

        $userCategoryList = UserCat::where('status', 'Active')->orderBy('cat_name')->get();
        $liststore = Store::where('status', 'Active')->orderBy('name')->get();

        $roleScreensByCategory = $this->getRoleScreensByCategory(
            $userCategoryList->pluck('cat_id')
        );

        $roleData = $userCategoryList->mapWithKeys(function ($category) use ($roleScreensByCategory) {
            $screenList = $roleScreensByCategory[$category->cat_id] ?? [];

            return [
                $category->cat_id => [
                    'name' => $category->cat_name,
                    'global' => (bool) $category->access_all_stores,
                    'screens' => count($screenList),
                    'screen_names' => array_column($screenList, 'name'),
                    'role_link_ids' => array_column($screenList, 'id'),
                ],
            ];
        });

        $allScreens = UserLink::where('status', 'Active')
            ->where('link_parent', '>', 0)
            ->orderBy('link_name')
            ->get(['link_id', 'link_name'])
            ->map(fn ($link) => ['id' => (int) $link->link_id, 'name' => $link->link_name])
            ->values();

        $roleLinkIds = UserCatLink::where('cat_id', $userData->user_cat)
            ->pluck('link_id')
            ->map(fn ($linkId) => (int) $linkId)
            ->all();

        $userExtraLinkIds = UserExtraLink::where('user_id', $userData->id)
            ->pluck('link_id')
            ->map(fn ($linkId) => (int) $linkId)
            ->filter(fn ($linkId) => !in_array($linkId, $roleLinkIds, true))
            ->values()
            ->all();

        $userStoreIds = $userData->getStoreIds();
        $staff = $userData->staff_id ? Staff::find($userData->staff_id) : null;

        $previewData = [
            'full_name' => $userData->name,
            'personal_email' => $userData->email,
            'contact_num' => $userData->phone ?? ($staff?->contact_num ?? ''),
            'department' => $staff?->department ?? '—',
            'picture' => $staff?->picture ?? null,
        ];

        $currentRole = $roleData[$userData->user_cat] ?? null;
        $extraScreenCount = count($userExtraLinkIds);
        $storeCount = count($userStoreIds);

        return view('user-management.edit-user-account', [
            'userData' => $userData,
            'userCategoryList' => $userCategoryList,
            'id' => $id,
            'liststore' => $liststore,
            'roleData' => $roleData,
            'allScreens' => $allScreens,
            'userExtraLinkIds' => $userExtraLinkIds,
            'userStoreIds' => $userStoreIds,
            'previewData' => $previewData,
            'currentRoleScreens' => $currentRole['screens'] ?? 0,
            'extraScreenCount' => $extraScreenCount,
            'storeCount' => $storeCount,
        ]);
    }

    public function editUserAccountProcess (Request $request, $id){

        $decodeId = Crypt::decrypt($id);
        $user = User::find($decodeId);

        if (!$user) {
            return redirect()
                ->route('user-management-list-create-account')
                ->with('error_message', 'User account not found.');
        }

        $request->validate([
            'user' => 'required|string|max:255',
            'category' => 'required|exists:user_cat,cat_id',
            'email' => 'required|email',
            'status' => 'required|in:Active,Inactive',
            'password' => 'nullable|string|min:8',
        ]);

        $accountStatus = $request->input('status') === 'Inactive' ? 'Inactive' : 'Active';

        $category = UserCat::find($request->category);

        if (!$category) {
            return back()->with('error_message', 'Invalid role selected.')->withInput();
        }

        if (!$category->access_all_stores) {
            $request->validate([
                'department_id' => 'required|array|min:1',
                'department_id.*' => 'integer|exists:stores,id',
            ], [
                'department_id.required' => 'Select at least one store for this role.',
            ]);
        }

        if ($request->filled('extra_link_ids')) {
            $request->validate([
                'extra_link_ids' => 'array',
                'extra_link_ids.*' => 'integer|exists:user_links,link_id',
            ]);
        }

        $user->name = $request->user;
        $user->user_cat = $request->category;
        $user->status = $accountStatus;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $extraScreenCount = $this->syncUserExtraLinks(
            $user,
            $request->extra_link_ids ?? []
        );

        $mapping = $this->applyStoreMapping(
            $user,
            $request->department_id ?? []
        );

        // Persist status last so later saves cannot leave it stale.
        DB::table('users')->where('id', $user->id)->update(['status' => $accountStatus]);
        $user->status = $accountStatus;

        $statusNote = $accountStatus === 'Inactive'
            ? ' Account is blocked and cannot log in.'
            : '';

        if ($mapping['global']) {
            $storeMessage = "All {$mapping['count']} active stores assigned.";
        } elseif ($mapping['count'] > 0) {
            $storeMessage = "{$mapping['count']} store(s) mapped.";
        } else {
            $storeMessage = 'No stores mapped — assign stores before this user can log in.';
        }

        $screenMessage = $extraScreenCount > 0
            ? " {$extraScreenCount} additional user-only screen(s) active."
            : '';

        return back()->with(
            'success_message',
            "Account updated for {$user->name}. {$storeMessage}{$screenMessage}{$statusNote}"
        );
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
    ]);

           // Get the user
        $user = User::where('staff_id', $request->staff_id)->first();

        if (!$user) {
            return back()->with('message_error', 'Staff not found.');
        }

        $category = UserCat::find($user->user_cat);

        if ($category && $category->access_all_stores) {
            $this->applyStoreMapping($user);
        } else {
            $request->validate([
                'department_id' => 'required|array',
            ]);

            $this->applyStoreMapping($user, $request->department_id);
        }

        $saved = true;

        return $saved 
            ? back()->with([
            'message_success' => 'Staff mapped successfully',
            'mapped_staff_id' => $user->staff_id,
            'mapped_departments' => $category && $category->access_all_stores
                ? Store::where('status', 'Active')->pluck('id')->all()
                : $request->department_id
          ])
            : back()->with('message_error','Something went wrong, please try again.');
     
    }

    
    public function getSubmenuItems($id)
    {
       $user = Auth::user();
       $userCat = $user->user_cat;
       $accessibleLinkIds = $user->getAccessibleLinkIds();

       $decodeID = Crypt::decrypt($id);

    if (!in_array($decodeID, $accessibleLinkIds, true)) {
        abort(403, 'Unauthorized Access');
    }

    $parent = UserLink::find($decodeID);

    if (!$parent) {
        abort(404, 'Menu not found');
    }

    $submenus = UserLink::where('link_parent', $decodeID)
        ->whereIn('link_id', $accessibleLinkIds)
        ->orderBy('link_name')
        ->get();

         return view('layouts.submenu', compact('parent', 'submenus'));
       
    }

    public function getstoremappingvoew()
    {
        $liststaff = Staff::all();
        $liststore = Store::all();

        return view('user-management.storemapping',['liststaff'=>$liststaff,'liststore'=>$liststore]);
    }

    public function getStaffStores($staff_id)
{
     $user = User::where('staff_id', $staff_id)->first();

    if (!$user) {
        return response()->json([
            'mapped_ids' => [],
            'access_all_stores' => false,
            'role_name' => null,
        ]);
    }

    $category = UserCat::find($user->user_cat);
    $mappedIds = $user->department_id ? explode('~', $user->department_id) : [];

    return response()->json([
        'mapped_ids' => $mappedIds,
        'access_all_stores' => $category ? (bool) $category->access_all_stores : false,
        'role_name' => $category?->cat_name,
    ]);
}


   
}
