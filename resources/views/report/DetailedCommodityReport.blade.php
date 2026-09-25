@extends('report.layouts.page')

@php
$reportTitle = 'Detailed Commodity Report';
$reportSubtitle = 'Item-level commodity breakdown by store and date range.';
$reportBadge = 'Reports';
$breadcrumbLabel = 'Detailed Commodity Report';
@endphp

@section('filter')
<form method="POST" action="{{ route('report.searchCommodityDetails-report') }}">
    @csrf
    <div class="rp-filter-grid">
        @include('report.partials.date-range')
        @include('report.partials.store-select')
        <div><button type="submit" class="btn-rp-search w-100"><i class="bi bi-search"></i> Search</button></div>
    </div>
</form>
@endsection

@if(isset($reportData) && count($reportData) > 0)
@section('results-actions')
<a href="{{ route('report.commoditydetailedreport-print', ['start_date' => request()->start_date, 'end_date' => request()->end_date, 'department' => request()->department]) }}" target="_blank" class="btn-rp-print"><i class="bi bi-printer"></i> Print</a>
@endsection

@section('results')
<div class="table-responsive">
    <table class="table rp-table mb-0" id="reportTable">
        <thead>
            <tr class="rp-table-title">
                <th colspan="14">
                    DETAILED COMMODITIES REPORT<br/>
                    @if(request()->start_date && request()->end_date)
                        {{ \Carbon\Carbon::parse(request()->start_date)->format('F Y') }}
                    @endif
                </th>
            </tr>
            <tr>
                <th>ID</th>
                <th>Item Description</th>
                <th>UoM</th>
                <th>Price GHS</th>
                <th>Balance B/F (Qty)</th>
                <th>Balance B/F Value GHS</th>
                <th>Receipts (Qty)</th>
                <th>Receipt Value GHS</th>
                <th>Total Stock (Qty)</th>
                <th>Total Stock Value GHS</th>
                <th>Issued (Qty)</th>
                <th>Issued Value GHS</th>
                <th>Closing Balance (Qty)</th>
                <th>Closing Balance Value GHS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['item_description'] }}</td>
                <td>{{ $item['uom'] }}</td>
                <td class="text-end">{{ rp_amt($item['price']) }}</td>
                <td class="text-end">{{ rp_qty($item['balance_bf_qty']) }}</td>
                <td class="text-end">{{ rp_amt($item['balance_bf_value']) }}</td>
                <td class="text-end">{{ rp_qty($item['receipts_qty']) }}</td>
                <td class="text-end">{{ rp_amt($item['receipts_value']) }}</td>
                <td class="text-end">{{ rp_qty($item['total_stock_qty']) }}</td>
                <td class="text-end">{{ rp_amt($item['total_stock_value']) }}</td>
                <td class="text-end">{{ rp_qty($item['issued_qty']) }}</td>
                <td class="text-end">{{ rp_amt($item['issued_value']) }}</td>
                <td class="text-end">{{ rp_qty($item['closing_balance_qty']) }}</td>
                <td class="text-end">{{ rp_amt($item['closing_balance_value']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@endif

@push('report-scripts')
<script>
if ($.fn.DataTable && $('#reportTable tbody tr').length) {
    $('#reportTable').DataTable({ pageLength: 25, order: [] });
}
</script>
@endpush
