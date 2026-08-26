@php
    $pageName = 'stock';
    $subpageName = 'stock-approval';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .ra-page { padding: 0 0.5rem 2rem; }

    .ra-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #60a5fa 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.28);
    }

    .ra-hero::before,
    .ra-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .ra-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .ra-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .ra-hero-inner { position: relative; z-index: 1; }

    .ra-hero-badge {
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

    .ra-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .ra-hero p { color: rgba(255, 255, 255, 0.88); font-size: 0.9rem; margin-bottom: 0; max-width: 620px; }
    .ra-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .ra-hero .breadcrumb-item.active { color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: raStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }

    @keyframes raStatIn {
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

    .ra-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: raStatIn 0.5s ease 0.2s both;
    }

    .ra-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .ra-table-head h5 { font-family: "SUSE", sans-serif; font-weight: 700; font-size: 1rem; margin: 0; }

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

    #returnApprovalTable thead th {
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

    #returnApprovalTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #returnApprovalTable tbody tr:nth-child(even) { background: #fafafa; }
    #returnApprovalTable tbody tr:hover { background: #eff6ff !important; }

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
        background: rgba(194, 65, 12, 0.1);
        color: #c2410c;
        font-weight: 700;
        font-size: 0.82rem;
    }

    .return-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.6rem;
        border-radius: 2rem;
        font-size: 0.72rem;
        font-weight: 600;
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
    }

    .comment-snippet {
        max-width: 180px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #64748b;
        font-size: 0.82rem;
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

    .btn-approve {
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

    .btn-approve:hover { background: #1d4ed8; color: #fff; }

    .ra-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .ra-empty-visual {
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
<div class="container-fluid ra-page px-3 px-lg-4 mt-3">
    <div class="ra-hero">
        <div class="ra-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="ra-hero-badge">
                        <i class="bi bi-shield-check"></i> Return Approval
                    </div>
                    <h2>Review Return Requests</h2>
                    <p>
                        Approve or review stock returns submitted by store staff. Approved returns adjust batch quantities and update inventory status.
                        @if($activeStore ?? null)
                            <span class="d-block mt-1 opacity-75"><i class="bi bi-shop me-1"></i>Store: {{ $activeStore->name }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('Return') }}" class="text-decoration-none">Return</a></li>
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
                <p class="stat-card-label">Units to Return</p>
            </div>
        </div>
    </div>

    <div class="ra-table-card mb-5">
        <div class="ra-table-head">
            <div>
                <h5><i class="bi bi-clipboard-check me-1 text-primary"></i> Pending Return Requests</h5>
                <span class="record-count-badge">
                    <i class="bi bi-bell"></i>
                    {{ $pendingCount ?? 0 }} request{{ ($pendingCount ?? 0) !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        @if(($listItem ?? collect())->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="returnApprovalTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Batch</th>
                            <th>Store</th>
                            <th>Return Qty</th>
                            <th>Type</th>
                            <th>Returned By</th>
                            <th>Comment</th>
                            <th>Submitted</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listItem as $lists)
                            <tr>
                                <td class="text-secondary">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="item-code-badge d-block mb-1">{{ $lists->itemcode->item_code ?? '—' }}</span>
                                    <strong>{{ $lists->itemname->name ?? $lists->itemcode->name ?? '—' }}</strong>
                                </td>
                                <td><span class="batch-badge">{{ $lists->batch_number ?? '—' }}</span></td>
                                <td>
                                    <span class="store-badge">
                                        <i class="bi bi-shop"></i>
                                        {{ $lists->stockdetails->storename->name ?? '—' }}
                                    </span>
                                </td>
                                <td><span class="qty-badge">{{ number_format($lists->quantity) }}</span></td>
                                <td>
                                    <span class="return-type-badge">
                                        <i class="bi bi-{{ strtolower($lists->return_status ?? '') === 'part' ? 'pie-chart' : 'check2-all' }}"></i>
                                        {{ $lists->return_status ?? '—' }}
                                    </span>
                                </td>
                                <td>{{ $lists->staffname->name ?? '—' }}</td>
                                <td>
                                    <span class="comment-snippet" title="{{ $lists->manager_comment ?? $lists->hod_comment ?? '' }}">
                                        {{ $lists->manager_comment ?? $lists->hod_comment ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $lists->created_at?->format('M d, Y') ?? '—' }}
                                    <div class="text-muted small">{{ $lists->created_at?->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn-approve showmodal"
                                            data-url="{{ route('return-item-approval-id', $lists->id) }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#returnApprovalModal">
                                        <i class="bi bi-check2-square"></i> Review
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="ra-empty">
                <div class="ra-empty-visual"><i class="bi bi-check2-circle"></i></div>
                <h5 class="fw-semibold text-dark">All caught up</h5>
                <p class="mb-0">No return requests are waiting for your approval right now.</p>
            </div>
        @endif
    </div>
</div>

@include('requisition.return-item-approval-modal')
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    if ($.fn.DataTable && $('#returnApprovalTable tbody tr').length) {
        $('#returnApprovalTable').DataTable({
            order: [[8, 'desc']],
            pageLength: 25,
            language: { emptyTable: 'No pending return requests.' }
        });
    }

    $('body').on('click', '.showmodal', function () {
        var userUrl = $(this).data('url');

        $.get(userUrl, function (data) {
            $('#approval_item_id').val(data.id);
            $('#approval_batch_number').val(data.batch_number);
            $('#approval_qty').val(data.quantity);
            $('#approval_item_name').text(data.item_name || 'Item');
            $('#approval_item_code').text(data.item_code || '—');
            $('#approval_batch_label').text(data.batch_number || '—');
            $('#approval_return_qty').text(Number(data.quantity || 0).toLocaleString());
            $('#approval_return_type').text(data.return_status || '—');
            $('#approval_returned_by').text(data.returned_by || '—');
            $('#approval_store_name').text(data.store_name || '—');
            $('#approval_staff_comment').text(data.manager_comment || '—');
            $('#approval_expiry_label').text(data.expiry_date
                ? new Date(data.expiry_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
                : '—');
            $('#approval_available_qty').text(data.available_qty != null
                ? Number(data.available_qty).toLocaleString()
                : '—');
        });
    });
});
</script>
@endsection
