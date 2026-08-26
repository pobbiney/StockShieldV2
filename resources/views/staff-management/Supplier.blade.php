@php
    $pageName = "staff";
    $subpageName = "supplier";
    $activePct   = $totalSuppliers > 0 ? round(($activeCount / $totalSuppliers) * 100) : 0;
    $inactivePct = $totalSuppliers > 0 ? round(($inactiveCount / $totalSuppliers) * 100) : 0;
@endphp

@extends('layouts.backendapp')

@section('page-alerts')
@endsection

@section('css')
<style>
    .supplier-page { padding: 0 0.5rem 2rem; }

    .supplier-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 60%, #4f8ef7 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(13, 110, 253, 0.25);
    }

    .supplier-hero::before,
    .supplier-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .supplier-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .supplier-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .supplier-hero-inner { position: relative; z-index: 1; }

    .supplier-hero-badge {
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

    .supplier-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .supplier-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 520px;
    }

    .supplier-hero-actions {
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

    .supplier-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .supplier-hero .breadcrumb-item a:hover { color: #fff; }
    .supplier-hero .breadcrumb-item.active { color: #fff; }

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

    .stat-card.total  .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-card.active .stat-card-icon { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
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

    .supplier-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: statIn 0.5s ease 0.32s both;
    }

    .supplier-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .supplier-table-head h5 {
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

    #supplierTable thead th {
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

    #supplierTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }

    #supplierTable tbody tr { transition: background 0.15s ease; background: #fff; }
    #supplierTable tbody tr:nth-child(even) { background: #fafafa; }
    #supplierTable tbody tr:hover { background: #f8fafc !important; }

    .supplier-table-card .dataTables_wrapper .dataTables_filter input {
        border: 1.5px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.35rem 0.75rem;
    }

    .supplier-avatar-initial {
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

    .supplier-avatar-initial.c0 { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .supplier-avatar-initial.c1 { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
    .supplier-avatar-initial.c2 { background: rgba(217, 119, 6, 0.12); color: #d97706; }
    .supplier-avatar-initial.c3 { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }

    .supplier-name { font-weight: 600; color: #0f172a; }
    .supplier-sub  { font-size: 0.78rem; color: #94a3b8; }

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

    .city-pill {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 2rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.78rem;
        color: #475569;
    }

    .supplier-actions { display: flex; gap: 0.35rem; }

    .btn-edit-supplier,
    .btn-delete-supplier {
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

    .btn-edit-supplier { color: #0d6efd; }
    .btn-edit-supplier:hover { background: #0d6efd; color: #fff; transform: scale(1.08); }

    .btn-delete-supplier { color: #dc3545; }
    .btn-delete-supplier:hover { background: #dc3545; color: #fff; transform: scale(1.08); }

    .btn-delete-supplier.disabled,
    .btn-delete-supplier:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: #f1f5f9;
        color: #94a3b8;
    }

    .btn-delete-supplier.disabled:hover,
    .btn-delete-supplier:disabled:hover {
        background: #f1f5f9;
        color: #94a3b8;
        transform: none;
    }

    .supplier-empty {
        text-align: center;
        padding: 4rem 2rem;
    }

    .supplier-empty-visual {
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

    .supplier-empty h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .supplier-empty p { color: #64748b; max-width: 380px; margin: 0 auto 1.5rem; }

    /* Modals */
    .modal-supplier .modal-dialog {
        max-width: 1140px;
        max-height: calc(100vh - 1.5rem);
        margin: 0.75rem auto;
    }

    .modal-supplier .modal-content {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.18);
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 1.5rem);
    }

    .modal-supplier #addSupplierForm,
    .modal-supplier #editSupplierForm {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
    }

    .modal-supplier-header {
        flex-shrink: 0;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.75rem;
    }

    .modal-supplier-header .modal-title {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1.15rem;
        color: #0f172a;
    }

    .modal-supplier .modal-body {
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1 1 auto;
        padding: 1.5rem 1.75rem;
    }

    .modal-supplier .modal-dialog.modal-dialog-scrollable .modal-content {
        max-height: calc(100vh - 1.5rem);
    }

    .modal-supplier .modal-body::-webkit-scrollbar { width: 6px; }
    .modal-supplier .modal-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .modal-supplier .modal-body::-webkit-scrollbar-track { background: #f1f5f9; }

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

    .modal-supplier .form-control,
    .modal-supplier .form-select {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.875rem;
    }

    .modal-supplier .form-control:focus,
    .modal-supplier .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
    }

    .modal-supplier-footer {
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
        transition: background 0.15s;
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
        transition: background 0.15s;
    }

    .btn-modal-save:hover { background: #0b5ed7; color: #fff; }

    .btn-modal-save.update { background: #16a34a; }
    .btn-modal-save.update:hover { background: #15803d; }

    .modal-supplier-header.edit-header {
        background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%);
        border-bottom: 1px solid #bbf7d0;
    }

    .modal-supplier-header.edit-header .modal-title { color: #15803d; }
</style>
@endsection

@section('content')

<div class="container-fluid supplier-page px-3 px-lg-4 mt-3">

    <div class="supplier-hero">
        <div class="supplier-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="supplier-hero-badge">
                        <i class="bi bi-truck"></i> Supplier Management
                    </div>
                    <h2>Manage Your Suppliers</h2>
                    <p>Register vendors, track active partnerships, and maintain supplier contact details for stock operations.</p>
                    <div class="supplier-hero-actions">
                        <button type="button" class="btn-hero-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                            <i class="bi bi-building-add"></i> Add New Supplier
                        </button>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Suppliers</li>
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
                    <div class="stat-card-icon"><i class="bi bi-building"></i></div>
                </div>
                <div class="stat-card-value" data-count="{{ $totalSuppliers }}">{{ number_format($totalSuppliers) }}</div>
                <p class="stat-card-label">Total Suppliers</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">All registered vendors</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card active">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <span class="stat-pct-badge">{{ $activePct }}%</span>
                </div>
                <div class="stat-card-value" data-count="{{ $activeCount }}">{{ number_format($activeCount) }}</div>
                <p class="stat-card-label">Active Suppliers</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $activePct }}%"></div></div>
                <p class="stat-card-meta">{{ $activePct }}% of total suppliers</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card inactive">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-pause-circle-fill"></i></div>
                    <span class="stat-pct-badge">{{ $inactivePct }}%</span>
                </div>
                <div class="stat-card-value" data-count="{{ $inactiveCount }}">{{ number_format($inactiveCount) }}</div>
                <p class="stat-card-label">Inactive Suppliers</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $inactivePct }}%"></div></div>
                <p class="stat-card-meta">{{ $inactivePct }}% of total suppliers</p>
            </div>
        </div>
    </div>

    @if($totalSuppliers > 0)
    <div class="status-ratio-strip">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="fw-semibold small">Status Distribution</span>
            <span class="text-secondary small">{{ $totalSuppliers }} supplier{{ $totalSuppliers !== 1 ? 's' : '' }}</span>
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

    <div class="supplier-table-card mb-5">
        <div class="supplier-table-head">
            <div>
                <h5>Registered Suppliers</h5>
                <span class="record-count-badge">
                    <i class="bi bi-database"></i>
                    {{ $totalSuppliers }} record{{ $totalSuppliers !== 1 ? 's' : '' }}
                </span>
            </div>
            <button type="button" class="btn btn-theme btn-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="bi bi-plus-lg me-1"></i> Add Supplier
            </button>
        </div>

        <div class="p-0">
            @if($list->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0 w-100" id="supplierTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company</th>
                                <th>Code</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>City</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list as $supplier)
                                @php
                                    $initials = strtoupper(substr($supplier->company ?? 'S', 0, 2));
                                    $colorIdx = $loop->index % 4;
                                    $hasStock = in_array((int) $supplier->id, $suppliersWithStock, true);
                                    $displayName = $supplier->company ?: $supplier->supplier;
                                @endphp
                                <tr>
                                    <td class="text-secondary">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="supplier-avatar-initial c{{ $colorIdx }}">{{ $initials }}</span>
                                            <div>
                                                <div class="supplier-name">{{ $supplier->company }}</div>
                                                <div class="supplier-sub">{{ $supplier->registration_number ?: 'No reg. number' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="code-badge">{{ $supplier->code }}</span></td>
                                    <td>{{ $supplier->supplier }}</td>
                                    <td class="text-secondary">{{ $supplier->phone }}</td>
                                    <td class="text-secondary">{{ $supplier->email ?: '—' }}</td>
                                    <td><span class="city-pill">{{ $supplier->city }}</span></td>
                                    <td>
                                        <span class="status-badge {{ strtolower($supplier->status) === 'active' ? 'active' : 'inactive' }}">
                                            <i class="bi bi-circle-fill" style="font-size:0.45rem"></i>
                                            {{ $supplier->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="supplier-actions">
                                            <button type="button"
                                                    class="btn-edit-supplier btn-open-edit-modal"
                                                    title="Edit supplier"
                                                    data-supplier-id="{{ $supplier->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @if($hasStock)
                                                <button type="button"
                                                        class="btn-delete-supplier disabled"
                                                        title="Cannot delete — linked to stock records"
                                                        data-has-stock="1"
                                                        data-supplier-name="{{ $displayName }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                        class="btn-delete-supplier btn-confirm-delete"
                                                        title="Delete supplier"
                                                        data-delete-url="{{ route('delete-supplier-process', $supplier->id) }}"
                                                        data-supplier-name="{{ $displayName }}">
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
                <div class="supplier-empty">
                    <div class="supplier-empty-visual"><i class="bi bi-building"></i></div>
                    <h5>No suppliers registered yet</h5>
                    <p>Add your first vendor to start tracking supplier details for stock entries and purchases.</p>
                    <button type="button" class="btn btn-theme" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                        <i class="bi bi-plus-lg me-1"></i> Add First Supplier
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<form id="deleteSupplierForm" method="POST" action="" class="d-none">
    @csrf
</form>

{{-- Add Supplier Modal --}}
<div class="modal fade modal-supplier" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('add-supplier-process') }}" id="addSupplierForm">
                @csrf
                <div class="modal-header modal-supplier-header">
                    <div>
                        <h5 class="modal-title" id="addSupplierModalLabel">Add New Supplier</h5>
                        <p class="mb-0 small text-secondary">Register a vendor into StockShield</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-section-title">Basic Information</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Supplier Code</label>
                            <input type="text" name="code" class="form-control" value="{{ old('code', $supCode) }}" placeholder="SUP-00001">
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Contact Person</label>
                            <input type="text" name="supplier" class="form-control" value="{{ old('supplier') }}" placeholder="Full name">
                            @error('supplier') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Phone">
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <p class="modal-section-title">Company Details</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="email@company.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Company Name</label>
                            <input type="text" name="company" class="form-control" value="{{ old('company') }}" placeholder="Company name">
                            @error('company') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">City / Location</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="City">
                            @error('city') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">TIN Number</label>
                            <input type="text" name="tin_number" class="form-control" value="{{ old('tin_number') }}" placeholder="TIN">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Registration Number</label>
                            <input type="text" name="registration_number" class="form-control" value="{{ old('registration_number') }}" placeholder="Reg. number">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Company Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Full address">{{ old('address') }}</textarea>
                            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
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
                <div class="modal-footer modal-supplier-footer">
                    <button type="button" class="btn-modal-clear" id="clearSupplierForm">
                        <i class="bi bi-arrow-counterclockwise"></i> Clear
                    </button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-check-lg"></i> Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Supplier Modal --}}
<div class="modal fade modal-supplier" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('update-supplier-process') }}" id="editSupplierForm">
                @csrf
                <input type="hidden" name="supplier_id" id="edit_supplier_id">
                <div class="modal-header modal-supplier-header edit-header">
                    <div>
                        <h5 class="modal-title" id="editSupplierModalLabel">Edit Supplier</h5>
                        <p class="mb-0 small text-secondary">Update supplier details</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-section-title">Basic Information</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Supplier Code</label>
                            <input type="text" name="code" id="edit_code" class="form-control">
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Contact Person</label>
                            <input type="text" name="supplier" id="edit_supplier" class="form-control">
                            @error('supplier') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Phone Number</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <p class="modal-section-title">Company Details</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Company Name</label>
                            <input type="text" name="company" id="edit_company" class="form-control">
                            @error('company') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">City / Location</label>
                            <input type="text" name="city" id="edit_city" class="form-control">
                            @error('city') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">TIN Number</label>
                            <input type="text" name="tin_number" id="edit_tin_number" class="form-control">
                            @error('tin_number') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Registration Number</label>
                            <input type="text" name="registration_number" id="edit_registration_number" class="form-control">
                            @error('registration_number') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Company Address</label>
                            <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
                            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-supplier-footer">
                    <button type="button" class="btn-modal-clear" id="clearEditSupplierForm">
                        <i class="bi bi-arrow-counterclockwise"></i> Clear
                    </button>
                    <button type="submit" class="btn-modal-save update">
                        <i class="bi bi-check-lg"></i> Update Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const supplierFetchBaseUrl = @json(url('supplier-id'));
const defaultSupCode = @json($supCode);

const SupplierAlert = {
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
            title: 'Delete Supplier?',
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

let editSupplierSnapshot = null;

function populateEditForm(data) {
    document.getElementById('edit_supplier_id').value = data.id;
    document.getElementById('edit_code').value = data.code || '';
    document.getElementById('edit_supplier').value = data.supplier || '';
    document.getElementById('edit_phone').value = data.phone || '';
    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_company').value = data.company || '';
    document.getElementById('edit_city').value = data.city || '';
    document.getElementById('edit_tin_number').value = data.tin_number || '';
    document.getElementById('edit_registration_number').value = data.registration_number || '';
    document.getElementById('edit_address').value = data.address || '';
    document.getElementById('edit_status').value = data.status || 'Active';
    editSupplierSnapshot = { ...data };
}

function openEditModal(supplierId) {
    fetch(supplierFetchBaseUrl + '/' + supplierId)
        .then(function (res) {
            if (!res.ok) throw new Error('Supplier not found');
            return res.json();
        })
        .then(function (data) {
            populateEditForm(data);
            new bootstrap.Modal(document.getElementById('editSupplierModal')).show();
        })
        .catch(function () {
            SupplierAlert.error('Load Failed', 'Could not load supplier details. Please try again.');
        });
}

document.getElementById('clearSupplierForm').addEventListener('click', function () {
    const form = document.getElementById('addSupplierForm');
    form.reset();
    form.querySelector('[name="code"]').value = defaultSupCode;
});

document.getElementById('clearEditSupplierForm').addEventListener('click', function () {
    if (editSupplierSnapshot) populateEditForm(editSupplierSnapshot);
});

document.querySelectorAll('.btn-open-edit-modal').forEach(function (btn) {
    btn.addEventListener('click', function () {
        openEditModal(this.dataset.supplierId);
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
    $('#supplierTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 10,
        language: { search: '', searchPlaceholder: 'Search suppliers...' },
        dom: '<"d-flex justify-content-between align-items-center px-3 pt-3 pb-2"lf>rt<"d-flex justify-content-between align-items-center px-3 py-3"ip>',
    });
});
@endif

@if($errors->any() && old('supplier_id'))
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('edit_supplier_id').value = @json(old('supplier_id'));
    document.getElementById('edit_code').value = @json(old('code'));
    document.getElementById('edit_supplier').value = @json(old('supplier'));
    document.getElementById('edit_phone').value = @json(old('phone'));
    document.getElementById('edit_email').value = @json(old('email'));
    document.getElementById('edit_company').value = @json(old('company'));
    document.getElementById('edit_city').value = @json(old('city'));
    document.getElementById('edit_tin_number').value = @json(old('tin_number'));
    document.getElementById('edit_registration_number').value = @json(old('registration_number'));
    document.getElementById('edit_address').value = @json(old('address'));
    document.getElementById('edit_status').value = @json(old('status'));
    new bootstrap.Modal(document.getElementById('editSupplierModal')).show();
});
@elseif($errors->any())
document.addEventListener('DOMContentLoaded', function () {
    new bootstrap.Modal(document.getElementById('addSupplierModal')).show();
});
@endif

@if(session('message_success'))
SupplierAlert.success('Success!', @json(session('message_success')));
@endif

@if(session('message_error'))
SupplierAlert.error('Oops!', @json(session('message_error')));
@endif

document.querySelectorAll('.btn-confirm-delete').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const name = this.dataset.supplierName;
        const url  = this.dataset.deleteUrl;
        SupplierAlert.confirmDelete(name, function () {
            const form = document.getElementById('deleteSupplierForm');
            form.action = url;
            form.submit();
        });
    });
});

document.querySelectorAll('.btn-delete-supplier[data-has-stock]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        SupplierAlert.info(
            'Cannot Delete',
            `<strong>${this.dataset.supplierName}</strong> is linked to stock records.<br><small class="text-secondary">Remove or reassign stock entries before deleting this supplier.</small>`
        );
    });
});
</script>
@endsection
