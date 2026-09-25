<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Staff;
use App\Models\Store;
use App\Models\Ward;
use App\Services\StoreContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function __construct(
        protected StoreContext $storeContext
    ) {}

    protected function resolveActiveStoreId(): ?int
    {
        $activeStoreId = $this->storeContext->getActiveStoreId();

        if ($activeStoreId) {
            return $activeStoreId;
        }

        $mapped = $this->storeContext->getMappedStoreIds(Auth::user());

        if (count($mapped) === 1) {
            $this->storeContext->setActiveStore($mapped[0]);

            return $mapped[0];
        }

        return null;
    }

    protected function assertSatelliteStore(): Store
    {
        $activeStoreId = $this->resolveActiveStoreId();

        if (!$activeStoreId) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->route('choose-store')
                    ->with('message_error', 'Select a satellite store to manage wards.')
            );
        }

        $activeStore = Store::find($activeStoreId);

        if (!$activeStore || $activeStore->store_group !== 'satellite') {
            abort(403, 'Ward management is only available for satellite stores.');
        }

        return $activeStore;
    }
    public function getDepartmentView()
    {
        $list = Department::orderBy('name')->get();
        $staffCounts = Staff::select('department_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('department_id')
            ->where('department_id', '>', 0)
            ->groupBy('department_id')
            ->pluck('cnt', 'department_id');

        return view('settings.department', [
            'list' => $list,
            'totalDepartments' => $list->count(),
            'activeCount' => $list->where('status', 'Active')->count(),
            'inactiveCount' => $list->where('status', 'Inactive')->count(),
            'staffCounts' => $staffCounts,
        ]);
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
        $list = Store::orderBy('name')->get();

        $satelliteStores = $list->where('store_group', 'satellite')->where('status', 'Active')->values();
        $requisitionHub = Store::requisitionHub();

        return view('settings.store', [
            'list' => $list,
            'totalStores' => $list->count(),
            'activeCount' => $list->where('status', 'Active')->count(),
            'inactiveCount' => $list->where('status', 'Inactive')->count(),
            'centralCount' => $list->where('store_group', 'central')->count(),
            'satelliteCount' => $list->where('store_group', 'satellite')->count(),
            'satelliteStores' => $satelliteStores,
            'requisitionHub' => $requisitionHub,
        ]);
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
            'store_group' => 'nullable|in:central,satellite',
        ]);
            $insertCat = new Store();
            $insertCat->name = trim($request->name);
            $insertCat->status = $request->status;
            $insertCat->store_group = $request->store_group;
            $this->applyRequisitionRouting($insertCat, $request);

            $insertCat = $insertCat->save();
            return $insertCat ? back()->with('message_success','Store   added successfully') : back()->with('message_error','Something went wrong, please try again.');
    }

     public function updateStore(Request $request)
    {
          $request->validate([
             'store_id' => 'required|exists:stores,id',
            'name' => 'required',
            'status' => 'required',
            'store_group' => 'nullable|in:central,satellite',
        ]);
            $insertCat = Store::find($request->store_id);
            $insertCat->name = trim($request->name);
            $insertCat->status = $request->status;
            $insertCat->store_group = $request->store_group;
            $this->applyRequisitionRouting($insertCat, $request);

            $insertCat = $insertCat->save();
            return $insertCat ? back()->with('message_success','Store updated successfully') : back()->with('message_error','Something went wrong, please try again.');
    }

    protected function applyRequisitionRouting(Store $store, Request $request): void
    {
        if ($store->store_group !== 'satellite') {
            $store->route_requisitions_to_hub = false;
            $store->route_requisitions_to_central = true;
            if ($store->is_requisition_hub) {
                $store->is_requisition_hub = false;
            }
            return;
        }

        if ($store->is_requisition_hub) {
            $store->route_requisitions_to_hub = false;
            $store->route_requisitions_to_central = false;
            return;
        }

        $toHub = $request->boolean('route_requisitions_to_hub');
        $toCentral = $request->boolean('route_requisitions_to_central');

        if (!$toHub && !$toCentral) {
            $toCentral = true;
        }

        $store->route_requisitions_to_hub = $toHub;
        $store->route_requisitions_to_central = $toCentral;
    }

    public function setRequisitionHub(Request $request)
    {
        $request->validate([
            'hub_store_id' => 'nullable|exists:stores,id',
        ]);

        Store::query()->update(['is_requisition_hub' => false]);

        $hubId = $request->input('hub_store_id');

        if ($hubId) {
            $hub = Store::find($hubId);

            if (!$hub || $hub->store_group !== 'satellite' || $hub->status !== 'Active') {
                return back()->with('message_error', 'Requisition hub must be an active satellite store.');
            }

            $hub->is_requisition_hub = true;
            $hub->route_requisitions_to_hub = false;
            $hub->route_requisitions_to_central = false;
            $hub->save();
        }

        return back()->with('message_success', $hubId ? 'Requisition hub store updated.' : 'Requisition hub cleared.');
    }

    public function getWardView()
    {
        $activeStore = $this->assertSatelliteStore();

        $list = Ward::where('store_id', $activeStore->id)
            ->orderBy('name')
            ->get();

        return view('settings.ward', [
            'list' => $list,
            'activeStore' => $activeStore,
            'totalWards' => $list->count(),
            'activeCount' => $list->where('status', 'Active')->count(),
            'inactiveCount' => $list->where('status', 'Inactive')->count(),
        ]);
    }

    public function addWard(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $activeStore = $this->assertSatelliteStore();

        $saved = Ward::create([
            'name' => trim($request->name),
            'status' => $request->status,
            'store_id' => $activeStore->id,
        ]);

        return $saved
            ? back()->with('message_success', 'Ward added successfully.')
            : back()->with('message_error', 'Something went wrong, please try again.');
    }

    public function getWardID($id)
    {
        $activeStore = $this->assertSatelliteStore();

        $data = Ward::where('store_id', $activeStore->id)->findOrFail($id);

        return response()->json($data);
    }

    public function updateWard(Request $request)
    {
        $request->validate([
            'ward_id' => 'required|exists:wards,id',
            'name' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $activeStore = $this->assertSatelliteStore();

        $ward = Ward::where('store_id', $activeStore->id)->findOrFail($request->ward_id);
        $ward->name = trim($request->name);
        $ward->status = $request->status;

        return $ward->save()
            ? back()->with('message_success', 'Ward updated successfully.')
            : back()->with('message_error', 'Something went wrong, please try again.');
    }
}
