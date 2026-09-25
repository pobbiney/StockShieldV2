@php
    $pageName = 'stock';
    $subpageName = 'pending-stock';
    $pickCount = ($listrequest ?? collect())->count();
    $activeStore = $activeStore ?? app(\App\Services\StoreContext::class)->getActiveStore();
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .pl-page { padding: 0 0.5rem 2rem; }

    .pl-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #60a5fa 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.28);
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

    .pl-hero p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 620px;
    }

    .pl-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .pl-hero .breadcrumb-item a:hover { color: #fff; }
    .pl-hero .breadcrumb-item.active { color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
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
        background: rgba(37, 99, 235, 0.12);
        color: #2563eb;
    }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
        color: #2563eb;
    }

    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0; }

    .pl-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        background: #fff;
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

    .pl-table-head h5 {
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
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
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
    #pickListTable tbody tr:hover { background: #eff6ff !important; }

    .req-no-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
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

    .pl-actions {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-pickup {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.9rem;
        border-radius: 0.625rem;
        background: #2563eb;
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        white-space: nowrap;
        transition: background 0.15s ease;
    }

    .btn-pickup:hover { background: #1d4ed8; color: #fff; }

    .btn-print {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.15rem;
        height: 2.15rem;
        border-radius: 0.625rem;
        background: #f1f5f9;
        color: #475569;
        text-decoration: none;
        border: 1px solid #e2e8f0;
    }

    .btn-print:hover { background: #e2e8f0; color: #0f172a; }

    .pl-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .pl-empty-visual {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(37, 99, 235, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.75rem;
        color: #2563eb;
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
                        <i class="bi bi-clipboard-check"></i> Stock — Pick List
                    </div>
                    <h2>Ready for Pick Up</h2>
                    <p>
                        Issued slips waiting to be collected. Open a requisition to review items and print the invoice.
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

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card">
                <div class="stat-card-icon"><i class="bi bi-truck"></i></div>
                <div class="stat-card-value">{{ number_format($pickCount) }}</div>
                <p class="stat-card-label">Pick Lists Ready</p>
            </div>
        </div>
    </div>

    <div class="pl-table-card mb-5">
        <div class="pl-table-head">
            <div>
                <h5><i class="bi bi-clipboard-check me-1 text-primary"></i> Issued Slips</h5>
                <span class="record-count-badge">
                    <i class="bi bi-hourglass-split"></i>
                    {{ $pickCount }} pick list{{ $pickCount !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        @if($pickCount > 0)
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="pickListTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Requisition No</th>
                            <th>Invoice</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Issued</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listrequest as $lists)
                            <tr>
                                <td class="text-secondary">{{ $loop->iteration }}</td>
                                <td><span class="req-no-badge">{{ $lists->requisition_no }}</span></td>
                                <td><code class="small">{{ $lists->invoice_number ?: '—' }}</code></td>
                                <td>
                                    <span class="store-badge">
                                        <i class="bi bi-building"></i>
                                        {{ optional($lists->issuefrom)->name ?? 'Store' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="store-badge">
                                        <i class="bi bi-geo-alt"></i>
                                        {{ optional($lists->storename)->name ?? 'Store' }}
                                    </span>
                                </td>
                                <td>
                                    {{ optional($lists->updated_at)->format('M d, Y') ?? '—' }}
                                    <div class="text-muted small">{{ optional($lists->updated_at)->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <span class="status-badge">
                                        <i class="bi bi-truck"></i> {{ $lists->status ?? 'issued' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="pl-actions">
                                        @if($lists->invoice_number)
                                            <a href="{{ route('requisition.print', Crypt::encrypt($lists->invoice_number)) }}"
                                               class="btn-print"
                                               target="_blank"
                                               title="Print invoice">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('viewPickUp', Crypt::encrypt($lists->requisition_no)) }}"
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
        @else
            <div class="pl-empty">
                <div class="pl-empty-visual"><i class="bi bi-truck"></i></div>
                <h5 class="fw-semibold text-dark">No pick lists right now</h5>
                <p class="mb-0">When stock is issued from or to {{ optional($activeStore)->name ?? 'your store' }}, slips will appear here.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    if ($('#pickListTable').length && $.fn.DataTable) {
        $('#pickListTable').DataTable({
            order: [[5, 'desc']],
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, 'All']],
            language: { search: '', searchPlaceholder: 'Search pick lists…' },
            columnDefs: [{ orderable: false, targets: [0, 7] }],
        });
    }
});
</script>
@endsection
