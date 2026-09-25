@extends('report.layouts.page')

@php
$reportTitle = 'Stock Report';
$reportSubtitle = 'Current on-hand quantities, values, and reorder status for a selected store.';
$reportBadge = 'Reports';
$breadcrumbLabel = 'Stock Report';

$itemCount = 0;
$totalQty = 0;
$totalValue = 0;
$lowCount = 0;
$outCount = 0;
$rows = collect();

if (isset($liststock) && $liststock->count() > 0) {
    $rows = $liststock->map(function ($item) use (&$itemCount, &$totalQty, &$totalValue, &$lowCount, &$outCount) {
        $qty = (float) $item->approveStock->sum('qty');
        $unitCost = (float) $item->approveStock->avg('amount');
        $lineTotal = (float) $item->approveStock->sum(function ($stock) {
            return $stock->qty * $stock->amount;
        });
        $reorder = (float) ($item->reorder_level ?? 0);

        if ($qty <= 0) {
            $status = 'out';
            $statusLabel = 'Out of stock';
            $outCount++;
        } elseif ($reorder > 0 && $qty <= $reorder) {
            $status = 'low';
            $statusLabel = 'Below reorder';
            $lowCount++;
        } else {
            $status = 'ok';
            $statusLabel = 'In stock';
        }

        $itemCount++;
        $totalQty += $qty;
        $totalValue += $lineTotal;

        return [
            'item' => $item,
            'qty' => $qty,
            'unitCost' => $unitCost,
            'lineTotal' => $lineTotal,
            'reorder' => $reorder,
            'status' => $status,
            'statusLabel' => $statusLabel,
        ];
    });
}
@endphp

