<?php

namespace App\Http\Controllers;

use App\Services\RejectedItemsService;
use App\Services\StoreContext;
use Illuminate\Http\Request;

class RejectedItemsController extends Controller
{
    public function __construct(
        protected StoreContext $storeContext,
        protected RejectedItemsService $rejectedItemsService,
    ) {}

    public function index(Request $request)
    {
        $storeIds = $this->storeContext->getScopedStoreIds();

        if (empty($storeIds) && !$this->storeContext->hasGlobalStoreAccess()) {
            return redirect()->route('choose-store')
                ->with('message_error', 'Select a store to view rejected items.');
        }

        $selectedType = $request->query('type', 'all');
        $validTypes = ['all', 'requisition', 'stock', 'issue', 'reverse_entry'];

        if (!in_array($selectedType, $validTypes, true)) {
            $selectedType = 'all';
        }

        $result = $this->rejectedItemsService->collect(
            $storeIds,
            $this->storeContext->hasGlobalStoreAccess(),
            $selectedType === 'all' ? null : $selectedType,
        );

        return view('requisition.RejectedItems', [
            'items'        => $result['items'],
            'typeCounts'   => $result['typeCounts'],
            'totalCount'   => $result['typeCounts']['all'],
            'selectedType' => $selectedType,
            'activeStore'  => $this->storeContext->getActiveStore(),
        ]);
    }
}
