@php
    $pageName = 'stock';
    $subpageName = 'stock-approval';
    $uniquePct = $pendingCount > 0 ? round(($uniqueItems / $pendingCount) * 100) : 0;
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .vse-page { padding: 0 0.5rem 2rem; }

    .vse-hero {
        background: linear-gradient(135deg, #92400e 0%, #d97706 55%, #f59e0b 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(217, 119, 6, 0.28);
    }

    .vse-hero::before,
    .vse-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .vse-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .vse-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .vse-hero-inner { position: relative; z-index: 1; }

    .vse-hero-badge {
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

    .vse-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .vse-hero p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 560px;
    }

    .vse-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .vse-hero .breadcrumb-item.active { color: #fff; }

    .store-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.95rem;
        border-radius: 2rem;
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.28);
        font-size: 0.82rem;
        font-weight: 600;
        margin-top: 0.75rem;
    }

    .btn-hero-back,
    .btn-hero-approve-all {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        transition: background 0.15s ease, transform 0.15s ease;
    }

    .btn-hero-back {
        border: 1px solid rgba(255, 255, 255, 0.35);
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }

    .btn-hero-back:hover {
        background: rgba(255, 255, 255, 0.22);
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-hero-approve-all {
        border: none;
        background: #fff;
        color: #d97706;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .btn-hero-approve-all:hover {
        color: #b45309;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.16);
    }

    .btn-hero-reject-all {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 2rem;
        border: none;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 16px rgba(220, 38, 38, 0.35);
        transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-hero-reject-all:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.42);
    }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: vseStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.12s; }
    .stat-card:nth-child(3) { animation-delay: 0.19s; }
    .stat-card:nth-child(4) { animation-delay: 0.26s; }

    @keyframes vseStatIn {
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

    .stat-card.pending .stat-card-icon { background: rgba(234, 88, 12, 0.12); color: #ea580c; }
    .stat-card.qty     .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-card.value   .stat-card-icon { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
    .stat-card.unique  .stat-card-icon { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }

    .stat-card-value {
        font-size: clamp(1.35rem, 3.5vw, 2rem);
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.pending .stat-card-value { color: #ea580c; }
    .stat-card.qty     .stat-card-value { color: #0d6efd; }
    .stat-card.value   .stat-card-value { color: #16a34a; }
    .stat-card.unique  .stat-card-value { color: #7c3aed; }

    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0 0 0.85rem; }

    .stat-bar-wrap {
        height: 4px;
        background: #f1f5f9;
        border-radius: 2rem;
        overflow: hidden;
    }

    .stat-bar-fill { height: 100%; border-radius: 2rem; }
    .stat-card.pending .stat-bar-fill { background: #ea580c; }
    .stat-card.qty     .stat-bar-fill { background: #0d6efd; }
    .stat-card.value   .stat-bar-fill { background: #16a34a; }
    .stat-card.unique  .stat-bar-fill { background: #7c3aed; }

    .stat-card-meta { font-size: 0.72rem; color: #94a3b8; margin-top: 0.4rem; }

    .stat-pct-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 2rem;
        background: rgba(124, 58, 237, 0.12);
        color: #7c3aed;
    }

    .vse-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: vseStatIn 0.5s ease 0.32s both;
        background: #fff;
    }

    .vse-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .vse-table-head h5 {
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
        background: rgba(217, 119, 6, 0.08);
        color: #d97706;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .vse-search-wrap {
        position: relative;
        min-width: 220px;
        flex: 1;
        max-width: 320px;
    }

    .vse-search-wrap i {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }

    .vse-search-wrap input {
        padding-left: 2.35rem;
        border-radius: 2rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.875rem;
        min-height: 42px;
    }

    .vse-search-wrap input:focus {
        border-color: #d97706;
        box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.12);
    }

    #viewStockEntryTable thead th {
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

    #viewStockEntryTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #viewStockEntryTable tbody tr:nth-child(even) { background: #fafafa; }
    #viewStockEntryTable tbody tr:hover { background: #fffbeb !important; }

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

    .price-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .total-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(22, 163, 74, 0.1);
        color: #16a34a;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .btn-approve-row {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.9rem;
        border-radius: 2rem;
        border: none;
        background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 3px 10px rgba(22, 163, 74, 0.28);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-approve-row:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(22, 163, 74, 0.35);
    }

    .btn-reject-row {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.9rem;
        border-radius: 2rem;
        border: none;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(220, 38, 38, 0.28);
        transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-reject-row:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(220, 38, 38, 0.35);
    }

    .action-group {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .reject-modal .modal-content {
        border-radius: 1.25rem;
        border: none;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
    }

    .reject-modal .modal-header {
        background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);
        color: #fff;
        border: none;
        padding: 1.25rem 1.5rem;
    }

    .reject-modal .modal-header .btn-close {
        filter: invert(1) grayscale(1) brightness(2);
    }

    .reject-modal .modal-body {
        padding: 1.35rem 1.5rem;
    }

    .reject-modal .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 1rem 1.5rem;
        background: #f8fafc;
    }

    .reject-modal .form-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.35rem;
    }

    .reject-modal textarea {
        border-radius: 0.75rem;
        border: 1.5px solid #e2e8f0;
        min-height: 120px;
        font-size: 0.875rem;
    }

    .reject-modal textarea:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
    }

    .btn-modal-reject {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.55rem 1.15rem;
        border-radius: 0.625rem;
        border: none;
        background: #dc2626;
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-modal-reject:hover { background: #b91c1c; color: #fff; }

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

    .reject-item-name {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.75rem;
        border-radius: 0.5rem;
        background: #fef2f2;
        color: #991b1b;
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .vse-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .vse-empty-visual {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(22, 163, 74, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 2rem;
        color: #16a34a;
    }

    .vse-empty h6 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.35rem;
    }
</style>
@endsection

@section('content')

<div class="container-fluid vse-page px-3 px-lg-4 mt-3">

    <div class="vse-hero">
        <div class="vse-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-7">
                    <div class="vse-hero-badge">
                        <i class="bi bi-shop"></i> Store Review
                    </div>
                    <h2>{{ $store->name }}</h2>
                    <p>Pending stock entries submitted for this store only. Approve individually or approve all at once.</p>
                    <div class="store-pill">
                        <i class="bi bi-geo-alt"></i>
                        Reviewing {{ number_format($pendingCount) }} pending {{ Str::plural('entry', $pendingCount) }} for {{ $store->name }}
                    </div>
                </div>
                <div class="col-lg-5 d-flex flex-column align-items-lg-end gap-3">
                    <nav aria-label="breadcrumb" class="d-none d-lg-block">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('stockApproval') }}" class="text-decoration-none">Stock Approval</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $store->name }}</li>
                        </ol>
                    </nav>
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <a href="{{ route('stockApproval') }}" class="btn-hero-back">
                            <i class="bi bi-arrow-left"></i> Back to Stores
                        </a>
                        @if($canApproveStock && $liststock->isNotEmpty())
                            <a href="{{ route('stock.approveAll', $storeId) }}"
                               class="btn-hero-approve-all"
                               onclick="return confirm('Approve all {{ $pendingCount }} pending {{ Str::plural('entry', $pendingCount) }} for {{ $store->name }}?')">
                                <i class="bi bi-check-all"></i> Approve All
                            </a>
                            <button type="button"
                                    class="btn-hero-reject-all"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectAllStockModal">
                                <i class="bi bi-x-octagon"></i> Reject All
                            </button>
                        @endif
                    </div>
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
                <div class="stat-card-value">{{ number_format($pendingCount) }}</div>
                <p class="stat-card-label">Pending Entries</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $pendingCount > 0 ? 100 : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">For {{ $store->name }} only</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card qty">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="stat-card-value">{{ number_format($totalQty) }}</div>
                <p class="stat-card-label">Total Quantity</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $pendingCount > 0 ? 100 : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">Units awaiting approval</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card value">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-cash-stack"></i></div>
                </div>
                <div class="stat-card-value">GH₵ {{ number_format($totalValue, 2) }}</div>
                <p class="stat-card-label">Estimated Value</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $pendingCount > 0 ? 100 : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">Qty &times; item price</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card unique">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-tags"></i></div>
                    @if($pendingCount > 0)
                        <span class="stat-pct-badge">{{ $uniquePct }}%</span>
                    @endif
                </div>
                <div class="stat-card-value">{{ number_format($uniqueItems) }}</div>
                <p class="stat-card-label">Unique Items</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $uniquePct }}%"></div>
                </div>
                <p class="stat-card-meta">Different products pending</p>
            </div>
        </div>
    </div>

    <div class="vse-table-card">
        <div class="vse-table-head">
            <div>
                <h5><i class="bi bi-list-check me-2" style="color:#d97706"></i>Pending Entries — {{ $store->name }}</h5>
                <small class="text-muted">Only stock submitted to this store is shown below</small>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($pendingCount > 0)
                    <span class="record-count-badge">
                        <i class="bi bi-collection"></i> {{ number_format($pendingCount) }} record{{ $pendingCount !== 1 ? 's' : '' }}
                    </span>
                @endif
                <div class="vse-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="tableSearch" placeholder="Search items…">
                </div>
            </div>
        </div>

        @if($liststock->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="viewStockEntryTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Batch</th>
                            <th>Expiry</th>
                            <th>Received</th>
                            <th>Qty</th>
                            <th>Item Price</th>
                            <th>Total Price</th>
                            <th>PO Ref</th>
                            <th>Supplier</th>
                            <th>Submitted By</th>
                            <th class="text-end">Action</th>
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
                                $submittedBy = optional($lists->staffname)->name ?? '—';
                                $lineTotal = (float) $lists->qty * (float) $lists->amount;
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $itemName }}</div>
                                    <span class="code-badge">{{ $itemCode }}</span>
                                </td>
                                <td><span class="batch-badge">{{ $lists->batch_number ?: '—' }}</span></td>
                                <td>{{ $lists->expiry_date ?: '—' }}</td>
                                <td>{{ $lists->created_at ? $lists->created_at->format('M d, Y') : '—' }}</td>
                                <td><span class="qty-badge">{{ number_format($lists->qty) }}</span></td>
                                <td><span class="price-badge">GH₵ {{ number_format((float) $lists->amount, 2) }}</span></td>
                                <td><span class="total-badge">GH₵ {{ number_format($lineTotal, 2) }}</span></td>
                                <td>{{ $lists->purchase_order ?: '—' }}</td>
                                <td>{{ $supplierName }}</td>
                                <td>{{ $submittedBy }}</td>
                                <td class="text-end">
                                    @if($canApproveStock)
                                        <div class="action-group">
                                            <a href="{{ route('stock.stockApproval', $lists->id) }}{{ ($isSatelliteStore ?? false) ? '?source=satellite' : '' }}"
                                               class="btn-approve-row"
                                               onclick="return confirm('Approve this stock entry for {{ $store->name }}?')">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </a>
                                            <button type="button"
                                                    class="btn-reject-row reject-stock-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectStockModal"
                                                    data-stock-id="{{ $lists->id }}"
                                                    data-item-name="{{ $itemName }}">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="vse-empty">
                <div class="vse-empty-visual"><i class="bi bi-check-circle"></i></div>
                <h6>No pending entries for {{ $store->name }}</h6>
                <p class="mb-3">All stock for this store has been approved or there are no submissions yet.</p>
                <a href="{{ route('stockApproval') }}" class="btn-hero-back" style="color:#d97706;border-color:#d97706;background:#fff;">
                    <i class="bi bi-arrow-left"></i> Back to Stock Approval
                </a>
            </div>
        @endif
    </div>

</div>

<div class="modal fade reject-modal" id="rejectStockModal" tabindex="-1" aria-labelledby="rejectStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="rejectStockForm" action="">
                @csrf
                <input type="hidden" name="form_type" value="single">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectStockModalLabel">
                        <i class="bi bi-x-octagon me-2"></i>Reject Stock Entry
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="reject-item-name">
                        <i class="bi bi-box-seam"></i>
                        <span id="rejectItemName">—</span>
                    </div>
                    <p class="text-muted small mb-3">
                        Provide a reason for rejecting this entry. The submitter will no longer see it as pending.
                    </p>
                    <div class="mb-0">
                        <label for="rejectionReason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('reason') is-invalid @enderror"
                                  id="rejectionReason"
                                  name="reason"
                                  rows="4"
                                  required
                                  minlength="3"
                                  maxlength="2000"
                                  placeholder="Explain why this stock entry is being rejected…">{{ old('form_type', 'single') === 'single' ? old('reason') : '' }}</textarea>
                        @error('reason')
                            @if(old('form_type', 'single') === 'single')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modal-clear" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-reject">
                        <i class="bi bi-x-circle"></i> Reject Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade reject-modal" id="rejectAllStockModal" tabindex="-1" aria-labelledby="rejectAllStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('stock.rejectAll', $storeId) }}">
                @csrf
                <input type="hidden" name="form_type" value="reject_all">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectAllStockModalLabel">
                        <i class="bi bi-x-octagon me-2"></i>Reject All Pending Entries
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="reject-item-name">
                        <i class="bi bi-shop"></i>
                        <span>{{ $store->name }} — {{ number_format($pendingCount) }} {{ Str::plural('entry', $pendingCount) }}</span>
                    </div>
                    <p class="text-muted small mb-3">
                        This will reject every pending stock entry for <strong>{{ $store->name }}</strong>.
                        The same reason will apply to all entries.
                    </p>
                    <div class="mb-0">
                        <label for="rejectAllReason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('reason') is-invalid @enderror"
                                  id="rejectAllReason"
                                  name="reason"
                                  rows="4"
                                  required
                                  minlength="3"
                                  maxlength="2000"
                                  placeholder="Explain why these stock entries are being rejected…">{{ old('form_type') === 'reject_all' ? old('reason') : '' }}</textarea>
                        @error('reason')
                            @if(old('form_type') === 'reject_all')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modal-clear" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-reject">
                        <i class="bi bi-x-octagon"></i> Reject All ({{ number_format($pendingCount) }})
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('tableSearch')?.addEventListener('keyup', function () {
        const searchValue = this.value.toLowerCase();
        document.querySelectorAll('#viewStockEntryTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(searchValue) ? '' : 'none';
        });
    });

    const rejectModal = document.getElementById('rejectStockModal');
    const rejectForm = document.getElementById('rejectStockForm');
    const rejectItemName = document.getElementById('rejectItemName');
    const rejectionReason = document.getElementById('rejectionReason');
    const rejectUrlTemplate = @json(route('stock.reject', ['id' => '__ID__'])) + (@json($isSatelliteStore ?? false) ? '?source=satellite' : '');

    document.querySelectorAll('.reject-stock-btn').forEach(button => {
        button.addEventListener('click', () => {
            const stockId = button.dataset.stockId;
            rejectForm.action = rejectUrlTemplate.replace('__ID__', stockId);
            rejectItemName.textContent = button.dataset.itemName || 'Stock entry';
            if (!@json($errors->has('reason'))) {
                rejectionReason.value = '';
            }
        });
    });

    @if($errors->has('reason'))
        @if(old('form_type') === 'reject_all')
            bootstrap.Modal.getOrCreateInstance(document.getElementById('rejectAllStockModal')).show();
        @else
            bootstrap.Modal.getOrCreateInstance(rejectModal).show();
        @endif
    @endif
</script>
@endsection
