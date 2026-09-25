@php
    $pageName = "stock";
    $subpageName = "requisition";
    $uniquePct = $pendingCount > 0 ? round(($uniqueItems / $pendingCount) * 100) : 0;
    $oldAddItem = old('item') ? $getItemid->firstWhere('id', (int) old('item')) : null;
    $reqItemsJson = $getItemid->map(function ($item) {
        return [
            'id' => $item->id,
            'code' => $item->item_code ?? '',
            'name' => $item->name ?? '',
            'uom' => optional($item->unitname)->name ?? '',
        ];
    })->values();
    $centralLabel = $fulfillmentLabel ?? ($centralStores->isNotEmpty()
        ? $centralStores->pluck('name')->join(', ')
        : 'Central Stores');
@endphp

@extends('layouts.backendapp')

@section('page-alerts')
@endsection

@section('css')
<style>
    .req-page { padding: 0 0.5rem 2rem; overflow: visible; }

    .req-page .row { overflow: visible; }

    .req-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 60%, #4f8ef7 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(13, 110, 253, 0.25);
    }

    .req-hero::before,
    .req-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .req-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .req-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .req-hero-inner { position: relative; z-index: 1; }

    .req-hero-badge {
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

    .req-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .req-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 560px;
    }

    .req-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .req-hero .breadcrumb-item.active { color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: statIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.12s; }
    .stat-card:nth-child(3) { animation-delay: 0.19s; }

    @keyframes statIn {
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
    .stat-card.unique  .stat-card-icon { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }
    .stat-card.submitted .stat-card-icon { background: rgba(16, 185, 129, 0.12); color: #059669; }
    .stat-card.open      .stat-card-icon { background: rgba(245, 158, 11, 0.12); color: #d97706; }

    .stat-card.pending .stat-card-value { color: #0d6efd; }
    .stat-card.qty     .stat-card-value { color: #d97706; }
    .stat-card.unique  .stat-card-value { color: #7c3aed; }
    .stat-card.submitted .stat-card-value { color: #059669; }
    .stat-card.open      .stat-card-value { color: #d97706; }

    .stat-card.pending .stat-bar-fill { background: #0d6efd; }
    .stat-card.qty     .stat-bar-fill { background: #d97706; }
    .stat-card.unique  .stat-bar-fill { background: #7c3aed; }
    .stat-card.submitted .stat-bar-fill { background: #059669; }
    .stat-card.open      .stat-bar-fill { background: #d97706; }

    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0 0 0.85rem; }

    .stat-card-value {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-bar-wrap {
        height: 4px;
        background: #f1f5f9;
        border-radius: 2rem;
        overflow: hidden;
    }

    .stat-card.pending .stat-bar-fill { background: #0d6efd; }
    .stat-card.qty     .stat-bar-fill { background: #d97706; }
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

    .add-item-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: #fff;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.75rem;
        overflow: visible;
        position: relative;
        z-index: 30;
        animation: statIn 0.5s ease 0.2s both;
    }

    .add-item-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .add-item-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
        color: #0f172a;
    }

    .add-item-body { padding: 1.25rem 1.5rem 1.5rem; overflow: visible; }

    .add-item-card .form-control,
    .add-item-card .form-select {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.875rem;
        min-height: 46px;
    }

    .add-item-card .form-control:focus,
    .add-item-card .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
    }

    .add-item-card .form-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.35rem;
    }

    .btn-add-line {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        padding: 0.65rem 1.25rem;
        border-radius: 0.625rem;
        border: none;
        background: #16a34a;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 600;
        height: 46px;
        width: 100%;
    }

    .btn-add-line:hover { background: #15803d; color: #fff; }

    .item-autocomplete { position: relative; z-index: 50; }
    .item-autocomplete-input-wrap { position: relative; }

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

    .item-autocomplete.has-value .item-autocomplete-clear { display: inline-flex; }

    .item-autocomplete-list {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 4px);
        z-index: 9999;
        max-height: 280px;
        overflow-y: auto;
        overflow-x: hidden;
        margin: 0;
        padding: 0.35rem;
        list-style: none;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.625rem;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.16);
    }

    .item-autocomplete-list:empty,
    .item-autocomplete-list[hidden] { display: none !important; }

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
    .item-autocomplete-option.active { background: rgba(13, 110, 253, 0.08); }

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
        padding: 0.65rem 0.7rem;
        font-size: 0.82rem;
        color: #94a3b8;
    }

    .field-hint {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.25rem;
    }

    .req-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: statIn 0.5s ease 0.32s both;
    }

    .req-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .req-table-head h5 {
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

    #reqTable thead th {
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

    #reqTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #reqTable tbody tr:nth-child(even) { background: #fafafa; }
    #reqTable tbody tr:hover { background: #f8fafc !important; }

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

    .btn-delete-req {
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #dc3545;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-delete-req:hover {
        background: #dc3545;
        color: #fff;
        transform: scale(1.08);
    }

    .req-empty {
        text-align: center;
        padding: 3rem 2rem;
        color: #64748b;
    }

    .req-empty-visual {
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

    .req-submit-bar {
        padding: 1rem 1.5rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .btn-submit-req {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 2rem;
        border: none;
        background: #0d6efd;
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-submit-req:hover { background: #0b5ed7; color: #fff; }

    .stock-hint {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.25rem;
    }

    .store-context-banner {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        padding: 0.85rem 1.15rem;
        border-radius: 0.875rem;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        margin-top: 1.25rem;
        font-size: 0.85rem;
    }

    .store-context-banner .store-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 2rem;
        background: rgba(255, 255, 255, 0.18);
        font-weight: 600;
    }

    .store-context-banner .arrow-icon { opacity: 0.7; }

    .qty-col-head {
        text-align: center !important;
        min-width: 110px;
    }

    .qty-cell {
        text-align: center;
        vertical-align: middle;
    }

    .qty-requested {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        padding: 0.25rem 0.65rem;
        border-radius: 0.375rem;
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .qty-issued {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        padding: 0.25rem 0.65rem;
        border-radius: 0.375rem;
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .qty-issued.zero {
        background: #f1f5f9;
        color: #94a3b8;
    }

    .qty-dash {
        color: #cbd5e1;
        font-weight: 600;
    }

    .source-store-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(99, 102, 241, 0.1);
        color: #4f46e5;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.fulfilled  { background: rgba(16, 185, 129, 0.15); color: #047857; }
    .status-badge.partial    { background: rgba(245, 158, 11, 0.15); color: #b45309; }
    .status-badge.rejected   { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
    .status-badge.approved   { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .status-badge.submitted  { background: rgba(255, 193, 7, 0.15); color: #b45309; }
    .status-badge.draft      { background: rgba(148, 163, 184, 0.15); color: #475569; }

    .fulfillment-bar-wrap {
        height: 5px;
        background: #e2e8f0;
        border-radius: 2rem;
        overflow: hidden;
        min-width: 80px;
        margin-top: 0.35rem;
    }

    .fulfillment-bar-fill {
        height: 100%;
        border-radius: 2rem;
        background: linear-gradient(90deg, #059669, #34d399);
    }

    .fulfillment-bar-fill.partial { background: linear-gradient(90deg, #d97706, #fbbf24); }

    .req-group-card {
        border-top: 1px solid #f1f5f9;
    }

    .req-group-card:first-of-type { border-top: none; }

    .req-group-head {
        padding: 1rem 1.5rem;
        background: #fafbfc;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .req-group-no {
        font-family: monospace;
        font-weight: 700;
        font-size: 0.9rem;
        color: #0f172a;
    }

    .req-group-meta {
        font-size: 0.78rem;
        color: #64748b;
    }

    .req-group-summary {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .section-divider-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
        padding: 0.5rem 1.5rem 0;
        margin: 0;
    }
</style>
@endsection

@section('content')

<div class="container-fluid req-page px-3 px-lg-4 mt-3">

    <div class="req-hero">
        <div class="req-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="req-hero-badge">
                        <i class="bi bi-arrow-left-right"></i>
                        @if(($routingMode ?? '') === 'both')
                            Satellite → Hub &amp; Central
                        @elseif($routesToHub ?? false)
                            Satellite → Hub Requisition
                        @else
                            Satellite → Central Requisition
                        @endif
                    </div>
                    <h2>Request Stock</h2>
                    <p>Choose the store you are requesting from — central stores or Admin Store — Satellite — then add items to your draft.</p>
                    @if($activeStore)
                    <div class="store-context-banner">
                        <span class="store-chip"><i class="bi bi-shop"></i> {{ $activeStore->name }}</span>
                        <i class="bi bi-arrow-right arrow-icon"></i>
                        <span class="store-chip"><i class="bi bi-building"></i> {{ $centralLabel }}</span>
                    </div>
                    @endif
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Requisition</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="stat-card pending">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-pencil-square"></i></div>
                </div>
                <div class="stat-card-value" data-count="{{ $pendingCount }}">{{ number_format($pendingCount) }}</div>
                <p class="stat-card-label">Draft Line Items</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">In current cart, not yet submitted</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card qty">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="stat-card-value" data-count="{{ $totalQtyRequested }}">{{ number_format($totalQtyRequested) }}</div>
                <p class="stat-card-label">Draft Quantity</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">Units in current draft</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card unique">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-tags"></i></div>
                    @if($pendingCount > 0)<span class="stat-pct-badge">{{ $uniquePct }}%</span>@endif
                </div>
                <div class="stat-card-value" data-count="{{ $uniqueItems }}">{{ number_format($uniqueItems) }}</div>
                <p class="stat-card-label">Unique Items</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $uniquePct }}%"></div></div>
                <p class="stat-card-meta">Different products in draft</p>
            </div>
        </div>
    </div>

    <div class="add-item-card">
        <div class="add-item-head">
            <h5><i class="bi bi-plus-circle me-2 text-primary"></i>Add Item to Draft</h5>
        </div>
        <div class="add-item-body">
            <form method="post" action="{{ route('add-request-process') }}" id="addRequestForm">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label" for="req_item_search">Item</label>
                        <div class="item-autocomplete" id="reqItemAutocomplete">
                            <div class="item-autocomplete-input-wrap">
                                <i class="bi bi-search item-autocomplete-icon"></i>
                                <input type="text"
                                       class="form-control item-autocomplete-input"
                                       id="req_item_search"
                                       placeholder="Start typing item name or code…"
                                       autocomplete="off"
                                       value="{{ $oldAddItem ? ($oldAddItem->item_code ? $oldAddItem->item_code . ' — ' : '') . $oldAddItem->name : '' }}">
                                <button type="button" class="item-autocomplete-clear" aria-label="Clear item">&times;</button>
                            </div>
                            <input type="hidden" name="item" id="req_item_id" value="{{ old('item') }}">
                            <ul class="item-autocomplete-list" id="req_item_list" role="listbox" hidden></ul>
                        </div>
                        @error('item') <small class="text-danger">{{ $message }}</small> @enderror
                        <div class="field-hint">Click the field or type to search all items</div>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label" for="item_store_id">Request from</label>
                        <select class="form-select" name="item_store_id" id="item_store_id" required>
                            <option value="">Select store…</option>
                            @foreach(($requestFromStores ?? $centralStores ?? collect()) as $fromStore)
                                <option value="{{ $fromStore->id }}" @selected((string) old('item_store_id') === (string) $fromStore->id)>
                                    {{ $fromStore->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_store_id') <small class="text-danger">{{ $message }}</small> @enderror
                        <div class="field-hint">Central stores or Admin Store — Satellite</div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">UoM</label>
                        <input type="text" id="uom" class="form-control" readonly placeholder="—">
                         <div class="field-hint">Unit of measure</div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" id="quantity" value="{{ old('quantity') }}" min="1" required placeholder="Qty">
                        <div class="field-hint">Enter any quantity you need</div>
                        @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn-add-line">
                            <i class="bi bi-plus-lg"></i> Add
                        </button>
                         <div class="field-hint">Add more</div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="req-table-card mb-5">
        <div class="req-table-head">
            <div>
                <h5><i class="bi bi-file-earmark-text me-1 text-primary"></i> Draft Requisition</h5>
                <span class="record-count-badge">
                    <i class="bi bi-cart"></i>
                    {{ $pendingCount }} line{{ $pendingCount !== 1 ? 's' : '' }}
                </span>
            </div>
            @if($pendingCount > 0)
                <a href="{{ route('requisition.SubmitRequest') }}"
                   class="btn btn-theme btn-sm btn-submit-req btn-confirm-submit">
                    <i class="bi bi-send-fill"></i> Submit Draft
                </a>
            @endif
        </div>

        @if($listitemissue->count() > 0)
            <p class="section-divider-label mb-0">Current draft — not yet submitted</p>
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="reqDraftTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Source Store</th>
                            <th>UoM</th>
                            <th class="qty-col-head">Req. Qty</th>
                            <th class="qty-col-head">Issued Qty</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listitemissue as $lists)
                            <tr>
                                <td class="text-secondary">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $lists->itemname->name ?? '—' }}</div>
                                    <span class="code-badge">{{ $lists->itemcode->item_code ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="source-store-badge">{{ $lists->sourceStore->name ?? 'Central' }}</span>
                                </td>
                                <td>{{ $lists->itemname->unitname->name ?? '—' }}</td>
                                <td class="qty-cell"><span class="qty-requested">{{ $lists->qty_requested }}</span></td>
                                <td class="qty-cell"><span class="qty-issued zero">0</span></td>
                                <td><span class="status-badge draft"><i class="bi bi-pencil"></i> Draft</span></td>
                                <td>
                                    <button type="button"
                                            class="btn-delete-req btn-confirm-delete"
                                            title="Remove item"
                                            data-delete-url="{{ url('Requisition/'.$lists->id.'/delete') }}"
                                            data-item-name="{{ $lists->itemname->name ?? 'this item' }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="req-submit-bar">
                <span class="text-muted small me-auto align-self-center">
                    Submit this draft to send it for approval. You can start another requisition immediately after.
                </span>
                <a href="{{ route('requisition.SubmitRequest') }}"
                   class="btn-submit-req btn-confirm-submit">
                    <i class="bi bi-send-fill"></i> Submit Draft for Approval
                </a>
            </div>
        @else
            <div class="req-empty">
                <div class="req-empty-visual"><i class="bi bi-cart"></i></div>
                <h5 class="fw-semibold text-dark">No items in draft</h5>
                <p>Search for an item above and add it. Submit when ready — you can create unlimited requisitions.</p>
            </div>
        @endif
    </div>
</div>

<form id="deleteRequestForm" method="GET" action="" class="d-none"></form>

@endsection

@section('scripts')
<script>
const ReqAlert = {
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
            title: 'Remove Item?',
            html: `Remove <strong>${name}</strong> from your requisition cart?`,
            btnClass: 'error',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, remove',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed && onConfirm) onConfirm();
        });
    },
    confirmSubmit(onConfirm) {
        return this._base({
            icon: 'question',
            title: 'Submit Requisition Draft?',
            html: 'Submit all draft items for approval?<br><small class="text-muted">After submission you can immediately start a new requisition.</small>',
            btnClass: 'success',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-send me-1"></i> Yes, submit',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed && onConfirm) onConfirm();
        });
    },
};

const REQ_ITEMS = @json($reqItemsJson);

function ItemAutocomplete(root, items, onSelect) {
    this.root = root;
    this.items = items || [];
    this.onSelect = onSelect || null;
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

ItemAutocomplete.prototype.filter = function (term) {
    const q = term.trim().toLowerCase();
    if (!q) {
        return this.items.slice();
    }

    const parts = q.split(/\s*[—–-]\s*/).map(function (p) { return p.trim(); }).filter(Boolean);
    const needles = parts.length > 1 ? parts : [q];

    return this.items.filter(function (item) {
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
    if (typeof this.onSelect === 'function') {
        this.onSelect(item);
    }
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
    $('#uom').val('');
    $('#stock_hint').text('');
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
    if (this.selectedItem) {
        this.input.select();
        this.render(this.filter(''));
        return;
    }
    this.render(this.filter(this.input.value));
};

ItemAutocomplete.prototype.onInput = function () {
    const term = this.input.value;
    if (this.selectedItem && this.labelFor(this.selectedItem) !== term) {
        this.selectedItem = null;
        this.hidden.value = '';
        this.root.classList.remove('has-value');
        $('#uom').val('');
        $('#stock_hint').text('');
    }
    if (!term.trim()) {
        this.render(this.filter(''));
        return;
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

function loadStockForItem(itemId) {
    const item = REQ_ITEMS.find(function (i) { return String(i.id) === String(itemId); });

    if (!item) {
        $('#uom').val('');
        $('#stock_hint').text('');
        return;
    }

    $('#uom').val(item.uom || '');

    $.ajax({
        url: @json(route('get.batch.number')),
        type: 'POST',
        data: { getID: itemId, _token: @json(csrf_token()) },
        success: function (response) {
            if (response.uom_name) {
                $('#uom').val(response.uom_name);
            }
            if (response.qty != null && response.qty !== '') {
                $('#stock_hint').text('Central stock on hand: ' + response.qty + ' (reference only)');
            } else {
                $('#stock_hint').text('');
            }
        },
        error: function () {
            $('#stock_hint').text('');
        },
    });
}

let reqItemAutocomplete;

$(document).ready(function () {
    reqItemAutocomplete = new ItemAutocomplete(
        document.getElementById('reqItemAutocomplete'),
        REQ_ITEMS,
        function (item) {
            loadStockForItem(item.id);
        }
    );

    if (reqItemAutocomplete.hidden.value) {
        reqItemAutocomplete.root.classList.add('has-value');
        loadStockForItem(reqItemAutocomplete.hidden.value);
    }

    $('#addRequestForm').on('submit', function (e) {
        if (!$('#req_item_id').val()) {
            e.preventDefault();
            ReqAlert.error('Item Required', 'Please search and select an item from the list.');
            $('#req_item_search').focus();
            return;
        }

        if (!$('#item_store_id').val()) {
            e.preventDefault();
            ReqAlert.error('Store Required', 'Please select the store you are requesting from.');
            $('#item_store_id').focus();
        }
    });
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
        ReqAlert.confirmDelete(name, function () {
            window.location.href = url;
        });
    });
});

document.querySelectorAll('.btn-confirm-submit').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const href = this.getAttribute('href');
        ReqAlert.confirmSubmit(function () {
            window.location.href = href;
        });
    });
});

@if(session('message_success'))
ReqAlert.success('Success!', @json(session('message_success')));
@endif

@if(session('message_error'))
ReqAlert.error('Oops!', @json(session('message_error')));
@endif
</script>
@endsection
