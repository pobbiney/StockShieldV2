@php
    $pageName = 'stock';
    $subpageName = 'receive-stock';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .rs-page { padding: 0 0.5rem 2rem; }

    .rs-hero {
        background: linear-gradient(135deg, #065f46 0%, #059669 55%, #34d399 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(5, 150, 105, 0.28);
    }

    .rs-hero::before,
    .rs-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .rs-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .rs-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .rs-hero-inner { position: relative; z-index: 1; }

    .rs-hero-badge {
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

    .rs-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .rs-hero p { color: rgba(255, 255, 255, 0.88); font-size: 0.9rem; margin-bottom: 0; max-width: 580px; }
    .rs-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .rs-hero .breadcrumb-item.active { color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: rsStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }

    @keyframes rsStatIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card-icon {
        width: 48px; height: 48px; border-radius: 0.875rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; margin-bottom: 1rem;
    }

    .stat-card.req  .stat-card-icon { background: rgba(5, 150, 105, 0.12); color: #059669; }
    .stat-card.line .stat-card-icon { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .stat-card.qty  .stat-card-icon { background: rgba(16, 185, 129, 0.12); color: #047857; }

    .stat-card-value { font-size: 2rem; font-weight: 800; line-height: 1; margin-bottom: 0.2rem; }
    .stat-card.req  .stat-card-value { color: #059669; }
    .stat-card.line .stat-card-value { color: #2563eb; }
    .stat-card.qty  .stat-card-value { color: #047857; }
    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0; }

    .rs-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: rsStatIn 0.5s ease 0.2s both;
    }

    .rs-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .rs-table-head h5 { font-family: "SUSE", sans-serif; font-weight: 700; font-size: 1rem; margin: 0; }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(5, 150, 105, 0.1);
        color: #059669;
        font-size: 0.78rem;
        font-weight: 600;
    }

    #receiveStockTable thead th {
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

    #receiveStockTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #receiveStockTable tbody tr:nth-child(even) { background: #fafafa; }
    #receiveStockTable tbody tr:hover { background: #ecfdf5 !important; }

    .req-no-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
        background: rgba(5, 150, 105, 0.1);
        color: #047857;
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
        background: rgba(217, 119, 6, 0.1);
        color: #b45309;
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
        background: rgba(5, 150, 105, 0.1);
        color: #059669;
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
        background: rgba(59, 130, 246, 0.12);
        color: #2563eb;
    }

    .btn-receive {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.9rem;
        border-radius: 0.625rem;
        border: none;
        background: #059669;
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .btn-receive:hover { background: #047857; color: #fff; }

    .rs-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .rs-empty-visual {
        width: 72px; height: 72px; border-radius: 50%;
        background: rgba(5, 150, 105, 0.1);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem; font-size: 1.75rem; color: #059669;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        padding: 0.4rem 0.75rem;
    }

    .staff-swal-confirm.receive { background: #059669 !important; }
    .staff-swal-confirm.receive:hover { background: #047857 !important; }
</style>
@endsection

@section('content')

<div class="container-fluid rs-page px-3 px-lg-4 mt-3">

    <div class="rs-hero">
        <div class="rs-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="rs-hero-badge">
                        <i class="bi bi-box-arrow-in-down"></i> Satellite Store — Receive Stock
                    </div>
                    <h2>Incoming Transfers</h2>
                    <p>
                        Accept items issued from central stores into your local inventory. Review each transfer and confirm receipt to update your store stock.
                        @if($activeStore ?? null)
                            <span class="d-block mt-1 opacity-75"><i class="bi bi-building me-1"></i>Receiving at: {{ $activeStore->name }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Receive Stock</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="stat-card req">
                <div class="stat-card-icon"><i class="bi bi-truck"></i></div>
                <div class="stat-card-value">{{ number_format($totalTransfers ?? 0) }}</div>
                <p class="stat-card-label">Pending Transfers</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card line">
                <div class="stat-card-icon"><i class="bi bi-list-check"></i></div>
                <div class="stat-card-value">{{ number_format($totalLineItems ?? 0) }}</div>
                <p class="stat-card-label">Line Items to Accept</p>
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

    <div class="rs-table-card mb-5">
        <div class="rs-table-head">
            <div>
                <h5><i class="bi bi-inbox me-1 text-success"></i> Awaiting Acceptance</h5>
                <span class="record-count-badge">
                    <i class="bi bi-hourglass-split"></i>
                    {{ $totalTransfers ?? 0 }} transfer{{ ($totalTransfers ?? 0) !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        @if(($transfers ?? collect())->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="receiveStockTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Requisition No</th>
                            <th>Invoice</th>
                            <th>From (Central)</th>
                            <th>Items</th>
                            <th>Qty</th>
                            <th>Issued</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                            <tr>
                                <td class="text-secondary">{{ $loop->iteration }}</td>
                                <td><span class="req-no-badge">{{ $transfer->requisition_no }}</span></td>
                                <td><code class="small">{{ $transfer->invoice_number ?? '—' }}</code></td>
                                <td>
                                    <span class="store-badge">
                                        <i class="bi bi-building"></i>
                                        {{ $transfer->central_store->name ?? 'Central' }}
                                    </span>
                                </td>
                                <td>{{ $transfer->unique_items }} item{{ $transfer->unique_items !== 1 ? 's' : '' }} ({{ $transfer->line_count }} lines)</td>
                                <td><span class="qty-badge">{{ number_format($transfer->total_qty) }}</span></td>
                                <td>
                                    {{ $transfer->issued_at?->format('M d, Y') ?? '—' }}
                                    <div class="text-muted small">{{ $transfer->issued_at?->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <span class="status-badge">
                                        <i class="bi bi-box-arrow-in-down"></i> Ready to Receive
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('viewReceiveStock', Crypt::encrypt($transfer->requisition_no)) }}"
                                       class="btn-receive">
                                        <i class="bi bi-check2-square"></i> Review & Accept
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rs-empty">
                <div class="rs-empty-visual"><i class="bi bi-inbox"></i></div>
                <h5 class="fw-semibold text-dark">No incoming stock right now</h5>
                <p>When central stores issue approved items to your satellite store, they will appear here for acceptance.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
const ReceiveAlert = {
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
            title: 'Stock Accepted',
            html: '<p class="mb-0">' + message + '</p>'
                + '<small class="text-muted d-block mt-2">Items have been added to your store inventory and are ready for use.</small>',
            btnClass: 'receive',
            timer: 4500,
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
    if ($('#receiveStockTable').length && $.fn.DataTable) {
        $('#receiveStockTable').DataTable({
            order: [[6, 'desc']],
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, 'All']],
            language: { search: '', searchPlaceholder: 'Search transfers…' },
            columnDefs: [{ orderable: false, targets: [0, 8] }],
        });
    }
});

@if(session('message_success'))
ReceiveAlert.success(@json(session('message_success')));
@endif

@if(session('message_error'))
ReceiveAlert.error('Receive Failed', @json(session('message_error')));
@endif
</script>
@endsection
