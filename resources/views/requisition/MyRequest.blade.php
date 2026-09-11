@php
    $pageName = 'request';
    $subpageName = 'my-request';
    $issuedPct = $totalQtyRequested > 0 ? min(100, round(($totalQtyIssued / $totalQtyRequested) * 100)) : 0;
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .mr-page { padding: 0 0.5rem 2rem; }

    .mr-hero {
        background: linear-gradient(135deg, #0f766e 0%, #14b8a6 55%, #5eead4 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(20, 184, 166, 0.28);
    }

    .mr-hero::before,
    .mr-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .mr-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .mr-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .mr-hero-inner { position: relative; z-index: 1; }

    .mr-hero-badge {
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

    .mr-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .mr-hero p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 560px;
    }

    .mr-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .mr-hero .breadcrumb-item.active { color: #fff; }

    .btn-hero-link {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 2rem;
        border: none;
        background: #fff;
        color: #0f766e;
        font-weight: 700;
        font-size: 0.875rem;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .btn-hero-link:hover { color: #0d9488; transform: translateY(-1px); }

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
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }
    .stat-card:nth-child(4) { animation-delay: 0.2s; }

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

    .stat-card.total   .stat-card-icon { background: rgba(20, 184, 166, 0.12); color: #0f766e; }
    .stat-card.pending .stat-card-icon { background: rgba(245, 158, 11, 0.12); color: #d97706; }
    .stat-card.done    .stat-card-icon { background: rgba(16, 185, 129, 0.12); color: #059669; }
    .stat-card.qty     .stat-card-icon { background: rgba(59, 130, 246, 0.12); color: #2563eb; }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.total   .stat-card-value { color: #0f766e; }
    .stat-card.pending .stat-card-value { color: #d97706; }
    .stat-card.done    .stat-card-value { color: #059669; }
    .stat-card.qty     .stat-card-value { color: #2563eb; }

    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0 0 0.5rem; }
    .stat-card-meta  { font-size: 0.72rem; color: #94a3b8; margin: 0; }

    .mr-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: statIn 0.5s ease 0.25s both;
        background: #fff;
    }

    .mr-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .mr-table-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
    }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(20, 184, 166, 0.1);
        color: #0f766e;
        font-size: 0.78rem;
        font-weight: 600;
    }

    #myRequestTable thead th {
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

    #myRequestTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #myRequestTable tbody tr:nth-child(even) { background: #fafafa; }
    #myRequestTable tbody tr:nth-child(odd)  { background: #fff; }
    #myRequestTable tbody tr:hover { background: #f0fdfa !important; }

    .mr-table-card .table-responsive,
    .mr-table-card .dataTables_wrapper {
        background: #fff;
    }

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

    .req-no-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
        background: rgba(20, 184, 166, 0.1);
        color: #0f766e;
        font-size: 0.78rem;
        font-weight: 700;
        font-family: monospace;
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

    .qty-requested {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.25rem;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .qty-issued {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.25rem;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .qty-issued.zero {
        background: #f1f5f9;
        color: #94a3b8;
    }

    .date-cell {
        font-size: 0.82rem;
        color: #475569;
        white-space: nowrap;
    }

    .date-cell .date-time {
        display: block;
        font-size: 0.72rem;
        color: #94a3b8;
    }

    .date-cell.empty { color: #cbd5e1; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-badge.fulfilled  { background: rgba(16, 185, 129, 0.15); color: #047857; }
    .status-badge.received   { background: rgba(5, 150, 105, 0.15); color: #047857; }
    .status-badge.partial    { background: rgba(245, 158, 11, 0.15); color: #b45309; }
    .status-badge.rejected   { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
    .status-badge.approved   { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .status-badge.submitted  { background: rgba(255, 193, 7, 0.15); color: #b45309; }
    .status-badge.issued     { background: rgba(139, 92, 246, 0.12); color: #7c3aed; }
    .status-badge.pending-issue { background: rgba(217, 119, 6, 0.15); color: #b45309; }
    .status-badge.draft      { background: rgba(148, 163, 184, 0.15); color: #475569; }

    .mr-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .mr-empty-visual {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(20, 184, 166, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.75rem;
        color: #14b8a6;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        padding: 0.4rem 0.75rem;
    }
</style>
@endsection

@section('content')

<div class="container-fluid mr-page px-3 px-lg-4 mt-3">

    <div class="mr-hero">
        <div class="mr-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="mr-hero-badge">
                        <i class="bi bi-clock-history"></i> My Requisitions
                    </div>
                    <h2>Request History</h2>
                    <p>
                        Track every item you have requested from central stores — quantities, status, and issue dates.
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
                            <li class="breadcrumb-item active" aria-current="page">My Requests</li>
                        </ol>
                    </nav>
                    <a href="{{ route('Requisition') }}" class="btn-hero-link">
                        <i class="bi bi-plus-lg"></i> New Requisition
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card total">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-list-ul"></i></div>
                </div>
                <div class="stat-card-value">{{ number_format($totalCount) }}</div>
                <p class="stat-card-label">Total Line Items</p>
                <p class="stat-card-meta">All submitted requests</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card pending">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <div class="stat-card-value">{{ number_format($pendingCount) }}</div>
                <p class="stat-card-label">In Progress</p>
                <p class="stat-card-meta">Awaiting approval or issue</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card done">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-check-circle"></i></div>
                </div>
                <div class="stat-card-value">{{ number_format($fulfilledCount) }}</div>
                <p class="stat-card-label">Fulfilled</p>
                <p class="stat-card-meta">{{ $rejectedCount }} rejected</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card qty">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="stat-card-value">{{ number_format($totalQtyIssued) }}<span style="font-size:1rem;color:#94a3b8"> / {{ number_format($totalQtyRequested) }}</span></div>
                <p class="stat-card-label">Total Issued / Requested</p>
                <p class="stat-card-meta">{{ $issuedPct }}% overall fulfillment</p>
            </div>
        </div>
    </div>

    <div class="mr-table-card mb-5">
        <div class="mr-table-head">
            <div>
                <h5><i class="bi bi-table me-1 text-success"></i> All Requested Items</h5>
                <span class="record-count-badge">
                    <i class="bi bi-inboxes"></i>
                    {{ $totalCount }} record{{ $totalCount !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        @if($listrequest->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="myRequestTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Requisition No</th>
                            <th>Item</th>
                            <th>Source Store</th>
                            <th>UoM</th>
                            <th>Req. Qty</th>
                            <th>Total Qty</th>
                            <th>Issued Qty</th>
                            <th>Total Issued</th>
                            <th>Date Requested</th>
                            <th>Date Issued</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listrequest as $row)
                            @php
                                $issuedQty = $row->issuedQuantity();
                                $reqMultiplier = $row->itemTotalQtyMultiplier();
                                $totalReqQty = $row->effectiveRequestedQuantity();
                                $totalIssuedQty = $row->effectiveIssuedQuantity();
                                $issuedAt = $row->issuedAt();
                                $statusClass = $row->statusClass();
                            @endphp
                            <tr>
                                <td class="text-secondary">{{ $loop->iteration }}</td>
                                <td><span class="req-no-badge">{{ $row->requisition_no }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $row->itemname->name ?? '—' }}</div>
                                    <span class="code-badge">{{ $row->itemcode->item_code ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="source-store-badge">{{ $row->sourceStore->name ?? 'Central' }}</span>
                                </td>
                                <td>{{ $row->itemname->unitname->name ?? '—' }}</td>
                                <td><span class="qty-requested">{{ number_format((int) $row->qty_requested) }}</span></td>
                                <td>
                                    <span class="qty-requested">{{ number_format($totalReqQty) }}</span>
                                    @if($reqMultiplier && $totalReqQty !== (int) $row->qty_requested)
                                        <span class="d-block date-time">{{ number_format((int) $row->qty_requested) }} × {{ number_format($reqMultiplier) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="qty-issued {{ $issuedQty > 0 ? '' : 'zero' }}">{{ number_format($issuedQty) }}</span>
                                </td>
                                <td>
                                    <span class="qty-issued {{ $totalIssuedQty > 0 ? '' : 'zero' }}">{{ number_format($totalIssuedQty) }}</span>
                                    @if($reqMultiplier && $totalIssuedQty !== $issuedQty)
                                        <span class="d-block date-time">{{ number_format($issuedQty) }} × {{ number_format($reqMultiplier) }}</span>
                                    @endif
                                </td>
                                <td class="date-cell">
                                    {{ $row->created_at?->format('M d, Y') ?? '—' }}
                                    <span class="date-time">{{ $row->created_at?->format('h:i A') }}</span>
                                </td>
                                <td class="date-cell {{ $issuedAt ? '' : 'empty' }}">
                                    @if($issuedAt)
                                        {{ $issuedAt->format('M d, Y') }}
                                        <span class="date-time">{{ $issuedAt->format('h:i A') }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge {{ $statusClass }}">
                                        @if($statusClass === 'fulfilled')<i class="bi bi-check-circle"></i>
                                        @elseif($statusClass === 'partial')<i class="bi bi-pie-chart"></i>
                                        @elseif($statusClass === 'rejected')<i class="bi bi-x-circle"></i>
                                        @elseif($statusClass === 'approved')<i class="bi bi-patch-check"></i>
                                        @else<i class="bi bi-clock"></i>
                                        @endif
                                        {{ $row->statusLabel() }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="mr-empty">
                <div class="mr-empty-visual"><i class="bi bi-inbox"></i></div>
                <h5 class="fw-semibold text-dark">No requisitions yet</h5>
                <p>Items you submit from the Requisition page will appear here with full tracking.</p>
                <a href="{{ route('Requisition') }}" class="btn-hero-link mt-2 d-inline-flex">
                    <i class="bi bi-plus-lg"></i> Create Requisition
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    if ($('#myRequestTable').length && $.fn.DataTable) {
        $('#myRequestTable').DataTable({
            order: [[9, 'desc']],
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, 'All']],
            language: { search: '', searchPlaceholder: 'Search requests…' },
            columnDefs: [{ orderable: false, targets: 0 }],
        });
    }
});
</script>
@endsection
