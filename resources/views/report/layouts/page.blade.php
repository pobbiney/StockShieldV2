@php
    $pageName = $pageName ?? 'reports';
    $subpageName = $subpageName ?? 'item';
@endphp

@extends('layouts.backendapp')

@section('css')
    @include('report.partials.styles')
    @stack('report-css')
@endsection

@section('content')
<div class="container-fluid rp-page px-3 px-lg-4 mt-3">
    @include('report.partials.hero', [
        'reportTitle' => $reportTitle ?? 'Report',
        'reportSubtitle' => $reportSubtitle ?? 'Search and export inventory reports for your stores.',
        'reportBadge' => $reportBadge ?? 'Reports',
        'breadcrumbLabel' => $breadcrumbLabel ?? ($reportTitle ?? 'Report'),
        'backUrl' => $backUrl ?? null,
        'backLabel' => $backLabel ?? 'Back',
    ])

    @if(!empty($navLinks))
        @include('report.partials.nav-links', ['navLinks' => $navLinks, 'activeRoute' => $activeRoute ?? null])
    @endif

    @hasSection('before-filter')
        @yield('before-filter')
    @endif

    @hasSection('filter')
        <div class="rp-card">
            <div class="rp-card-head">
                <h5><i class="bi bi-funnel me-1 text-primary"></i> Search Filters</h5>
            </div>
            <div class="rp-card-body">
                @include('report.partials.flash')
                @yield('filter')
            </div>
        </div>
    @else
        @include('report.partials.flash')
    @endif

    @hasSection('results')
        <div class="rp-card mb-5">
            <div class="rp-card-head">
                <h5><i class="bi bi-table me-1 text-primary"></i> Results</h5>
                @yield('results-actions')
            </div>
            <div class="rp-card-body pt-0">
                @yield('results')
            </div>
        </div>
    @endif

    @hasSection('hub')
        @yield('hub')
    @endif
</div>
@endsection

@section('scripts')
    @stack('report-scripts')
@endsection
