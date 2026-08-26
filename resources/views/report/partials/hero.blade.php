<div class="rp-hero">
    <div class="rp-hero-inner">
        <div class="row align-items-end g-3">
            <div class="col-lg-8">
                <div class="rp-hero-badge">
                    <i class="bi bi-bar-chart-line"></i> {{ $reportBadge }}
                </div>
                <h2>{{ $reportTitle }}</h2>
                @if(!empty($reportSubtitle))
                    <p>{{ $reportSubtitle }}</p>
                @endif
                @if(!empty($backUrl))
                    <a href="{{ $backUrl }}" class="btn-rp-back mt-2 d-inline-flex d-lg-none">
                        <i class="bi bi-arrow-left"></i> {{ $backLabel }}
                    </a>
                @endif
            </div>
            <div class="col-lg-4 d-none d-lg-block text-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumbLabel }}</li>
                    </ol>
                </nav>
                @if(!empty($backUrl))
                    <a href="{{ $backUrl }}" class="btn-rp-back mt-2 d-inline-flex">
                        <i class="bi bi-arrow-left"></i> {{ $backLabel }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
