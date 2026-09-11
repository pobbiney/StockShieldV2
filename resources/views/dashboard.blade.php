@php
    $pageName = 'dashboard';
    $subpageName = '';
    $initials = strtoupper(collect(explode(' ', $userName))->filter()->take(2)->map(fn ($w) => substr($w, 0, 1))->join(''));
    $todayLabel = now()->format('l, F j, Y');
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .db-page { padding: 0 0.5rem 2rem; }

    .db-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 50%, #6366f1 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.28);
    }

    .db-hero::before,
    .db-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .db-hero::before { width: 260px; height: 260px; top: -90px; right: -60px; }
    .db-hero::after  { width: 160px; height: 160px; bottom: -50px; left: 10%; }

    .db-hero-inner { position: relative; z-index: 1; }

    .db-welcome-row {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex-wrap: wrap;
    }

    .db-avatar {
        width: 72px;
        height: 72px;
        border-radius: 1.125rem;
        background: linear-gradient(135deg, rgba(255,255,255,0.25), rgba(255,255,255,0.08));
        border: 2px solid rgba(255, 255, 255, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: "SUSE", sans-serif;
        font-weight: 800;
        font-size: 1.5rem;
        flex-shrink: 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .db-hero h1 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.5rem, 3.5vw, 2rem);
        margin: 0 0 0.35rem;
        letter-spacing: -0.02em;
    }

    .db-hero-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .db-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 0.78rem;
        font-weight: 600;
    }

    .db-hero-date {
        text-align: right;
        margin-left: auto;
    }

    .db-hero-date .day {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
    }

    .db-hero-date .sub {
        font-size: 0.78rem;
        opacity: 0.75;
    }

    @media (max-width: 767.98px) {
        .db-hero-date { text-align: left; margin-left: 0; width: 100%; }
    }

    .stat-card {
        position: relative;
        border-radius: 1.125rem;
        padding: 1.35rem 1.4rem;
        height: 100%;
        background: linear-gradient(
            145deg,
            rgba(var(--stat-accent-rgb, 99, 102, 241), 0.1) 0%,
            #fff 58%
        );
        border: 1px solid rgba(var(--stat-accent-rgb, 99, 102, 241), 0.2);
        box-shadow: 0 4px 18px rgba(var(--stat-accent-rgb, 99, 102, 241), 0.1);
        animation: dbStatIn 0.5s ease both;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(
            90deg,
            rgb(var(--stat-accent-rgb, 99, 102, 241)),
            rgba(var(--stat-accent-rgb, 99, 102, 241), 0.45)
        );
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(var(--stat-accent-rgb, 99, 102, 241), 0.08);
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(var(--stat-accent-rgb, 99, 102, 241), 0.18);
        border-color: rgba(var(--stat-accent-rgb, 99, 102, 241), 0.35);
    }

    .stat-card.pending  { --stat-accent-rgb: 124, 58, 237; }
    .stat-card.reorder  { --stat-accent-rgb: 234, 88, 12; }
    .stat-card.expiry   { --stat-accent-rgb: 220, 38, 38; }
    .stat-card.stock    { --stat-accent-rgb: 22, 163, 74; }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }
    .stat-card:nth-child(4) { animation-delay: 0.2s; }

    @keyframes dbStatIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card-top {
        position: relative;
        z-index: 1;
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
        background: linear-gradient(
            135deg,
            rgba(var(--stat-accent-rgb, 99, 102, 241), 0.22),
            rgba(var(--stat-accent-rgb, 99, 102, 241), 0.08)
        );
        color: rgb(var(--stat-accent-rgb, 99, 102, 241));
        border: 1px solid rgba(var(--stat-accent-rgb, 99, 102, 241), 0.2);
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .stat-card:hover .stat-card-icon {
        background: rgb(var(--stat-accent-rgb, 99, 102, 241));
        color: #fff;
        transform: scale(1.06);
    }

    .stat-card-value {
        position: relative;
        z-index: 1;
        font-size: clamp(1.75rem, 4vw, 2.25rem);
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.25rem;
        color: rgb(var(--stat-accent-rgb, 99, 102, 241));
    }

    .stat-card-label {
        position: relative;
        z-index: 1;
        font-size: 0.82rem;
        font-weight: 600;
        color: rgb(var(--stat-accent-rgb, 99, 102, 241));
        opacity: 0.85;
        margin: 0;
    }

    .stat-card-link {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.72rem;
        font-weight: 600;
        color: rgb(var(--stat-accent-rgb, 99, 102, 241));
        text-decoration: none;
        margin-top: 0.65rem;
        opacity: 0.9;
    }

    .stat-card-link:hover {
        color: rgb(var(--stat-accent-rgb, 99, 102, 241));
        opacity: 1;
    }

    .stat-card-link.text-muted { color: #64748b !important; opacity: 1; }
    .stat-card-link.text-danger { color: #dc2626 !important; opacity: 1; }

    .db-panel {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: #fff;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        animation: dbStatIn 0.5s ease 0.25s both;
        height: 100%;
    }

    .db-panel-head {
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .db-panel-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
    }

    .db-panel-head small { color: #64748b; display: block; margin-top: 0.15rem; }

    .count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.28rem 0.7rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .count-badge.warn  { background: rgba(234, 88, 12, 0.12); color: #c2410c; }
    .count-badge.danger { background: rgba(220, 38, 38, 0.12); color: #b91c1c; }
    .count-badge.info   { background: rgba(37, 99, 235, 0.12); color: #1d4ed8; }

    .req-table { width: 100%; border-collapse: collapse; }

    .req-table th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        padding: 0.75rem 1.35rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .req-table td {
        padding: 0.85rem 1.35rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .req-table tbody tr:hover { background: #fffbeb; }

    .req-no-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(217, 119, 6, 0.1);
        color: #b45309;
        font-size: 0.78rem;
        font-weight: 700;
        font-family: monospace;
    }

    .store-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.55rem;
        border-radius: 2rem;
        background: rgba(99, 102, 241, 0.1);
        color: #4f46e5;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .btn-issue-sm {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
        background: #d97706;
        color: #fff;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-issue-sm:hover { background: #b45309; color: #fff; }

    .hod-wait-banner {
        margin: 0 1.35rem 1rem;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        background: rgba(37, 99, 235, 0.08);
        border: 1px solid rgba(37, 99, 235, 0.15);
        font-size: 0.82rem;
        color: #1e40af;
    }

    .alert-list { list-style: none; padding: 0; margin: 0; }

    .alert-item {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        padding: 1rem 1.35rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }

    .alert-item:last-child { border-bottom: none; }
    .alert-item:hover { background: #fafafa; }

    .alert-icon {
        width: 40px;
        height: 40px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .alert-icon.reorder { background: rgba(234, 88, 12, 0.12); color: #ea580c; }
    .alert-icon.expiry  { background: rgba(220, 38, 38, 0.12); color: #dc2626; }
    .alert-icon.at-level { background: rgba(217, 119, 6, 0.12); color: #d97706; }

    .alert-body h6 {
        font-size: 0.875rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.2rem;
    }

    .alert-body p {
        margin: 0;
        font-size: 0.78rem;
        color: #64748b;
        line-height: 1.45;
    }

    .alert-tag {
        display: inline-block;
        margin-top: 0.35rem;
        padding: 0.15rem 0.5rem;
        border-radius: 0.35rem;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .alert-tag.critical { background: #fef2f2; color: #b91c1c; }
    .alert-tag.warning  { background: #fff7ed; color: #c2410c; }

    .db-empty {
        padding: 2.5rem 1.5rem;
        text-align: center;
        color: #64748b;
    }

    .db-empty i {
        font-size: 2rem;
        color: #22c55e;
        margin-bottom: 0.75rem;
        display: block;
    }

    .quick-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        padding: 1.15rem 1.35rem 1.35rem;
    }

    .quick-link {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 1rem;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
    }

    .quick-link:hover {
        border-color: #93c5fd;
        background: #eff6ff;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.12);
        color: inherit;
    }

    .quick-link-icon {
        width: 38px;
        height: 38px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
    }

    .quick-link:nth-child(1) .quick-link-icon { background: rgba(37,99,235,0.12); color: #2563eb; }
    .quick-link:nth-child(2) .quick-link-icon { background: rgba(124,58,237,0.12); color: #7c3aed; }
    .quick-link:nth-child(3) .quick-link-icon { background: rgba(234,88,12,0.12); color: #ea580c; }
    .quick-link:nth-child(4) .quick-link-icon { background: rgba(22,163,74,0.12); color: #16a34a; }
    .quick-link:nth-child(5) .quick-link-icon { background: rgba(8,145,178,0.12); color: #0891b2; }
    .quick-link:nth-child(6) .quick-link-icon { background: rgba(217,119,6,0.12); color: #d97706; }

    .quick-link span {
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
    }

    .quick-link small {
        font-size: 0.68rem;
        color: #94a3b8;
        line-height: 1.3;
    }

    .expiry-scroll {
        max-height: 420px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')

<div class="container-fluid db-page px-3 px-lg-4 mt-3">

    <div class="db-hero">
        <div class="db-hero-inner">
            <div class="db-welcome-row">
                <div class="db-avatar">{{ $initials ?: 'SS' }}</div>
                <div>
                    <h1>Welcome back, {{ $userName }}</h1>
                    <p class="mb-0 opacity-75">Here’s what’s happening across your inventory today.</p>
                    <div class="db-hero-meta">
                        <span class="db-meta-pill"><i class="bi bi-person-badge"></i> {{ $userRole }}</span>
                        @if($activeStore)
                            <span class="db-meta-pill"><i class="bi bi-shop"></i> {{ $activeStore->name }}</span>
                        @endif
                                        </div>
                                    </div>
                <div class="db-hero-date">
                    <div class="day">{{ now()->format('M j') }}</div>
                    <div class="sub">{{ $todayLabel }}</div>
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
                <div class="stat-card-value">{{ number_format($pendingStockCount) }}</div>
                <p class="stat-card-label">Pending Stock Entries</p>
                <a href="{{ route('pendingStock') }}" class="stat-card-link">View pending <i class="bi bi-arrow-right"></i></a>
                                                                </div>
                                                            </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card reorder">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-exclamation-triangle"></i></div>
                                                        </div>
                <div class="stat-card-value">{{ number_format($reorderItemsCount) }}</div>
                <p class="stat-card-label">Re-order Alerts</p>
                @if($alertStoreName ?? null)
                    <span class="stat-card-link text-muted" style="cursor:default;">{{ $alertStoreName }}</span>
                @else
                    <a href="{{ route('reOrder') }}" class="stat-card-link">Manage levels <i class="bi bi-arrow-right"></i></a>
                @endif
                                                        </div>
                                                    </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card expiry">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-calendar-x"></i></div>
                </div>
                <div class="stat-card-value">{{ number_format($expiryAlertCount ?? $count ?? 0) }}</div>
                <p class="stat-card-label">Expiry Alerts</p>
                @if(($expiredCount ?? 0) > 0)
                    <span class="stat-card-link text-danger" style="cursor:default;">{{ number_format($expiredCount) }} expired</span>
                @else
                    <span class="stat-card-link text-muted" style="cursor:default;">Expiring within 3 months</span>
                @endif
                                                </div>
                                            </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stock">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                                        </div>
                <div class="stat-card-value">{{ number_format($totalStockQty) }}</div>
                <p class="stat-card-label">Total Stock Units</p>
                <span class="stat-card-link text-muted" style="cursor:default;">{{ number_format($approvedStockLines) }} batch lines</span>
                                    </div>
                                </div>
                            </div>

    @if($isCentralStore)
    @if(($canApproveRequisitions ?? false) && ($awaitingHodCount ?? 0) > 0)
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="db-panel">
                <div class="db-panel-head">
                    <div>
                        <h5><i class="bi bi-patch-check me-2 text-primary"></i>Requisitions Awaiting HOD Approval</h5>
                        <small>Approve requisitions before the store manager can issue stock</small>
                    </div>
                    <span class="count-badge info">{{ $awaitingHodCount }}</span>
                </div>
                <div class="table-responsive">
                    <table class="req-table">
                        <thead>
                            <tr>
                                <th>Requisition No</th>
                                <th>Requesting Store</th>
                                <th>Items</th>
                                <th>Requested Qty</th>
                                <th>Submitted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($awaitingHodRequisitions->take(10) as $req)
                                <tr>
                                    <td><span class="req-no-badge">{{ $req->requisition_no }}</span></td>
                                    <td>
                                        <span class="store-chip">
                                            <i class="bi bi-shop"></i>
                                            {{ $req->requesting_store->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td>{{ $req->line_count }}</td>
                                    <td><strong>{{ number_format($req->total_qty) }}</strong></td>
                                    <td>
                                        {{ $req->submitted_at?->format('M d, Y') }}
                                        <div class="text-muted small">{{ $req->submitted_at?->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('ApproveRequest') }}" class="btn-issue-sm" style="background:#2563eb;">
                                            <i class="bi bi-check2-square"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($canIssueStock ?? false)
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="db-panel">
                <div class="db-panel-head">
                    <div>
                        <h5><i class="bi bi-truck me-2 text-warning"></i>Ready to Issue</h5>
                        <small>HOD-approved requisitions waiting for store manager to issue from {{ $activeStore->name ?? 'your central store' }}</small>
                    </div>
                    @if(($pendingRequisitionCount ?? 0) > 0)
                        <span class="count-badge warn">{{ $pendingRequisitionCount }} ready</span>
                    @endif
                </div>

                @if(($canIssueStock ?? false) && ($awaitingHodCount ?? 0) > 0 && !($canApproveRequisitions ?? false))
                    <div class="hod-wait-banner">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>{{ $awaitingHodCount }}</strong> requisition{{ $awaitingHodCount !== 1 ? 's' : '' }}
                        still awaiting HOD approval before you can issue.
                    </div>
                @endif

                @if($incomingRequisitions->isNotEmpty())
                    <div class="table-responsive">
                        <table class="req-table">
                            <thead>
                                <tr>
                                    <th>Requisition No</th>
                                    <th>Requesting Store</th>
                                    <th>Items</th>
                                    <th>Approved Qty</th>
                                    <th>Submitted</th>
                                    <th>Requested By</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incomingRequisitions->take(10) as $req)
                                    <tr>
                                        <td><span class="req-no-badge">{{ $req->requisition_no }}</span></td>
                                        <td>
                                            <span class="store-chip">
                                                <i class="bi bi-shop"></i>
                                                {{ $req->requesting_store->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td>{{ $req->line_count }}</td>
                                        <td><strong>{{ number_format($req->total_qty) }}</strong></td>
                                        <td>
                                            {{ $req->submitted_at?->format('M d, Y') }}
                                            <div class="text-muted small">{{ $req->submitted_at?->format('h:i A') }}</div>
                                        </td>
                                        <td>{{ $req->requested_by->name ?? '—' }}</td>
                                        <td>
                                            <a href="{{ route('viewStoreRequest', Crypt::encrypt($req->requisition_no)) }}"
                                               class="btn-issue-sm">
                                                <i class="bi bi-box-arrow-right"></i> Issue
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($pendingRequisitionCount > 10)
                        <div class="p-3 border-top text-center">
                            <a href="{{ route('IssueItem') }}" class="stat-card-link">
                                View all {{ $pendingRequisitionCount }} requisitions <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="db-empty">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">No approved requisitions waiting to be issued.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if(($canApproveIssues ?? false))
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="db-panel">
                <div class="db-panel-head">
                    <div>
                        <h5><i class="bi bi-shield-check me-2 text-purple"></i>Issue Approvals</h5>
                        <small>Approve issues only after the store manager has submitted them</small>
                    </div>
                    @if(($pendingIssueApprovalCount ?? 0) > 0)
                        <span class="count-badge warn">{{ $pendingIssueApprovalCount }}</span>
                    @endif
                </div>

                @if(($pendingIssueApprovals ?? collect())->isNotEmpty())
                    <div class="table-responsive">
                        <table class="req-table">
                            <thead>
                                <tr>
                                    <th>Requisition No</th>
                                    <th>Requesting Store</th>
                                    <th>Items</th>
                                    <th>Issued Qty</th>
                                    <th>Submitted By</th>
                                    <th>Submitted</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingIssueApprovals->take(10) as $req)
                                    <tr>
                                        <td><span class="req-no-badge">{{ $req->requisition_no }}</span></td>
                                        <td>
                                            <span class="store-chip">
                                                <i class="bi bi-shop"></i>
                                                {{ $req->requesting_store->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td>{{ $req->line_count }}</td>
                                        <td><strong>{{ number_format($req->total_qty) }}</strong></td>
                                        <td>{{ $req->issued_by->name ?? '—' }}</td>
                                        <td>
                                            {{ $req->submitted_at?->format('M d, Y') }}
                                            <div class="text-muted small">{{ $req->submitted_at?->format('h:i A') }}</div>
                                        </td>
                                        <td>
                                            <a href="{{ route('viewIssues', Crypt::encrypt($req->requisition_no)) }}"
                                               class="btn-issue-sm" style="background:#7c3aed;">
                                                <i class="bi bi-check2-square"></i> Approve
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(($pendingIssueApprovalCount ?? 0) > 10)
                        <div class="p-3 border-top text-center">
                            <a href="{{ route('IssueApproval') }}" class="stat-card-link">
                                View all {{ $pendingIssueApprovalCount }} pending issues <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="db-empty">
                        <i class="bi bi-check-circle"></i>
                        <p class="mb-0">No issues awaiting your approval. Items appear here after the store manager issues stock.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    @endif

    @if($isSatelliteStore ?? false)
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="db-panel">
                <div class="db-panel-head">
                    <div>
                        <h5><i class="bi bi-box-arrow-in-down me-2 text-success"></i>Incoming Stock to Accept</h5>
                        <small>Items issued from central stores awaiting receipt at {{ $activeStore->name ?? 'your store' }}</small>
                                            </div>
                    @if(($pendingReceiptCount ?? 0) > 0)
                        <span class="count-badge warn">{{ $pendingReceiptCount }} line(s)</span>
                    @endif
                                                    </div>
                                                     
                @if(($incomingTransfers ?? collect())->isNotEmpty())
                    <div class="table-responsive">
                        <table class="req-table">
                            <thead>
                                <tr>
                                    <th>Requisition No</th>
                                    <th>From (Central)</th>
                                    <th>Lines</th>
                                    <th>Qty</th>
                                    <th>Issued</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incomingTransfers->take(10) as $transfer)
                                    <tr>
                                        <td><span class="req-no-badge">{{ $transfer->requisition_no }}</span></td>
                                        <td>
                                            <span class="store-chip">
                                                <i class="bi bi-building"></i>
                                                {{ $transfer->central_store->name ?? 'Central' }}
                                            </span>
                                        </td>
                                        <td>{{ $transfer->line_count }}</td>
                                        <td><strong>{{ number_format($transfer->total_qty) }}</strong></td>
                                        <td>
                                            {{ $transfer->issued_at?->format('M d, Y') }}
                                            <div class="text-muted small">{{ $transfer->issued_at?->format('h:i A') }}</div>
                                        </td>
                                        <td>
                                            <a href="{{ route('viewReceiveStock', Crypt::encrypt($transfer->requisition_no)) }}"
                                               class="btn-issue-sm" style="background:#059669;">
                                                <i class="bi bi-check2-square"></i> Accept
                                            </a>
                                        </td>
                                    </tr>
                                                        @endforeach
                            </tbody>
                        </table>
                                                    </div>
                    <div class="p-3 border-top text-center">
                        <a href="{{ route('ReceiveStock') }}" class="stat-card-link">
                            Open Receive Stock
                            @if(($pendingReceiptQty ?? 0) > 0)
                                ({{ number_format($pendingReceiptQty) }} units pending)
                            @endif
                            <i class="bi bi-arrow-right"></i>
                        </a>
                                                        </div>
                @else
                    <div class="db-empty">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">No issued stock waiting to be accepted.</p>
                                                    </div>
                @endif
                                                </div>
                                            </div>
                                        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="db-panel">
                        <div class="db-panel-head">
                            <div>
                                <h5><i class="bi bi-arrow-repeat me-2 text-warning"></i>Re-order Alerts</h5>
                                <small>
                                    Items at or below reorder level
                                    @if($alertStoreName ?? null)
                                        &middot; {{ $alertStoreName }}
                                    @endif
                                </small>
                                    </div>
                            @if($reorderItemsCount > 0)
                                <span class="count-badge warn">{{ $reorderItemsCount }}</span>
                            @endif
                                                            </div>
                        @if($reorderItems->isNotEmpty())
                            <ul class="alert-list">
                                @foreach($reorderItems->take(8) as $item)
                                    @php
                                        $isCritical = $item->total_qty < $item->reorder_level;
                                    @endphp
                                    <li class="alert-item">
                                        <div class="alert-icon {{ $isCritical ? 'reorder' : 'at-level' }}">
                                            <i class="bi bi-{{ $isCritical ? 'arrow-down-circle' : 'dash-circle' }}"></i>
                                        </div>
                                        <div class="alert-body">
                                            <h6>{{ $item->name }}</h6>
                                            <p>
                                                Stock: <strong>{{ number_format($item->total_qty) }}</strong>
                                                &middot; Reorder level: {{ number_format($item->reorder_level) }}
                                            </p>
                                            <span class="alert-tag {{ $isCritical ? 'critical' : 'warning' }}">
                                                {{ $isCritical ? 'Below level' : 'At level' }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                                                            </ul>
                        @else
                            <div class="db-empty">
                                <i class="bi bi-check-circle"></i>
                                <p class="mb-0">All items are above reorder levels.</p>
                                                        </div>
                        @endif
                                    </div>
                                </div>

                <div class="col-md-6">
                    <div class="db-panel">
                        <div class="db-panel-head">
                            <div>
                                <h5><i class="bi bi-calendar-event me-2 text-danger"></i>Expiry Watch</h5>
                                <small>
                                    Expiring soon and expired stock
                                    @if($alertStoreName ?? null)
                                        &middot; {{ $alertStoreName }}
                                    @endif
                                </small>
                                    </div>
                            @if(($expiryAlertCount ?? $count ?? 0) > 0)
                                <span class="count-badge danger">{{ $expiryAlertCount ?? $count }}</span>
                            @endif
                                                    </div>
                        @if(($expiringItems ?? $notifications ?? collect())->isNotEmpty() || ($expiredItems ?? collect())->isNotEmpty())
                            <div class="expiry-scroll">
                                <ul class="alert-list">
                                    @foreach(($expiredItems ?? collect())->take(5) as $note)
                                        <li class="alert-item">
                                            <div class="alert-icon reorder">
                                                <i class="bi bi-calendar-x"></i>
                                            </div>
                                            <div class="alert-body">
                                                <h6>{{ $note->name }}</h6>
                                                <p>
                                                    Qty {{ number_format($note->qty) }}
                                                    &middot; Expired {{ \Carbon\Carbon::parse($note->expiry_date)->format('M d, Y') }}
                                                </p>
                                                <span class="alert-tag critical">
                                                    Expired {{ abs((int) $note->days_left) }} day{{ abs((int) $note->days_left) !== 1 ? 's' : '' }} ago
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                    @foreach(($expiringItems ?? $notifications ?? collect())->take(10) as $note)
                                        <li class="alert-item">
                                            <div class="alert-icon expiry">
                                                <i class="bi bi-clock-history"></i>
                                            </div>
                                            <div class="alert-body">
                                                <h6>{{ $note->name }}</h6>
                                                <p>
                                                    Qty {{ number_format($note->qty) }}
                                                    &middot; Expires {{ \Carbon\Carbon::parse($note->expiry_date)->format('M d, Y') }}
                                                </p>
                                                <span class="alert-tag {{ $note->days_left <= 30 ? 'critical' : 'warning' }}">
                                                    {{ $note->days_left }} day{{ $note->days_left !== 1 ? 's' : '' }} left
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                            </div>
                        @else
                            <div class="db-empty">
                                <i class="bi bi-shield-check"></i>
                                <p class="mb-0">No expiring or expired items for your store.</p>
                                        </div>
                        @endif
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>

        <div class="col-lg-4">
            <div class="db-panel">
                <div class="db-panel-head">
                    <div>
                        <h5><i class="bi bi-lightning-charge me-2 text-primary"></i>Quick Actions</h5>
                        <small>Jump to common tasks</small>
                                                            </div>
                                                        </div>
                <div class="quick-grid">
                    @if($canStockEntry ?? false)
                        <a href="{{ route('stockEntry') }}" class="quick-link">
                            <div class="quick-link-icon"><i class="bi bi-box-arrow-in-down"></i></div>
                            <span>Stock Entry</span>
                            <small>Record incoming stock</small>
                        </a>
                    @endif
                    @if($canPendingStock ?? false)
                        <a href="{{ route('pendingStock') }}" class="quick-link">
                            <div class="quick-link-icon"><i class="bi bi-hourglass"></i></div>
                            <span>Pending Stock</span>
                            <small>Awaiting approval</small>
                        </a>
                    @endif
                    @if($canStockApprovalMenu ?? false)
                        <a href="{{ route('stockApproval') }}" class="quick-link">
                            <div class="quick-link-icon"><i class="bi bi-check2-square"></i></div>
                            <span>Stock Approval</span>
                            <small>Review submissions</small>
                        </a>
                    @endif
                    @if($canApproveIssues ?? false)
                        <a href="{{ route('IssueApproval') }}" class="quick-link">
                            <div class="quick-link-icon"><i class="bi bi-shield-check"></i></div>
                            <span>Issue Approval</span>
                            <small>HOD review of issued stock</small>
                        </a>
                    @endif
                    @if($canIssueStock ?? false)
                        <a href="{{ route('IssueItem') }}" class="quick-link">
                            <div class="quick-link-icon"><i class="bi bi-box-arrow-right"></i></div>
                            <span>Issue Items</span>
                            <small>Issue from inventory</small>
                        </a>
                    @endif
                    @if($canManageItems ?? false)
                        <a href="{{ route('Item') }}" class="quick-link">
                            <div class="quick-link-icon"><i class="bi bi-tags"></i></div>
                            <span>Items</span>
                            <small>Manage product catalog</small>
                        </a>
                    @endif
                    @if($canReorder ?? false)
                        <a href="{{ route('reOrder') }}" class="quick-link">
                            <div class="quick-link-icon"><i class="bi bi-sliders"></i></div>
                            <span>Reorder Levels</span>
                            <small>Set minimum stock</small>
                        </a>
                    @endif
                    @if(!($canStockEntry ?? false) && !($canPendingStock ?? false) && !($canStockApprovalMenu ?? false) && !($canApproveIssues ?? false) && !($canIssueStock ?? false) && !($canManageItems ?? false) && !($canReorder ?? false))
                        <div class="db-empty py-4">
                            <i class="bi bi-lock"></i>
                            <p class="mb-0">No quick actions available for your role.</p>
                        </div>
                    @endif
                </div>
                                                    </div>
                                                </div>
                                            </div>
                             
                            </div>

@endsection

@section('scripts')
@endsection
