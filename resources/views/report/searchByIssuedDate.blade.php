@extends('report.layouts.page')

@php
$reportTitle = 'Issued Items Report';
$reportSubtitle = 'Filter issued items by date range and store.';
$reportBadge = 'Reports';
$breadcrumbLabel = 'Issued Items — By Date';
$backUrl = route('IssuedItemsReport');
@endphp

@section('filter')
<form method="POST" action="{{ route('report.searchByIssuedDate-report') }}">
    @csrf
    <div class="rp-filter-grid">
        @include('report.partials.date-range')
        @include('report.partials.store-select')
        <div><button type="submit" class="btn-rp-search w-100"><i class="bi bi-search"></i> Search</button></div>
    </div>
</form>
@endsection

@if(isset($liststock) && $liststock->count() > 0)
@section('results-actions')
<a href="{{ route('report.issued-item-date-print', ['department' => request()->department, 'start_date' => request()->start_date, 'end_date' => request()->end_date]) }}" target="_blank" class="btn-rp-print"><i class="bi bi-printer"></i> Print</a>
@endsection

@section('results')
<div class="table-responsive">
    <table class="table rp-table mb-0" id="reportTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>UoM</th>
                <th>Issuing Store</th>
                <th>Receiving Store</th>
                <th>Invoice No</th>
                <th>Issued By</th>
                <th>Date of Issue</th>
                <th>Qty</th>
                <th>Unit Cost</th>
                <th>Amount</th>
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
                <td>{{ $lists->itemname->unitname->name }}</td>
                <td>{{ $lists->issuefrom->name }}</td>
                <td>{{ $lists->storename->name }}</td>
                <td>{{ $lists->invoice_number }}</td>
                <td>{{ $lists->staffname->name }}</td>
                <td>{{ Carbon\Carbon::parse($lists->created_at)->format('F jS, Y \a\t h:i A') }}</td>
                <td><b>{{ $lists->qty }}</b></td>
                <td><b>{{ $lists->amount }}</b></td>
                <td><b>{{ number_format($lineTotal, 2) }}</b></td>
            </tr>
            @endforeach
            <tr>
                <td colspan="9"><b style="float: right">TOTAL</b></td>
                <td><b>{{ $totalqty }}</b></td>
                <td><b></b></td>
                <td><b>{{ number_format($grandTotal, 2) }}</b></td>
            </tr>
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
