@php
    $pageName = 'stock';
    $subpageName = 'issue-item';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .ii-page { padding: 0 0.5rem 2rem; }

    .ii-hero {
        background: linear-gradient(135deg, #92400e 0%, #d97706 55%, #fbbf24 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(217, 119, 6, 0.28);
    }

    .ii-hero::before,
    .ii-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .ii-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .ii-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .ii-hero-inner { position: relative; z-index: 1; }

    .ii-hero-badge {
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

    .ii-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .ii-hero p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 560px;
    }

    .ii-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .ii-hero .breadcrumb-item.active { color: #fff; }

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

    @keyframes statIn {
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

    .stat-card.req  .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }
    .stat-card.line .stat-card-icon { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .stat-card.qty  .stat-card-icon { background: rgba(16, 185, 129, 0.12); color: #059669; }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.req  .stat-card-value { color: #d97706; }
    .stat-card.line .stat-card-value { color: #2563eb; }
    .stat-card.qty  .stat-card-value { color: #059669; }

    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0; }

    .ii-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: statIn 0.5s ease 0.2s both;
    }

    .ii-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .ii-table-head h5 {
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
        background: rgba(217, 119, 6, 0.1);
        color: #d97706;
        font-size: 0.78rem;
        font-weight: 600;
    }

    #issueItemTable thead th {
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

    #issueItemTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #issueItemTable tbody tr:nth-child(even) { background: #fafafa; }
    #issueItemTable tbody tr:hover { background: #fffbeb !important; }

    .req-no-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
        background: rgba(217, 119, 6, 0.1);
        color: #b45309;
        font-size: 0.78rem;
        font-weight: 700;
        font-family: monospace;
    }

    .store-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 2rem;
        background: rgba(99, 102, 241, 0.1);
        color: #4f46e5;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .qty-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
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
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
    }

    .btn-open-req {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.9rem;
        border-radius: 0.625rem;
        border: none;
        background: #d97706;
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .btn-open-req:hover { background: #b45309; color: #fff; }

    .ii-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .ii-empty-visual {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(217, 119, 6, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.75rem;
        color: #d97706;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        padding: 0.4rem 0.75rem;
    }

    .staff-swal-confirm.issue { background: #d97706 !important; }
    .staff-swal-confirm.issue:hover { background: #b45309 !important; }
</style>
@endsection

@section('content')

<div class="container-fluid ii-page px-3 px-lg-4 mt-3">

    <div class="ii-hero">
        <div class="ii-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="ii-hero-badge">
                        <i class="bi bi-box-arrow-right"></i> Central Store — Issue Items
                    </div>
                    <h2>Approved Requisitions</h2>
                    <p>
                        Satellite stores have requested stock from your store. Review approved requisitions and issue items.
                        @if($activeStore)
                            <span class="d-block mt-1 opacity-75"><i class="bi bi-building me-1"></i>Fulfilling from: {{ $activeStore->name }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Issue Items</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="stat-card req">
                <div class="stat-card-icon"><i class="bi bi-inboxes"></i></div>
                <div class="stat-card-value">{{ number_format($totalRequisitions) }}</div>
                <p class="stat-card-label">Pending Requisitions</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card line">
                <div class="stat-card-icon"><i class="bi bi-list-check"></i></div>
                <div class="stat-card-value">{{ number_format($totalLineItems) }}</div>
                <p class="stat-card-label">Line Items to Issue</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card qty">
                <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-card-value">{{ number_format($totalQty) }}</div>
                <p class="stat-card-label">Total Approved Qty</p>
            </div>
        </div>
    </div>

    <div class="ii-table-card mb-5">
        <div class="ii-table-head">
            <div>
                <h5><i class="bi bi-truck me-1 text-warning"></i> Requests Awaiting Issue</h5>
                <span class="record-count-badge">
                    <i class="bi bi-hourglass-split"></i>
                    {{ $totalRequisitions }} requisition{{ $totalRequisitions !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        @if($requisitions->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="issueItemTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Requisition No</th>
                            <th>Requesting Store</th>
                            <th>Items</th>
                            <th>Req. Qty</th>
                            <th>Approved Qty</th>
                            <th>Submitted</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requisitions as $req)
                            <tr>
                                <td class="text-secondary">{{ $loop->iteration }}</td>
                                <td><span class="req-no-badge">{{ $req->requisition_no }}</span></td>
                                <td>
                                    <span class="store-badge">
                                        <i class="bi bi-shop"></i>
                                        {{ $req->requesting_store->name ?? '—' }}
                                    </span>
                                </td>
                                <td>{{ $req->line_count }} item{{ $req->line_count !== 1 ? 's' : '' }}</td>
                                <td><span class="qty-badge">{{ number_format($req->total_qty_requested) }}</span></td>
                                <td><span class="qty-badge" style="background:rgba(16,185,129,0.1);color:#059669;">{{ number_format($req->total_qty_approved) }}</span></td>
                                <td>
                                    {{ $req->submitted_at?->format('M d, Y') ?? '—' }}
                                    <div class="text-muted small">{{ $req->submitted_at?->format('h:i A') }}</div>
                                </td>
                                <td>{{ $req->requested_by->name ?? '—' }}</td>
                                <td><span class="status-badge"><i class="bi bi-patch-check"></i> Approved</span></td>
                                <td>
                                    <a href="{{ route('viewStoreRequest', Crypt::encrypt($req->requisition_no)) }}"
                                       class="btn-open-req">
                                        <i class="bi bi-box-arrow-up-right"></i> Issue Items
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="ii-empty">
                <div class="ii-empty-visual"><i class="bi bi-inbox"></i></div>
                <h5 class="fw-semibold text-dark">No approved requests right now</h5>
                <p>When satellite stores submit requisitions and they are approved by HOD, they will appear here for you to issue stock.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
const IssueAlert = {
    _base(opts) {
        return Swal.fire(Object.assign({
            width: '28rem',
            padding: '1.5rem 1.75rem 1.35rem',
            buttonsStyling: false,
            customClass: {
                popup: 'staff-swal-popup',
                title: 'staff-swal-title',
                htmlContainer: 'staff-swal-text',
                confirmButton: 'btn staff-swal-confirm ' + (opts.btnClass || 'neutral'),
                cancelButton: 'btn btn-light border staff-swal-cancel',
            },
        }, opts));
    },
    issued(message) {
        return this._base({
            icon: 'success',
            title: 'Submitted for Issue',
            html: '<p class="mb-0">' + message + '</p>'
                + '<small class="text-muted d-block mt-2">Items are pending issue approval by HOD before stock is deducted and dispatched.</small>',
            btnClass: 'issue',
            timer: 4500,
            timerProgressBar: true,
            confirmButtonText: '<i class="bi bi-hourglass-split me-1"></i> OK',
        });
    },
    deleted(message) {
        return this._base({
            icon: 'success',
            title: 'Item Removed',
            text: message,
            btnClass: 'issue',
            timer: 3200,
            timerProgressBar: true,
            confirmButtonText: '<i class="bi bi-check-lg me-1"></i> OK',
        });
    },
    success(message) {
        return this._base({
            icon: 'success',
            title: 'Success',
            text: message,
            btnClass: 'issue',
            timer: 3500,
            timerProgressBar: true,
            confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Done',
        });
    },
    error(title, text) {
        return this._base({
            icon: 'error',
            title: title || 'Something went wrong',
            text: text,
            btnClass: 'error',
            confirmButtonText: '<i class="bi bi-x-lg me-1"></i> Close',
        });
    },
};

$(document).ready(function () {
    if ($('#issueItemTable').length && $.fn.DataTable) {
        $('#issueItemTable').DataTable({
            order: [[6, 'desc']],
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, 'All']],
            language: { search: '', searchPlaceholder: 'Search requisitions…' },
            columnDefs: [{ orderable: false, targets: [0, 9] }],
        });
    }
});

@if(session('message_success'))
@php $successMsg = session('message_success'); @endphp
@if(stripos($successMsg, 'deleted') !== false)
IssueAlert.deleted(@json($successMsg));
@elseif(stripos($successMsg, 'submitted') !== false || stripos($successMsg, 'HOD approval') !== false)
IssueAlert.issued(@json($successMsg));
@else
IssueAlert.success(@json($successMsg));
@endif
@endif

@if(session('message_error'))
IssueAlert.error('Issue Failed', @json(session('message_error')));
@endif
</script>
@endsection
