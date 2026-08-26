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

@push('report-css')
<style>
    .select2-container .select2-selection--single { height: 42px !important; padding-top: 6px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px; }
</style>
@endpush

@section('filter')
<form method="POST" action="{{ route('report.stockreceivedbyitem-report') }}">
    @csrf
    <div class="rp-filter-grid">
        <div>
            <label class="form-label">Item</label>
            <select class="js-example-basic-single form-select" name="item">
                <option value="" disabled {{ old('item', request('item')) ? '' : 'selected' }}>Choose item</option>
                @foreach ($getItemid as $listitems)
                    <option value="{{ $listitems->id }}" {{ (string) old('item', request('item')) === (string) $listitems->id ? 'selected' : '' }}>
                        {{ $listitems->name }}
                    </option>
                @endforeach
            </select>
            @error('item') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
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
                <td>{{ $lists->amount ?? '' }}</td>
                <td>{{ $lists->qty ?? '' }}</td>
                <td>{{ number_format($lineTotal, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="11"><b style="float: right">TOTAL</b></td>
                <td></td>
                <td><b>{{ $totalqty }}</b></td>
                <td><b>{{ number_format($grandTotal, 2) }}</b></td>
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
