@php
    $pageName = 'stock';
    $subpageName = 'pending-stock';
    $lineCount = ($listrequest ?? collect())->count();
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .pu-page { padding: 0 0.5rem 2rem; }

    .pu-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #60a5fa 100%);
        border-radius: 1.25rem;
        padding: 1.75rem 2rem 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.28);
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

    .stat-card.items .stat-card-icon { background: rgba(37, 99, 235, 0.12); color: #2563eb; }
    .stat-card.lines .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }
    .stat-card.qty   .stat-card-icon { background: rgba(16, 185, 129, 0.12); color: #059669; }

    .stat-card-value { font-size: 1.65rem; font-weight: 800; line-height: 1; margin-bottom: 0.15rem; }
    .stat-card.items .stat-card-value { color: #2563eb; }
    .stat-card.lines .stat-card-value { color: #d97706; }
    .stat-card.qty   .stat-card-value { color: #059669; }
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
    #pickUpTable tbody tr:hover { background: #eff6ff !important; }

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
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
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
        background: rgba(37, 99, 235, 0.12);
        color: #1d4ed8;
        text-transform: capitalize;
    }

    .pu-note {
        margin: 1rem 1.5rem 0;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        background: #eff6ff;
        border: 1px solid rgba(37, 99, 235, 0.15);
        font-size: 0.82rem;
        color: #1e40af;
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
        background: #2563eb;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
    }

    .btn-print-slip:hover { background: #1d4ed8; color: #fff; }

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

    .pu-empty {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #64748b;
    }
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
                <i class="bi bi-truck"></i> Stock — Pick Up
            </div>
            <h2>Pick Up Slip</h2>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="req-no-badge">{{ $requisitionNo ?? '' }}</span>
                @if($invoiceNumber ?? null)
                    <span class="req-no-badge">{{ $invoiceNumber }}</span>
                @endif
            </div>
            <p>
                Items issued from <strong>{{ $fromStoreLabel ?? ($fromStore->name ?? 'store') }}</strong>
                to <strong>{{ $toStoreLabel ?? ($toStore->name ?? ($activeStore->name ?? 'your store')) }}</strong>.
                Review the lines below, then print the invoice for collection.
            </p>
            <div class="pu-meta">
                <span class="pu-meta-chip"><i class="bi bi-building"></i> From: {{ $fromStoreLabel ?? ($fromStore->name ?? '—') }}</span>
                <span class="pu-meta-chip"><i class="bi bi-geo-alt"></i> To: {{ $toStoreLabel ?? ($toStore->name ?? ($activeStore->name ?? '—')) }}</span>
                @if(optional($issuedBy)->name || optional($createdBy)->name)
                    <span class="pu-meta-chip"><i class="bi bi-person-check"></i> {{ optional($issuedBy)->name ?? optional($createdBy)->name }}</span>
                @endif
                @if($issuedAt)
                    <span class="pu-meta-chip"><i class="bi bi-calendar3"></i> {{ $issuedAt->format('M d, Y · h:i A') }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="stat-card items">
                <div class="stat-card-icon"><i class="bi bi-tags"></i></div>
                <div class="stat-card-value">{{ number_format($uniqueItems ?? 0) }}</div>
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
                <div class="stat-card-value">{{ number_format($totalQty ?? 0) }}</div>
                <p class="stat-card-label">Total Quantity</p>
            </div>
        </div>
    </div>

    <div class="pu-card mb-5">
        <div class="pu-card-head">
            <h5><i class="bi bi-clipboard-data me-1 text-primary"></i> Issued Items</h5>
            @if($invoiceNumber ?? null)
                <a href="{{ route('requisition.print', Crypt::encrypt($invoiceNumber)) }}"
                   target="_blank"
                   class="btn-print-ghost">
                    <i class="bi bi-printer"></i> Print Invoice
                </a>
            @endif
        </div>

        @if($lineCount > 0)
            <div class="pu-note">
                <i class="bi bi-info-circle me-1"></i>
                Confirm each batch and quantity before handing over stock. Print the invoice once pick-up is complete.
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
                                <td><span class="qty-badge">{{ number_format((int) $lists->qty) }}</span></td>
                                <td>
                                    <span class="status-badge">
                                        <i class="bi bi-truck"></i> {{ $lists->status ?? 'issued' }}
                                    </span>
                                </td>
                                <td>{{ optional($lists->staffname)->name ?? optional($createdBy)->name ?? '—' }}</td>
                                <td>{{ optional($lists->authorised)->name ?? optional($issuedBy)->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pu-footer">
                <div class="pu-footer-meta">
                    <i class="bi bi-info-circle me-1"></i>
                    {{ $lineCount }} line{{ $lineCount !== 1 ? 's' : '' }}
                    · {{ number_format($uniqueItems ?? 0) }} item{{ ($uniqueItems ?? 0) !== 1 ? 's' : '' }}
                    · {{ number_format($totalQty ?? 0) }} units
                </div>
                @if($invoiceNumber ?? null)
                    <a href="{{ route('requisition.print', Crypt::encrypt($invoiceNumber)) }}"
                       target="_blank"
                       class="btn-print-slip">
                        <i class="bi bi-printer"></i> Print Invoice
                    </a>
                @endif
            </div>
        @else
            <div class="pu-empty">
                <h5 class="fw-semibold text-dark">No issued lines on this slip</h5>
                <p class="mb-0">Return to the pick list and open another requisition.</p>
            </div>
        @endif
    </div>
</div>
@endsection
