@extends('report.layouts.page')

@php
$reportTitle = 'Issued Items Report';
$reportSubtitle = 'Find issued items by item and store.';
$reportBadge = 'Reports';
$breadcrumbLabel = 'Issued Items — By Item';
$backUrl = route('IssuedItemsReport');
@endphp

@push('report-css')
<style>
    .select2-container .select2-selection--single { height: 42px !important; padding-top: 6px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px; }
</style>
@endpush

@section('filter')
<form method="POST" action="{{ route('report.searchbyIssueItemRep-report') }}">
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
<a href="{{ route('report.issueditem-print', ['department' => request()->department, 'item' => request()->item]) }}" target="_blank" class="btn-rp-print"><i class="bi bi-printer"></i> Print</a>
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
if (typeof jQuery !== 'undefined' && $.fn.select2) {
    $('.js-example-basic-single').select2({ placeholder: 'Select Item', allowClear: true, width: '100%' });
}
if ($.fn.DataTable && $('#reportTable tbody tr').length) {
    $('#reportTable').DataTable({ pageLength: 25, order: [] });
}
</script>
@endpush
