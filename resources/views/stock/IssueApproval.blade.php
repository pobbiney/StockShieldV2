@php
    $pageName = 'stock';
    $subpageName = 'approve-issues';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .ia-page { padding: 0 0.5rem 2rem; }

    .ia-hero {
        background: linear-gradient(135deg, #5b21b6 0%, #7c3aed 55%, #a78bfa 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(124, 58, 237, 0.28);
    }

    .ia-hero::before,
    .ia-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .ia-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .ia-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .ia-hero-inner { position: relative; z-index: 1; }

    .ia-hero-badge {
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

    .ia-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .ia-hero p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 580px;
    }

    .ia-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .ia-hero .breadcrumb-item.active { color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: iaStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }

    @keyframes iaStatIn {
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

    .stat-card.req  .stat-card-icon { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }
    .stat-card.line .stat-card-icon { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .stat-card.qty  .stat-card-icon { background: rgba(16, 185, 129, 0.12); color: #059669; }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.req  .stat-card-value { color: #7c3aed; }
    .stat-card.line .stat-card-value { color: #2563eb; }
    .stat-card.qty  .stat-card-value { color: #059669; }

    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0; }

    .ia-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: iaStatIn 0.5s ease 0.2s both;
    }

    .ia-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .ia-table-head h5 {
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
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
        font-size: 0.78rem;
        font-weight: 600;
    }

    #issueApprovalTable thead th {
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

    #issueApprovalTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #issueApprovalTable tbody tr:nth-child(even) { background: #fafafa; }
    #issueApprovalTable tbody tr:hover { background: #f5f3ff !important; }

    .req-no-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
        background: rgba(124, 58, 237, 0.1);
        color: #6d28d9;
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
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
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
        background: rgba(245, 158, 11, 0.15);
        color: #b45309;
    }

    .btn-review {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.9rem;
        border-radius: 0.625rem;
        border: none;
        background: #7c3aed;
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .btn-review:hover { background: #6d28d9; color: #fff; }

    .ia-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .ia-empty-visual {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(124, 58, 237, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.75rem;
        color: #7c3aed;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        padding: 0.4rem 0.75rem;
    }

    .staff-swal-confirm.approve { background: #7c3aed !important; }
    .staff-swal-confirm.approve:hover { background: #6d28d9 !important; }
</style>
@endsection

@section('content')

<div class="container-fluid ia-page px-3 px-lg-4 mt-3">

    <div class="ia-hero">
        <div class="ia-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="ia-hero-badge">
                        <i class="bi bi-shield-check"></i> HOD — Issue Approval
                    </div>
                    <h2>Pending Issue Approvals</h2>
                    <p>
                        Central store has prepared stock issues for satellite stores. Review quantities, approve to deduct stock and generate an invoice, or reject individual lines.
                        @if($activeStore ?? null)
                            <span class="d-block mt-1 opacity-75"><i class="bi bi-building me-1"></i>Central store: {{ $activeStore->name }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Issue Approval</li>
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
                <div class="stat-card-value">{{ number_format($totalRequisitions ?? 0) }}</div>
                <p class="stat-card-label">Pending Requisitions</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card line">
                <div class="stat-card-icon"><i class="bi bi-list-check"></i></div>
                <div class="stat-card-value">{{ number_format($totalLineItems ?? 0) }}</div>
                <p class="stat-card-label">Line Items to Approve</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card qty">
                <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-card-value">{{ number_format($totalQty ?? 0) }}</div>
                <p class="stat-card-label">Total Issue Qty</p>
            </div>
        </div>
    </div>

    <div class="ia-table-card mb-5">
        <div class="ia-table-head">
            <div>
                <h5><i class="bi bi-clipboard2-check me-1 text-primary"></i> Issues Awaiting Approval</h5>
                <span class="record-count-badge">
                    <i class="bi bi-hourglass-split"></i>
                    {{ $totalRequisitions ?? 0 }} requisition{{ ($totalRequisitions ?? 0) !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        @if(($requisitions ?? collect())->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="issueApprovalTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Requisition No</th>
                            <th>Issue To (Store)</th>
                            <th>Line Items</th>
                            <th>Issue Qty</th>
                            <th>Prepared By</th>
                            <th>Submitted</th>
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
                                <td><span class="qty-badge">{{ number_format($req->total_qty) }}</span></td>
                                <td>{{ $req->issued_by->name ?? '—' }}</td>
                                <td>
                                    {{ $req->submitted_at?->format('M d, Y') ?? '—' }}
                                    <div class="text-muted small">{{ $req->submitted_at?->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <span class="status-badge">
                                        <i class="bi bi-hourglass-split"></i> Pending Issue
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('viewIssues', Crypt::encrypt($req->requisition_no)) }}"
                                       class="btn-review">
                                        <i class="bi bi-eye"></i> Review & Approve
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="ia-empty">
                <div class="ia-empty-visual"><i class="bi bi-check2-circle"></i></div>
                <h5 class="fw-semibold text-dark">All caught up</h5>
                <p>No pending issue approvals right now. When the central store submits issued items, they will appear here for your review.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
const IssueApprovalAlert = {
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
    success(message) {
        return this._base({
            icon: 'success',
            title: 'Issues Approved',
            html: '<p class="mb-0">' + message + '</p>'
                + '<small class="text-muted d-block mt-2">Stock has been deducted and an invoice was generated for dispatch.</small>',
            btnClass: 'approve',
            timer: 4000,
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
    if ($('#issueApprovalTable').length && $.fn.DataTable) {
        $('#issueApprovalTable').DataTable({
            order: [[6, 'desc']],
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, 'All']],
            language: { search: '', searchPlaceholder: 'Search issues…' },
            columnDefs: [{ orderable: false, targets: [0, 8] }],
        });
    }
});

@if(session('message_success'))
IssueApprovalAlert.success(@json(session('message_success')));
@endif

@if(session('message_error'))
IssueApprovalAlert.error('Approval Failed', @json(session('message_error')));
@endif

@if(session('print_url'))
(function () {
    var printWindow = window.open(@json(session('print_url')), '_blank');
    if (!printWindow) {
        IssueApprovalAlert.error('Popup Blocked', 'Please allow popups to open the print page automatically.');
    }
})();
@endif
</script>
@endsection
