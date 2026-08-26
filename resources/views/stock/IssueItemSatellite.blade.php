@php
    $pageName = 'stock';
    $subpageName = 'issue-item-satellite';
    $uniquePct = $pendingCount > 0 ? round(($uniqueItems / $pendingCount) * 100) : 0;
    $oldAddItem = old('item') ? $getItemid->firstWhere('id', (int) old('item')) : null;
    $reqItemsJson = $getItemid->map(function ($item) {
        return [
            'id' => $item->id,
            'code' => $item->item_code ?? '',
            'name' => $item->name ?? '',
        ];
    })->values();
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .sis-page { padding: 0 0.5rem 2rem; }

    .sis-hero {
        background: linear-gradient(135deg, #92400e 0%, #d97706 60%, #f59e0b 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(217, 119, 6, 0.25);
    }

    .sis-hero::before, .sis-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .sis-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .sis-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }
    .sis-hero-inner { position: relative; z-index: 1; }

    .sis-hero-badge {
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

    .sis-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .sis-hero p { color: rgba(255, 255, 255, 0.88); font-size: 0.9rem; margin-bottom: 0; max-width: 560px; }
    .sis-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .sis-hero .breadcrumb-item.active { color: #fff; }

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

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .stat-card-icon {
        width: 48px; height: 48px; border-radius: 0.875rem;
        display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
    }

    .stat-card.pending .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }
    .stat-card.qty .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-card.unique .stat-card-icon { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }
    .stat-card-value { font-size: 2.25rem; font-weight: 800; line-height: 1; margin-bottom: 0.2rem; }
    .stat-card.pending .stat-card-value { color: #d97706; }
    .stat-card.qty .stat-card-value { color: #0d6efd; }
    .stat-card.unique .stat-card-value { color: #7c3aed; }
    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0 0 0.85rem; }
    .stat-bar-wrap { height: 4px; background: #f1f5f9; border-radius: 2rem; overflow: hidden; }
    .stat-bar-fill { height: 100%; border-radius: 2rem; background: #d97706; }
    .stat-card-meta { font-size: 0.72rem; color: #94a3b8; margin-top: 0.4rem; }
    .stat-pct-badge { font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 2rem; background: rgba(124, 58, 237, 0.12); color: #7c3aed; }

    .add-item-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: #fff;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.75rem;
        overflow: visible;
        position: relative;
        z-index: 30;
    }

    .sis-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: #fff;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.75rem;
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .add-item-head, .sis-table-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .add-item-head h5, .sis-table-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
        color: #0f172a;
    }

    .add-item-body { padding: 1.25rem 1.5rem 1.5rem; overflow: visible; }
    .add-item-card .form-control, .add-item-card .form-select {
        border-radius: 0.625rem; border: 1.5px solid #e2e8f0; font-size: 0.875rem; min-height: 46px;
    }

    .btn-add-line {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem;
        padding: 0.65rem 1.25rem; border-radius: 0.625rem; border: none;
        background: #d97706; color: #fff; font-size: 0.875rem; font-weight: 600; height: 46px; width: 100%;
    }

    .btn-add-line:hover { background: #b45309; color: #fff; }

    .item-autocomplete { position: relative; z-index: 50; }
    .item-autocomplete-icon { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; z-index: 2; }
    .item-autocomplete-input { padding-left: 2.35rem !important; padding-right: 2.25rem !important; position: relative; z-index: 1; }
    .item-autocomplete-list {
        position: absolute; left: 0; right: 0; top: calc(100% + 4px); z-index: 9999;
        max-height: 240px; overflow-y: auto; margin: 0; padding: 0.35rem; list-style: none;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 0.625rem;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.16);
    }

    .item-autocomplete-list[hidden] { display: none !important; }

    .item-autocomplete-option { padding: 0.55rem 0.7rem; border-radius: 0.5rem; cursor: pointer; }
    .item-autocomplete-option:hover, .item-autocomplete-option.active { background: rgba(217, 119, 6, 0.08); }
    .field-hint, .stock-hint { font-size: 0.72rem; color: #94a3b8; margin-top: 0.25rem; }

    .record-count-badge {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.3rem 0.75rem; border-radius: 2rem;
        background: rgba(217, 119, 6, 0.08); color: #d97706; font-size: 0.78rem; font-weight: 600;
    }

    .sis-table thead th {
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
        color: #64748b; border-bottom: 2px solid #e2e8f0; padding: 0.9rem 1rem; background: #f8fafc; white-space: nowrap;
    }

    .sis-table tbody td { padding: 0.9rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; }
    .sis-table tbody tr:nth-child(even) { background: #fafafa; }
    .code-badge { display: inline-block; padding: 0.2rem 0.55rem; border-radius: 0.375rem; background: #f1f5f9; color: #475569; font-size: 0.78rem; font-weight: 600; font-family: monospace; }
    .ward-badge { display: inline-block; padding: 0.2rem 0.55rem; border-radius: 0.375rem; background: rgba(99, 102, 241, 0.1); color: #4f46e5; font-size: 0.75rem; font-weight: 600; }

    .qty-requested, .qty-issued {
        display: inline-flex; align-items: center; justify-content: center; min-width: 2.5rem;
        padding: 0.25rem 0.65rem; border-radius: 0.375rem; font-size: 0.85rem; font-weight: 700;
    }

    .qty-requested { background: rgba(217, 119, 6, 0.1); color: #d97706; }
    .qty-issued { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .qty-issued.zero { background: #f1f5f9; color: #94a3b8; }

    .status-badge {
        display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.25rem 0.65rem;
        border-radius: 2rem; font-size: 0.75rem; font-weight: 600;
    }

    .status-badge.draft { background: rgba(148, 163, 184, 0.15); color: #475569; }
    .status-badge.submitted { background: rgba(255, 193, 7, 0.15); color: #b45309; }
    .status-badge.fulfilled { background: rgba(16, 185, 129, 0.15); color: #047857; }
    .status-badge.partial { background: rgba(245, 158, 11, 0.15); color: #b45309; }

    .btn-delete-req {
        width: 34px; height: 34px; border-radius: 0.5rem; border: 1.5px solid #e2e8f0;
        background: #fff; color: #dc3545; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;
    }

    .btn-delete-req:hover { background: #dc3545; color: #fff; }

    .btn-submit-req, .btn-issue-batch {
        display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.65rem 1.35rem;
        border-radius: 2rem; border: none; font-weight: 600; font-size: 0.875rem; color: #fff;
    }

    .btn-submit-req { background: #d97706; }
    .btn-submit-req:hover { background: #b45309; color: #fff; }
    .btn-issue-batch { background: #059669; }
    .btn-issue-batch:hover { background: #047857; color: #fff; }

    .sis-empty { text-align: center; padding: 3rem 2rem; color: #64748b; }
    .sis-empty-visual {
        width: 72px; height: 72px; border-radius: 50%; background: rgba(217, 119, 6, 0.08);
        display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.75rem; color: #d97706;
    }

    .sis-submit-bar {
        padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9;
        display: flex; justify-content: flex-end; align-items: center; flex-wrap: wrap; gap: 0.75rem;
    }

    .req-group-head {
        padding: 1rem 1.5rem; background: #fafbfc; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;
    }

    .req-group-no { font-family: monospace; font-weight: 700; font-size: 0.9rem; color: #0f172a; }
    .req-group-meta { font-size: 0.78rem; color: #64748b; }
    .section-divider-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8; padding: 0.5rem 1.5rem 0; margin: 0; }
</style>
@endsection

@section('content')
<div class="container-fluid sis-page px-3 px-lg-4 mt-3">

    <div class="sis-hero">
        <div class="sis-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="sis-hero-badge"><i class="bi bi-box-arrow-right"></i> Satellite Internal Issue</div>
                    <h2>Issue Items from Satellite Stock</h2>
                    <p>Build issue drafts from your satellite inventory and issue to {{ strtolower($destinationLabel ?? 'ward') }}s when ready.</p>
                    @if($activeStore)
                    <div class="store-context-banner">
                        <span class="store-chip"><i class="bi bi-shop"></i> {{ $activeStore->name }}</span>
                        <i class="bi bi-arrow-right"></i>
                        <span class="store-chip"><i class="bi bi-hospital"></i> Wards</span>
                    </div>
                    @endif
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active">Issue Item (Satellite)</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="stat-card pending">
                <div class="stat-card-icon"><i class="bi bi-pencil-square"></i></div>
                <div class="stat-card-value">{{ number_format($pendingCount) }}</div>
                <p class="stat-card-label">Draft Line Items</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill"></div></div>
                <p class="stat-card-meta">In current cart, not yet submitted</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card qty">
                <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-card-value">{{ number_format($totalQtyRequested) }}</div>
                <p class="stat-card-label">Draft Quantity</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="background:#0d6efd"></div></div>
                <p class="stat-card-meta">Units in current draft</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card unique">
                <div class="stat-card-icon"><i class="bi bi-tags"></i></div>
                @if($pendingCount > 0)<span class="stat-pct-badge">{{ $uniquePct }}%</span>@endif
                <div class="stat-card-value">{{ number_format($uniqueItems) }}</div>
                <p class="stat-card-label">Unique Items</p>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="background:#7c3aed;width:{{ $uniquePct }}%"></div></div>
                <p class="stat-card-meta">Different products in draft</p>
            </div>
        </div>
    </div>

    <div class="add-item-card">
        <div class="add-item-head">
            <h5><i class="bi bi-plus-circle me-2" style="color:#d97706"></i>Add Item to Draft</h5>
        </div>
        <div class="add-item-body">
            <form method="post" action="{{ route('add-satellite-issue-process') }}" id="addIssueForm">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label">Item</label>
                        <div class="item-autocomplete" id="sisItemAutocomplete">
                            <i class="bi bi-search item-autocomplete-icon"></i>
                            <input type="text" class="form-control item-autocomplete-input" id="sis_item_search"
                                   placeholder="Start typing item name or code…" autocomplete="off"
                                   value="{{ $oldAddItem ? ($oldAddItem->item_code ? $oldAddItem->item_code . ' — ' : '') . $oldAddItem->name : '' }}">
                            <input type="hidden" name="item" id="sis_item_id" value="{{ old('item') }}">
                            <ul class="item-autocomplete-list" id="sis_item_list" hidden></ul>
                        </div>
                        @error('item') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $destinationLabel ?? 'Ward' }}</label>
                        @if($usesStoreDestinations ?? false)
                            <select name="issue_to_store" class="form-select" required>
                                <option value="">Select store…</option>
                                @foreach($destinationStores as $destStore)
                                    <option value="{{ $destStore->id }}" @selected(old('issue_to_store') == $destStore->id)>{{ $destStore->name }}</option>
                                @endforeach
                            </select>
                            @error('issue_to_store') <small class="text-danger">{{ $message }}</small> @enderror
                            @if(($destinationStores ?? collect())->isEmpty())
                                <div class="field-hint">No other active stores found in the stores table.</div>
                            @endif
                        @else
                            <select name="ward" class="form-select" required>
                                <option value="">Select ward…</option>
                                @foreach($wards as $ward)
                                    <option value="{{ $ward->id }}" @selected(old('ward') == $ward->id)>{{ $ward->name }}</option>
                                @endforeach
                            </select>
                            @error('ward') <small class="text-danger">{{ $message }}</small> @enderror
                            @if($wards->isEmpty())
                                <div class="field-hint"><a href="{{ route('ward') }}">Add wards</a> for this store first.</div>
                            @endif
                        @endif
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">UoM</label>
                        <input type="text" id="uom" class="form-control" readonly placeholder="—">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" min="1" required value="{{ old('quantity') }}">
                        <div class="stock-hint" id="stock_hint"></div>
                        @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn-add-line"><i class="bi bi-plus-lg"></i> Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="sis-table-card mb-4">
        <div class="sis-table-head">
            <div>
                <h5><i class="bi bi-file-earmark-text me-1" style="color:#d97706"></i> Draft Issue Slip</h5>
                <span class="record-count-badge"><i class="bi bi-cart"></i> {{ $pendingCount }} line{{ $pendingCount !== 1 ? 's' : '' }}</span>
            </div>
            @if($pendingCount > 0)
                <a href="{{ route('satellite-issue.submit') }}" class="btn-submit-req btn-confirm-submit">
                    <i class="bi bi-send-fill"></i> Submit Draft
                </a>
            @endif
        </div>

        @if($listitemissue->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 sis-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>{{ $destinationLabel ?? 'Ward' }}</th>
                            <th>UoM</th>
                            <th class="text-center">Req. Qty</th>
                            <th class="text-center">Issued Qty</th>
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
                                <td><span class="ward-badge">{{ $lists->destinationLabel() }}</span></td>
                                <td>{{ $lists->itemname->unitname->name ?? '—' }}</td>
                                <td class="text-center"><span class="qty-requested">{{ $lists->qty_requested }}</span></td>
                                <td class="text-center"><span class="qty-issued zero">0</span></td>
                                <td><span class="status-badge draft"><i class="bi bi-pencil"></i> Draft</span></td>
                                <td>
                                    <button type="button" class="btn-delete-req btn-confirm-delete"
                                            data-delete-url="{{ url('IssueItemSatellite/'.$lists->id.'/delete') }}"
                                            data-item-name="{{ $lists->itemname->name ?? 'this item' }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="sis-submit-bar">
                <span class="text-muted small me-auto">Submit this draft to create an issue slip, then issue stock to deduct from satellite inventory.</span>
                <a href="{{ route('satellite-issue.submit') }}" class="btn-submit-req btn-confirm-submit">
                    <i class="bi bi-send-fill"></i> Submit Draft
                </a>
            </div>
        @else
            <div class="sis-empty">
                <div class="sis-empty-visual"><i class="bi bi-cart"></i></div>
                <h5 class="fw-semibold text-dark">No items in draft</h5>
                <p>Add items from your satellite approved stock above.</p>
            </div>
        @endif
    </div>

    <div class="sis-table-card mb-5">
        <div class="sis-table-head">
            <div>
                <h5><i class="bi bi-clock-history me-1" style="color:#059669"></i> Submitted Issue Slips</h5>
                <span class="record-count-badge" style="background:rgba(16,185,129,0.08);color:#059669">
                    {{ $submittedGroups->count() }} slip{{ $submittedGroups->count() !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        @if($submittedGroups->isNotEmpty())
            @foreach($submittedGroups as $group)
                <div class="req-group-head">
                    <div>
                        <div class="req-group-no">{{ $group->issue_no }}</div>
                        <div class="req-group-meta">
                            {{ $group->ward_label }} ·
                            {{ $group->line_count }} line(s) ·
                            {{ $group->submitted_at ? $group->submitted_at->format('M d, Y h:i A') : '—' }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="qty-requested">{{ $group->total_qty_requested }} req.</span>
                        <span class="qty-issued {{ $group->total_qty_issued > 0 ? '' : 'zero' }}">{{ $group->total_qty_issued }} issued</span>
                        @if($group->can_issue)
                            <a href="{{ route('satellite-issue.issue-batch', encrypt($group->issue_no)) }}"
                               class="btn-issue-batch btn-confirm-issue">
                                <i class="bi bi-box-arrow-right"></i> Issue Stock
                            </a>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 sis-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>{{ $destinationLabel ?? 'Ward' }}</th>
                                <th class="text-center">Req. Qty</th>
                                <th class="text-center">Issued Qty</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group->lines as $line)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $line->itemname->name ?? '—' }}</div>
                                        <span class="code-badge">{{ $line->itemcode->item_code ?? '—' }}</span>
                                    </td>
                                    <td><span class="ward-badge">{{ $line->destinationLabel() }}</span></td>
                                    <td class="text-center"><span class="qty-requested">{{ $line->qty_requested }}</span></td>
                                    <td class="text-center">
                                        <span class="qty-issued {{ (int)$line->qty_issued > 0 ? '' : 'zero' }}">{{ (int)$line->qty_issued }}</span>
                                    </td>
                                    <td><span class="status-badge {{ $line->statusClass() }}">{{ $line->fulfillmentLabel() }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <div class="sis-empty">
                <div class="sis-empty-visual" style="background:rgba(16,185,129,0.08);color:#059669"><i class="bi bi-inbox"></i></div>
                <h5 class="fw-semibold text-dark">No submitted issue slips yet</h5>
                <p>Submit a draft above to create your first issue slip.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
const SisAlert = {
    _base(opts) {
        return Swal.fire(Object.assign({
            width: '28rem', padding: '1.5rem 1.75rem 1.35rem', buttonsStyling: false,
            customClass: {
                popup: 'staff-swal-popup', title: 'staff-swal-title', htmlContainer: 'staff-swal-text',
                confirmButton: 'btn staff-swal-confirm ' + (opts.btnClass || 'neutral'),
                cancelButton: 'btn btn-light border staff-swal-cancel',
            },
        }, opts));
    },
    success(title, text) { return this._base({ icon: 'success', title, text, btnClass: 'success', timer: 2800, timerProgressBar: true }); },
    error(title, text) { return this._base({ icon: 'error', title, text, btnClass: 'error' }); },
    confirmDelete(name, onConfirm) {
        return this._base({
            icon: 'warning', title: 'Remove Item?', html: `Remove <strong>${name}</strong> from your draft?`,
            btnClass: 'error', showCancelButton: true,
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, remove', cancelButtonText: 'Cancel',
        }).then(r => { if (r.isConfirmed && onConfirm) onConfirm(); });
    },
    confirmSubmit(onConfirm) {
        return this._base({
            icon: 'question', title: 'Submit Issue Draft?',
            html: 'Submit all draft items as an issue slip?',
            btnClass: 'success', showCancelButton: true,
            confirmButtonText: '<i class="bi bi-send me-1"></i> Yes, submit', cancelButtonText: 'Cancel',
        }).then(r => { if (r.isConfirmed && onConfirm) onConfirm(); });
    },
    confirmIssue(onConfirm) {
        return this._base({
            icon: 'question', title: 'Issue Stock?',
            html: 'Deduct stock from satellite inventory and mark lines as issued?',
            btnClass: 'success', showCancelButton: true,
            confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> Yes, issue', cancelButtonText: 'Cancel',
        }).then(r => { if (r.isConfirmed && onConfirm) onConfirm(); });
    },
};

const SIS_ITEMS = @json($reqItemsJson);

function filterItems(term) {
    const q = term.trim().toLowerCase();
    if (!q) return [];
    return SIS_ITEMS.filter(i => i.name.toLowerCase().includes(q) || String(i.code).toLowerCase().includes(q)).slice(0, 12);
}

const searchInput = document.getElementById('sis_item_search');
const hiddenInput = document.getElementById('sis_item_id');
const listEl = document.getElementById('sis_item_list');

function renderList(matches) {
    listEl.innerHTML = '';
    if (!matches.length) { listEl.hidden = true; return; }
    matches.forEach(item => {
        const li = document.createElement('li');
        li.className = 'item-autocomplete-option';
        li.textContent = (item.code ? item.code + ' — ' : '') + item.name;
        li.addEventListener('mousedown', e => {
            e.preventDefault();
            hiddenInput.value = item.id;
            searchInput.value = li.textContent;
            listEl.hidden = true;
            loadStock(item.id);
        });
        listEl.appendChild(li);
    });
    listEl.hidden = false;
}

searchInput?.addEventListener('input', () => {
    if (!searchInput.value.trim()) { hiddenInput.value = ''; listEl.hidden = true; return; }
    renderList(filterItems(searchInput.value));
});

function loadStock(itemId) {
    if (!itemId) return;
    $.ajax({
        url: @json(route('get.satellite.batch.number')),
        type: 'POST',
        data: { getID: itemId, _token: @json(csrf_token()) },
        success(response) {
            if (response.batch_number) {
                $('#uom').val(response.uom_name ?? '');
                $('#stock_hint').text(response.qty != null ? 'Satellite stock available: ' + response.qty : '');
            } else {
                SisAlert.error('No Stock Available', response.message || response.message_error || 'No stock available');
                hiddenInput.value = '';
                searchInput.value = '';
            }
        }
    });
}

document.querySelectorAll('.btn-confirm-delete').forEach(btn => {
    btn.addEventListener('click', () => SisAlert.confirmDelete(btn.dataset.itemName, () => { window.location.href = btn.dataset.deleteUrl; }));
});

document.querySelectorAll('.btn-confirm-submit').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        const href = btn.getAttribute('href');
        SisAlert.confirmSubmit(() => { window.location.href = href; });
    });
});

document.querySelectorAll('.btn-confirm-issue').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        const href = btn.getAttribute('href');
        SisAlert.confirmIssue(() => { window.location.href = href; });
    });
});

@if(session('message_success')) SisAlert.success('Success!', @json(session('message_success'))); @endif
@if(session('message_error')) SisAlert.error('Oops!', @json(session('message_error'))); @endif
</script>
@endsection
