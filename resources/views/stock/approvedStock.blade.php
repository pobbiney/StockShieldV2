@php
    $pageName = 'stock';
    $subpageName = 'approved-stock';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .as-page { padding: 0 0.5rem 2rem; }

    .as-hero {
        background: linear-gradient(135deg, #065f46 0%, #059669 55%, #34d399 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(5, 150, 105, 0.28);
    }

    .as-hero::before,
    .as-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .as-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .as-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .as-hero-inner { position: relative; z-index: 1; }

    .as-hero-badge {
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

    .as-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .as-hero p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 560px;
    }

    .as-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .as-hero .breadcrumb-item.active { color: #fff; }

    .btn-hero-link {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 2rem;
        border: none;
        background: #fff;
        color: #059669;
        font-weight: 700;
        font-size: 0.875rem;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-hero-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.16);
        color: #047857;
    }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: asStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.12s; }
    .stat-card:nth-child(3) { animation-delay: 0.19s; }
    .stat-card:nth-child(4) { animation-delay: 0.26s; }

    @keyframes asStatIn {
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

    .stat-card.approved .stat-card-icon { background: rgba(5, 150, 105, 0.12); color: #059669; }
    .stat-card.qty      .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-card.value    .stat-card-icon { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }
    .stat-card.unique   .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }

    .stat-card-value {
        font-size: clamp(1.5rem, 4vw, 2.25rem);
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.approved .stat-card-value { color: #059669; }
    .stat-card.qty      .stat-card-value { color: #0d6efd; }
    .stat-card.value    .stat-card-value { color: #7c3aed; }
    .stat-card.unique   .stat-card-value { color: #d97706; }

    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0 0 0.85rem; }

    .stat-bar-wrap {
        height: 4px;
        background: #f1f5f9;
        border-radius: 2rem;
        overflow: hidden;
    }

    .stat-bar-fill { height: 100%; border-radius: 2rem; }
    .stat-card.approved .stat-bar-fill { background: #059669; }
    .stat-card.qty      .stat-bar-fill { background: #0d6efd; }
    .stat-card.value    .stat-bar-fill { background: #7c3aed; }
    .stat-card.unique   .stat-bar-fill { background: #d97706; }

    .stat-card-meta { font-size: 0.72rem; color: #94a3b8; margin-top: 0.4rem; }

    .stat-pct-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 2rem;
        background: rgba(5, 150, 105, 0.12);
        color: #059669;
    }

    .dist-strip {
        border-radius: 1rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 1rem 1.25rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: asStatIn 0.5s ease 0.3s both;
    }

    .dist-strip-title {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        margin-bottom: 0.75rem;
    }

    .dist-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.78rem;
        font-weight: 600;
        color: #475569;
        margin: 0 0.35rem 0.35rem 0;
    }

    .dist-chip strong { color: #059669; }

    .as-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: asStatIn 0.5s ease 0.32s both;
        background: #fff;
    }

    .as-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .as-table-head h5 {
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
        background: rgba(5, 150, 105, 0.08);
        color: #059669;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .as-search-wrap {
        position: relative;
        min-width: 220px;
        flex: 1;
        max-width: 320px;
    }

    .as-search-wrap i {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }

    .as-search-wrap input {
        padding-left: 2.35rem;
        border-radius: 2rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.875rem;
        min-height: 42px;
    }

    .as-search-wrap input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
    }

    #approvedStockTable thead th {
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

    #approvedStockTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #approvedStockTable tbody tr:nth-child(even) { background: #fafafa; }
    #approvedStockTable tbody tr:hover { background: #ecfdf5 !important; }

    .code-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.78rem;
        font-weight: 600;
        font-family: monospace;
    }

    .batch-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(8, 145, 178, 0.1);
        color: #0891b2;
        font-size: 0.78rem;
        font-weight: 600;
        font-family: monospace;
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

    .price-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(22, 163, 74, 0.1);
        color: #16a34a;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        background: rgba(22, 163, 74, 0.12);
        color: #15803d;
    }

    .expiry-soon {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        font-size: 0.72rem;
        font-weight: 600;
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626;
    }

    .as-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .as-empty-visual {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(5, 150, 105, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 2rem;
        color: #059669;
    }

    .as-empty h6 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.35rem;
    }
</style>
@endsection

@section('content')

<div class="container-fluid as-page px-3 px-lg-4 mt-3">

    <div class="as-hero">
        <div class="as-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-7">
                    <div class="as-hero-badge">
                        <i class="bi bi-check-circle"></i> Approved Stock
                    </div>
                    <h2>Active Inventory</h2>
                    <p>
                        Stock that has been approved and is available for issuing, requisitions, and reports.
                        @if($expiringSoon > 0)
                            <strong>{{ number_format($expiringSoon) }}</strong> {{ Str::plural('batch', $expiringSoon) }} expiring within 3 months.
                        @endif
                    </p>
                </div>
                <div class="col-lg-5 d-flex flex-column align-items-lg-end gap-3">
                    <nav aria-label="breadcrumb" class="d-none d-lg-block">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('stockEntry') }}" class="text-decoration-none">Stock</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Approved Stock</li>
                        </ol>
                    </nav>
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <a href="{{ route('pendingStock') }}" class="btn-hero-link" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);">
                            <i class="bi bi-hourglass"></i> Pending Stock
                        </a>
                        <a href="{{ route('IssueItem') }}" class="btn-hero-link">
                            <i class="bi bi-box-arrow-right"></i> Issue Items
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card approved">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-check2-all"></i></div>
                </div>
                <div class="stat-card-value">{{ number_format($approvedCount) }}</div>
                <p class="stat-card-label">Approved Batches</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $approvedCount > 0 ? 100 : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">Ready for issue</p>
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
                    <div class="stat-bar-fill" style="width:{{ $approvedCount > 0 ? 100 : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">Units in inventory</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card value">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-cash-stack"></i></div>
                </div>
                <div class="stat-card-value">GH₵ {{ number_format($totalValue, 2) }}</div>
                <p class="stat-card-label">Inventory Value</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $approvedCount > 0 ? 100 : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">Qty &times; item price</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card unique">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-tags"></i></div>
                    @if($approvedCount > 0)
                        <span class="stat-pct-badge">{{ $uniquePct }}%</span>
                    @endif
                </div>
                <div class="stat-card-value">{{ number_format($uniqueItems) }}</div>
                <p class="stat-card-label">Unique Items</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $uniquePct }}%"></div>
                </div>
                <p class="stat-card-meta">Different products in stock</p>
            </div>
        </div>
    </div>

    @if($storeBreakdown->count() > 1)
        <div class="dist-strip">
            <div class="dist-strip-title"><i class="bi bi-shop me-1"></i> Approved by Store</div>
            @foreach($storeBreakdown->sortByDesc('count') as $store)
                <span class="dist-chip">
                    {{ $store['name'] }} <strong>{{ $store['count'] }}</strong>
                </span>
            @endforeach
        </div>
    @endif

    <div class="as-table-card">
        <div class="as-table-head">
            <div>
                <h5><i class="bi bi-table me-2" style="color:#059669"></i>Approved Inventory</h5>
                <small class="text-muted">Live stock available for issuing and requisitions</small>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($approvedCount > 0)
                    <span class="record-count-badge">
                        <i class="bi bi-collection"></i> {{ number_format($approvedCount) }} batch{{ $approvedCount !== 1 ? 'es' : '' }}
                    </span>
                @endif
                <div class="as-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="tableSearch" placeholder="Search inventory…">
                </div>
            </div>
        </div>

        @if($liststock->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="approvedStockTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Batch</th>
                            <th>Expiry</th>
                            <th>Qty</th>
                            <th>Item Price</th>
                            <th>Total</th>
                            <th>PO Ref</th>
                            <th>Supplier</th>
                            <th>Store</th>
                            <th>Approved On</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($liststock as $lists)
                            @php
                                $itemCode = optional($lists->itemcode)->item_code ?? '—';
                                $itemName = optional($lists->itemname)->name ?? 'Unknown';
                                $supplierName = optional($lists->supname)->company
                                    ?? optional($lists->supname)->supplier
                                    ?? '—';
                                $storeName = optional($lists->storename)->name ?? '—';
                                $lineTotal = (float) $lists->qty * (float) $lists->amount;
                                $isExpiringSoon = $lists->expiry_date
                                    && \Carbon\Carbon::parse($lists->expiry_date)->lte(now()->addMonths(3));
                                $approvedOn = $lists->received_at ?? $lists->created_at;
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $itemName }}</div>
                                    <span class="code-badge">{{ $itemCode }}</span>
                                </td>
                                <td><span class="batch-badge">{{ $lists->batch_number ?: '—' }}</span></td>
                                <td>
                                    @if($lists->expiry_date)
                                        {{ \Carbon\Carbon::parse($lists->expiry_date)->format('M d, Y') }}
                                        @if($isExpiringSoon)
                                            <br><span class="expiry-soon"><i class="bi bi-exclamation-triangle"></i> Soon</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td><span class="qty-badge">{{ number_format($lists->qty) }}</span></td>
                                <td><span class="price-badge">GH₵ {{ number_format((float) $lists->amount, 2) }}</span></td>
                                <td><span class="price-badge">GH₵ {{ number_format($lineTotal, 2) }}</span></td>
                                <td>{{ $lists->purchase_order ?: '—' }}</td>
                                <td>{{ $supplierName }}</td>
                                <td>{{ $storeName }}</td>
                                <td>{{ $approvedOn ? $approvedOn->format('M d, Y h:i A') : '—' }}</td>
                                <td><span class="status-badge"><i class="bi bi-check-circle"></i> Approved</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="as-empty">
                <div class="as-empty-visual"><i class="bi bi-inbox"></i></div>
                <h6>No approved stock yet</h6>
                <p class="mb-3">Approved inventory will appear here once stock entries are accepted.</p>
                <a href="{{ route('stockEntry') }}" class="btn-hero-link" style="display:inline-flex;">
                    <i class="bi bi-box-arrow-in-down"></i> Go to Stock Entry
                </a>
            </div>
        @endif
    </div>

</div>

@endsection

@section('scripts')
<script>
    document.getElementById('tableSearch')?.addEventListener('keyup', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#approvedStockTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endsection
