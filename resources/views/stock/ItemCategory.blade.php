@php
    $pageName = "stock";
    $subpageName = "itemcat";
    $activePct   = $totalCategories > 0 ? round(($activeCount / $totalCategories) * 100) : 0;
    $inactivePct = $totalCategories > 0 ? round(($inactiveCount / $totalCategories) * 100) : 0;
@endphp

@extends('layouts.backendapp')

@section('page-alerts')
@endsection

@section('css')
<style>
    .icat-page { padding: 0 0.5rem 2rem; }

    .icat-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 60%, #4f8ef7 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(13, 110, 253, 0.25);
    }

    .icat-hero::before,
    .icat-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .icat-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .icat-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .icat-hero-inner { position: relative; z-index: 1; }

    .icat-hero-badge {
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

    .icat-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .icat-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 520px;
    }

    .icat-hero-actions {
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

    .icat-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .icat-hero .breadcrumb-item a:hover { color: #fff; }
    .icat-hero .breadcrumb-item.active { color: #fff; }

    .stock-subnav {
        display: inline-flex;
        gap: 0.35rem;
        padding: 0.35rem;
        background: #fff;
        border-radius: 2rem;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.5rem;
    }

    .stock-subnav .nav-link {
        border-radius: 2rem;
        padding: 0.5rem 1.15rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        border: none;
        transition: all 0.2s ease;
    }

    .stock-subnav .nav-link:hover { color: #0d6efd; background: rgba(13, 110, 253, 0.06); }
    .stock-subnav .nav-link.active {
        background: #0d6efd;
        color: #fff;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
    }

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

    .stat-card.total   .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-card.active  .stat-card-icon { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
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

    .status-ratio-strip {
        background: #fff;
        border-radius: 1rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.75rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: statIn 0.5s ease 0.25s both;
    }

    .status-ratio-bar {
        height: 10px;
        border-radius: 2rem;
        overflow: hidden;
        display: flex;
        background: #f1f5f9;
        margin: 0.6rem 0 0.4rem;
    }

    .status-ratio-active   { background: linear-gradient(90deg, #16a34a, #4ade80); }
    .status-ratio-inactive { background: linear-gradient(90deg, #64748b, #94a3b8); }

    .status-ratio-legend {
        display: flex;
        justify-content: space-between;
        font-size: 0.78rem;
        color: #64748b;
    }

    .legend-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 4px;
    }

    .icat-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: statIn 0.5s ease 0.32s both;
    }

    .icat-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .icat-table-head h5 {
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

    #categoryTable thead th {
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

    #categoryTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }

    #categoryTable tbody tr { transition: background 0.15s ease; background: #fff; }
    #categoryTable tbody tr:nth-child(even) { background: #fafafa; }
    #categoryTable tbody tr:hover { background: #f8fafc !important; }

    .icat-table-card .dataTables_wrapper .dataTables_filter input {
        border: 1.5px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.35rem 0.75rem;
    }

    .cat-avatar-initial {
        width: 38px;
        height: 38px;
        border-radius: 0.625rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .cat-avatar-initial.c0 { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .cat-avatar-initial.c1 { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
    .cat-avatar-initial.c2 { background: rgba(217, 119, 6, 0.12); color: #d97706; }
    .cat-avatar-initial.c3 { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }

    .cat-name { font-weight: 600; color: #0f172a; }

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

    .icat-actions { display: flex; gap: 0.35rem; }

    .btn-edit-cat,
    .btn-delete-cat {
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

    .btn-edit-cat { color: #0d6efd; }
    .btn-edit-cat:hover { background: #0d6efd; color: #fff; transform: scale(1.08); }

    .btn-delete-cat { color: #dc3545; }
    .btn-delete-cat:hover { background: #dc3545; color: #fff; transform: scale(1.08); }

    .btn-delete-cat.disabled,
    .btn-delete-cat:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: #f1f5f9;
        color: #94a3b8;
    }

    .btn-delete-cat.disabled:hover,
    .btn-delete-cat:disabled:hover {
        background: #f1f5f9;
        color: #94a3b8;
        transform: none;
    }

    .icat-empty {
        text-align: center;
        padding: 4rem 2rem;
    }

    .icat-empty-visual {
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

    .icat-empty h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .icat-empty p { color: #64748b; max-width: 380px; margin: 0 auto 1.5rem; }

    .modal-icat .modal-dialog {
        max-width: 520px;
        max-height: calc(100vh - 1.5rem);
        margin: 0.75rem auto;
    }

    .modal-icat .modal-content {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.18);
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 1.5rem);
    }

    .modal-icat #addCategoryForm,
    .modal-icat #editCategoryForm {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
    }

    .modal-icat-header {
        flex-shrink: 0;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.75rem;
    }

    .modal-icat-header .modal-title {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1.15rem;
        color: #0f172a;
    }

    .modal-icat .modal-body {
        overflow-y: auto;
        flex: 1 1 auto;
        padding: 1.5rem 1.75rem;
    }

    .modal-icat .form-control,
    .modal-icat .form-select {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.875rem;
    }

    .modal-icat .form-control:focus,
    .modal-icat .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
    }

    .modal-icat-footer {
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

    .modal-icat-header.edit-header {
        background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%);
        border-bottom: 1px solid #bbf7d0;
    }

    .modal-icat-header.edit-header .modal-title { color: #15803d; }
</style>
@endsection

@section('content')

<div class="container-fluid icat-page px-3 px-lg-4 mt-3">

    <div class="icat-hero">
        <div class="icat-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="icat-hero-badge">
                        <i class="bi bi-tags-fill"></i> Stock Management
                    </div>
                    <h2>Manage Item Categories</h2>
                    <p>Organize inventory items into categories and control which groups are active for stock operations.</p>
                    <div class="icat-hero-actions">
                        <button type="button" class="btn-hero-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="bi bi-plus-circle-fill"></i> Add Category
                        </button>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Item Category</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav stock-subnav">
        <li class="nav-item">
            <a href="{{ route('ItemCategory') }}" class="nav-link active">Item Category</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('unitOfmeasure') }}" class="nav-link">Unit Of Issue</a>
        </li>
    </ul>

    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="stat-card total">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-collection-fill"></i></div>
                </div>
                <div class="stat-card-value" data-count="{{ $totalCategories }}">{{ number_format($totalCategories) }}</div>
                <p class="stat-card-label">Total Categories</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">All registered categories</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card active">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <span class="stat-pct-badge">{{ $activePct }}%</span>
                </div>
                <div class="stat-card-value" data-count="{{ $activeCount }}">{{ number_format($activeCount) }}</div>
                <p class="stat-card-label">Active Categories</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $activePct }}%"></div></div>
                <p class="stat-card-meta">{{ $activePct }}% of total categories</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card inactive">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-pause-circle-fill"></i></div>
                    <span class="stat-pct-badge">{{ $inactivePct }}%</span>
                </div>
                <div class="stat-card-value" data-count="{{ $inactiveCount }}">{{ number_format($inactiveCount) }}</div>
                <p class="stat-card-label">Inactive Categories</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $inactivePct }}%"></div></div>
                <p class="stat-card-meta">{{ $inactivePct }}% of total categories</p>
            </div>
        </div>
    </div>

    @if($totalCategories > 0)
    <div class="status-ratio-strip">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="fw-semibold small">Status Distribution</span>
            <span class="text-secondary small">{{ $totalCategories }} categor{{ $totalCategories !== 1 ? 'ies' : 'y' }}</span>
        </div>
        <div class="status-ratio-bar">
            @if($activePct > 0)<div class="status-ratio-active" style="width:{{ $activePct }}%"></div>@endif
            @if($inactivePct > 0)<div class="status-ratio-inactive" style="width:{{ $inactivePct }}%"></div>@endif
        </div>
        <div class="status-ratio-legend">
            <span><span class="legend-dot" style="background:#16a34a"></span>Active {{ $activeCount }} ({{ $activePct }}%)</span>
            <span><span class="legend-dot" style="background:#64748b"></span>Inactive {{ $inactiveCount }} ({{ $inactivePct }}%)</span>
        </div>
    </div>
    @endif

    <div class="icat-table-card mb-5">
        <div class="icat-table-head">
            <div>
                <h5>Registered Categories</h5>
                <span class="record-count-badge">
                    <i class="bi bi-database"></i>
                    {{ $totalCategories }} record{{ $totalCategories !== 1 ? 's' : '' }}
                </span>
            </div>
            <button type="button" class="btn btn-theme btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="bi bi-plus-lg me-1"></i> Add Category
            </button>
        </div>

        <div class="p-0">
            @if($list->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0 w-100" id="categoryTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list as $category)
                                @php
                                    $initials = strtoupper(substr($category->name ?? 'C', 0, 2));
                                    $colorIdx = $loop->index % 4;
                                    $hasItems = in_array((int) $category->id, $categoriesWithItems, true);
                                @endphp
                                <tr>
                                    <td class="text-secondary">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="cat-avatar-initial c{{ $colorIdx }}">{{ $initials }}</span>
                                            <span class="cat-name">{{ $category->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ strtolower($category->status) === 'active' ? 'active' : 'inactive' }}">
                                            <i class="bi bi-circle-fill" style="font-size:0.45rem"></i>
                                            {{ $category->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="icat-actions">
                                            <button type="button"
                                                    class="btn-edit-cat btn-open-edit-modal"
                                                    title="Edit category"
                                                    data-category-id="{{ $category->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @if($hasItems)
                                                <button type="button"
                                                        class="btn-delete-cat disabled"
                                                        title="Cannot delete — linked to items"
                                                        data-has-items="1"
                                                        data-category-name="{{ $category->name }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                        class="btn-delete-cat btn-confirm-delete"
                                                        title="Delete category"
                                                        data-delete-url="{{ route('delete-itemcategory-process', $category->id) }}"
                                                        data-category-name="{{ $category->name }}">
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
                <div class="icat-empty">
                    <div class="icat-empty-visual"><i class="bi bi-tags"></i></div>
                    <h5>No categories registered yet</h5>
                    <p>Create your first item category to start organizing inventory items in StockShield.</p>
                    <button type="button" class="btn btn-theme" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bi bi-plus-lg me-1"></i> Add First Category
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<form id="deleteCategoryForm" method="POST" action="" class="d-none">
    @csrf
</form>

{{-- Add Category Modal --}}
<div class="modal fade modal-icat" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('add-itemcategory-process') }}" id="addCategoryForm">
                @csrf
                <div class="modal-header modal-icat-header">
                    <div>
                        <h5 class="modal-title" id="addCategoryModalLabel">Add Item Category</h5>
                        <p class="mb-0 small text-secondary">Create a new category for inventory items</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Office Supplies">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="" disabled {{ old('status') ? '' : 'selected' }}>Choose status</option>
                            <option value="Active" {{ old('status') === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer modal-icat-footer">
                    <button type="button" class="btn-modal-clear" id="clearCategoryForm">
                        <i class="bi bi-arrow-counterclockwise"></i> Clear
                    </button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-check-lg"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Category Modal --}}
<div class="modal fade modal-icat" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('edit-itemcategory-process') }}" id="editCategoryForm">
                @csrf
                <input type="hidden" name="cat_id" id="edit_cat_id">
                <div class="modal-header modal-icat-header edit-header">
                    <div>
                        <h5 class="modal-title" id="editCategoryModalLabel">Edit Item Category</h5>
                        <p class="mb-0 small text-secondary">Update category details</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category Name</label>
                        <input type="text" name="name" id="edit_cat_name" class="form-control">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" id="edit_cat_status" class="form-select">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer modal-icat-footer">
                    <button type="button" class="btn-modal-clear" id="clearEditCategoryForm">
                        <i class="bi bi-arrow-counterclockwise"></i> Clear
                    </button>
                    <button type="submit" class="btn-modal-save update">
                        <i class="bi bi-check-lg"></i> Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const categoryFetchBaseUrl = @json(url('itemcat-id'));

const CategoryAlert = {
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
            title: 'Delete Category?',
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

let editCategorySnapshot = null;

function populateEditForm(data) {
    document.getElementById('edit_cat_id').value = data.id;
    document.getElementById('edit_cat_name').value = data.name || '';
    document.getElementById('edit_cat_status').value = data.status || 'Active';
    editCategorySnapshot = { ...data };
}

function openEditModal(categoryId) {
    fetch(categoryFetchBaseUrl + '/' + categoryId)
        .then(function (res) {
            if (!res.ok) throw new Error('Category not found');
            return res.json();
        })
        .then(function (data) {
            populateEditForm(data);
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        })
        .catch(function () {
            CategoryAlert.error('Load Failed', 'Could not load category details. Please try again.');
        });
}

document.getElementById('clearCategoryForm').addEventListener('click', function () {
    document.getElementById('addCategoryForm').reset();
});

document.getElementById('clearEditCategoryForm').addEventListener('click', function () {
    if (editCategorySnapshot) populateEditForm(editCategorySnapshot);
});

document.querySelectorAll('.btn-open-edit-modal').forEach(function (btn) {
    btn.addEventListener('click', function () {
        openEditModal(this.dataset.categoryId);
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
    $('#categoryTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 10,
        language: { search: '', searchPlaceholder: 'Search categories...' },
        dom: '<"d-flex justify-content-between align-items-center px-3 pt-3 pb-2"lf>rt<"d-flex justify-content-between align-items-center px-3 py-3"ip>',
    });
});
@endif

@if($errors->any() && old('cat_id'))
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('edit_cat_id').value = @json(old('cat_id'));
    document.getElementById('edit_cat_name').value = @json(old('name'));
    document.getElementById('edit_cat_status').value = @json(old('status'));
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
});
@elseif($errors->any())
document.addEventListener('DOMContentLoaded', function () {
    new bootstrap.Modal(document.getElementById('addCategoryModal')).show();
});
@endif

@if(session('message_success'))
CategoryAlert.success('Success!', @json(session('message_success')));
@endif

@if(session('message_error'))
CategoryAlert.error('Oops!', @json(session('message_error')));
@endif

document.querySelectorAll('.btn-confirm-delete').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const name = this.dataset.categoryName;
        const url  = this.dataset.deleteUrl;
        CategoryAlert.confirmDelete(name, function () {
            const form = document.getElementById('deleteCategoryForm');
            form.action = url;
            form.submit();
        });
    });
});

document.querySelectorAll('.btn-delete-cat[data-has-items]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        CategoryAlert.info(
            'Cannot Delete',
            `<strong>${this.dataset.categoryName}</strong> is linked to inventory items.<br><small class="text-secondary">Remove or reassign those items before deleting this category.</small>`
        );
    });
});
</script>
@endsection
