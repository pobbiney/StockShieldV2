@php
    $pageName = 'stock';
    $subpageName = 'reverse-entry-approval';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .rea-page { padding: 0 0.5rem 2rem; }

    .rea-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #60a5fa 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.28);
    }

    .rea-hero::before,
    .rea-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .rea-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .rea-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .rea-hero-inner { position: relative; z-index: 1; }

    .rea-hero-badge {
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

    .rea-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .rea-hero p { color: rgba(255, 255, 255, 0.88); font-size: 0.9rem; margin-bottom: 0; max-width: 620px; }
    .rea-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .rea-hero .breadcrumb-item.active { color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: reaStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }

    @keyframes reaStatIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card-icon {
        width: 48px; height: 48px; border-radius: 0.875rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; margin-bottom: 1rem;
    }

    .stat-card.pending .stat-card-icon { background: rgba(37, 99, 235, 0.12); color: #2563eb; }
    .stat-card.items  .stat-card-icon { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }
    .stat-card.qty    .stat-card-icon { background: rgba(16, 185, 129, 0.12); color: #059669; }

    .stat-card-value { font-size: 2rem; font-weight: 800; line-height: 1; margin-bottom: 0.2rem; }
    .stat-card.pending .stat-card-value { color: #2563eb; }
    .stat-card.items  .stat-card-value { color: #7c3aed; }
    .stat-card.qty    .stat-card-value { color: #059669; }
    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0; }

    .rea-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: reaStatIn 0.5s ease 0.2s both;
    }

    .rea-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .rea-table-head h5 { font-family: "SUSE", sans-serif; font-weight: 700; font-size: 1rem; margin: 0; }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        font-size: 0.78rem;
        font-weight: 600;
    }

    #reverseApprovalTable thead th {
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

    #reverseApprovalTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #reverseApprovalTable tbody tr:nth-child(even) { background: #fafafa; }
    #reverseApprovalTable tbody tr:hover { background: #eff6ff !important; }

    .item-code-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(100, 116, 139, 0.1);
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        font-family: monospace;
    }

    .batch-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        font-size: 0.78rem;
        font-weight: 600;
        font-family: monospace;
    }

    .qty-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(5, 150, 105, 0.1);
        color: #059669;
        font-weight: 700;
        font-size: 0.82rem;
    }

    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.6rem;
        border-radius: 2rem;
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .store-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.6rem;
        border-radius: 2rem;
        background: rgba(217, 119, 6, 0.1);
        color: #b45309;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .comment-snippet {
        display: block;
        max-width: 160px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #64748b;
        font-size: 0.82rem;
    }

    .btn-review {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.9rem;
        border-radius: 0.625rem;
        border: none;
        background: #2563eb;
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-review:hover { background: #1d4ed8; color: #fff; }

    .rea-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .rea-empty-visual {
        width: 72px; height: 72px; border-radius: 50%;
        background: rgba(37, 99, 235, 0.1);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem; font-size: 1.75rem; color: #2563eb;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        padding: 0.4rem 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid rea-page px-3 px-lg-4 mt-3">
    <div class="rea-hero">
        <div class="rea-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="rea-hero-badge">
                        <i class="bi bi-shield-exclamation"></i> Reverse Entry Approval
                    </div>
                    <h2>Review Batch Correction Requests</h2>
                    <p>
                        Approve or reject reverse entry requests. On approval, both the stock entry and approved inventory records are updated.
                        @if($activeStore ?? null)
                            <span class="d-block mt-1 opacity-75"><i class="bi bi-shop me-1"></i>Store: {{ $activeStore->name }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('reverseEntry') }}" class="text-decoration-none">Reverse Entry</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Approval</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="stat-card pending">
                <div class="stat-card-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-card-value">{{ number_format($pendingCount ?? 0) }}</div>
                <p class="stat-card-label">Awaiting Approval</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card items">
                <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-card-value">{{ number_format($uniqueItems ?? 0) }}</div>
                <p class="stat-card-label">Unique Items</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card qty">
                <div class="stat-card-icon"><i class="bi bi-stack"></i></div>
                <div class="stat-card-value">{{ number_format($totalQty ?? 0) }}</div>
                <p class="stat-card-label">Units to Reverse</p>
            </div>
        </div>
    </div>

    <div class="rea-table-card mb-5">
        <div class="rea-table-head">
            <div>
                <h5><i class="bi bi-clipboard-check me-1 text-primary"></i> Pending Reverse Entry Requests</h5>
                <span class="record-count-badge">
                    <i class="bi bi-bell"></i>
                    {{ $pendingCount ?? 0 }} request{{ ($pendingCount ?? 0) !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        @if(($listReversals ?? collect())->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="reverseApprovalTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Batch</th>
                            <th>Store</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Requested By</th>
                            <th>Reason</th>
                            <th>Submitted</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listReversals as $reversal)
                            <tr>
                                <td class="text-secondary">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="item-code-badge d-block mb-1">{{ $reversal->itemcode->item_code ?? '—' }}</span>
                                    <strong>{{ $reversal->itemname->name ?? '—' }}</strong>
                                </td>
                                <td><span class="batch-badge">{{ $reversal->batch_number ?? '—' }}</span></td>
                                <td>
                                    <span class="store-badge">
                                        <i class="bi bi-shop"></i>
                                        {{ $reversal->store->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="type-badge">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        {{ $reversal->typeLabel() }}
                                    </span>
                                </td>
                                <td><span class="qty-badge">{{ number_format($reversal->qty_to_reverse) }}</span></td>
                                <td>{{ $reversal->requestedByUser->name ?? '—' }}</td>
                                <td>
                                    <span class="comment-snippet" title="{{ $reversal->reason }}">
                                        {{ $reversal->reason ?: '—' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $reversal->created_at?->format('M d, Y') ?? '—' }}
                                    <div class="text-muted small">{{ $reversal->created_at?->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn-review review-reversal"
                                            data-id="{{ $reversal->id }}"
                                            data-item-name="{{ $reversal->itemname->name ?? 'Item' }}"
                                            data-item-code="{{ $reversal->itemcode->item_code ?? '—' }}"
                                            data-batch="{{ $reversal->batch_number }}"
                                            data-store="{{ $reversal->store->name ?? '—' }}"
                                            data-type="{{ $reversal->typeLabel() }}"
                                            data-qty="{{ $reversal->qty_to_reverse }}"
                                            data-reason="{{ $reversal->reason }}"
                                            data-requested-by="{{ $reversal->requestedByUser->name ?? '—' }}"
                                            data-approve-url="{{ route('reverse-entry.approve', $reversal) }}"
                                            data-reject-url="{{ route('reverse-entry.reject', $reversal) }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#reverseApprovalModal">
                                        <i class="bi bi-check2-square"></i> Review
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rea-empty">
                <div class="rea-empty-visual"><i class="bi bi-check2-circle"></i></div>
                <h5 class="fw-semibold text-dark">No pending reversals</h5>
                <p class="mb-0">Reverse entry requests awaiting your approval will appear here.</p>
            </div>
        @endif
    </div>
</div>

@include('stock.reverse-entry-approval-modal')
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    if ($.fn.DataTable && $('#reverseApprovalTable tbody tr').length) {
        $('#reverseApprovalTable').DataTable({
            order: [[8, 'desc']],
            pageLength: 25,
            language: { emptyTable: 'No pending reverse entry requests.' }
        });
    }

    $('body').on('click', '.review-reversal', function () {
        $('#approval_item_name').text($(this).data('item-name') || 'Item');
        $('#approval_item_code').text($(this).data('item-code') || '—');
        $('#approval_batch_label').text($(this).data('batch') || '—');
        $('#approval_store_name').text($(this).data('store') || '—');
        $('#approval_reversal_type').text($(this).data('type') || '—');
        $('#approval_reverse_qty').text(Number($(this).data('qty') || 0).toLocaleString());
        $('#approval_staff_comment').text($(this).data('reason') || '—');
        $('#approval_requested_by').text($(this).data('requested-by') || '—');
        $('#approval_comment').val('');
        $('#reverseApprovalForm').attr('action', '');
        $('#reverseApprovalForm').data('approve-url', $(this).data('approve-url'));
        $('#reverseApprovalForm').data('reject-url', $(this).data('reject-url'));
    });

    $('#btnApproveReversal').on('click', function () {
        var form = $('#reverseApprovalForm');
        form.attr('action', form.data('approve-url'));
        form.trigger('submit');
    });

    $('#btnRejectReversal').on('click', function () {
        var form = $('#reverseApprovalForm');
        form.attr('action', form.data('reject-url'));
        form.trigger('submit');
    });
});
</script>
@endsection
