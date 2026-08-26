@extends('report.layouts.page')

@php
$reportTitle = 'Stock Report';
$reportSubtitle = 'Current stock levels and values for a selected store.';
$reportBadge = 'Reports';
$breadcrumbLabel = 'Stock Report';
@endphp

@section('filter')
<form method="POST" action="{{ route('report.stock-report') }}">
    @csrf
    <div class="rp-filter-grid">
        @include('report.partials.store-select')
        <div><button type="submit" class="btn-rp-search w-100"><i class="bi bi-search"></i> Search</button></div>
    </div>
</form>
@endsection

@if(isset($liststock) && $liststock->count() > 0)
@section('results')
<div class="table-responsive">
    <table class="table rp-table mb-0" id="reportTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>ITEM CODE</th>
                <th>ITEM NAME</th>
                <th>UoM</th>
                <th>STOCK LEVEL</th>
                <th>UNIT COST</th>
                <th>TOTAL AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($liststock as $lists)
            @php
                $qty = $lists->approveStock->sum('qty');
                $unitCost = $lists->approveStock->avg('amount');
                $totalAmount = $lists->approveStock->sum(function($stock) {
                    return $stock->qty * $stock->amount;
                });
                $batchNumbers = $lists->approveStock->pluck('batch_number')->implode(', ');
                $expiryDates = $lists->approveStock->pluck('expiry_date')->implode(', ');
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $lists->item_code }}</td>
                <td>{{ $lists->name }}</td>
                <td>{{ $lists->unitname->name ?? '' }}</td>
                <td>{{ $qty ?? 0 }}</td>
                <td>{{ number_format($unitCost ?? 0, 2) }}</td>
                <td>{{ number_format($totalAmount ?? 0, 2) }}</td>
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
