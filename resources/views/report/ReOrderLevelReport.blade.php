@extends('report.layouts.page')

@php
$reportTitle = 'Reorder Level Report';
$reportSubtitle = 'Items at or below reorder level for a selected store.';
$reportBadge = 'Reports';
$breadcrumbLabel = 'Reorder Level Report';
@endphp

@section('filter')
<form method="POST" action="{{ route('report.stockreorderlevel-report') }}">
    @csrf
    <div class="rp-filter-grid">
        @include('report.partials.store-select')
        <div><button type="submit" class="btn-rp-search w-100"><i class="bi bi-search"></i> Search</button></div>
    </div>
</form>
@endsection

@if(isset($liststock) && $liststock->count() > 0)
@section('results-actions')
<a href="{{ route('report.reorderlevel-stock-print', request()->department) }}" target="_blank" class="btn-rp-print"><i class="bi bi-printer"></i> Print</a>
@endsection

@section('results')
<div class="table-responsive">
    <table class="table rp-table mb-0" id="reportTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>UoM</th>
                <th>Store</th>
                <th>ReOrder Level</th>
                <th>Stock Level</th>
            </tr>
        </thead>
        <tbody>
            @if($liststock)
            @foreach($liststock as $lists)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $lists->item_code }}</td>
                <td>{{ $lists->name }}</td>
                <td>{{ $lists->categoryname->name }}</td>
                <td>{{ $lists->unitname->name }}</td>
                <td>{{ $lists->storename->name }}</td>
                <td><b>{{ $lists->reorder_level }}</b></td>
                <td><b>{{ $lists->total_qty ?? 0 }}</b></td>
            </tr>
            @endforeach
            @endif
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
