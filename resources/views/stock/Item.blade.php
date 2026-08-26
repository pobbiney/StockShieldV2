@php
    $pageName = "stock";
    $subpageName = "item";
    $categoryColors = ['#0d6efd', '#16a34a', '#d97706', '#7c3aed', '#0891b2', '#be185d', '#ca8a04', '#64748b'];
@endphp

@extends('layouts.backendapp')

@section('page-alerts')
@endsection

@section('css')
<style>
    .item-page { padding: 0 0.5rem 2rem; }

    .item-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 60%, #4f8ef7 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(13, 110, 253, 0.25);
    }

    .item-hero::before,
    .item-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .item-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .item-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .item-hero-inner { position: relative; z-index: 1; }

    .item-hero-badge {
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

    .item-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .item-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 520px;
    }

    .item-hero-actions {
        display: flex;
        gap: 0.65rem;
        flex-wrap: wrap;
        margin-top: 1.25rem;
    }

    .btn-hero-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 2rem;
        border: none;
        background: #fff;
        color: #0d6efd;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        color: #0d6efd;
    }

    .btn-hero-ghost {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.15rem;
        border-radius: 2rem;
        border: 1.5px solid rgba(255, 255, 255, 0.45);
        background: transparent;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-hero-ghost:hover { background: rgba(255, 255, 255, 0.12); color: #fff; }

    .item-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .item-hero .breadcrumb-item a:hover { color: #fff; }
    .item-hero .breadcrumb-item.active { color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        animation: statIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.12s; }
    .stat-card:nth-child(3) { animation-delay: 0.19s; }

    @keyframes statIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
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

    .stat-card.total    .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-card.active   .stat-card-icon { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
    .stat-card.inactive .stat-card-icon { background: rgba(100, 116, 139, 0.12); color: #64748b; }

    .stat-card-value {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.total    .stat-card-value { color: #0d6efd; }
    .stat-card.active   .stat-card-value { color: #16a34a; }
    .stat-card.inactive .stat-card-value { color: #64748b; }

    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0 0 0.85rem; }

    .stat-bar-wrap {
        height: 4px;
        background: #f1f5f9;
        border-radius: 2rem;
        overflow: hidden;
    }

    .stat-card.total    .stat-bar-fill { background: #0d6efd; }
    .stat-card.active   .stat-bar-fill { background: #16a34a; }
    .stat-card.inactive .stat-bar-fill { background: #64748b; }

    .stat-bar-fill { height: 100%; border-radius: 2rem; }

    .stat-card-meta { font-size: 0.72rem; color: #94a3b8; margin-top: 0.4rem; }

    .stat-pct-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 2rem;
    }

    .stat-card.active   .stat-pct-badge { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
    .stat-card.inactive .stat-pct-badge { background: rgba(100, 116, 139, 0.12); color: #64748b; }

    .stat-card.categories .stat-card-icon { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }
    .stat-card.top-cat   .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }

    .stat-card.categories .stat-card-value { color: #7c3aed; font-size: 2rem; }
    .stat-card.top-cat   .stat-card-value { color: #d97706; font-size: 2rem; }

    .stat-card.categories .stat-bar-fill { background: #7c3aed; }
    .stat-card.top-cat   .stat-bar-fill { background: #d97706; }

    .stat-card.top-cat .stat-card-value {
        font-size: 1.35rem;
        line-height: 1.2;
        word-break: break-word;
    }

    .category-ratio-strip {
        background: #fff;
        border-radius: 1rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.75rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: statIn 0.5s ease 0.25s both;
    }

    .category-ratio-bar {
        height: 10px;
        border-radius: 2rem;
        overflow: hidden;
        display: flex;
        background: #f1f5f9;
        margin: 0.6rem 0 0.75rem;
    }

    .category-ratio-segment { transition: width 1s ease; min-width: 0; }

    .category-ratio-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.5rem 1rem;
    }

    .category-ratio-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        font-size: 0.78rem;
        color: #64748b;
    }

    .category-ratio-item .cat-label {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        min-width: 0;
    }

    .category-ratio-item .cat-label span:not(.legend-dot) {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .category-ratio-item .cat-count {
        font-weight: 700;
        color: #0f172a;
        flex-shrink: 0;
    }

    .legend-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .item-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: statIn 0.5s ease 0.32s both;
    }

    .item-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .item-table-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.15rem;
        color: #0f172a;
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

    #itemTable thead th {
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

    #itemTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }

    #itemTable tbody tr { transition: background 0.15s ease; background: #fff; }
    #itemTable tbody tr:nth-child(even) { background: #fafafa; }
    #itemTable tbody tr:hover { background: #f8fafc !important; }

    .item-table-card .dataTables_wrapper .dataTables_filter input {
        border: 1.5px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.35rem 0.75rem;
    }

    .item-avatar-initial {
        width: 38px;
        height: 38px;
        border-radius: 0.625rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.78rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .item-avatar-initial.c0 { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .item-avatar-initial.c1 { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
    .item-avatar-initial.c2 { background: rgba(217, 119, 6, 0.12); color: #d97706; }
    .item-avatar-initial.c3 { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }

    .item-name { font-weight: 600; color: #0f172a; }
    .item-sub  { font-size: 0.78rem; color: #94a3b8; font-family: monospace; }

    .meta-pill {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 2rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.78rem;
        color: #475569;
    }

    .reorder-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(217, 119, 6, 0.1);
        color: #d97706;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.active   { background: rgba(22, 163, 74, 0.1); color: #16a34a; }
    .status-badge.inactive { background: rgba(100, 116, 139, 0.1); color: #64748b; }

    .item-actions { display: flex; gap: 0.35rem; }

    .btn-edit-item,
    .btn-delete-item {
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
        font-size: 0.85rem;
    }

    .btn-edit-item { color: #0d6efd; }
    .btn-edit-item:hover { background: #0d6efd; color: #fff; transform: scale(1.08); }

    .btn-delete-item { color: #dc3545; }
    .btn-delete-item:hover { background: #dc3545; color: #fff; transform: scale(1.08); }

    .btn-delete-item.disabled,
    .btn-delete-item:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: #f1f5f9;
        color: #94a3b8;
    }

    .btn-delete-item.disabled:hover,
    .btn-delete-item:disabled:hover {
        background: #f1f5f9;
        color: #94a3b8;
        transform: none;
    }

    .item-empty {
        text-align: center;
        padding: 4rem 2rem;
    }

    .item-empty-visual {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(13, 110, 253, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 2rem;
        color: #0d6efd;
    }

    .item-empty h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .item-empty p { color: #64748b; max-width: 420px; margin: 0 auto 1.5rem; }

    .modal-item .modal-dialog {
        max-width: 900px;
        max-height: calc(100vh - 1.5rem);
        margin: 0.75rem auto;
    }

    .modal-item .modal-content {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.18);
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 1.5rem);
    }

    .modal-item #addItemForm,
    .modal-item #editItemForm,
    .modal-item #bulkUploadForm {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
    }

    .modal-item-header {
        flex-shrink: 0;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.75rem;
    }

    .modal-item-header .modal-title {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1.15rem;
        color: #0f172a;
    }

    .modal-item .modal-body {
        overflow-y: auto;
        flex: 1 1 auto;
        padding: 1.5rem 1.75rem;
    }

    .modal-section-title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .modal-item .form-control,
    .modal-item .form-select {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.875rem;
    }

    .modal-item .form-control:focus,
    .modal-item .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
    }

    .code-preview {
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        color: #475569;
        font-family: monospace;
        font-weight: 600;
    }

    .modal-item-footer {
        padding: 1rem 1.75rem;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        flex-shrink: 0;
        display: flex;
        justify-content: flex-end;
        gap: 0.65rem;
    }

    .btn-modal-clear {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.55rem 1.1rem;
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-modal-clear:hover { background: #f8fafc; color: #475569; }

    .btn-modal-save {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.55rem 1.25rem;
        border-radius: 0.625rem;
        border: none;
        background: #0d6efd;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-modal-save:hover { background: #0b5ed7; color: #fff; }
    .btn-modal-save.update { background: #16a34a; }
    .btn-modal-save.update:hover { background: #15803d; }

    .modal-item-header.edit-header {
        background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%);
        border-bottom: 1px solid #bbf7d0;
    }

    .modal-item-header.edit-header .modal-title { color: #15803d; }
</style>
@endsection

@section('content')

<div class="container-fluid item-page px-3 px-lg-4 mt-3">

    <div class="item-hero">
        <div class="item-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="item-hero-badge">
                        <i class="bi bi-box-seam-fill"></i> Stock Management
                    </div>
                    <h2>Manage Inventory Items</h2>
                    <p>
                        @if(!empty($isGlobalAccess))
                            Register items, assign categories and units, set reorder levels, and track inventory across your stores.
                        @elseif(!empty($activeStore))
                            Managing: <strong>{{ $activeStore->name }}</strong>. Items registered here belong only to this store.
                        @else
                            Register items, assign categories and units, and set reorder levels for your store.
                        @endif
                    </p>
                    <div class="item-hero-actions">
                        <button type="button" class="btn-hero-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="bi bi-plus-circle-fill"></i> Add New Item
                        </button>
                        <button type="button" class="btn-hero-ghost" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                            <i class="bi bi-upload"></i> Bulk Upload
                        </button>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Items</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="stat-card total">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-boxes"></i></div>
                </div>
                <div class="stat-card-value" data-count="{{ $totalItems }}">{{ number_format($totalItems) }}</div>
                <p class="stat-card-label">Total Items</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">Items in your assigned stores</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card categories">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-tags-fill"></i></div>
                </div>
                <div class="stat-card-value" data-count="{{ $categoriesUsed }}">{{ number_format($categoriesUsed) }}</div>
                <p class="stat-card-label">Categories Used</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $listcat->count() > 0 ? round(($categoriesUsed / $listcat->count()) * 100) : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">Categories with registered items</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card top-cat">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-trophy-fill"></i></div>
                    @if($topCategory)
                        <span class="stat-pct-badge">{{ $topCategory['pct'] }}%</span>
                    @endif
                </div>
                @if($topCategory)
                    <div class="stat-card-value">{{ $topCategory['name'] }}</div>
                    <p class="stat-card-label">{{ number_format($topCategory['count']) }} item{{ $topCategory['count'] !== 1 ? 's' : '' }}</p>
                    <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $topCategory['pct'] }}%"></div></div>
                    <p class="stat-card-meta">Largest category by item count</p>
                @else
                    <div class="stat-card-value">—</div>
                    <p class="stat-card-label">Top Category</p>
                    <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:0%"></div></div>
                    <p class="stat-card-meta">No category data yet</p>
                @endif
            </div>
        </div>
    </div>

    @if($categoryStats->count() > 0)
    <div class="category-ratio-strip">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="fw-semibold small">Items by Category</span>
            <span class="text-secondary small">{{ $totalItems }} item{{ $totalItems !== 1 ? 's' : '' }} across {{ $categoryStats->count() }} categor{{ $categoryStats->count() !== 1 ? 'ies' : 'y' }}</span>
        </div>
        <div class="category-ratio-bar">
            @foreach($categoryStats as $catStat)
                @php $color = $categoryColors[$loop->index % count($categoryColors)]; @endphp
                @if($catStat['pct'] > 0)
                    <div class="category-ratio-segment" style="width:{{ $catStat['pct'] }}%; background:{{ $color }};"></div>
                @endif
            @endforeach
        </div>
        <div class="category-ratio-list">
            @foreach($categoryStats as $catStat)
                @php $color = $categoryColors[$loop->index % count($categoryColors)]; @endphp
                <div class="category-ratio-item">
                    <span class="cat-label">
                        <span class="legend-dot" style="background:{{ $color }}"></span>
                        <span>{{ $catStat['name'] }}</span>
                    </span>
                    <span class="cat-count">{{ $catStat['count'] }} ({{ $catStat['pct'] }}%)</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="item-table-card mb-5">
        <div class="item-table-head">
            <div>
                <h5>Registered Items</h5>
                <span class="record-count-badge">
                    <i class="bi bi-database"></i>
                    {{ $totalItems }} record{{ $totalItems !== 1 ? 's' : '' }}
                </span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-theme btn-sm" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                    <i class="bi bi-upload me-1"></i> Bulk Upload
                </button>
                <button type="button" class="btn btn-theme btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Item
                </button>
            </div>
        </div>

        <div class="p-0">
            @if($list->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0 w-100" id="itemTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Store</th>
                                <th>Reorder</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list as $item)
                                @php
                                    $initials = strtoupper(substr($item->name ?? 'I', 0, 2));
                                    $colorIdx = $loop->index % 4;
                                    $hasUsage = in_array((int) $item->id, $itemsWithUsage, true);
                                @endphp
                                <tr>
                                    <td class="text-secondary">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="item-avatar-initial c{{ $colorIdx }}">{{ $initials }}</span>
                                            <div>
                                                <div class="item-name">{{ $item->name }}</div>
                                                <div class="item-sub">{{ $item->item_code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="meta-pill">{{ $item->categoryname->name ?? '—' }}</span></td>
                                    <td><span class="meta-pill">{{ $item->unitname->name ?? '—' }}</span></td>
                                    <td>{{ $item->storename->name ?? '—' }}</td>
                                    <td><span class="reorder-badge">{{ $item->reorder_level ?? 0 }}</span></td>
                                    <td>
                                        <span class="status-badge {{ strtolower($item->status) === 'active' ? 'active' : 'inactive' }}">
                                            <i class="bi bi-circle-fill" style="font-size:0.45rem"></i>
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="item-actions">
                                            <button type="button"
                                                    class="btn-edit-item btn-open-edit-modal"
                                                    title="Edit item"
                                                    data-item-id="{{ $item->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @if($hasUsage)
                                                <button type="button"
                                                        class="btn-delete-item disabled"
                                                        title="Cannot delete — linked to records"
                                                        data-has-usage="1"
                                                        data-item-name="{{ $item->name }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                        class="btn-delete-item btn-confirm-delete"
                                                        title="Delete item"
                                                        data-delete-url="{{ route('delete-item-process', $item->id) }}"
                                                        data-item-name="{{ $item->name }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="item-empty">
                    <div class="item-empty-visual"><i class="bi bi-box-seam"></i></div>
                    <h5>No items registered yet</h5>
                    <p>Add your first inventory item or use bulk upload to import items into your assigned stores.</p>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <button type="button" class="btn btn-theme" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="bi bi-plus-lg me-1"></i> Add First Item
                        </button>
                        <button type="button" class="btn btn-outline-theme" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                            <i class="bi bi-upload me-1"></i> Bulk Upload
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<form id="deleteItemForm" method="POST" action="" class="d-none">
    @csrf
</form>

{{-- Add Item Modal --}}
<div class="modal fade modal-item" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('add-item-process') }}" id="addItemForm">
                @csrf
                <div class="modal-header modal-item-header">
                    <div>
                        <h5 class="modal-title" id="addItemModalLabel">Add New Item</h5>
                        <p class="mb-0 small text-secondary">Register a new inventory item</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-section-title">Item Details</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Item Code</label>
                            <input type="text" class="form-control code-preview" value="{{ $itemCodePreview }}" readonly>
                            <small class="text-secondary">Auto-generated on save</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Item Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter item name">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <p class="modal-section-title">Classification & Store</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Choose category</option>
                                @foreach($listcat as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Unit of Issue</label>
                            <select name="unit_of_measure_id" class="form-select">
                                <option value="" disabled {{ old('unit_of_measure_id') ? '' : 'selected' }}>Choose unit</option>
                                @foreach($listunit as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_of_measure_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('unit_of_measure_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Store</label>
                            @if(!empty($isGlobalAccess))
                                <select name="store_id" class="form-select">
                                    <option value="" disabled {{ old('store_id') ? '' : 'selected' }}>Choose store</option>
                                    @foreach($getstoreid as $store)
                                        <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                    @endforeach
                                </select>
                                @error('store_id') <small class="text-danger">{{ $message }}</small> @enderror
                            @else
                                <input type="hidden" name="store_id" value="{{ $activeStore?->id }}">
                                <div class="form-control bg-light d-flex align-items-center gap-2">
                                    <i class="bi bi-shop text-primary"></i>
                                    <span>{{ $activeStore?->name ?? 'No store selected' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <p class="modal-section-title">Stock Settings</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Reorder Level</label>
                            <input type="number" name="re_order_level" class="form-control" value="{{ old('re_order_level') }}" min="0" placeholder="Minimum stock level">
                            @error('re_order_level') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="" disabled {{ old('status') ? '' : 'selected' }}>Choose status</option>
                                <option value="Active" {{ old('status') === 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-item-footer">
                    <button type="button" class="btn-modal-clear" id="clearItemForm">
                        <i class="bi bi-arrow-counterclockwise"></i> Clear
                    </button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-check-lg"></i> Save Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Item Modal --}}
<div class="modal fade modal-item" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('update-item-process') }}" id="editItemForm">
                @csrf
                <input type="hidden" name="item_id" id="edit_item_id">
                <div class="modal-header modal-item-header edit-header">
                    <div>
                        <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                        <p class="mb-0 small text-secondary">Update item details</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-section-title">Item Details</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Item Code</label>
                            <input type="text" id="edit_item_code" class="form-control code-preview" readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Item Name</label>
                            <input type="text" name="name" id="edit_item_name" class="form-control">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <p class="modal-section-title">Classification & Store</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Category</label>
                            <select name="category_id" id="edit_category_id" class="form-select">
                                @foreach($listcat as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Unit of Issue</label>
                            <select name="unit_of_measure_id" id="edit_unit_id" class="form-select">
                                @foreach($listunit as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('unit_of_measure_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Store</label>
                            @if(!empty($isGlobalAccess))
                                <select name="store_id" id="edit_store_id" class="form-select">
                                    @foreach($getstoreid as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                                @error('store_id') <small class="text-danger">{{ $message }}</small> @enderror
                            @else
                                <input type="hidden" name="store_id" id="edit_store_id" value="{{ $activeStore?->id }}">
                                <div class="form-control bg-light d-flex align-items-center gap-2">
                                    <i class="bi bi-shop text-primary"></i>
                                    <span>{{ $activeStore?->name ?? 'No store selected' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <p class="modal-section-title">Stock Settings</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Reorder Level</label>
                            <input type="number" name="re_order_level" id="edit_reorder_level" class="form-control" min="0">
                            @error('re_order_level') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-item-footer">
                    <button type="button" class="btn-modal-clear" id="clearEditItemForm">
                        <i class="bi bi-arrow-counterclockwise"></i> Clear
                    </button>
                    <button type="submit" class="btn-modal-save update">
                        <i class="bi bi-check-lg"></i> Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Bulk Upload Modal --}}
<div class="modal fade modal-item" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" action="{{ route('add-bulkupload-process') }}" id="bulkUploadForm">
                @csrf
                <div class="modal-header modal-item-header">
                    <div>
                        <h5 class="modal-title" id="bulkUploadModalLabel">Bulk Upload Items</h5>
                        <p class="mb-0 small text-secondary">Import multiple items from a spreadsheet</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Upload File</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv">
                        <small class="text-secondary d-block mt-2">
                            Row 1 headers (or fixed order): <strong>name</strong>, <strong>category_id</strong> or category name,
                            <strong>unit_id</strong> or unit name, <strong>status</strong>.
                            Add <strong>store_id</strong> for global admins without an active store.
                        </small>
                        @error('file') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer modal-item-footer">
                    <button type="button" class="btn-modal-clear" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-upload"></i> Upload File
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const itemFetchBaseUrl = @json(url('item-id'));
const defaultItemCode = @json($itemCodePreview);

const ItemAlert = {
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
        return this._base({
            icon: 'success',
            title: title,
            text: text,
            btnClass: 'success',
            confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Done',
            timer: 2800,
            timerProgressBar: true,
        });
    },
    error(title, text) {
        return this._base({
            icon: 'error',
            title: title,
            text: text,
            btnClass: 'error',
            confirmButtonText: '<i class="bi bi-x-lg me-1"></i> Close',
        });
    },
    info(title, html) {
        return this._base({
            icon: 'info',
            title: title,
            html: html,
            btnClass: 'info',
            confirmButtonText: 'Got it',
        });
    },
    confirmDelete(name, onConfirm) {
        return this._base({
            icon: 'warning',
            title: 'Delete Item?',
            html: `Are you sure you want to delete <strong>${name}</strong>?<br><small class="text-secondary">This action cannot be undone.</small>`,
            btnClass: 'error',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, delete',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed && onConfirm) onConfirm();
        });
    },
};

let editItemSnapshot = null;

function populateEditForm(data) {
    document.getElementById('edit_item_id').value = data.id;
    document.getElementById('edit_item_code').value = data.item_code || '';
    document.getElementById('edit_item_name').value = data.name || '';
    document.getElementById('edit_category_id').value = data.cat_id || '';
    document.getElementById('edit_unit_id').value = data.unit_id || '';
    document.getElementById('edit_store_id').value = data.store_id || '';
    document.getElementById('edit_reorder_level').value = data.reorder_level ?? '';
    document.getElementById('edit_status').value = data.status || 'Active';
    editItemSnapshot = { ...data };
}

function openEditModal(itemId) {
    fetch(itemFetchBaseUrl + '/' + itemId)
        .then(function (res) {
            if (!res.ok) throw new Error('Item not found');
            return res.json();
        })
        .then(function (data) {
            populateEditForm(data);
            new bootstrap.Modal(document.getElementById('editItemModal')).show();
        })
        .catch(function () {
            ItemAlert.error('Load Failed', 'Could not load item details. Please try again.');
        });
}

document.getElementById('clearItemForm').addEventListener('click', function () {
    document.getElementById('addItemForm').reset();
});

document.getElementById('clearEditItemForm').addEventListener('click', function () {
    if (editItemSnapshot) populateEditForm(editItemSnapshot);
});

document.querySelectorAll('.btn-open-edit-modal').forEach(function (btn) {
    btn.addEventListener('click', function () {
        openEditModal(this.dataset.itemId);
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

@if($list->count() > 0)
$(document).ready(function () {
    $('#itemTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 10,
        language: { search: '', searchPlaceholder: 'Search items...' },
        dom: '<"d-flex justify-content-between align-items-center px-3 pt-3 pb-2"lf>rt<"d-flex justify-content-between align-items-center px-3 py-3"ip>',
    });
});
@endif

@if($errors->any() && old('item_id'))
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('edit_item_id').value = @json(old('item_id'));
    document.getElementById('edit_item_name').value = @json(old('name'));
    document.getElementById('edit_category_id').value = @json(old('category_id'));
    document.getElementById('edit_unit_id').value = @json(old('unit_of_measure_id'));
    document.getElementById('edit_store_id').value = @json(old('store_id'));
    document.getElementById('edit_reorder_level').value = @json(old('re_order_level'));
    document.getElementById('edit_status').value = @json(old('status'));
    new bootstrap.Modal(document.getElementById('editItemModal')).show();
});
@elseif($errors->has('file'))
document.addEventListener('DOMContentLoaded', function () {
    new bootstrap.Modal(document.getElementById('bulkUploadModal')).show();
});
@elseif($errors->any())
document.addEventListener('DOMContentLoaded', function () {
    new bootstrap.Modal(document.getElementById('addItemModal')).show();
});
@endif

@if(session('message_success'))
ItemAlert.success('Success!', @json(session('message_success')));
@endif

@if(session('message_error'))
ItemAlert.error('Oops!', @json(session('message_error')));
@endif

document.querySelectorAll('.btn-confirm-delete').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const name = this.dataset.itemName;
        const url  = this.dataset.deleteUrl;
        ItemAlert.confirmDelete(name, function () {
            const form = document.getElementById('deleteItemForm');
            form.action = url;
            form.submit();
        });
    });
});

document.querySelectorAll('.btn-delete-item[data-has-usage]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        ItemAlert.info(
            'Cannot Delete',
            `<strong>${this.dataset.itemName}</strong> is linked to stock or transaction records.<br><small class="text-secondary">Remove those records before deleting this item.</small>`
        );
    });
});
</script>
@endsection
