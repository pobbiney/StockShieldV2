@php
    $pageName = 'settings';
    $subpageName = 'store';
    $activePct = $totalStores > 0 ? round(($activeCount / $totalStores) * 100) : 0;
    $inactivePct = $totalStores > 0 ? round(($inactiveCount / $totalStores) * 100) : 0;
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .store-page { padding: 0 0.5rem 2rem; }

    .store-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #3b82f6 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.28);
    }

    .store-hero::before,
    .store-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .store-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .store-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .store-hero-inner { position: relative; z-index: 1; }

    .store-hero-badge {
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

    .store-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .store-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 540px;
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

    .stat-card.total    .stat-card-icon { background: rgba(37, 99, 235, 0.12); color: #2563eb; }
    .stat-card.active   .stat-card-icon { background: rgba(22, 163, 74, 0.12);  color: #16a34a; }
    .stat-card.inactive .stat-card-icon { background: rgba(100, 116, 139, 0.12); color: #64748b; }

    .stat-card-value {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.total    .stat-card-value { color: #2563eb; }
    .stat-card.active   .stat-card-value { color: #16a34a; }
    .stat-card.inactive .stat-card-value { color: #64748b; }

    .stat-card-label {
        font-size: 0.82rem;
        color: #64748b;
        margin: 0 0 0.85rem;
    }

    .stat-bar-wrap {
        height: 4px;
        background: #f1f5f9;
        border-radius: 2rem;
        overflow: hidden;
    }

    .stat-bar-fill { height: 100%; border-radius: 2rem; transition: width 1s ease; }
    .stat-card.total    .stat-bar-fill { background: #2563eb; }
    .stat-card.active   .stat-bar-fill { background: #16a34a; }
    .stat-card.inactive .stat-bar-fill { background: #94a3b8; }

    .stat-card-meta {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.4rem;
    }

    .store-shell {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        background: #fff;
        animation: statIn 0.5s ease 0.25s both;
    }

    .store-shell-form .store-shell-head {
        padding: 1rem 1.25rem;
    }

    .store-shell-form .store-shell-body {
        padding: 1.15rem 1.25rem 1.25rem;
    }

    .store-shell-form .form-section-title {
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
    }

    .store-shell-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
    }

    .store-shell-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.15rem;
        color: #0f172a;
    }

    .store-shell-body { padding: 1.5rem; }

    .form-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        margin: 0 0 1rem;
        padding-bottom: 0.65rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .form-section-title i {
        width: 28px;
        height: 28px;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        font-size: 0.85rem;
    }

    .field-error {
        color: #dc3545;
        font-size: 0.78rem;
        margin-top: 0.35rem;
    }

    .btn-create-store {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 0.625rem;
        border: none;
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-create-store:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.42);
        color: #fff;
    }

    .store-table-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        background: rgba(37, 99, 235, 0.08);
        color: #2563eb;
        font-size: 0.78rem;
        font-weight: 600;
    }

    #storeTable thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        border-bottom-width: 1px;
        padding: 0.85rem 1rem;
        background: #f8fafc;
    }

    #storeTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-color: #f1f5f9;
        font-size: 0.875rem;
    }

    .store-name-cell {
        font-weight: 600;
        color: #0f172a;
    }

    .store-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.28rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .store-badge.status-active {
        background: rgba(22, 163, 74, 0.1);
        color: #15803d;
    }

    .store-badge.status-inactive {
        background: rgba(100, 116, 139, 0.1);
        color: #475569;
    }

    .store-badge.group-central {
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
    }

    .store-badge.group-satellite {
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
    }

    .store-badge.group-none {
        background: rgba(148, 163, 184, 0.12);
        color: #64748b;
    }

    .btn-edit-store {
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }

    .btn-edit-store:hover {
        background: rgba(37, 99, 235, 0.08);
        border-color: rgba(37, 99, 235, 0.25);
        color: #1d4ed8;
    }

    .store-modal-content { border-radius: 1rem !important; overflow: hidden; }

    .store-modal-header {
        padding: 1.35rem 1.5rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #eff6ff 0%, #fff 100%);
    }

    .store-modal-header .modal-title {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
        color: #0f172a;
    }

    .store-modal-footer {
        padding: 1rem 1.5rem 1.35rem;
        border-top: 1px solid #f1f5f9;
        background: #fafafa;
    }

    .btn-modal-save {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.15rem;
        border-radius: 0.625rem;
        border: none;
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-modal-save:hover { color: #fff; opacity: 0.95; }

    .btn-modal-cancel {
        padding: 0.65rem 1.15rem;
        border-radius: 0.625rem;
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #64748b;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-modal-cancel:hover { background: #f8fafc; color: #334155; }

    .empty-store-state {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #94a3b8;
    }

    .empty-store-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: block;
        color: #cbd5e1;
    }
</style>
@endsection

@section('content')
<div class="container-fluid store-page px-3 px-lg-4 mt-3">

    {{-- Hero --}}
    <div class="store-hero">
        <div class="store-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="store-hero-badge">
                        <i class="bi bi-gear-fill"></i> Settings
                    </div>
                    <h2>Manage Stores</h2>
                    <p>Create and configure stores for stock control, user mapping, and multi-location operations.</p>
                </div>
                <div class="col-lg-4 d-none d-lg-flex justify-content-end align-items-start">
                    @include('layouts.partials.breadcrumb', [
                        'variant' => 'dark',
                        'items' => [
                            ['label' => 'Home', 'url' => route('dashboard'), 'icon' => 'bi-house-door-fill'],
                            ['label' => 'Settings', 'url' => route('store'), 'icon' => 'bi-gear-fill'],
                            ['label' => 'Stores', 'active' => true, 'icon' => 'bi-shop'],
                        ],
                    ])
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card total">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Total Stores</p>
                        <div class="stat-card-value" data-count="{{ $totalStores }}">{{ number_format($totalStores) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-shop"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">{{ $centralCount }} central · {{ $satelliteCount }} satellite</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card active">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Active</p>
                        <div class="stat-card-value" data-count="{{ $activeCount }}">{{ number_format($activeCount) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $activePct }}%"></div></div>
                <p class="stat-card-meta">{{ $activePct }}% of stores are active</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card inactive">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Inactive</p>
                        <div class="stat-card-value" data-count="{{ $inactiveCount }}">{{ number_format($inactiveCount) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-pause-circle-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $inactivePct }}%"></div></div>
                <p class="stat-card-meta">Stores not currently in use</p>
            </div>
        </div>
    </div>

    <div class="store-shell mb-4">
        <div class="store-shell-head">
            <h5><i class="bi bi-diagram-3 me-2 text-primary"></i>Requisition hub store</h5>
            <p class="text-secondary small mb-0">Satellites can request from this hub, from central stores, or from both. Tick both sources on a satellite so hub-stocked items go here and other items go to their main stores.</p>
        </div>
        <div class="store-shell-body">
            <form method="POST" action="{{ route('set-requisition-hub') }}" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-8">
                    <label class="form-label small fw-semibold" for="hub_store_id">Hub satellite store</label>
                    <select class="form-select" name="hub_store_id" id="hub_store_id">
                        <option value="">— None —</option>
                        @foreach($satelliteStores ?? collect() as $sat)
                            <option value="{{ $sat->id }}" @selected(($requisitionHub->id ?? null) == $sat->id)>{{ $sat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn-create-store w-100 justify-content-center">
                        <i class="bi bi-check2"></i> Save hub
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        {{-- Create form --}}
        <div class="col-lg-5">
            <div class="store-shell store-shell-form">
                <div class="store-shell-head">
                    <h5><i class="bi bi-plus-circle me-2 text-primary"></i>Add New Store</h5>
                    <p class="text-secondary small mb-0">Register a store location for inventory and user access.</p>
                </div>
                <div class="store-shell-body">
                    <form action="{{ route('add-store-process') }}" method="POST">
                        @csrf

                        <div class="form-section-title">
                            <i class="bi bi-pencil-square"></i> Store Details
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="store_name">Store Name</label>
                            <input type="text" class="form-control" id="store_name" name="name"
                                value="{{ old('name') }}" placeholder="e.g. Main Warehouse">
                            @error('name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="store_group">Store Group</label>
                            <select class="form-select" id="store_group" name="store_group">
                                <option value="" selected>None (optional)</option>
                                <option value="central" {{ old('store_group') === 'central' ? 'selected' : '' }}>Central Store</option>
                                <option value="satellite" {{ old('store_group') === 'satellite' ? 'selected' : '' }}>Satellite Store</option>
                            </select>
                            @error('store_group')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="store_status">Status</label>
                            <select class="form-select" id="store_status" name="status">
                                <option value="" selected disabled>Choose status</option>
                                <option value="Active" {{ old('status') === 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3 {{ old('store_group') === 'satellite' ? '' : 'd-none' }}" id="add_route_to_hub_wrap">
                            <p class="small fw-semibold mb-2">Requisition sources</p>
                            <input type="hidden" name="route_requisitions_to_hub" value="0">
                            <input type="hidden" name="route_requisitions_to_central" value="0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="route_requisitions_to_hub" value="1" id="add_route_requisitions_to_hub" @checked(old('route_requisitions_to_hub') == 1)>
                                <label class="form-check-label small" for="add_route_requisitions_to_hub">
                                    Request from Admin Store — Satellite (hub)
                                </label>
                            </div>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="route_requisitions_to_central" value="1" id="add_route_requisitions_to_central" @checked(old('route_requisitions_to_central', 1) == 1)>
                                <label class="form-check-label small" for="add_route_requisitions_to_central">
                                    Request from central / main stores
                                </label>
                            </div>
                            <p class="text-secondary small mb-0 mt-2">Shown for satellite stores. Tick both if this store requests from the hub and from main stores.</p>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn-create-store">
                                <i class="bi bi-plus-lg"></i> Add Store
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Stores list --}}
        <div class="col-lg-7">
            <div class="store-shell">
                <div class="store-shell-head">
                    <div class="store-table-head w-100 mb-0">
                        <div>
                            <h5><i class="bi bi-list-ul me-2 text-primary"></i>Existing Stores</h5>
                            <p class="text-secondary small mb-0">All store locations configured in the system.</p>
                        </div>
                        <span class="record-count-badge">
                            <i class="bi bi-collection"></i> {{ $totalStores }} stores
                        </span>
                    </div>
                </div>
                <div class="store-shell-body pt-3">
                    @if($list->isEmpty())
                        <div class="empty-store-state">
                            <i class="bi bi-shop"></i>
                            <p class="mb-0 fw-semibold">No stores yet</p>
                            <p class="small mb-0">Use the form to add your first store.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="storeTable" class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Group</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list as $store)
                                        <tr>
                                            <td class="text-secondary">{{ $loop->iteration }}</td>
                                            <td class="store-name-cell">
                                                {{ $store->name }}
                                                @if($store->is_requisition_hub)
                                                    <span class="store-badge group-central ms-1"><i class="bi bi-star-fill"></i> Hub</span>
                                                @elseif($store->requestsFromHubAndCentral())
                                                    <span class="store-badge group-satellite ms-1"><i class="bi bi-signpost-split"></i> Hub + Central</span>
                                                @elseif($store->route_requisitions_to_hub)
                                                    <span class="store-badge group-satellite ms-1"><i class="bi bi-signpost-split"></i> Routes to hub</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($store->store_group === 'central')
                                                    <span class="store-badge group-central"><i class="bi bi-building"></i> Central</span>
                                                @elseif($store->store_group === 'satellite')
                                                    <span class="store-badge group-satellite"><i class="bi bi-geo-alt"></i> Satellite</span>
                                                @else
                                                    <span class="store-badge group-none">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="store-badge {{ $store->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                                    {{ $store->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn-edit-store btn-open-edit-store"
                                                    title="Edit store"
                                                    data-id="{{ $store->id }}"
                                                    data-name="{{ $store->name }}"
                                                    data-status="{{ $store->status }}"
                                                    data-group="{{ $store->store_group ?? '' }}"
                                                    data-route-hub="{{ $store->route_requisitions_to_hub ? '1' : '0' }}"
                                                    data-route-central="{{ $store->routesRequisitionsToCentral() ? '1' : '0' }}"
                                                    data-is-hub="{{ $store->is_requisition_hub ? '1' : '0' }}">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('settings.edit-store-modal')
@endsection

@section('scripts')
<script>
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
    $('#storeTable').DataTable({
        pageLength: 10,
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [4] },
        ],
        language: {
            search: '',
            searchPlaceholder: 'Search stores…',
            lengthMenu: 'Show _MENU_',
            info: 'Showing _START_–_END_ of _TOTAL_',
            paginate: { previous: '‹', next: '›' },
        },
        dom: '<"d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3"lf>t<"d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3"ip>',
    });
});
@endif

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-open-edit-store');
    if (!btn) return;

    document.getElementById('edit_store_id').value = btn.dataset.id || '';
    document.getElementById('edit_store_name').value = btn.dataset.name || '';
    document.getElementById('edit_store_status').value = btn.dataset.status || 'Active';
    document.getElementById('edit_store_group').value = btn.dataset.group || '';

    const routeCheckbox = document.getElementById('edit_route_requisitions_to_hub');
    const centralCheckbox = document.getElementById('edit_route_requisitions_to_central');
    const routeWrap = document.getElementById('edit_route_to_hub_wrap');
    const isHub = btn.dataset.isHub === '1';
    const isSatellite = btn.dataset.group === 'satellite';

    routeCheckbox.checked = btn.dataset.routeHub === '1';
    centralCheckbox.checked = btn.dataset.routeCentral === '1';
    routeCheckbox.disabled = !isSatellite || isHub;
    centralCheckbox.disabled = !isSatellite || isHub;
    routeWrap.classList.toggle('d-none', !isSatellite || isHub);

    const modalEl = document.getElementById('editStoreModal');
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
});

document.getElementById('edit_store_group')?.addEventListener('change', function () {
    const routeCheckbox = document.getElementById('edit_route_requisitions_to_hub');
    const centralCheckbox = document.getElementById('edit_route_requisitions_to_central');
    const routeWrap = document.getElementById('edit_route_to_hub_wrap');
    const isSatellite = this.value === 'satellite';
    routeWrap.classList.toggle('d-none', !isSatellite);
    if (!isSatellite) {
        routeCheckbox.checked = false;
        centralCheckbox.checked = true;
        routeCheckbox.disabled = true;
        centralCheckbox.disabled = true;
    } else {
        routeCheckbox.disabled = false;
        centralCheckbox.disabled = false;
        if (!routeCheckbox.checked && !centralCheckbox.checked) {
            centralCheckbox.checked = true;
        }
    }
});

function syncAddStoreRouting() {
    const group = document.getElementById('store_group');
    const wrap = document.getElementById('add_route_to_hub_wrap');
    const hubBox = document.getElementById('add_route_requisitions_to_hub');
    const centralBox = document.getElementById('add_route_requisitions_to_central');
    if (!group || !wrap) return;
    const isSatellite = group.value === 'satellite';
    wrap.classList.toggle('d-none', !isSatellite);
    if (!isSatellite) {
        if (hubBox) hubBox.checked = false;
        if (centralBox) centralBox.checked = true;
    }
}

document.getElementById('store_group')?.addEventListener('change', syncAddStoreRouting);
syncAddStoreRouting();
</script>
@endsection
