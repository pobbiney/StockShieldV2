@php
    $pageName = 'stock';
    $subpageName = 'reverse-entry';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .rv-page { padding: 0 0.5rem 2rem; }

    .rv-hero {
        background: linear-gradient(135deg, #7c2d12 0%, #c2410c 55%, #fb923c 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(194, 65, 12, 0.28);
    }

    .rv-hero::before,
    .rv-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .rv-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .rv-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .rv-hero-inner { position: relative; z-index: 1; }

    .rv-hero-badge {
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

    .rv-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .rv-hero p { color: rgba(255, 255, 255, 0.88); font-size: 0.9rem; margin-bottom: 0; max-width: 620px; }
    .rv-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .rv-hero .breadcrumb-item.active { color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: rvStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }
    .stat-card:nth-child(4) { animation-delay: 0.2s; }

    @keyframes rvStatIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card-icon {
        width: 48px; height: 48px; border-radius: 0.875rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; margin-bottom: 1rem;
    }

    .stat-card.batch .stat-card-icon { background: rgba(194, 65, 12, 0.12); color: #c2410c; }
    .stat-card.items .stat-card-icon { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .stat-card.qty   .stat-card-icon { background: rgba(16, 185, 129, 0.12); color: #059669; }
    .stat-card.pending .stat-card-icon { background: rgba(234, 88, 12, 0.12); color: #ea580c; }

    .stat-card-value { font-size: 2rem; font-weight: 800; line-height: 1; margin-bottom: 0.2rem; }
    .stat-card.batch .stat-card-value { color: #c2410c; }
    .stat-card.items .stat-card-value { color: #2563eb; }
    .stat-card.qty   .stat-card-value { color: #059669; }
    .stat-card.pending .stat-card-value { color: #ea580c; }
    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0; }

    .rv-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: rvStatIn 0.5s ease 0.2s both;
    }

    .rv-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .rv-table-head h5 { font-family: "SUSE", sans-serif; font-weight: 700; font-size: 1rem; margin: 0; }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(194, 65, 12, 0.1);
        color: #c2410c;
        font-size: 0.78rem;
        font-weight: 600;
    }

    #reverseStockTable thead th {
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

    #reverseStockTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #reverseStockTable tbody tr:nth-child(even) { background: #fafafa; }
    #reverseStockTable tbody tr:hover { background: #fff7ed !important; }

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
        background: rgba(194, 65, 12, 0.1);
        color: #c2410c;
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

    .btn-reverse {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.9rem;
        border-radius: 0.625rem;
        border: none;
        background: #c2410c;
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-reverse:hover { background: #9a3412; color: #fff; }

    .rv-empty {
        text-align: center;
        padding: 3.5rem 2rem;
        color: #64748b;
    }

    .rv-empty-visual {
        width: 72px; height: 72px; border-radius: 50%;
        background: rgba(194, 65, 12, 0.1);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem; font-size: 1.75rem; color: #c2410c;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        padding: 0.4rem 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid rv-page px-3 px-lg-4 mt-3">
    <div class="rv-hero">
        <div class="rv-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="rv-hero-badge">
                        <i class="bi bi-arrow-counterclockwise"></i> Reverse Entry
                    </div>
                    <h2>Correct Wrongly Entered Batches</h2>
                    <p>
                        Submit a reversal request by batch number to fix incorrect stock entries. Changes apply to both stock entry and approved inventory after approval.
                        @if($activeStore ?? null)
                            <span class="d-block mt-1 opacity-75"><i class="bi bi-shop me-1"></i>Store: {{ $activeStore->name }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Reverse Entry</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card batch">
                <div class="stat-card-icon"><i class="bi bi-layers"></i></div>
                <div class="stat-card-value">{{ number_format($totalBatches ?? 0) }}</div>
                <p class="stat-card-label">Reversible Batches</p>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card items">
                <div class="stat-card-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-card-value">{{ number_format($uniqueItems ?? 0) }}</div>
                <p class="stat-card-label">Unique Items</p>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card qty">
                <div class="stat-card-icon"><i class="bi bi-stack"></i></div>
                <div class="stat-card-value">{{ number_format($totalQty ?? 0) }}</div>
                <p class="stat-card-label">Total Units Available</p>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card pending">
                <div class="stat-card-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-card-value">{{ number_format($pendingReversals ?? 0) }}</div>
                <p class="stat-card-label">Pending Reversals</p>
            </div>
        </div>
    </div>

    <div class="rv-table-card mb-5">
        <div class="rv-table-head">
            <div>
                <h5><i class="bi bi-inboxes me-1 text-warning"></i> Approved Stock — Reversible Batches</h5>
                <span class="record-count-badge">
                    <i class="bi bi-check-circle"></i>
                    {{ $totalBatches ?? 0 }} batch{{ ($totalBatches ?? 0) !== 1 ? 'es' : '' }}
                </span>
            </div>
        </div>

        @if(($liststock ?? collect())->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 w-100" id="reverseStockTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Batch</th>
                            <th>Expiry</th>
                            <th>Qty</th>
                            <th>P.O.</th>
                            <th>Vendor</th>
                            <th>Received</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($liststock as $lists)
                            @php
                                $isExpiringSoon = $lists->expiry_date
                                    && \Carbon\Carbon::parse($lists->expiry_date)->lte(now()->addMonths(3));
                            @endphp
                            <tr>
                                <td class="text-secondary">{{ $loop->iteration }}</td>
                                <td><span class="item-code-badge">{{ $lists->itemcode->item_code ?? '—' }}</span></td>
                                <td><strong>{{ $lists->itemname->name ?? '—' }}</strong></td>
                                <td><span class="batch-badge">{{ $lists->batch_number ?? '—' }}</span></td>
                                <td>
                                    @if($lists->expiry_date)
                                        {{ \Carbon\Carbon::parse($lists->expiry_date)->format('M d, Y') }}
                                        @if($isExpiringSoon)
                                            <div class="text-danger small fw-semibold"><i class="bi bi-exclamation-triangle"></i> Soon</div>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td><span class="qty-badge">{{ number_format($lists->qty) }}</span></td>
                                <td>{{ $lists->purchase_order ?: '—' }}</td>
                                <td>{{ $lists->supname->company ?? '—' }}</td>
                                <td>
                                    {{ $lists->created_at?->format('M d, Y') ?? '—' }}
                                    <div class="text-muted small">{{ $lists->created_at?->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn-reverse showmodal"
                                            data-url="{{ route('reverse-entry-id', $lists->id) }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#reverseModal">
                                        <i class="bi bi-arrow-counterclockwise"></i> Reverse
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rv-empty">
                <div class="rv-empty-visual"><i class="bi bi-inbox"></i></div>
                <h5 class="fw-semibold text-dark">No reversible stock</h5>
                <p class="mb-0">Approved batches with available quantity will appear here.</p>
            </div>
        @endif
    </div>
</div>

@include('stock.reverse-entry-modal')
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    if ($.fn.DataTable && $('#reverseStockTable tbody tr').length) {
        $('#reverseStockTable').DataTable({
            order: [[8, 'desc']],
            pageLength: 25,
            language: { emptyTable: 'No reversible stock found.' }
        });
    }

    $('body').on('click', '.showmodal', function () {
        var userUrl = $(this).data('url');

        $.get(userUrl, function (data) {
            $('#reverse_batch_number').val(data.batch_number);
            $('#reverse_max_qty').val(data.qty);
            $('#reverse_qty').attr('max', data.qty).val('');
            $('#reverse_item_name').text(data.item_name || 'Item');
            $('#reverse_item_code').text(data.item_code || '—');
            $('#reverse_batch_label').text(data.batch_number || '—');
            $('#reverse_available_qty').text(Number(data.qty || 0).toLocaleString());
            $('#reverse_store_name').text(data.store_name || '—');
            $('#reverse_expiry_label').text(data.expiry_date
                ? new Date(data.expiry_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
                : '—');
            $('#reverse_type').val('');
            $('#reverse_qty_wrap').addClass('d-none');
            $('#reverseForm textarea[name="reason"]').val('');
        });
    });

    $('#reverse_type').on('change', function () {
        var type = $(this).val();
        var maxQty = parseInt($('#reverse_max_qty').val(), 10) || 0;

        if (type === 'partial') {
            $('#reverse_qty_wrap').removeClass('d-none');
            $('#reverse_qty').prop('required', true).val('');
        } else {
            $('#reverse_qty_wrap').addClass('d-none');
            $('#reverse_qty').prop('required', false);

            if (type === 'full' || type === 'delete') {
                $('#reverse_qty').val(maxQty);
            }
        }
    });

    $('#reverse_qty').on('input', function () {
        var max = parseInt($('#reverse_max_qty').val(), 10) || 0;
        var val = parseInt($(this).val(), 10) || 0;

        if (val > max) {
            $(this).val(max);
        }
    });
});
</script>
@endsection