@push('report-css')
<style>
    .ir-stats { margin-bottom: 1.5rem; }

    .ir-stat {
        border-radius: 1.125rem;
        padding: 1.25rem 1.4rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .ir-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 0.85rem;
    }

    .ir-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .ir-stat.items .ir-stat-icon { background: rgba(79, 70, 229, 0.12); color: #4f46e5; }
    .ir-stat.qty   .ir-stat-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .ir-stat.value .ir-stat-icon { background: rgba(5, 150, 105, 0.12); color: #059669; }
    .ir-stat.alert .ir-stat-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }

    .ir-stat-value {
        font-size: clamp(1.35rem, 3vw, 1.85rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 0.2rem;
    }

    .ir-stat.items .ir-stat-value { color: #4f46e5; }
    .ir-stat.qty   .ir-stat-value { color: #0d6efd; }
    .ir-stat.value .ir-stat-value { color: #059669; }
    .ir-stat.alert .ir-stat-value { color: #d97706; }

    .ir-stat-label { font-size: 0.8rem; color: #64748b; margin: 0; }

    .ir-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 0.75rem;
        font-weight: 700;
        background: #eef2ff;
        color: #4338ca;
        padding: 0.2rem 0.5rem;
        border-radius: 0.4rem;
        letter-spacing: 0.02em;
    }

    .ir-status {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.22rem 0.6rem;
        border-radius: 2rem;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .ir-status.ok  { background: #ecfdf5; color: #047857; }
    .ir-status.low { background: #fffbeb; color: #b45309; }
    .ir-status.out { background: #fef2f2; color: #b91c1c; }

    .ir-qty-low { color: #b45309; font-weight: 700; }
    .ir-qty-out { color: #b91c1c; font-weight: 700; }

    .ir-skip { width: 1%; white-space: nowrap; text-align: center; }
    .ir-skip .form-check-input { width: 1.05rem; height: 1.05rem; cursor: pointer; }
    .ir-print-hint { font-size: 0.78rem; color: #64748b; font-weight: 500; }
    .ir-print-actions { display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap; }
    .ir-bulk {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
        padding: 0.85rem 1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.85rem;
    }
    .ir-bulk-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-right: 0.25rem; }
    .ir-bulk-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border: none;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.42rem 0.85rem;
        border-radius: 2rem;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
    }
    .ir-bulk-btn.skip-zero { background: #dc2626; }
    .ir-bulk-btn.skip-zero:hover { background: #b91c1c; color: #fff; }
    .ir-bulk-btn.uncheck-zero { background: #059669; }
    .ir-bulk-btn.uncheck-zero:hover { background: #047857; color: #fff; }
    .ir-bulk-btn.skip-low { background: #d97706; }
    .ir-bulk-btn.skip-low:hover { background: #b45309; color: #fff; }
    .ir-bulk-btn.skip-all { background: #4f46e5; }
    .ir-bulk-btn.skip-all:hover { background: #4338ca; color: #fff; }
    .ir-bulk-btn.clear { background: #475569; }
    .ir-bulk-btn.clear:hover { background: #334155; color: #fff; }
    .ir-skip-count {
        margin-left: auto;
        font-size: 0.78rem;
        font-weight: 700;
        color: #334155;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 2rem;
        padding: 0.35rem 0.75rem;
    }

    #reportTable tbody tr.ir-skipped { opacity: 0.45; }
    #reportTable tbody tr.ir-skipped td { text-decoration: line-through; text-decoration-color: #94a3b8; }
</style>
@endpush

@if($rows->isNotEmpty())
@section('before-filter')
<div class="ir-stats">
    <div class="row g-3">
        <div class="col-sm-6 col-xl-3">
            <div class="ir-stat items">
                <div class="ir-stat-top">
                    <div class="ir-stat-icon"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="ir-stat-value">{{ number_format($itemCount) }}</div>
                <p class="ir-stat-label">Active items{{ !empty($selectedStore) ? ' — '.$selectedStore->name : '' }}</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="ir-stat qty">
                <div class="ir-stat-top">
                    <div class="ir-stat-icon"><i class="bi bi-stack"></i></div>
                </div>
                <div class="ir-stat-value">{{ number_format($totalQty) }}</div>
                <p class="ir-stat-label">Total on-hand quantity</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="ir-stat value">
                <div class="ir-stat-top">
                    <div class="ir-stat-icon"><i class="bi bi-cash-stack"></i></div>
                </div>
                <div class="ir-stat-value">GH₵ {{ number_format($totalValue, 2) }}</div>
                <p class="ir-stat-label">Estimated stock value</p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="ir-stat alert">
                <div class="ir-stat-top">
                    <div class="ir-stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                </div>
                <div class="ir-stat-value">{{ number_format($lowCount + $outCount) }}</div>
                <p class="ir-stat-label">{{ $lowCount }} below reorder · {{ $outCount }} out of stock</p>
            </div>
        </div>
    </div>
</div>
@endsection
@endif

@section('filter')
<form method="POST" action="{{ route('report.stock-report') }}">
    @csrf
    <div class="rp-filter-grid">
        @include('report.partials.store-select')
        <div><button type="submit" class="btn-rp-search w-100"><i class="bi bi-search"></i> Search</button></div>
    </div>
</form>
@endsection

@if($rows->isNotEmpty())
@section('results-actions')
<div class="ir-print-actions">
    <span class="ir-print-hint"><i class="bi bi-info-circle me-1"></i>Tick items you do not want to print</span>
    <form id="printStockForm" method="POST" action="{{ route('report.item-report-print', request()->department) }}" target="_blank" class="mb-0">
        @csrf
        <button type="submit" class="btn-rp-print mb-0"><i class="bi bi-printer"></i> Print</button>
    </form>
</div>
@endsection

@section('results')
<div class="ir-bulk">
    <span class="ir-bulk-label">Bulk skip</span>
    <button type="button" class="ir-bulk-btn skip-zero" data-skip-action="skip-zero">
        <i class="bi bi-dash-circle"></i> Skip zero balance
    </button>
    <button type="button" class="ir-bulk-btn uncheck-zero" data-skip-action="uncheck-zero">
        <i class="bi bi-check2-circle"></i> Uncheck zero balance
    </button>
    <button type="button" class="ir-bulk-btn skip-low" data-skip-action="skip-low">
        <i class="bi bi-exclamation-circle"></i> Skip below reorder
    </button>
    <button type="button" class="ir-bulk-btn skip-all" data-skip-action="skip-all">
        <i class="bi bi-check2-square"></i> Skip all
    </button>
    <button type="button" class="ir-bulk-btn clear" data-skip-action="clear">
        <i class="bi bi-x-square"></i> Uncheck all
    </button>
    <span class="ir-skip-count" id="irSkipCount"></span>
</div>
<div class="table-responsive">
    <table class="table rp-table mb-0" id="reportTable">
        <thead>
            <tr>
                <th class="ir-skip">Skip</th>
                <th>#</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>UoM</th>
                <th>Stock Level</th>
                <th>Unit Cost</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr data-item-id="{{ $row['item']->id }}" data-qty="{{ $row['qty'] }}" data-status="{{ $row['status'] }}">
                <td class="ir-skip">
                    <input type="checkbox" class="form-check-input ir-skip-item" value="{{ $row['item']->id }}" data-qty="{{ $row['qty'] }}" data-status="{{ $row['status'] }}" title="Do not print this item">
                </td>
                <td>{{ $loop->iteration }}</td>
                <td><span class="ir-code">{{ $row['item']->item_code }}</span></td>
                <td class="fw-semibold">{{ $row['item']->name }}</td>
                <td>{{ optional($row['item']->categoryname)->name ?? '—' }}</td>
                <td>{{ optional($row['item']->unitname)->name ?? '—' }}</td>
                <td class="{{ $row['status'] === 'out' ? 'ir-qty-out' : ($row['status'] === 'low' ? 'ir-qty-low' : 'fw-semibold') }}">
                    {{ rp_qty($row['qty']) }}
                </td>
                <td>{{ rp_amt($row['unitCost']) }}</td>
                <td>{{ rp_amt($row['lineTotal']) }}</td>
                <td>
                    <span class="ir-status {{ $row['status'] }}">
                        <i class="bi {{ $row['status'] === 'ok' ? 'bi-check-circle' : ($row['status'] === 'low' ? 'bi-exclamation-circle' : 'bi-x-circle') }}"></i>
                        {{ $row['statusLabel'] }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th class="text-end">TOTAL</th>
                <th>{{ rp_qty($totalQty) }}</th>
                <th></th>
                <th>{{ rp_amt($totalValue) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
@endif

@push('report-scripts')
<script>
(function () {
    var skipped = {};
    var $table = $('#reportTable');
    var dt = null;

    function allCheckboxes() {
        if (dt) {
            return $(dt.rows().nodes()).find('.ir-skip-item');
        }
        return $table.find('.ir-skip-item');
    }

    function setSkipped($checkbox, on) {
        var id = String($checkbox.val());
        $checkbox.prop('checked', on);
        if (on) {
            skipped[id] = true;
        } else {
            delete skipped[id];
        }
        $checkbox.closest('tr').toggleClass('ir-skipped', on);
    }

    function refreshCount() {
        var total = allCheckboxes().length;
        var skipCount = Object.keys(skipped).length;
        $('#irSkipCount').text(skipCount + ' skipped · ' + (total - skipCount) + ' will print');
    }

    function applyVisibleState() {
        allCheckboxes().each(function () {
            var on = !!skipped[String(this.value)];
            this.checked = on;
            $(this).closest('tr').toggleClass('ir-skipped', on);
        });
        refreshCount();
    }

    $table.on('change', '.ir-skip-item', function () {
        setSkipped($(this), this.checked);
        refreshCount();
    });

    $('[data-skip-action]').on('click', function () {
        var action = $(this).data('skip-action');
        allCheckboxes().each(function () {
            var $box = $(this);
            var qty = parseFloat($box.data('qty')) || 0;
            var status = String($box.data('status') || '');

            if (action === 'skip-zero' && qty <= 0) {
                setSkipped($box, true);
            } else if (action === 'uncheck-zero' && qty <= 0) {
                setSkipped($box, false);
            } else if (action === 'skip-low' && (status === 'low' || qty <= 0)) {
                setSkipped($box, true);
            } else if (action === 'skip-all') {
                setSkipped($box, true);
            } else if (action === 'clear') {
                setSkipped($box, false);
            }
        });
        refreshCount();
    });

    if ($.fn.DataTable && $table.find('tbody tr').length) {
        dt = $table.DataTable({
            pageLength: 25,
            order: [],
            columnDefs: [{ orderable: false, targets: [0, 1] }]
        });
        dt.on('draw', applyVisibleState);
    }

    refreshCount();

    $('#printStockForm').on('submit', function (e) {
        var ids = Object.keys(skipped);
        var total = allCheckboxes().length;
        if (ids.length && ids.length >= total) {
            e.preventDefault();
            alert('Select at least one item to print.');
            return;
        }

        $(this).find('input[name="exclude[]"]').remove();
        ids.forEach(function (id) {
            $('<input>', { type: 'hidden', name: 'exclude[]', value: id }).appendTo('#printStockForm');
        });
    });
})();
</script>
@endpush
