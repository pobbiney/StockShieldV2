@php
    $pageName = 'stock';
    $subpageName = 'pending-stock';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .ps-page { padding: 0 0.5rem 2rem; }

    .ps-hero {
        background: linear-gradient(135deg, #5b21b6 0%, #7c3aed 55%, #a78bfa 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(124, 58, 237, 0.32);
    }

    .ps-hero::before,
    .ps-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .ps-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .ps-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .ps-hero-inner { position: relative; z-index: 1; }

    .ps-hero-badge {
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

    .ps-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .ps-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 560px;
    }

    .ps-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .ps-hero .breadcrumb-item.active { color: #fff; }

    .btn-hero-link {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 2rem;
        border: none;
        background: #fff;
        color: #7c3aed;
        font-weight: 700;
        font-size: 0.875rem;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-hero-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.16);
        color: #6d28d9;
    }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: psStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.12s; }
    .stat-card:nth-child(3) { animation-delay: 0.19s; }
    .stat-card:nth-child(4) { animation-delay: 0.26s; }

    @keyframes psStatIn {
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

    .stat-card.pending .stat-card-icon { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }
    .stat-card.qty     .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }
    .stat-card.value   .stat-card-icon { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
    .stat-card.unique  .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }

    .stat-card-value {
        font-size: clamp(1.5rem, 4vw, 2.25rem);
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.pending .stat-card-value { color: #7c3aed; }
    .stat-card.qty     .stat-card-value { color: #d97706; }
    .stat-card.value   .stat-card-value { color: #16a34a; }
    .stat-card.unique  .stat-card-value { color: #0d6efd; }

    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0 0 0.85rem; }

    .stat-bar-wrap {
        height: 4px;
        background: #f1f5f9;
        border-radius: 2rem;
        overflow: hidden;
    }

    .stat-bar-fill { height: 100%; border-radius: 2rem; }
    .stat-card.pending .stat-bar-fill { background: #7c3aed; }
    .stat-card.qty     .stat-bar-fill { background: #d97706; }
    .stat-card.value   .stat-bar-fill { background: #16a34a; }
    .stat-card.unique  .stat-bar-fill { background: #0d6efd; }

    .stat-card-meta { font-size: 0.72rem; color: #94a3b8; margin-top: 0.4rem; }

    .stat-pct-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 2rem;
        background: rgba(124, 58, 237, 0.12);
        color: #7c3aed;
    }

    .dist-strip {
        border-radius: 1rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 1rem 1.25rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: psStatIn 0.5s ease 0.3s both;
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

    .dist-chip strong { color: #7c3aed; }

    .ps-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: psStatIn 0.5s ease 0.32s both;
        background: #fff;
    }

    .ps-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .ps-table-head h5 {
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
        background: rgba(124, 58, 237, 0.08);
        color: #7c3aed;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .ps-search-wrap {
        position: relative;
        min-width: 220px;
        flex: 1;
        max-width: 320px;
    }

    .ps-search-wrap i {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }

    .ps-search-wrap input {
        padding-left: 2.35rem;
        border-radius: 2rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.875rem;
        min-height: 42px;
    }

    .ps-search-wrap input:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
    }

    #pendingStockTable thead th {
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

    #pendingStockTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #pendingStockTable tbody tr:nth-child(even) { background: #fafafa; }
    #pendingStockTable tbody tr:hover { background: #f5f3ff !important; }

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
        background: rgba(217, 119, 6, 0.1);
        color: #d97706;
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
        background: rgba(124, 58, 237, 0.12);
        color: #6d28d9;
    }

    .btn-action {
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }

    .btn-action.edit { color: #7c3aed; }
    .btn-action.edit:hover { background: #7c3aed; color: #fff; transform: scale(1.08); }
    .btn-action.delete { color: #dc3545; }
    .btn-action.delete:hover { background: #dc3545; color: #fff; transform: scale(1.08); }

    .ps-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .ps-empty-visual {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(124, 58, 237, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 2rem;
        color: #7c3aed;
    }

    .ps-empty h6 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.35rem;
    }
</style>
@endsection

@section('content')

<div class="container-fluid ps-page px-3 px-lg-4 mt-3">

    <div class="ps-hero">
        <div class="ps-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-7">
                    <div class="ps-hero-badge">
                        <i class="bi bi-hourglass-split"></i> Pending Stock
                    </div>
                    <h2>Awaiting Approval</h2>
                    <p>
                        All stock entries awaiting approval in your store scope — central and satellite.
                        You can edit or delete any line that is still pending and not yet approved.
                    </p>
                </div>
                <div class="col-lg-5 d-flex flex-column align-items-lg-end gap-3">
                    <nav aria-label="breadcrumb" class="d-none d-lg-block">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('stockEntry') }}" class="text-decoration-none">Stock</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pending Stock</li>
                        </ol>
                    </nav>
                    <a href="{{ route('stockEntry') }}" class="btn-hero-link">
                        <i class="bi bi-box-arrow-in-down"></i> New Stock Entry
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
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
                <p class="stat-card-meta">Not yet in inventory</p>
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
                <p class="stat-card-meta">Units awaiting approval</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card value">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-cash-stack"></i></div>
                </div>
                <div class="stat-card-value">GH₵ {{ number_format($totalValue, 2) }}</div>
                <p class="stat-card-label">Estimated Value</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $pendingCount > 0 ? 100 : 0 }}%"></div>
                </div>
                <p class="stat-card-meta">Qty &times; item price</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card unique">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-tags"></i></div>
                    @if($pendingCount > 0)
                        <span class="stat-pct-badge">{{ $uniquePct }}%</span>
                    @endif
                </div>
                <div class="stat-card-value">{{ number_format($uniqueItems) }}</div>
                <p class="stat-card-label">Unique Items</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $uniquePct }}%"></div>
                </div>
                <p class="stat-card-meta">Different products pending</p>
            </div>
        </div>
    </div>

    @if($storeBreakdown->count() > 1)
        <div class="dist-strip">
            <div class="dist-strip-title"><i class="bi bi-shop me-1"></i> Pending by Store</div>
            @foreach($storeBreakdown->sortByDesc('count') as $store)
                <span class="dist-chip">
                    {{ $store['name'] }} <strong>{{ $store['count'] }}</strong>
                </span>
            @endforeach
        </div>
    @endif

    <div class="ps-table-card">
        <div class="ps-table-head">
            <div>
                <h5><i class="bi bi-list-ul me-2" style="color:#7c3aed"></i>Pending Stock List</h5>
                <small class="text-muted">Edit or delete entries while they remain pending</small>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($pendingCount > 0)
                    <span class="record-count-badge">
                        <i class="bi bi-collection"></i> {{ number_format($pendingCount) }} record{{ $pendingCount !== 1 ? 's' : '' }}
                    </span>
                @endif
                <div class="ps-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="tableSearch" placeholder="Search entries…">
                </div>
            </div>
        </div>

        @if($liststock->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="pendingStockTable">
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
                            <th>Status</th>
                            <th class="text-end">Actions</th>
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
                                $entrySource = $lists->entry_source ?? 'central';
                                $stockIdUrl = route('stock-id', ['id' => $lists->id, 'source' => $entrySource]);
                                $deleteUrl = url('stockEntry/'.$lists->id.'/delete?source='.$entrySource);
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $itemName }}</div>
                                    <span class="code-badge">{{ $itemCode }}</span>
                                </td>
                                <td><span class="batch-badge">{{ $lists->batch_number ?: '—' }}</span></td>
                                <td>{{ $lists->expiry_date ?: '—' }}</td>
                                <td><span class="qty-badge">{{ number_format($lists->qty) }}</span></td>
                                <td><span class="price-badge">GH₵ {{ number_format((float) $lists->amount, 2) }}</span></td>
                                <td><span class="price-badge">GH₵ {{ number_format($lineTotal, 2) }}</span></td>
                                <td>{{ $lists->purchase_order ?: '—' }}</td>
                                <td>{{ $supplierName }}</td>
                                <td>{{ $storeName }}</td>
                                <td><span class="status-badge"><i class="bi bi-clock"></i> Pending</span></td>
                                <td class="text-end text-nowrap">
                                    <button type="button"
                                            class="btn-action edit showmodal me-1"
                                            title="Edit pending entry"
                                            data-url="{{ $stockIdUrl }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a class="btn-action delete"
                                       title="Delete pending entry"
                                       onclick="return confirm('Delete this pending entry for {{ addslashes($itemName) }}? It has not been approved yet.')"
                                       href="{{ $deleteUrl }}">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="ps-empty">
                <div class="ps-empty-visual"><i class="bi bi-inbox"></i></div>
                <h6>No pending stock entries</h6>
                <p class="mb-3">Submit a stock entry and it will appear here until approved.</p>
                <a href="{{ route('stockEntry') }}" class="btn-hero-link" style="display:inline-flex;">
                    <i class="bi bi-plus-lg"></i> Add Stock Entry
                </a>
            </div>
        @endif
    </div>

</div>

@include('stock.edit-stock-modal')

@endsection

@section('scripts')
<script>
    document.getElementById('tableSearch')?.addEventListener('keyup', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#pendingStockTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    $(document).ready(function () {
        $('body').on('click', '.showmodal', function () {
            const userUrl = $(this).data('url');

            $.get(userUrl, function (data) {
                $('#stockID').val(data.id);
                $('#skbatchnumber').val(data.batch_number);
                $('#skitem').val(data.item_id);
                $('#skmanufactured').val(data.manufacturing_date);
                $('#skexpiryd').val(data.expiry_date);
                $('#sksupplier').val(data.supplier_id);
                $('#skpurchaseorder').val(data.purchase_order);
                $('#skwaybill').val(data.waybill);
                $('#skqty').val(data.qty);
                $('#skaward').val(data.award_letter);
                $('#skamount').val(data.amount);
                $('#skstore').val(data.store_id);
                $('#skbarcode').val(data.barcode);

                bootstrap.Modal.getOrCreateInstance(document.getElementById('xlmodal')).show();
            });
        });
    });
</script>
@endsection
