@php
    $pageName = 'stock';
    $subpageName = 'stock-approval';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .sa-page { padding: 0 0.5rem 2rem; }

    .sa-hero {
        background: linear-gradient(135deg, #92400e 0%, #d97706 55%, #f59e0b 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(217, 119, 6, 0.28);
    }

    .sa-hero::before,
    .sa-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .sa-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .sa-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .sa-hero-inner { position: relative; z-index: 1; }

    .sa-hero-badge {
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

    .sa-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .sa-hero p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 560px;
    }

    .sa-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .sa-hero .breadcrumb-item.active { color: #fff; }

    .btn-hero-link {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.35);
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        transition: background 0.15s ease, transform 0.15s ease;
    }

    .btn-hero-link:hover {
        background: rgba(255, 255, 255, 0.22);
        color: #fff;
        transform: translateY(-1px);
    }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: saStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.12s; }
    .stat-card:nth-child(3) { animation-delay: 0.19s; }
    .stat-card:nth-child(4) { animation-delay: 0.26s; }

    @keyframes saStatIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .stat-card.stores  .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }
    .stat-card.pending .stat-card-icon { background: rgba(234, 88, 12, 0.12); color: #ea580c; }
    .stat-card.qty     .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-card.value   .stat-card-icon { background: rgba(22, 163, 74, 0.12); color: #16a34a; }

    .stat-card-value {
        font-size: clamp(1.5rem, 4vw, 2.25rem);
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.stores  .stat-card-value { color: #d97706; }
    .stat-card.pending .stat-card-value { color: #ea580c; }
    .stat-card.qty     .stat-card-value { color: #0d6efd; }
    .stat-card.value   .stat-card-value { color: #16a34a; }

    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0 0 0.85rem; }

    .stat-bar-wrap {
        height: 4px;
        background: #f1f5f9;
        border-radius: 2rem;
        overflow: hidden;
    }

    .stat-bar-fill { height: 100%; border-radius: 2rem; }
    .stat-card.stores  .stat-bar-fill { background: #d97706; }
    .stat-card.pending .stat-bar-fill { background: #ea580c; }
    .stat-card.qty     .stat-bar-fill { background: #0d6efd; }
    .stat-card.value   .stat-bar-fill { background: #16a34a; }

    .stat-card-meta { font-size: 0.72rem; color: #94a3b8; margin-top: 0.4rem; }

    .stat-pct-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 2rem;
        background: rgba(217, 119, 6, 0.12);
        color: #d97706;
    }

    .sa-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: saStatIn 0.5s ease 0.32s both;
        background: #fff;
    }

    .sa-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .sa-table-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.15rem;
    }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(217, 119, 6, 0.08);
        color: #d97706;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .sa-search-wrap {
        position: relative;
        min-width: 220px;
        flex: 1;
        max-width: 320px;
    }

    .sa-search-wrap i {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }

    .sa-search-wrap input {
        padding-left: 2.35rem;
        border-radius: 2rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.875rem;
        min-height: 42px;
    }

    .sa-search-wrap input:focus {
        border-color: #d97706;
        box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.12);
    }

    #stockApprovalTable thead th {
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

    #stockApprovalTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #stockApprovalTable tbody tr:nth-child(even) { background: #fafafa; }
    #stockApprovalTable tbody tr:hover { background: #fffbeb !important; }

    .store-name-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .store-avatar {
        width: 40px;
        height: 40px;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, rgba(217, 119, 6, 0.15), rgba(245, 158, 11, 0.2));
        color: #d97706;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .store-title {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.1rem;
    }

    .store-sub {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.28rem 0.7rem;
        border-radius: 2rem;
        font-size: 0.78rem;
        font-weight: 700;
        background: rgba(234, 88, 12, 0.12);
        color: #c2410c;
    }

    .qty-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .value-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(22, 163, 74, 0.1);
        color: #16a34a;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .price-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .btn-review {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 1rem;
        border-radius: 2rem;
        border: none;
        background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        color: #fff;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 3px 12px rgba(217, 119, 6, 0.3);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-review:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 5px 16px rgba(217, 119, 6, 0.38);
    }

    .sa-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .sa-empty-visual {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(22, 163, 74, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 2rem;
        color: #16a34a;
    }

    .sa-empty h6 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.35rem;
    }
</style>
@endsection

@section('content')

<div class="container-fluid sa-page px-3 px-lg-4 mt-3">

    <div class="sa-hero">
        <div class="sa-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-7">
                    <div class="sa-hero-badge">
                        <i class="bi bi-check2-square"></i> Stock Approval
                    </div>
                    <h2>Review Pending Stock Entries</h2>
                    <p>
                        Approve store manager submissions to move inventory into the active stock ledger.
                        Only users assigned the Stock Approval screen can perform approvals.
                    </p>
                </div>
                <div class="col-lg-5 d-flex flex-column align-items-lg-end gap-3">
                    <nav aria-label="breadcrumb" class="d-none d-lg-block">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('stockEntry') }}" class="text-decoration-none">Stock Entry</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Stock Approval</li>
                        </ol>
                    </nav>
                    <a href="{{ route('stockEntry') }}" class="btn-hero-link">
                        <i class="bi bi-box-arrow-in-down"></i> Go to Stock Entry
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stores">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-shop"></i></div>
                    @if($topStore && $pendingCount > 0)
                        <span class="stat-pct-badge">{{ $topStorePct }}%</span>
                    @endif
                </div>
                <div class="stat-card-value">{{ number_format($storeCount) }}</div>
                <p class="stat-card-label">Stores with Pending</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $storeCount > 0 ? 100 : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">
                    @if($topStore)
                        Most: {{ $topStore->name }} ({{ $topStore->pending_count }})
                    @else
                        No stores awaiting review
                    @endif
                </p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card pending">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <div class="stat-card-value">{{ number_format($pendingCount) }}</div>
                <p class="stat-card-label">Pending Entries</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $pendingCount > 0 ? 100 : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">Awaiting your approval</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card qty">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="stat-card-value">{{ number_format($totalQty) }}</div>
                <p class="stat-card-label">Total Quantity</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $pendingCount > 0 ? 100 : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">Units across all pending entries</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card value">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-currency-dollar"></i></div>
                </div>
                <div class="stat-card-value">GH₵ {{ number_format($totalValue, 2) }}</div>
                <p class="stat-card-label">Estimated Value</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $pendingCount > 0 ? 100 : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">Qty &times; unit cost</p>
            </div>
        </div>
    </div>

    <div class="sa-table-card">
        <div class="sa-table-head">
            <div>
                <h5><i class="bi bi-building me-2" style="color:#d97706"></i>Pending by Store</h5>
                <small class="text-muted">Select a store to review individual entries and approve them into inventory</small>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($storeCount > 0)
                    <span class="record-count-badge">
                        <i class="bi bi-shop"></i> {{ number_format($storeCount) }} store{{ $storeCount !== 1 ? 's' : '' }}
                    </span>
                @endif
                <div class="sa-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="tableSearch" placeholder="Search stores…">
                </div>
            </div>
        </div>

        @if($liststock->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="stockApprovalTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Store</th>
                            <th>Date of Entry</th>
                            <th>Pending</th>
                            <th>Total Qty</th>
                            <th>Avg. Item Price</th>
                            <th>Est. Value</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($liststock as $store)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="store-name-cell">
                                        <div class="store-avatar"><i class="bi bi-shop"></i></div>
                                        <div>
                                            <div class="store-title">{{ $store->name }}</div>
                                            <div class="store-sub">
                                                {{ $store->pending_count === 1 ? '1 entry' : $store->pending_count . ' entries' }} waiting
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($store->latest_entry_at)
                                        {{ \Carbon\Carbon::parse($store->latest_entry_at)->format('M d, Y') }}
                                        <div class="store-sub">{{ \Carbon\Carbon::parse($store->latest_entry_at)->format('h:i A') }}</div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="count-badge">
                                        <i class="bi bi-hourglass-split"></i>
                                        {{ number_format($store->pending_count) }}
                                    </span>
                                </td>
                                <td><span class="qty-badge">{{ number_format($store->pending_qty) }}</span></td>
                                <td><span class="price-badge">GH₵ {{ number_format($store->pending_avg_price, 2) }}</span></td>
                                <td><span class="value-badge">GH₵ {{ number_format($store->pending_value, 2) }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('viewStockEntry', Crypt::encrypt($store->id)) }}" class="btn-review">
                                        <i class="bi bi-eye"></i> Review &amp; Approve
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="sa-empty">
                <div class="sa-empty-visual"><i class="bi bi-check-circle"></i></div>
                <h6>All caught up</h6>
                <p class="mb-0">There are no pending stock entries to approve right now.</p>
            </div>
        @endif
    </div>

</div>

@endsection

@section('scripts')
<script>
    document.getElementById('tableSearch')?.addEventListener('keyup', function () {
        const searchValue = this.value.toLowerCase();
        document.querySelectorAll('#stockApprovalTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(searchValue) ? '' : 'none';
        });
    });
</script>
@endsection
