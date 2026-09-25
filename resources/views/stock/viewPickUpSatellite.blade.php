@php
    $pageName = 'stock';
    $subpageName = 'pending-stock';
    $isWardIssue = ($pickType ?? '') === 'ward_issue';
    $isSatelliteIssue = ($pickType ?? '') === 'satellite_issue';
    $toStore = $toStore ?? null;
    $centralStore = $centralStore ?? null;
    $lineCount = ($listrequest ?? collect())->count();
    $totalQty = $isWardIssue
        ? (int) $listrequest->sum('qty_issued')
        : (int) $listrequest->sum('qty');
    $uniqueItems = $listrequest->pluck('item_id')->unique()->count();
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .pu-page { padding: 0 0.5rem 2rem; }

    .pu-hero {
        background: linear-gradient(135deg, #92400e 0%, #d97706 55%, #fbbf24 100%);
        border-radius: 1.25rem;
        padding: 1.75rem 2rem 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(217, 119, 6, 0.28);
    }

    .pu-hero::before,
    .pu-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .pu-hero::before { width: 200px; height: 200px; top: -60px; right: -40px; }
    .pu-hero::after  { width: 120px; height: 120px; bottom: -36px; left: 8%; }

    .pu-hero-inner { position: relative; z-index: 1; }

    .pu-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.85rem;
        border-radius: 2rem;
        background: rgba(255, 255, 255, 0.15);
        font-size: 0.78rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .pu-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.3rem, 2.5vw, 1.7rem);
        margin-bottom: 0.35rem;
    }

    .pu-hero p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 640px;
    }

    .btn-back-link {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        font-size: 0.85rem;
    }

    .btn-back-link:hover { color: #fff; }

    .req-no-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
        background: rgba(255, 255, 255, 0.2);
        font-family: monospace;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .pu-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        margin-top: 1rem;
    }

    .pu-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 0.8rem;
    }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.25rem 1.4rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .stat-card-icon {
        width: 42px; height: 42px; border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; margin-bottom: 0.75rem;
    }

    .stat-card.items .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }
    .stat-card.lines .stat-card-icon { background: rgba(37, 99, 235, 0.12); color: #2563eb; }
    .stat-card.qty   .stat-card-icon { background: rgba(180, 83, 9, 0.12); color: #b45309; }

    .stat-card-value { font-size: 1.65rem; font-weight: 800; line-height: 1; margin-bottom: 0.15rem; }
    .stat-card.items .stat-card-value { color: #d97706; }
    .stat-card.lines .stat-card-value { color: #2563eb; }
    .stat-card.qty   .stat-card-value { color: #b45309; }
    .stat-card-label { font-size: 0.8rem; color: #64748b; margin: 0; }

    .pu-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        background: #fff;
    }

    .pu-card-head {
        padding: 1.15rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .pu-card-head h5 {
        margin: 0;
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
    }

    #pickUpTable thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        border: none;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.85rem 1rem;
        background: #f8fafc;
        white-space: nowrap;
    }

    #pickUpTable tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #pickUpTable tbody tr:nth-child(even) { background: #fafafa; }
    #pickUpTable tbody tr:hover { background: #fff7ed !important; }

    .batch-code {
        display: inline-block;
        padding: 0.15rem 0.45rem;
        border-radius: 0.35rem;
        background: #f1f5f9;
        font-family: monospace;
        font-size: 0.78rem;
        color: #475569;
    }

    .qty-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(217, 119, 6, 0.1);
        color: #b45309;
        font-weight: 700;
        font-size: 0.82rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-badge.ward { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .status-badge.central { background: rgba(37, 99, 235, 0.12); color: #1d4ed8; }

    .pu-note {
        margin: 1rem 1.5rem 0;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        background: #fff7ed;
        border: 1px solid rgba(217, 119, 6, 0.15);
        font-size: 0.82rem;
        color: #92400e;
    }

    .pu-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f1f5f9;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .pu-footer-meta { font-size: 0.82rem; color: #64748b; }

    .btn-print-slip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.6rem 1.15rem;
        border-radius: 0.625rem;
        background: #0f172a;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
    }

    .btn-print-slip:hover { color: #fff; opacity: 0.9; }

    .btn-receive-link {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.6rem 1.15rem;
        border-radius: 0.625rem;
        background: #059669;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-receive-link:hover { color: #fff; opacity: 0.92; }

    .btn-print-ghost {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 0.95rem;
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #475569;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-print-ghost:hover { background: #f8fafc; color: #0f172a; }
</style>
@endsection

@section('content')
<div class="container-fluid pu-page px-3 px-lg-4 mt-3">
    <div class="pu-hero">
        <div class="pu-hero-inner">
            <a href="{{ route('PickList') }}" class="btn-back-link d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Back to Pick List
            </a>
            <div class="pu-hero-badge">
                <i class="bi bi-truck"></i>
                Satellite Store —
                @if($isWardIssue)
                    Ward Issue
                @elseif($isSatelliteIssue)
                    Issued from Store
                @else
                    Central Transfer
                @endif
            </div>
            <h2>Pick Up Slip</h2>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="req-no-badge">{{ $requisitionNo }}</span>
                @if(!$isWardIssue && ($invoiceNumber ?? null))
                    <span class="req-no-badge">{{ $invoiceNumber }}</span>
                @endif
            </div>
            @if($isWardIssue)
                <p>
                    Items issued from <strong>{{ $activeStore->name ?? 'satellite store' }}</strong>
                    to <strong>{{ $wardLabel ?? strtolower($destinationLabel ?? 'ward') }}</strong>.
                </p>
            @elseif($isSatelliteIssue)
                <p>
                    Items issued from <strong>{{ $activeStore->name ?? ($centralStore->name ?? 'satellite store') }}</strong>
                    to <strong>{{ $toStore->name ?? 'store' }}</strong>.
                </p>
            @else
                <p>
                    Items issued to <strong>{{ $activeStore->name ?? 'your store' }}</strong>
                    from <strong>{{ $centralStore->name ?? 'central store' }}</strong>.
                </p>
            @endif
            <div class="pu-meta">
                @if($isWardIssue)
                    <span class="pu-meta-chip"><i class="bi bi-hash"></i> Issue: {{ $issueNo }}</span>
                    <span class="pu-meta-chip"><i class="bi bi-hospital"></i> {{ $destinationLabel ?? 'Ward' }}: {{ $wardLabel ?? '—' }}</span>
                @elseif($isSatelliteIssue)
                    <span class="pu-meta-chip"><i class="bi bi-building"></i> From: {{ $activeStore->name ?? ($centralStore->name ?? 'Store') }}</span>
                    <span class="pu-meta-chip"><i class="bi bi-geo-alt"></i> To: {{ $toStore->name ?? '—' }}</span>
                    @if($invoiceNumber ?? null)
                        <span class="pu-meta-chip"><i class="bi bi-receipt"></i> Invoice: {{ $invoiceNumber }}</span>
                    @endif
                @else
                    <span class="pu-meta-chip"><i class="bi bi-building"></i> From: {{ $centralStore->name ?? 'Central Store' }}</span>
                    @if($invoiceNumber ?? null)
                        <span class="pu-meta-chip"><i class="bi bi-receipt"></i> Invoice: {{ $invoiceNumber }}</span>
                    @endif
                @endif
                @if(optional($issuedBy)->name)
                    <span class="pu-meta-chip"><i class="bi bi-person-check"></i> {{ $issuedBy->name }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="stat-card items">
                <div class="stat-card-icon"><i class="bi bi-tags"></i></div>
                <div class="stat-card-value">{{ number_format($uniqueItems) }}</div>
                <p class="stat-card-label">Unique Items</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card lines">
                <div class="stat-card-icon"><i class="bi bi-list-check"></i></div>
                <div class="stat-card-value">{{ number_format($lineCount) }}</div>
                <p class="stat-card-label">Line Items</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card qty">
                <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-card-value">{{ number_format($totalQty) }}</div>
                <p class="stat-card-label">Total Quantity</p>
            </div>
        </div>
    </div>

    <div class="pu-card mb-5">
        <div class="pu-card-head">
            <h5><i class="bi bi-clipboard-data me-1 text-warning"></i> Issued Items</h5>
            <div class="d-flex flex-wrap gap-2">
                @if($isWardIssue && $issueNo)
                    <a href="{{ route('satellite-issue.print', Crypt::encrypt($issueNo)) }}"
                       target="_blank"
                       class="btn-print-ghost">
                        <i class="bi bi-printer"></i> Print Issue Slip
                    </a>
                @elseif(!$isWardIssue && $invoiceNumber)
                    <a href="{{ route('requisition.print', Crypt::encrypt($invoiceNumber)) }}"
                       target="_blank"
                       class="btn-print-ghost">
                        <i class="bi bi-printer"></i> Print Invoice
                    </a>
                    @if(!$isSatelliteIssue)
                        <a href="{{ route('viewReceiveStock', Crypt::encrypt($requisitionNo)) }}"
                           class="btn-receive-link">
                            <i class="bi bi-check2-square"></i> Receive Stock
                        </a>
                    @endif
                @endif
            </div>
        </div>

        <div class="pu-note">
            <i class="bi bi-info-circle me-1"></i>
            @if($isWardIssue)
                Confirm each line before handing stock to the {{ strtolower($destinationLabel ?? 'ward') }}. Print the issue slip for the collecting staff.
            @elseif($isSatelliteIssue)
                Confirm each line before handing stock to {{ $toStore->name ?? 'the receiving store' }}. Print the invoice for collection.
            @else
                Review the transfer, print the invoice, then accept the stock into {{ $activeStore->name ?? 'your store' }} inventory.
            @endif
        </div>

        <div class="table-responsive">
            <table class="table mb-0 w-100" id="pickUpTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>UoM</th>
                        <th>Batch</th>
                        @if($isWardIssue)
                            <th>{{ $destinationLabel ?? 'Ward' }}</th>
                        @endif
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Requested By</th>
                        <th>Issued By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listrequest as $lists)
                        <tr>
                            <td class="text-secondary">{{ $loop->iteration }}</td>
                            <td><code>{{ optional($lists->itemcode)->item_code ?? '—' }}</code></td>
                            <td class="fw-semibold">{{ optional($lists->itemname)->name ?? '—' }}</td>
                            <td>{{ optional($lists->unitname)->name ?? optional(optional($lists->itemname)->unitname)->name ?? '—' }}</td>
                            <td><span class="batch-code">{{ $lists->batch_number ?: '—' }}</span></td>
                            @if($isWardIssue)
                                <td>{{ $lists->destinationLabel() }}</td>
                                <td><span class="qty-badge">{{ number_format((int) $lists->qty_issued) }}</span></td>
                                <td>
                                    <span class="status-badge ward">
                                        <i class="bi bi-check-circle"></i> {{ $lists->fulfillmentLabel() }}
                                    </span>
                                </td>
                                <td>{{ optional($lists->staffname)->name ?? '—' }}</td>
                                <td>{{ optional($lists->issuedByUser)->name ?? optional($issuedBy)->name ?? '—' }}</td>
                            @else
                                <td><span class="qty-badge">{{ number_format((int) $lists->qty) }}</span></td>
                                <td>
                                    <span class="status-badge central">
                                        <i class="bi bi-truck"></i> {{ $lists->status }}
                                    </span>
                                </td>
                                <td>{{ optional($lists->staffname)->name ?? '—' }}</td>
                                <td>{{ optional($lists->authorised)->name ?? optional($issuedBy)->name ?? '—' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pu-footer">
            <div class="pu-footer-meta">
                <i class="bi bi-info-circle me-1"></i>
                {{ $lineCount }} line{{ $lineCount !== 1 ? 's' : '' }}
                · {{ number_format($uniqueItems) }} item{{ $uniqueItems !== 1 ? 's' : '' }}
                · {{ number_format($totalQty) }} units
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($isWardIssue && $issueNo)
                    <a href="{{ route('satellite-issue.print', Crypt::encrypt($issueNo)) }}"
                       target="_blank"
                       class="btn-print-slip">
                        <i class="bi bi-printer"></i> Print Issue Slip
                    </a>
                @elseif(!$isWardIssue && $invoiceNumber)
                    <a href="{{ route('requisition.print', Crypt::encrypt($invoiceNumber)) }}"
                       target="_blank"
                       class="btn-print-slip">
                        <i class="bi bi-printer"></i> Print Invoice
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
