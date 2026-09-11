@php
    $pageName = 'request';
    $subpageName = 'rejected-items';
    $filteredCount = $items->count();

    $tabs = [
        'all'           => ['label' => 'All',           'count' => $typeCounts['all']],
        'requisition'   => ['label' => 'Requisitions',  'count' => $typeCounts['requisition']],
        'stock'         => ['label' => 'Stock',         'count' => $typeCounts['stock']],
        'issue'         => ['label' => 'Issues',        'count' => $typeCounts['issue']],
        'reverse_entry' => ['label' => 'Reversals',     'count' => $typeCounts['reverse_entry']],
    ];
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .ri-page { padding: 0 0.5rem 2rem; }

    .ri-hero {
        background: linear-gradient(135deg, #7f1d1d 0%, #dc2626 55%, #f87171 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(220, 38, 38, 0.28);
    }

    .ri-hero::before,
    .ri-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .ri-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .ri-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .ri-hero-inner { position: relative; z-index: 1; }

    .ri-hero-badge {
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

    .ri-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .ri-hero p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 620px;
    }

    .ri-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .ri-hero .breadcrumb-item.active { color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: riStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }
    .stat-card:nth-child(4) { animation-delay: 0.2s; }
    .stat-card:nth-child(5) { animation-delay: 0.25s; }

    @keyframes riStatIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .stat-card.total .stat-card-icon  { background: rgba(220, 38, 38, 0.12); color: #dc2626; }
    .stat-card.req .stat-card-icon    { background: rgba(37, 99, 235, 0.12); color: #2563eb; }
    .stat-card.stock .stat-card-icon  { background: rgba(245, 158, 11, 0.12); color: #d97706; }
    .stat-card.issue .stat-card-icon  { background: rgba(139, 92, 246, 0.12); color: #7c3aed; }
    .stat-card.rev .stat-card-icon    { background: rgba(100, 116, 139, 0.12); color: #475569; }

    .stat-card-value {
        font-family: "SUSE", sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
        margin-bottom: 0.25rem;
    }

    .stat-card-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0;
    }

    .ri-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }

    .ri-tab {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        color: #64748b;
        background: #fff;
        border: 1px solid #e2e8f0;
        transition: all 0.15s ease;
    }

    .ri-tab:hover { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
    .ri-tab.active {
        color: #fff;
        background: linear-gradient(135deg, #991b1b, #dc2626);
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
    }

    .ri-tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.35rem;
        height: 1.35rem;
        padding: 0 0.35rem;
        border-radius: 1rem;
        font-size: 0.72rem;
        background: rgba(0, 0, 0, 0.06);
    }

    .ri-tab.active .ri-tab-count { background: rgba(255, 255, 255, 0.22); }

    .ri-table-card {
        background: #fff;
        border-radius: 1.125rem;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .ri-table-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .ri-table-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        margin-bottom: 0;
        font-size: 1rem;
    }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        background: #fef2f2;
        color: #b91c1c;
        margin-left: 0.5rem;
    }

    .ri-table-card table thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.85rem 1rem;
        white-space: nowrap;
    }

    .ri-table-card table tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .type-badge.requisition   { background: rgba(37, 99, 235, 0.12); color: #2563eb; }
    .type-badge.stock_entry   { background: rgba(245, 158, 11, 0.12); color: #b45309; }
    .type-badge.satellite_entry { background: rgba(234, 88, 12, 0.12); color: #c2410c; }
    .type-badge.issue         { background: rgba(139, 92, 246, 0.12); color: #7c3aed; }
    .type-badge.reverse_entry { background: rgba(100, 116, 139, 0.12); color: #475569; }

    .ref-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        font-size: 0.78rem;
        font-weight: 600;
        font-family: ui-monospace, monospace;
        background: #f1f5f9;
        color: #334155;
    }

    .code-badge {
        display: inline-block;
        padding: 0.15rem 0.45rem;
        border-radius: 0.3rem;
        font-size: 0.72rem;
        font-weight: 600;
        background: #eff6ff;
        color: #2563eb;
    }

    .reason-cell {
        max-width: 220px;
        white-space: normal;
        font-size: 0.82rem;
        color: #475569;
        line-height: 1.4;
    }

    .date-cell { white-space: nowrap; }
    .date-cell .date-time { display: block; font-size: 0.72rem; color: #94a3b8; }

    .ri-empty {
        text-align: center;
        padding: 3.5rem 1.5rem;
    }

    .ri-empty-visual {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #fef2f2;
        color: #dc2626;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')

<div class="container-fluid ri-page px-3 px-lg-4 mt-3">

    <div class="ri-hero">
        <div class="ri-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="ri-hero-badge">
                        <i class="bi bi-x-circle"></i> Rejected Items
                    </div>
                    <h2>Rejection History</h2>
                    <p>
                        All rejected requests across requisitions, stock entries, issue approvals, and reverse entries —
                        scoped to your store or department.
                        @if($activeStore)
                            <span class="d-block mt-1 opacity-75"><i class="bi bi-shop me-1"></i>{{ $activeStore->name }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-lg-4 d-flex flex-column align-items-lg-end gap-2">
                    <nav aria-label="breadcrumb" class="d-none d-lg-block">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('Requisition') }}" class="text-decoration-none">Requisition</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Rejected Items</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl">
            <div class="stat-card total">
                <div class="stat-card-icon"><i class="bi bi-x-octagon"></i></div>
                <div class="stat-card-value">{{ number_format($typeCounts['all']) }}</div>
                <p class="stat-card-label">Total Rejected</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="stat-card req">
                <div class="stat-card-icon"><i class="bi bi-clipboard-x"></i></div>
                <div class="stat-card-value">{{ number_format($typeCounts['requisition']) }}</div>
                <p class="stat-card-label">Requisitions</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="stat-card stock">
                <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-card-value">{{ number_format($typeCounts['stock']) }}</div>
                <p class="stat-card-label">Stock Entries</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="stat-card issue">
                <div class="stat-card-icon"><i class="bi bi-arrow-left-right"></i></div>
                <div class="stat-card-value">{{ number_format($typeCounts['issue']) }}</div>
                <p class="stat-card-label">Issue Approvals</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="stat-card rev">
                <div class="stat-card-icon"><i class="bi bi-arrow-counterclockwise"></i></div>
                <div class="stat-card-value">{{ number_format($typeCounts['reverse_entry']) }}</div>
                <p class="stat-card-label">Reverse Entries</p>
            </div>
        </div>
    </div>

    <div class="ri-tabs">
        @foreach($tabs as $key => $tab)
            <a href="{{ route('RejectedItems', ['type' => $key]) }}"
               class="ri-tab {{ $selectedType === $key ? 'active' : '' }}">
                {{ $tab['label'] }}
                <span class="ri-tab-count">{{ $tab['count'] }}</span>
            </a>
        @endforeach
    </div>

    <div class="ri-table-card mb-5">
        <div class="ri-table-head">
            <div>
                <h5><i class="bi bi-table me-1 text-danger"></i> Rejected Records</h5>
                <span class="record-count-badge">
                    <i class="bi bi-x-circle"></i>
                    {{ $filteredCount }} record{{ $filteredCount !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        @if($items->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="rejectedItemsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Item</th>
                            <th>Store</th>
                            <th>Qty</th>
                            <th>Reason</th>
                            <th>Rejected By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $row)
                            <tr>
                                <td class="text-secondary">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="type-badge {{ $row['type'] }}">
                                        <i class="bi bi-x-circle"></i>
                                        {{ $row['type_label'] }}
                                    </span>
                                </td>
                                <td><span class="ref-badge">{{ $row['reference'] }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $row['item_name'] }}</div>
                                    <span class="code-badge">{{ $row['item_code'] }}</span>
                                </td>
                                <td>{{ $row['store_label'] }}</td>
                                <td>{{ $row['qty'] !== null ? number_format($row['qty']) : '—' }}</td>
                                <td class="reason-cell">{{ $row['reason'] }}</td>
                                <td>{{ $row['rejected_by'] }}</td>
                                <td class="date-cell">
                                    @if($row['rejected_at'])
                                        {{ $row['rejected_at']->format('M d, Y') }}
                                        <span class="date-time">{{ $row['rejected_at']->format('h:i A') }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="ri-empty">
                <div class="ri-empty-visual"><i class="bi bi-check-circle"></i></div>
                <h5 class="fw-semibold text-dark">No rejected items</h5>
                <p class="text-muted mb-0">
                    @if($selectedType !== 'all')
                        No rejected records found for this category in your store scope.
                    @else
                        There are no rejected records in your store scope at this time.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    if ($('#rejectedItemsTable').length && $.fn.DataTable) {
        $('#rejectedItemsTable').DataTable({
            order: [[8, 'desc']],
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, 'All']],
            language: { search: '', searchPlaceholder: 'Search rejections…' },
            columnDefs: [{ orderable: false, targets: 0 }],
        });
    }
});
</script>
@endsection
