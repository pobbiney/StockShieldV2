@php
    $pageName = 'stock';
    $subpageName = 'reorder';
    $isSatelliteStore = $isSatelliteStore ?? false;
    $uniquePct = $pendingCount > 0 ? round(($uniqueItems / $pendingCount) * 100) : 0;
    $storeBreakdown = $liststock->groupBy('store_id')->map(function ($rows) {
        return [
            'name' => optional($rows->first()->storename)->name ?? 'Unknown',
            'count' => $rows->count(),
        ];
    });
    $topStorePct = ($pendingCount > 0 && $storeBreakdown->isNotEmpty())
        ? round(($storeBreakdown->sortByDesc('count')->first()['count'] / $pendingCount) * 100)
        : 0;
    $oldAddItem = old('item') ? $getItemid->firstWhere('id', (int) old('item')) : null;
    $stockItemsJson = $getItemid->map(function ($item) {
        return [
            'id' => $item->id,
            'code' => $item->item_code ?? '',
            'name' => $item->name ?? '',
            'store' => optional($item->storename)->name ?? '',
            'storeId' => (int) $item->store_id,
        ];
    })->values();
@endphp

@extends('layouts.backendapp')

@section('page-alerts')
@endsection

@section('css')
<style>
    .se-page { padding: 0 0.5rem 2rem; }

    .se-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 60%, #4f8ef7 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(13, 110, 253, 0.25);
    }

    .se-hero::before,
    .se-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .se-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .se-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .se-hero-inner { position: relative; z-index: 1; }

    .se-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.85rem;
        border-radius: 2rem;
        background: rgba(255, 255, 255, 0.15);
        font-size: 0.78rem;
        font-weight: 600;
        margin-bottom: 0.85rem;
        border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .se-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .se-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 560px;
    }

    .se-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .se-hero .breadcrumb-item.active { color: #fff; }

    .btn-hero-add {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 2rem;
        border: none;
        background: #fff;
        color: #0d6efd;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-hero-add:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.16);
        color: #0b5ed7;
    }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: seStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.12s; }
    .stat-card:nth-child(3) { animation-delay: 0.19s; }
    .stat-card:nth-child(4) { animation-delay: 0.26s; }

    @keyframes seStatIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .stat-card.pending .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-card.qty     .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }
    .stat-card.value   .stat-card-icon { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
    .stat-card.unique  .stat-card-icon { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }

    .stat-card-value {
        font-size: clamp(1.5rem, 4vw, 2.25rem);
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.pending .stat-card-value { color: #0d6efd; }
    .stat-card.qty     .stat-card-value { color: #d97706; }
    .stat-card.value   .stat-card-value { color: #16a34a; }
    .stat-card.unique  .stat-card-value { color: #7c3aed; }

    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0 0 0.85rem; }

    .stat-bar-wrap {
        height: 4px;
        background: #f1f5f9;
        border-radius: 2rem;
        overflow: hidden;
    }

    .stat-card.pending .stat-bar-fill { background: #0d6efd; }
    .stat-card.qty     .stat-bar-fill { background: #d97706; }
    .stat-card.value   .stat-bar-fill { background: #16a34a; }
    .stat-card.unique  .stat-bar-fill { background: #7c3aed; }

    .stat-bar-fill { height: 100%; border-radius: 2rem; }
    .stat-card-meta { font-size: 0.72rem; color: #94a3b8; margin-top: 0.4rem; }

    .stat-pct-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 2rem;
        background: rgba(124, 58, 237, 0.12);
        color: #7c3aed;
    }

    .dist-strip {
        border-radius: 1rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 1rem 1.25rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: seStatIn 0.5s ease 0.3s both;
    }

    .dist-strip-title {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        margin-bottom: 0.75rem;
    }

    .dist-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.78rem;
        font-weight: 600;
        color: #475569;
        margin: 0 0.35rem 0.35rem 0;
    }

    .dist-chip strong { color: #0d6efd; }

    .se-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: seStatIn 0.5s ease 0.32s both;
        background: #fff;
    }

    .se-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .se-table-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.15rem;
    }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
        font-size: 0.78rem;
        font-weight: 600;
    }

    #stockEntryTable thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        border: none;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.9rem 1rem;
        background: #f8fafc;
        white-space: nowrap;
    }

    #stockEntryTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #stockEntryTable tbody tr:nth-child(even) { background: #fafafa; }
    #stockEntryTable tbody tr:hover { background: #f8fafc !important; }

    .code-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.78rem;
        font-weight: 600;
        font-family: monospace;
    }

    .batch-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(8, 145, 178, 0.1);
        color: #0891b2;
        font-size: 0.78rem;
        font-weight: 600;
        font-family: monospace;
    }

    .qty-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(217, 119, 6, 0.1);
        color: #d97706;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .cost-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(22, 163, 74, 0.1);
        color: #16a34a;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        background: rgba(255, 193, 7, 0.15);
        color: #b45309;
    }

    .btn-action {
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }

    .btn-action.edit { color: #0d6efd; }
    .btn-action.edit:hover { background: #0d6efd; color: #fff; transform: scale(1.08); }
    .btn-action.delete { color: #dc3545; }
    .btn-action.delete:hover { background: #dc3545; color: #fff; transform: scale(1.08); }

    .se-empty {
        text-align: center;
        padding: 3rem 2rem;
        color: #64748b;
    }

    .se-empty-visual {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(13, 110, 253, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.75rem;
        color: #0d6efd;
    }

    .stock-modal .modal-content {
        border-radius: 1.25rem;
        border: none;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
    }

    .stock-modal .modal-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 100%);
        color: #fff;
        border: none;
        padding: 1.25rem 1.5rem;
    }

    .stock-modal .modal-header .btn-close {
        filter: invert(1) grayscale(1) brightness(2);
    }

    .stock-modal .modal-body {
        padding: 1.25rem 1.5rem;
        max-height: calc(100vh - 220px);
        overflow-y: auto;
    }

    .stock-modal .form-section {
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .stock-modal .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .stock-modal .section-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        margin-bottom: 0.85rem;
    }

    .stock-modal .form-control,
    .stock-modal .form-select {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.875rem;
        min-height: 46px;
    }

    .stock-modal .form-control:focus,
    .stock-modal .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
    }

    .stock-modal .form-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.35rem;
    }

    .stock-modal .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 1rem 1.5rem;
        background: #f8fafc;
    }

    .btn-modal-save {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.55rem 1.25rem;
        border-radius: 0.625rem;
        border: none;
        background: #0d6efd;
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-modal-save:hover { background: #0b5ed7; color: #fff; }

    .btn-modal-clear {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.55rem 1.1rem;
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .item-autocomplete {
        position: relative;
    }

    .item-autocomplete-input-wrap {
        position: relative;
    }

    .item-autocomplete-icon {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.9rem;
        pointer-events: none;
        z-index: 2;
    }

    .item-autocomplete-input {
        padding-left: 2.35rem !important;
        padding-right: 2.25rem !important;
    }

    .item-autocomplete-clear {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        border: none;
        background: #f1f5f9;
        color: #64748b;
        border-radius: 50%;
        font-size: 1.1rem;
        line-height: 1;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 2;
    }

    .item-autocomplete-clear:hover {
        background: #e2e8f0;
        color: #334155;
    }

    .item-autocomplete.has-value .item-autocomplete-clear {
        display: inline-flex;
    }

    .item-autocomplete-list {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 4px);
        z-index: 1060;
        max-height: 320px;
        overflow-y: auto;
        margin: 0;
        padding: 0.35rem;
        list-style: none;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.625rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .item-autocomplete-list:empty,
    .item-autocomplete-list[hidden] {
        display: none !important;
    }

    .item-autocomplete-option {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
        padding: 0.55rem 0.7rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background 0.12s ease;
    }

    .item-autocomplete-option:hover,
    .item-autocomplete-option.active {
        background: rgba(13, 110, 253, 0.08);
    }

    .item-autocomplete-option .item-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: #0f172a;
    }

    .item-autocomplete-option .item-code {
        font-size: 0.72rem;
        font-weight: 600;
        color: #64748b;
        font-family: monospace;
    }

    .item-autocomplete-empty {
        padding: 0.75rem;
        text-align: center;
        font-size: 0.82rem;
        color: #94a3b8;
    }

    .field-hint {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.25rem;
    }
</style>
@endsection

@section('content')

<div class="container-fluid se-page px-3 px-lg-4 mt-3">

    <div class="se-hero">
        <div class="se-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-7">
                    <div class="se-hero-badge">
                        <i class="bi bi-box-arrow-in-down"></i> Stock Entry
                    </div>
                    <h2>{{ $isSatelliteStore ? 'Record Stock (All Items)' : 'Receive & Record Stock' }}</h2>
                    <p>
                        @if($isSatelliteStore)
                            Capture incoming inventory using any active item. Entries stay pending until approved via Stock Approval and are recorded in satellite inventory.
                        @elseif($requiresApproval)
                            Capture incoming inventory details. Entries stay pending until approved by a user with Stock Approval access.
                        @else
                            Capture incoming inventory with batch, supplier, and cost details. Your entries are approved immediately.
                        @endif
                    </p>
                </div>
                <div class="col-lg-5 d-flex flex-column align-items-lg-end gap-3">
                    <nav aria-label="breadcrumb" class="d-none d-lg-block">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Stock</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Stock Entry</li>
                        </ol>
                    </nav>
                    <button type="button" class="btn-hero-add" data-bs-toggle="modal" data-bs-target="#addStockModal">
                        <i class="bi bi-plus-lg"></i> Add Stock Entry
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card pending">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <div class="stat-card-value" data-count="{{ $pendingCount }}">{{ number_format($pendingCount) }}</div>
                <p class="stat-card-label">Pending Entries</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">Awaiting approval</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card qty">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="stat-card-value" data-count="{{ $totalQty }}">{{ number_format($totalQty) }}</div>
                <p class="stat-card-label">Total Quantity</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">Units across pending entries</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card value">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-currency-dollar"></i></div>
                </div>
                <div class="stat-card-value">{{ number_format($totalValue, 2) }}</div>
                <p class="stat-card-label">Estimated Value</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">Qty &times; unit cost</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card unique">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-tags"></i></div>
                    @if($pendingCount > 0)<span class="stat-pct-badge">{{ $uniquePct }}%</span>@endif
                </div>
                <div class="stat-card-value" data-count="{{ $uniqueItems }}">{{ number_format($uniqueItems) }}</div>
                <p class="stat-card-label">Unique Items</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $uniquePct }}%"></div></div>
                <p class="stat-card-meta">Different products pending</p>
            </div>
        </div>
    </div>

    @if($storeBreakdown->count() > 1)
    <div class="dist-strip">
        <div class="dist-strip-title"><i class="bi bi-shop me-1"></i> Pending by Store</div>
        @foreach($storeBreakdown->sortByDesc('count') as $store)
            <span class="dist-chip">
                {{ $store['name'] }} <strong>{{ $store['count'] }}</strong>
            </span>
        @endforeach
    </div>
    @endif

    <div class="se-table-card">
        <div class="se-table-head">
            <div>
                <h5><i class="bi bi-table me-2 text-primary"></i>Pending Stock Entries</h5>
                <small class="text-muted">
                    @if($requiresApproval)
                        Your submitted entries appear here until an approver accepts them into inventory
                    @else
                        Pending entries awaiting approval action
                    @endif
                </small>
            </div>
            @if($pendingCount > 0)
                <span class="record-count-badge">
                    <i class="bi bi-collection"></i> {{ number_format($pendingCount) }} record{{ $pendingCount !== 1 ? 's' : '' }}
                </span>
            @endif
        </div>

        @if($liststock->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="stockEntryTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Batch</th>
                            <th>Expiry</th>
                            <th>Qty</th>
                            <th>Unit Cost</th>
                            <th>PO Ref</th>
                            <th>Supplier</th>
                            <th>Store</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($liststock as $lists)
                            @php
                                $itemCode = optional($lists->itemcode)->item_code ?? '—';
                                $itemName = optional($lists->itemname)->name ?? 'Unknown';
                                $supplierName = optional($lists->supname)->company
                                    ?? optional($lists->supname)->supplier
                                    ?? '—';
                                $storeName = optional($lists->storename)->name ?? '—';
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $itemName }}</div>
                                    <span class="code-badge">{{ $itemCode }}</span>
                                </td>
                                <td><span class="batch-badge">{{ $lists->batch_number }}</span></td>
                                <td>{{ $lists->expiry_date ?: '—' }}</td>
                                <td><span class="qty-badge">{{ number_format($lists->qty) }}</span></td>
                                <td><span class="cost-badge">{{ number_format((float) $lists->amount, 2) }}</span></td>
                                <td>{{ $lists->purchase_order ?: '—' }}</td>
                                <td>{{ $supplierName }}</td>
                                <td>{{ $storeName }}</td>
                                <td><span class="status-badge"><i class="bi bi-clock"></i> Pending</span></td>
                                <td class="text-end text-nowrap">
                                    <button type="button"
                                            class="btn-action edit showmodal me-1"
                                            title="Edit"
                                            data-url="{{ route('stock-id', ['id' => $lists->id, 'source' => ($isSatelliteStore ?? false) ? 'satellite' : 'central']) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button"
                                            class="btn-action delete btn-confirm-delete"
                                            title="Delete"
                                            data-item-name="{{ $itemName }}"
                                            data-delete-url="{{ url('stockEntry/'.$lists->id.'/delete'.($isSatelliteStore ?? false ? '?source=satellite' : '')) }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="se-empty">
                <div class="se-empty-visual"><i class="bi bi-inbox"></i></div>
                <h5 class="fw-semibold text-dark">No pending stock entries</h5>
                <p>Add a new stock entry to begin recording incoming inventory.</p>
                <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addStockModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Stock Entry
                </button>
            </div>
        @endif
    </div>
</div>

{{-- Add Stock Modal --}}
<div class="modal fade stock-modal" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStockModalLabel">
                    <i class="bi bi-box-arrow-in-down me-2"></i>Add Stock Entry
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form enctype="multipart/form-data" method="POST" action="{{ route('add-stock-process') }}" id="addStockForm">
                @csrf
                <div class="modal-body">
                    <div class="form-section">
                        <div class="section-label">Item &amp; Batch</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="add_item_search">Item <span class="text-danger">*</span></label>
                                <div class="item-autocomplete" id="addItemAutocomplete">
                                    <div class="item-autocomplete-input-wrap">
                                        <i class="bi bi-search item-autocomplete-icon"></i>
                                        <input type="text"
                                               class="form-control item-autocomplete-input"
                                               id="add_item_search"
                                               placeholder="Start typing item name or code…"
                                               autocomplete="off"
                                               value="{{ $oldAddItem ? $oldAddItem->item_code . ' — ' . $oldAddItem->name : '' }}">
                                        <button type="button" class="item-autocomplete-clear" aria-label="Clear item">&times;</button>
                                    </div>
                                    <input type="hidden" name="item" id="add_item" value="{{ old('item') }}">
                                    <ul class="item-autocomplete-list" id="add_item_list" role="listbox" hidden></ul>
                                </div>
                                @error('item') <small class="text-danger">{{ $message }}</small> @enderror
                                <div class="field-hint">{{ $isSatelliteStore ? 'All items in the system — store shown in suggestions' : 'Click the field to browse all items for the selected store, or type to search' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="add_batch">Batch Number</label>
                                <input type="text" class="form-control" id="add_batch" name="batch_number"
                                       value="{{ old('batch_number') }}" placeholder="Auto-generated if empty">
                                @error('batch_number') <small class="text-danger">{{ $message }}</small> @enderror
                                <div class="field-hint">Leave blank to auto-generate</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-label">Dates</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="add_mfg">Manufacturing Date</label>
                                <input type="text" class="form-control datepicker1" id="add_mfg"
                                       name="manufacturing_date" value="{{ old('manufacturing_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="add_expiry">Expiry Date</label>
                                <input type="text" class="form-control datepicker2" id="add_expiry"
                                       name="expiry_date" value="{{ old('expiry_date') }}">
                                @error('expiry_date') <small class="text-danger">{{ $message }}</small> @enderror
                                <div class="field-hint">Required unless receiving into store ID 2</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-label">Supplier &amp; References</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="add_supplier">Supplier <span class="text-danger">*</span></label>
                                <select class="form-select" name="supplier" id="add_supplier">
                                    <option value="" disabled {{ old('supplier') ? '' : 'selected' }}>Choose supplier</option>
                                    @foreach($listsup as $sup)
                                        <option value="{{ $sup->id }}" {{ old('supplier') == $sup->id ? 'selected' : '' }}>
                                            {{ $sup->company }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="add_po">Purchase Order</label>
                                <input type="text" class="form-control" id="add_po" name="purchase_order"
                                       value="{{ old('purchase_order') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="add_waybill">Waybill <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_waybill" name="waybill"
                                       value="{{ old('waybill') }}">
                                @error('waybill') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="add_award">Contract Reference <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_award" name="award_letter"
                                       value="{{ old('award_letter') }}">
                                @error('award_letter') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="add_barcode">Barcode</label>
                                <input type="text" class="form-control" id="add_barcode" name="bar_code"
                                       value="{{ old('bar_code') }}">
                                <div class="field-hint">Leave blank to auto-generate</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-label">Quantity, Cost &amp; Location</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label" for="add_qty">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_qty" name="quantity"
                                       value="{{ old('quantity') }}" min="1">
                                @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="add_amount">Unit Cost <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_amount" name="amount"
                                       value="{{ old('amount') }}">
                                @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="add_store">Store Location <span class="text-danger">*</span></label>
                                @if($requiresApproval && $activeStoreId)
                                    <input type="hidden" name="store" value="{{ $activeStoreId }}">
                                    <input type="text" class="form-control" id="add_store"
                                           value="{{ optional($getstoreId->firstWhere('id', $activeStoreId))->name ?? 'Active store' }}"
                                           readonly>
                                    <div class="field-hint">Stock will be submitted for approval for this store</div>
                                @else
                                    <select class="form-select" name="store" id="add_store">
                                        <option value="" disabled {{ old('store') ? '' : 'selected' }}>Choose store</option>
                                        @foreach($getstoreId as $liststore)
                                            <option value="{{ $liststore->id }}" {{ (string) old('store', $activeStoreId) === (string) $liststore->id ? 'selected' : '' }}>
                                                {{ $liststore->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('store') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="add_comment">Comment</label>
                                <textarea class="form-control" id="add_comment" name="comment" rows="2"
                                          placeholder="Optional notes">{{ old('comment') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-clear" id="clearAddForm">
                        <i class="bi bi-arrow-counterclockwise"></i> Clear
                    </button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-check-lg"></i>
                        @if($requiresApproval)
                            Submit for Approval
                        @else
                            Save &amp; Approve
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Stock Modal --}}
<div class="modal fade stock-modal" id="editStockModal" tabindex="-1" aria-labelledby="editStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStockModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Stock Entry
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form enctype="multipart/form-data" method="POST" action="{{ route('edit-stock-process') }}" id="editStockForm">
                @csrf
                <div class="modal-body">
                    <div class="form-section">
                        <div class="section-label">Item &amp; Batch</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="edit_item_search">Item <span class="text-danger">*</span></label>
                                <div class="item-autocomplete" id="editItemAutocomplete">
                                    <div class="item-autocomplete-input-wrap">
                                        <i class="bi bi-search item-autocomplete-icon"></i>
                                        <input type="text"
                                               class="form-control item-autocomplete-input"
                                               id="edit_item_search"
                                               placeholder="Start typing item name or code…"
                                               autocomplete="off">
                                        <button type="button" class="item-autocomplete-clear" aria-label="Clear item">&times;</button>
                                    </div>
                                    <input type="hidden" name="item" id="edit_item" value="">
                                    <ul class="item-autocomplete-list" id="edit_item_list" role="listbox" hidden></ul>
                                </div>
                                <div class="field-hint">Suggestions appear as you type</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="edit_batch">Batch Number</label>
                                <input type="text" class="form-control" id="edit_batch" name="batch_number">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-label">Dates</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="edit_mfg">Manufacturing Date</label>
                                <input type="text" class="form-control datepicker1" id="edit_mfg" name="manufacturing_date">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="edit_expiry">Expiry Date</label>
                                <input type="text" class="form-control datepicker2" id="edit_expiry" name="expiry_date">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-label">Supplier &amp; References</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="edit_supplier">Supplier</label>
                                <select class="form-select" name="supplier" id="edit_supplier">
                                    <option value="" disabled>Choose supplier</option>
                                    @foreach($listsup as $sup)
                                        <option value="{{ $sup->id }}">{{ $sup->company }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="edit_po">Purchase Order</label>
                                <input type="text" class="form-control" id="edit_po" name="purchase_order">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="edit_waybill">Waybill</label>
                                <input type="text" class="form-control" id="edit_waybill" name="waybill">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="edit_award">Contract Reference</label>
                                <input type="text" class="form-control" id="edit_award" name="award_letter">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="edit_barcode">Barcode</label>
                                <input type="text" class="form-control" id="edit_barcode" name="bar_code">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-label">Quantity, Cost &amp; Location</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label" for="edit_qty">Quantity</label>
                                <input type="number" class="form-control" id="edit_qty" name="quantity" min="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="edit_amount">Unit Cost</label>
                                <input type="text" class="form-control" id="edit_amount" name="amount">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="edit_store">Store Location</label>
                                <select class="form-select" name="store" id="edit_store">
                                    <option value="" disabled>Choose store</option>
                                    @foreach($getstoreId as $liststore)
                                        <option value="{{ $liststore->id }}">{{ $liststore->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="edit_comment">Comment</label>
                                <textarea class="form-control" id="edit_comment" name="comment" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-check-lg"></i> Update Entry
                    </button>
                </div>
                <input type="hidden" name="stock_id" id="edit_stock_id">
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const StockAlert = {
    _base(opts) {
        return Swal.fire(Object.assign({
            width: '28rem',
            padding: '1.5rem 1.75rem 1.35rem',
            buttonsStyling: false,
            customClass: {
                popup: 'staff-swal-popup',
                title: 'staff-swal-title',
                htmlContainer: 'staff-swal-text',
                confirmButton: 'btn staff-swal-confirm ' + (opts.btnClass || 'neutral'),
                cancelButton: 'btn btn-light border staff-swal-cancel',
            },
        }, opts));
    },
    success(title, text) {
        return this._base({ icon: 'success', title, text, btnClass: 'success', timer: 2800, timerProgressBar: true, confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Done' });
    },
    error(title, text) {
        return this._base({ icon: 'error', title, text, btnClass: 'error', confirmButtonText: '<i class="bi bi-x-lg me-1"></i> Close' });
    },
    confirmDelete(name, onConfirm) {
        return this._base({
            icon: 'warning',
            title: 'Delete Entry?',
            html: `Remove pending stock entry for <strong>${name}</strong>? This cannot be undone.`,
            btnClass: 'error',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, delete',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed && onConfirm) onConfirm();
        });
    },
};

const STOCK_ITEMS = @json($stockItemsJson);

function ItemAutocomplete(root, items, options) {
    options = options || {};
    this.root = root;
    this.items = items || [];
    this.getStoreFilterId = options.getStoreFilterId || null;
    this.input = root.querySelector('.item-autocomplete-input');
    this.hidden = root.querySelector('input[type="hidden"]');
    this.list = root.querySelector('.item-autocomplete-list');
    this.clearBtn = root.querySelector('.item-autocomplete-clear');
    this.activeIndex = -1;
    this.selectedItem = null;

    this.input.addEventListener('input', () => this.onInput());
    this.input.addEventListener('focus', () => this.onFocus());
    this.input.addEventListener('keydown', (e) => this.onKeydown(e));
    this.clearBtn.addEventListener('click', () => this.clear());

    document.addEventListener('click', (e) => {
        if (!this.root.contains(e.target)) {
            this.closeList();
        }
    });
}

ItemAutocomplete.prototype.labelFor = function (item) {
    return (item.code ? item.code + ' — ' : '') + item.name;
};

ItemAutocomplete.prototype.poolForStore = function () {
    const storeId = this.getStoreFilterId ? this.getStoreFilterId() : null;
    if (!storeId) {
        return this.items.slice();
    }
    return this.items.filter(function (item) {
        return String(item.storeId) === String(storeId);
    });
};

ItemAutocomplete.prototype.filter = function (term) {
    const pool = this.poolForStore();
    const q = term.trim().toLowerCase();
    if (!q) {
        return pool;
    }

    const parts = q.split(/\s*[—–-]\s*/).map(function (p) { return p.trim(); }).filter(Boolean);
    const needles = parts.length > 1 ? parts : [q];

    return pool.filter(function (item) {
        const name = (item.name || '').toLowerCase();
        const code = String(item.code || '').toLowerCase();
        return needles.some(function (n) {
            return name.indexOf(n) > -1 || code.indexOf(n) > -1;
        });
    });
};

ItemAutocomplete.prototype.render = function (matches) {
    this.list.innerHTML = '';
    this.activeIndex = -1;

    if (!matches.length) {
        const empty = document.createElement('li');
        empty.className = 'item-autocomplete-empty';
        empty.textContent = 'No items match your search';
        this.list.appendChild(empty);
        this.list.hidden = false;
        return;
    }

    const self = this;
    matches.forEach(function (item, index) {
        const li = document.createElement('li');
        li.className = 'item-autocomplete-option';
        li.setAttribute('role', 'option');
        li.dataset.index = index;

        const nameSpan = document.createElement('span');
        nameSpan.className = 'item-name';
        nameSpan.textContent = item.name;
        li.appendChild(nameSpan);

        if (item.code) {
            const codeSpan = document.createElement('span');
            codeSpan.className = 'item-code';
            codeSpan.textContent = item.code;
            li.appendChild(codeSpan);
        }

        if (item.store) {
            const storeSpan = document.createElement('span');
            storeSpan.className = 'item-code';
            storeSpan.textContent = item.store;
            li.appendChild(storeSpan);
        }

        li.addEventListener('mousedown', function (e) {
            e.preventDefault();
            self.select(item);
        });
        self.list.appendChild(li);
    });

    this.list.hidden = false;
    this._currentMatches = matches;
};

ItemAutocomplete.prototype.select = function (item) {
    this.selectedItem = item;
    this.input.value = this.labelFor(item);
    this.hidden.value = item.id;
    this.root.classList.add('has-value');
    this.closeList();
};

ItemAutocomplete.prototype.setById = function (id) {
    const item = this.items.find(function (i) { return String(i.id) === String(id); });
    if (item) {
        this.select(item);
    } else {
        this.clear(false);
    }
};

ItemAutocomplete.prototype.clear = function (focusInput) {
    this.selectedItem = null;
    this.input.value = '';
    this.hidden.value = '';
    this.root.classList.remove('has-value');
    this.closeList();
    if (focusInput !== false) {
        this.input.focus();
    }
};

ItemAutocomplete.prototype.closeList = function () {
    this.list.innerHTML = '';
    this.list.hidden = true;
    this.activeIndex = -1;
    this._currentMatches = [];
};

ItemAutocomplete.prototype.onFocus = function () {
    this.render(this.filter(this.input.value));
};

ItemAutocomplete.prototype.onInput = function () {
    const term = this.input.value;
    if (this.selectedItem && this.labelFor(this.selectedItem) !== term) {
        this.selectedItem = null;
        this.hidden.value = '';
        this.root.classList.remove('has-value');
    }
    this.render(this.filter(term));
};

ItemAutocomplete.prototype.highlight = function (index) {
    const options = this.list.querySelectorAll('.item-autocomplete-option');
    options.forEach(function (el, i) {
        el.classList.toggle('active', i === index);
    });
    this.activeIndex = index;
    if (options[index]) {
        options[index].scrollIntoView({ block: 'nearest' });
    }
};

ItemAutocomplete.prototype.onKeydown = function (e) {
    const matches = this._currentMatches || [];
    const options = this.list.querySelectorAll('.item-autocomplete-option');

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (!options.length) {
            this.render(this.filter(this.input.value));
            return;
        }
        const next = this.activeIndex < options.length - 1 ? this.activeIndex + 1 : 0;
        this.highlight(next);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (!options.length) return;
        const prev = this.activeIndex > 0 ? this.activeIndex - 1 : options.length - 1;
        this.highlight(prev);
    } else if (e.key === 'Enter') {
        if (this.activeIndex >= 0 && matches[this.activeIndex]) {
            e.preventDefault();
            this.select(matches[this.activeIndex]);
        }
    } else if (e.key === 'Escape') {
        this.closeList();
    }
};

let addItemAutocomplete;
let editItemAutocomplete;

function stockEntryStoreIdFromForm(formId, fallbackStoreId) {
    const form = document.getElementById(formId);
    if (!form) {
        return fallbackStoreId ? String(fallbackStoreId) : null;
    }
    const hidden = form.querySelector('input[name="store"]');
    if (hidden && hidden.value) {
        return String(hidden.value);
    }
    const select = form.querySelector('select[name="store"]');
    if (select && select.value) {
        return String(select.value);
    }
    return fallbackStoreId ? String(fallbackStoreId) : null;
}

$(document).ready(function () {
    const activeStoreId = @json($activeStoreId);

    addItemAutocomplete = new ItemAutocomplete(document.getElementById('addItemAutocomplete'), STOCK_ITEMS, {
        getStoreFilterId: function () {
            return stockEntryStoreIdFromForm('addStockForm', activeStoreId);
        },
    });
    editItemAutocomplete = new ItemAutocomplete(document.getElementById('editItemAutocomplete'), STOCK_ITEMS, {
        getStoreFilterId: function () {
            return stockEntryStoreIdFromForm('editStockForm', activeStoreId);
        },
    });

    $('#add_store').on('change', function () {
        addItemAutocomplete.clear(false);
        addItemAutocomplete.onFocus();
    });
    $('#edit_store').on('change', function () {
        editItemAutocomplete.clear(false);
        editItemAutocomplete.onFocus();
    });

    if (addItemAutocomplete.hidden.value) {
        addItemAutocomplete.root.classList.add('has-value');
    }

    if ($('#stockEntryTable').length && $.fn.DataTable) {
        $('#stockEntryTable').DataTable({
            order: [[0, 'asc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            language: { search: '', searchPlaceholder: 'Search entries…' },
            columnDefs: [{ orderable: false, targets: -1 }],
        });
    }

    $('body').on('click', '.showmodal', function () {
        const userUrl = $(this).data('url');

        $.get(userUrl, function (data) {
            $('#edit_stock_id').val(data.id);
            $('#edit_batch').val(data.batch_number);
            editItemAutocomplete.setById(data.item_id);
            $('#edit_mfg').val(data.manufacturing_date);
            $('#edit_expiry').val(data.expiry_date);
            $('#edit_supplier').val(data.supplier_id);
            $('#edit_po').val(data.purchase_order);
            $('#edit_waybill').val(data.waybill);
            $('#edit_qty').val(data.qty);
            $('#edit_award').val(data.award_letter);
            $('#edit_amount').val(data.amount);
            $('#edit_store').val(data.store_id);
            $('#edit_barcode').val(data.barcode);
            $('#edit_comment').val(data.comment);

            const editModal = new bootstrap.Modal(document.getElementById('editStockModal'));
            editModal.show();
        }).fail(function () {
            StockAlert.error('Load Failed', 'Could not load stock entry details. Please try again.');
        });
    });

    $('#clearAddForm').on('click', function () {
        const form = document.getElementById('addStockForm');
        form.reset();
        addItemAutocomplete.clear(false);
    });

    @if($errors->any() && !old('stock_id'))
    const addModal = new bootstrap.Modal(document.getElementById('addStockModal'));
    addModal.show();
    @endif
});

document.querySelectorAll('.stat-card-value[data-count]').forEach(function (el) {
    const target = parseInt(el.dataset.count, 10);
    if (isNaN(target) || target === 0) return;
    let current = 0;
    const step = Math.ceil(target / 30);
    const timer = setInterval(function () {
        current = Math.min(current + step, target);
        el.textContent = current.toLocaleString();
        if (current >= target) clearInterval(timer);
    }, 30);
});

document.querySelectorAll('.btn-confirm-delete').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const name = this.dataset.itemName;
        const url  = this.dataset.deleteUrl;
        StockAlert.confirmDelete(name, function () {
            window.location.href = url;
        });
    });
});

@if(session('message_success'))
StockAlert.success('Success!', @json(session('message_success')));
@endif

@if(session('message_error'))
StockAlert.error('Oops!', @json(session('message_error')));
@endif
</script>
@endsection
