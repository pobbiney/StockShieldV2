@php
    $pageName = 'stock';
    $subpageName = 'pending-stock';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .pl-page { padding: 0 0.5rem 2rem; }

    .pl-hero {
        background: linear-gradient(135deg, #92400e 0%, #d97706 55%, #fbbf24 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(217, 119, 6, 0.28);
    }

    .pl-hero::before,
    .pl-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .pl-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .pl-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .pl-hero-inner { position: relative; z-index: 1; }

    .pl-hero-badge {
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

    .pl-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .pl-hero p { color: rgba(255, 255, 255, 0.88); font-size: 0.9rem; margin-bottom: 0; max-width: 620px; }
    .pl-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .pl-hero .breadcrumb-item a:hover { color: #fff; }
    .pl-hero .breadcrumb-item.active { color: #fff; }

    .stat-card { animation: plStatIn 0.5s ease both; }
    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }

    @keyframes plStatIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .stat-card-icon {
        width: 48px; height: 48px; border-radius: 0.875rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; margin-bottom: 1rem;
    }

    .stat-card.req  .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }
    .stat-card.line .stat-card-icon { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .stat-card.qty  .stat-card-icon { background: rgba(180, 83, 9, 0.12); color: #b45309; }

    .stat-card-value { font-size: 2rem; font-weight: 800; line-height: 1; margin-bottom: 0.2rem; }
    .stat-card.req  .stat-card-value { color: #d97706; }
    .stat-card.line .stat-card-value { color: #2563eb; }
    .stat-card.qty  .stat-card-value { color: #b45309; }
    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0; }

    .pl-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    }

    .pl-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .pl-table-head h5 { font-family: "SUSE", sans-serif; font-weight: 700; font-size: 1rem; margin: 0; }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(217, 119, 6, 0.1);
        color: #b45309;
        font-size: 0.78rem;
        font-weight: 600;
    }

    #pickListTable thead th {
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

    #pickListTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #pickListTable tbody tr:nth-child(even) { background: #fafafa; }
    #pickListTable tbody tr:hover { background: #fff7ed !important; }

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
        background: rgba(15, 23, 42, 0.08);
        color: #0f172a;
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
        background: rgba(59, 130, 246, 0.12);
        color: #2563eb;
    }

    .pl-actions {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-print {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 0.5rem;
        border: 1.5px solid rgba(146, 64, 14, 0.2);
        background: #fff;
        color: #92400e;
        text-decoration: none;
    }

    .btn-print:hover { background: #fff7ed; color: #78350f; }

    .btn-pickup {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.9rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #d97706, #b45309);
        color: #fff;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        white-space: nowrap;
    }

    .btn-pickup:hover { color: #fff; opacity: 0.92; }

    .pl-empty {
        text-align: center;
        padding: 3.5rem 1.5rem;
    }

    .pl-empty-visual {
        width: 72px; height: 72px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        background: rgba(217, 119, 6, 0.1);
        color: #d97706;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
    }

    .pl-link-note {
        margin: 0 1.5rem 1.5rem;
        padding: 0.85rem 1rem;
        border-radius: 0.75rem;
        background: #fff7ed;
        border: 1px solid rgba(217, 119, 6, 0.15);
        font-size: 0.85rem;
        color: #92400e;
    }

    .dataTables_wrapper { padding: 0 1rem 1rem; }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        padding: 0.4rem 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid pl-page px-3 px-lg-4 mt-3">
    <div class="pl-hero">
        <div class="pl-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="pl-hero-badge">
                        <i class="bi bi-truck"></i> Satellite Store — Pick List
                    </div>
                    <h2>Ready for Pick Up</h2>
                    <p>
                        View and print items issued from your satellite store to wards or other stores, and transfers awaiting pick-up.
                        @if($activeStore ?? null)
                            <span class="d-block mt-1 opacity-75"><i class="bi bi-building me-1"></i>{{ $activeStore->name }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pick List</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-card req">
                <div class="stat-card-icon"><i class="bi bi-truck"></i></div>
                <div class="stat-card-value">{{ number_format($totalPickLists ?? 0) }}</div>
                <p class="stat-card-label">Issued Slips Ready</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card line">
                <div class="stat-card-icon"><i class="bi bi-list-check"></i></div>
                <div class="stat-card-value">{{ number_format($totalLineItems ?? 0) }}</div>
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

    <div class="pl-table-card mb-5">
        <div class="pl-table-head">
            <div>
                <h5><i class="bi bi-clipboard-check me-1 text-warning"></i> Ready for Pick Up</h5>
                <span class="record-count-badge">
                    <i class="bi bi-hourglass-split"></i>
                    {{ $totalPickLists ?? 0 }} pick list{{ ($totalPickLists ?? 0) !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        @if(($pickLists ?? collect())->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="pickListTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Reference No</th>
                            <th>Ward / Store</th>
                            <th>Items</th>
                            <th>Qty</th>
                            <th>Issued</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pickLists as $pick)
                            <tr>
                                <td class="text-secondary">{{ $loop->iteration }}</td>
                                <td>
                                    @if(($pick->pick_type ?? '') === 'ward_issue')
                                        <span class="badge bg-warning-subtle text-warning-emphasis">Ward Issue</span>
                                    @elseif(($pick->pick_type ?? '') === 'satellite_issue')
                                        <span class="badge bg-success-subtle text-success-emphasis">Issued from store</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">From Central</span>
                                    @endif
                                </td>
                                <td><span class="req-no-badge">{{ $pick->reference_no }}</span></td>
                                <td>
                                    @if(($pick->pick_type ?? '') === 'ward_issue')
                                        <span class="store-badge">
                                            <i class="bi bi-hospital"></i>
                                            {{ $pick->ward_label ?? '—' }}
                                        </span>
                                    @elseif(($pick->pick_type ?? '') === 'satellite_issue')
                                        <span class="store-badge">
                                            <i class="bi bi-geo-alt"></i>
                                            To: {{ $pick->to_store->name ?? ($pick->storename->name ?? 'Store') }}
                                        </span>
                                    @else
                                        <span class="store-badge">
                                            <i class="bi bi-building"></i>
                                            {{ $pick->central_store->name ?? 'Central Store' }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $pick->unique_items }} item{{ $pick->unique_items !== 1 ? 's' : '' }} ({{ $pick->line_count }} lines)</td>
                                <td><span class="qty-badge">{{ number_format($pick->total_qty) }}</span></td>
                                <td>
                                    {{ $pick->issued_at?->format('M d, Y') ?? '—' }}
                                    <div class="text-muted small">{{ $pick->issued_at?->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <span class="status-badge">
                                        <i class="bi bi-truck"></i> Ready to Pick
                                    </span>
                                </td>
                                <td>
                                    <div class="pl-actions">
                                        @if($pick->invoice_number ?? null)
                                            <a href="{{ route('requisition.print', Crypt::encrypt($pick->invoice_number)) }}"
                                               class="btn-print"
                                               target="_blank"
                                               title="Print invoice">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('viewPickUp', Crypt::encrypt($pick->reference_no)) }}"
                                           class="btn-pickup">
                                            <i class="bi bi-truck"></i> Pick Up
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pl-link-note">
                <i class="bi bi-info-circle me-1"></i>
                Ward issues are created via
                <a href="{{ route('IssueItemSatellite') }}" class="fw-semibold">Issue Item (Satellite)</a>.
                Stock issued from this store to other stores also appears here. For inbound transfers, use
                <a href="{{ route('ReceiveStock') }}" class="fw-semibold">Receive Stock</a>
                after pick-up.
            </div>
        @else
            <div class="pl-empty">
                <div class="pl-empty-visual"><i class="bi bi-truck"></i></div>
                <h5 class="fw-semibold text-dark">No pick lists right now</h5>
                <p class="text-muted mb-0">When you issue stock to wards or other stores, or when stock is issued to {{ $activeStore->name ?? 'your store' }}, entries will appear here for pick-up and printing.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    if ($('#pickListTable').length && $.fn.DataTable && $('#pickListTable tbody tr').length) {
        $('#pickListTable').DataTable({
            order: [[6, 'desc']],
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, 'All']],
            language: { search: '', searchPlaceholder: 'Search pick lists…', emptyTable: 'No pick lists available.' },
            columnDefs: [{ orderable: false, targets: [0, 8] }],
        });
    }
});
</script>
@endsection
