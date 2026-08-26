@php
    $pageName = 'stock';
    $subpageName = 'pending-stock';
    $isWardIssue = ($pickType ?? '') === 'ward_issue';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .pu-page { padding: 0 0.5rem 2rem; }

    .pu-hero {
        background: linear-gradient(135deg, #92400e 0%, #d97706 55%, #fbbf24 100%);
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        color: #fff;
        box-shadow: 0 8px 32px rgba(217, 119, 6, 0.22);
    }

    .pu-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.25rem, 2.5vw, 1.65rem);
        margin-bottom: 0.35rem;
    }

    .pu-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
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

    .pu-card-head h5 { margin: 0; font-weight: 700; }

    #pickUpTable thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
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

    .btn-back-link {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        font-size: 0.85rem;
    }

    .btn-back-link:hover { color: #fff; }

    .btn-print-slip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1.1rem;
        border-radius: 0.5rem;
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
        padding: 0.55rem 1.1rem;
        border-radius: 0.5rem;
        background: #059669;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-receive-link:hover { color: #fff; opacity: 0.92; }
</style>
@endsection

@section('content')
<div class="container-fluid pu-page mt-3">
    <div class="pu-hero">
        <a href="{{ route('PickList') }}" class="btn-back-link d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Back to Pick List
        </a>
        <h2>Pick Up List — {{ $requisitionNo }}</h2>
        @if($isWardIssue)
            <p class="mb-0 opacity-90">
                Items issued from {{ $activeStore->name ?? 'satellite store' }} to
                <strong>{{ $wardLabel ?? strtolower($destinationLabel ?? 'ward') }}</strong>.
            </p>
        @else
            <p class="mb-0 opacity-90">
                Items issued to {{ $activeStore->name ?? 'your store' }} from {{ $centralStore->name ?? 'central store' }}.
            </p>
        @endif
        <div class="pu-meta">
            @if($isWardIssue)
                <span class="pu-meta-chip"><i class="bi bi-hash"></i> Issue: {{ $issueNo }}</span>
                <span class="pu-meta-chip"><i class="bi bi-hospital"></i> {{ $destinationLabel ?? 'Ward' }}: {{ $wardLabel ?? '—' }}</span>
            @else
                <span class="pu-meta-chip"><i class="bi bi-receipt"></i> Invoice: {{ $invoiceNumber ?? '—' }}</span>
            @endif
            <span class="pu-meta-chip"><i class="bi bi-list-check"></i> {{ $listrequest->count() }} line(s)</span>
            <span class="pu-meta-chip">
                <i class="bi bi-box-seam"></i>
                {{ number_format($isWardIssue ? $listrequest->sum('qty_issued') : $listrequest->sum('qty')) }} units
            </span>
        </div>
    </div>

    <div class="pu-card mb-4">
        <div class="pu-card-head">
            <h5><i class="bi bi-clipboard-data me-1 text-warning"></i> Issued Items</h5>
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
                    <a href="{{ route('viewReceiveStock', Crypt::encrypt($requisitionNo)) }}"
                       class="btn-receive-link">
                        <i class="bi bi-check2-square"></i> Receive Stock
                    </a>
                @endif
            </div>
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
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $lists->itemcode->item_code ?? '—' }}</td>
                            <td>{{ $lists->itemname->name ?? '—' }}</td>
                            <td>{{ optional(optional($lists->itemname)->unitname)->name ?? '—' }}</td>
                            <td>{{ $lists->batch_number ?? '—' }}</td>
                            @if($isWardIssue)
                                <td>{{ $lists->destinationLabel() }}</td>
                                <td><strong>{{ $lists->qty_issued }}</strong></td>
                                <td><span class="badge bg-success-subtle text-success">{{ $lists->fulfillmentLabel() }}</span></td>
                                <td>{{ $lists->staffname->name ?? '—' }}</td>
                                <td>{{ $lists->issuedByUser->name ?? ($issuedBy->name ?? '—') }}</td>
                            @else
                                <td><strong>{{ $lists->qty }}</strong></td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $lists->status }}</span></td>
                                <td>{{ $lists->staffname->name ?? '—' }}</td>
                                <td>{{ $lists->authorised->name ?? '—' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
