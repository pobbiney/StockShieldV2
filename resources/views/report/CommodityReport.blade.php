@extends('report.layouts.page')

@php
$reportTitle = 'Commodity Report';
$reportSubtitle = 'Summary of commodity values across all stores for a date range.';
$reportBadge = 'Reports';
$breadcrumbLabel = 'Commodity Report';
@endphp

@section('filter')
<form method="POST" action="{{ route('report.searchCommodity-report') }}">
    @csrf
    <div class="rp-filter-grid">
        @include('report.partials.date-range')
        <div><button type="submit" class="btn-rp-search w-100"><i class="bi bi-search"></i> Search</button></div>
    </div>
</form>
@endsection

@if(isset($reportData) && count($reportData) > 0)
@section('results-actions')
<a href="{{ route('report.commodityreport-print', ['start_date' => request()->start_date, 'end_date' => request()->end_date]) }}" target="_blank" class="btn-rp-print"><i class="bi bi-printer"></i> Print</a>
@endsection

@section('results')
<div class="table-responsive">
    <table class="table rp-table mb-0" id="reportTable">
        <thead>
            <tr class="rp-table-title">
                <th colspan="7">
                    SUMMARY OF COMMODITIES REPORT<br/>
                    @if(request()->start_date && request()->end_date)
                        {{ \Carbon\Carbon::parse(request()->start_date)->format('F Y') }}
                    @endif
                </th>
            </tr>
            <tr>
                <th>ID</th>
                <th>Store Location</th>
                <th>Balance B/F Value GHS</th>
                <th>Receipt Value GHS</th>
                <th>Total Stock Value GHS</th>
                <th>Issued Value GHS</th>
                <th>Closing Balance Value GHS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $store)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $store['store_name'] }}</td>
                <td class="text-end">{{ $store['balance_bf_value'] }}</td>
                <td class="text-end">{{ $store['receipts_value'] }}</td>
                <td class="text-end">{{ $store['total_stock_value'] }}</td>
                <td class="text-end">{{ $store['issued_value'] }}</td>
                <td class="text-end">{{ $store['closing_balance_value'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-danger"><strong>No store data available</strong></td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-end">GRAND TOTAL:</th>
                <th class="text-end"><b>{{ number_format($totalBF, 2) }}</b></th>
                <th class="text-end"><b>{{ number_format($totalREc, 2) }}</b></th>
                <th class="text-end"><b>{{ number_format($totalStockval, 2) }}</b></th>
                <th class="text-end"><b>{{ number_format($totalIssVal, 2) }}</b></th>
                <th class="text-end"><b>{{ number_format($totalClVal, 2) }}</b></th>
            </tr>
        </tfoot>
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
