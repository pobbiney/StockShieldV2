@extends('report.layouts.page')

@php
$reportTitle = 'Received Items Report';
$reportSubtitle = 'View received stock entries filtered by item and store.';
$reportBadge = 'Reports';
$breadcrumbLabel = 'Received Items — By Item';
$navLinks = [
    ['route' => 'ReceivedStocks', 'label' => 'By Store', 'icon' => 'shop'],
    ['route' => 'ReceivedStockByDate', 'label' => 'By Date', 'icon' => 'calendar-date'],
    ['route' => 'searchByItem', 'label' => 'By Item', 'icon' => 'box-seam'],
    ['route' => 'searchByItemDate', 'label' => 'Item + Date', 'icon' => 'calendar-range'],
];
$activeRoute = 'searchByItem';
@endphp

@section('filter')
<form method="POST" action="{{ route('report.stockreceivedbyitem-report') }}">
    @csrf
    <div class="rp-filter-grid">
        @include('report.partials.item-select')
        @include('report.partials.store-select')
        <div><button type="submit" class="btn-rp-search w-100"><i class="bi bi-search"></i> Search</button></div>
    </div>
</form>
@endsection

@if(isset($liststock) && $liststock->count() > 0)
@section('results-actions')
<a href="{{ route('report.received-stock-byitem-print', ['department' => request()->department, 'item' => request()->item]) }}" target="_blank" class="btn-rp-print"><i class="bi bi-printer"></i> Print</a>
@endsection

@section('results')
<div class="table-responsive">
    <table class="table rp-table mb-0" id="reportTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>ITEM CODE</th>
                <th>ITEM NAME</th>
                <th>UoM</th>
                <th>BATCH NO</th>
                <th>VENDOR</th>
                <th>PO</th>
                <th>WAYBILL</th>
                <th>EXPIRY DATE</th>
                <th>CONTRACT REF.</th>
                <th>RECEIVED BY</th>
                <th>UNIT COST</th>
                <th>QTY</th>
                <th>TOTAL AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; $totalqty = 0; @endphp
            @foreach($liststock as $lists)
            @php
                $lineTotal = $lists->amount * $lists->qty;
                $grandTotal += $lineTotal;
                $totalqty += $lists->qty;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $lists->itemname->item_code }}</td>
                <td>{{ $lists->itemname->name }}</td>
                <td>{{ $lists->itemname->unitname->name ?? '' }}</td>
                <td>{{ $lists->batch_number ?? '' }}</td>
                <td>{{ $lists->supname->company ?? '' }}</td>
                <td>{{ $lists->purchase_order ?? '' }}</td>
                <td>{{ $lists->waybill }}</td>
                <td>{{ $lists->expiry_date }}</td>
                <td>{{ $lists->award_letter }}</td>
                <td>{{ $lists->staffname->name }}</td>
                <td>{{ rp_amt($lists->amount) }}</td>
                <td>{{ rp_qty($lists->qty) }}</td>
                <td>{{ rp_amt($lineTotal) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="11"><b style="float: right">TOTAL</b></td>
                <td></td>
                <td><b>{{ rp_qty($totalqty) }}</b></td>
                <td><b>{{ rp_amt($grandTotal) }}</b></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
@endif

@push('report-scripts')
<script>
if (typeof jQuery !== 'undefined' && $.fn.select2) {
    $('.js-example-basic-single').select2({ placeholder: 'Select Item', allowClear: true, width: '100%' });
}
if ($.fn.DataTable && $('#reportTable tbody tr').length) {
    $('#reportTable').DataTable({ pageLength: 25, order: [] });
}
</script>
@endpush
