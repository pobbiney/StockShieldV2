@extends('report.layouts.page')

@php
$reportTitle = 'Issued Items Report';
$reportSubtitle = 'Choose a search type to view issued items by store, item, date, or department.';
$reportBadge = 'Reports';
$breadcrumbLabel = 'Issued Items Report';
@endphp

@section('hub')
<p class="rp-section-title">Store Issues</p>
<div class="rp-hub-grid mb-2">
    <a href="{{ route('searchIssueItemByStore') }}" class="rp-hub-card store">
        <div class="rp-hub-icon"><i class="bi bi-bar-chart-line"></i></div>
        <h6>Search By Store</h6>
        <p>View all issued items for a store</p>
    </a>
    <a href="{{ route('searchByIssueItem') }}" class="rp-hub-card store">
        <div class="rp-hub-icon"><i class="bi bi-boxes"></i></div>
        <h6>Search By Item</h6>
        <p>Find issues for a specific item</p>
    </a>
    <a href="{{ route('searchByIssuedDate') }}" class="rp-hub-card store">
        <div class="rp-hub-icon"><i class="bi bi-calendar"></i></div>
        <h6>Search By Date</h6>
        <p>Filter issues by date range</p>
    </a>
    <a href="{{ route('searchIssuedItemByDateIntev') }}" class="rp-hub-card store">
        <div class="rp-hub-icon"><i class="bi bi-calendar-range"></i></div>
        <h6>Search Item By Date Intervals</h6>
        <p>Item issues within a date range</p>
    </a>
</div>

<p class="rp-section-title">Items Issued to Departments</p>
<div class="rp-hub-grid">
    <a href="{{ route('searchIssuedItemByDepartment') }}" class="rp-hub-card dept">
        <div class="rp-hub-icon"><i class="bi bi-house-door"></i></div>
        <h6>Search By Department</h6>
        <p>Issues to a department by store</p>
    </a>
    <a href="{{ route('searchByIssueItemDepartment') }}" class="rp-hub-card dept">
        <div class="rp-hub-icon"><i class="bi bi-boxes"></i></div>
        <h6>Search By Item</h6>
        <p>Department issues for a specific item</p>
    </a>
    <a href="{{ route('searchByIssuedDepartmentDate') }}" class="rp-hub-card dept">
        <div class="rp-hub-icon"><i class="bi bi-calendar"></i></div>
        <h6>Search By Date</h6>
        <p>Department issues by date range</p>
    </a>
    <a href="{{ route('searchIssuedItemByDepartmentDateIntev') }}" class="rp-hub-card dept">
        <div class="rp-hub-icon"><i class="bi bi-calendar-range"></i></div>
        <h6>Search Item By Date Intervals</h6>
        <p>Department item issues by date range</p>
    </a>
</div>
@endsection
