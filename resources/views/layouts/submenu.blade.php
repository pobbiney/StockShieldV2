@php $pageName = "submenu"; $subpageName = "sub-menu"; @endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .submenu-hero {
        background: linear-gradient(135deg, var(--adminuiux-theme-1-subtle, rgba(13, 110, 253, 0.08)) 0%, transparent 60%);
        border: 1px solid rgba(var(--adminuiux-theme-1-rgb, 13, 110, 253), 0.12);
        border-radius: 1rem;
        overflow: hidden;
        position: relative;
    }

    .submenu-hero::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -5%;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(var(--adminuiux-theme-1-rgb, 13, 110, 253), 0.15) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .submenu-hero-icon {
        width: 64px;
        height: 64px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--adminuiux-theme-1, #0d6efd), var(--adminuiux-theme-2, #6610f2));
        color: #fff;
        font-size: 1.75rem;
        box-shadow: 0 8px 24px rgba(var(--adminuiux-theme-1-rgb, 13, 110, 253), 0.35);
        flex-shrink: 0;
    }

    .submenu-stat {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 2rem;
        background: var(--adminuiux-theme-1-subtle, rgba(13, 110, 253, 0.1));
        color: var(--adminuiux-theme-1, #0d6efd);
        font-size: 0.8rem;
        font-weight: 600;
    }

    .submenu-page {
        width: 100%;
        max-width: 100%;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .submenu-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        width: 100%;
    }

    @media (max-width: 991.98px) {
        .submenu-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 575.98px) {
        .submenu-grid {
            grid-template-columns: 1fr;
        }
    }

    .submenu-card {
        position: relative;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
        border-radius: 1rem;
        background: var(--adminuiux-card-bg, #fff);
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 2rem 1.5rem;
        min-height: 180px;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        overflow: hidden;
        height: 100%;
    }

    [data-theme="theme-dark"] .submenu-card,
    .adminuiux-header-standard.theme-dark .submenu-card {
        border-color: rgba(255, 255, 255, 0.08);
    }

    .submenu-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, transparent 60%, rgba(var(--card-accent-rgb, 13, 110, 253), 0.06));
        opacity: 0;
        transition: opacity 0.25s ease;
        pointer-events: none;
    }

    .submenu-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.1);
        border-color: rgba(var(--card-accent-rgb, 13, 110, 253), 0.25);
        color: inherit;
    }

    .submenu-card:hover::after {
        opacity: 1;
    }

    .submenu-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        margin-bottom: 1rem;
        background: rgba(var(--card-accent-rgb, 13, 110, 253), 0.12);
        color: rgb(var(--card-accent-rgb, 13, 110, 253));
        transition: transform 0.25s ease, background 0.25s ease;
    }

    .submenu-card:hover .submenu-card-icon {
        transform: scale(1.08);
        background: rgba(var(--card-accent-rgb, 13, 110, 253), 0.18);
    }

    .submenu-card-title {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 0.35rem;
        line-height: 1.35;
        color: var(--adminuiux-content-color, #212529);
    }

    .submenu-card-arrow {
        position: absolute;
        bottom: 1.25rem;
        right: 1.25rem;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(var(--card-accent-rgb, 13, 110, 253), 0.1);
        color: rgb(var(--card-accent-rgb, 13, 110, 253));
        font-size: 0.75rem;
        opacity: 0;
        transform: translateX(-6px);
        transition: opacity 0.25s ease, transform 0.25s ease;
    }

    .submenu-card:hover .submenu-card-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    .submenu-card-accent-0 { --card-accent-rgb: 13, 110, 253; }
    .submenu-card-accent-1 { --card-accent-rgb: 102, 16, 242; }
    .submenu-card-accent-2 { --card-accent-rgb: 25, 135, 84; }
    .submenu-card-accent-3 { --card-accent-rgb: 253, 126, 20; }
    .submenu-card-accent-4 { --card-accent-rgb: 220, 53, 69; }
    .submenu-card-accent-5 { --card-accent-rgb: 13, 202, 240; }

    .submenu-card-animate {
        opacity: 0;
        transform: translateY(16px);
        animation: submenuFadeUp 0.45s ease forwards;
    }

    @keyframes submenuFadeUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .submenu-empty {
        text-align: center;
        padding: 4rem 2rem;
        border-radius: 1rem;
        border: 2px dashed rgba(0, 0, 0, 0.08);
        background: var(--adminuiux-theme-1-subtle, rgba(13, 110, 253, 0.04));
    }

    .submenu-empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.05);
        font-size: 2rem;
        color: var(--adminuiux-text-secondary, #6c757d);
        margin-bottom: 1rem;
    }

    .btn-back-dashboard {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 1rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        font-weight: 500;
        border: 1px solid rgba(0, 0, 0, 0.1);
        background: transparent;
        color: inherit;
        text-decoration: none;
        transition: background 0.2s ease, border-color 0.2s ease;
    }

    .btn-back-dashboard:hover {
        background: var(--adminuiux-theme-1-subtle, rgba(13, 110, 253, 0.08));
        border-color: rgba(var(--adminuiux-theme-1-rgb, 13, 110, 253), 0.3);
        color: var(--adminuiux-theme-1, #0d6efd);
    }
</style>
@endsection

@section('content')

<div class="container-fluid submenu-page mt-3 px-3 px-lg-4">
    <div class="submenu-hero px-4 py-4 mb-4">
        <div class="row gx-3 align-items-center position-relative">
            <div class="col-auto d-none d-sm-block">
                <div class="submenu-hero-icon">
                    <i class="{{ $parent->link_image ?? 'bi bi-grid-3x3-gap' }}"></i>
                </div>
            </div>
            <div class="col">
                @include('layouts.partials.breadcrumb', [
                    'variant' => 'dark',
                    'items' => [
                        ['label' => 'Home', 'url' => route('dashboard'), 'icon' => 'bi-house-door-fill'],
                        ['label' => $parent->link_name, 'active' => true, 'icon' => 'bi-grid-3x3-gap'],
                    ],
                ])
                <h4 class="mb-1 fw-semibold">{{ $parent->link_name }}</h4>
                <p class="text-secondary small mb-0">Choose a module below to continue</p>
            </div>
            <div class="col-auto d-flex flex-column flex-sm-row align-items-sm-center gap-2 mt-3 mt-md-0">
                <span class="submenu-stat">
                    <i class="bi bi-layers"></i>
                    {{ $submenus->count() }} module{{ $submenus->count() !== 1 ? 's' : '' }}
                </span>
                <a href="{{ route('dashboard') }}" class="btn-back-dashboard">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid submenu-page pb-5 px-3 px-lg-4">
    @if($submenus->count() > 0)
        <div class="submenu-grid">
            @foreach($submenus as $submenu)
                <a href="{{ route($submenu->link_url) }}"
                   class="submenu-card submenu-card-accent-{{ $loop->index % 6 }} submenu-card-animate"
                   style="animation-delay: {{ $loop->index * 0.06 }}s">
                    <div class="submenu-card-icon">
                        <i class="{{ $submenu->link_image ?? 'bi bi-box-arrow-up-right' }}"></i>
                    </div>
                    <div class="submenu-card-title">{{ $submenu->link_name }}</div>
                    <p class="text-secondary small mb-0">Open module</p>
                    <span class="submenu-card-arrow">
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </a>
            @endforeach
        </div>
    @else
        <div class="submenu-empty">
            <div class="submenu-empty-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <h5 class="fw-semibold mb-2">No modules available</h5>
            <p class="text-secondary small mb-3">There are no sub-modules assigned to your role under this section.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-theme btn-sm">
                <i class="bi bi-speedometer2 me-1"></i> Go to Dashboard
            </a>
        </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    document.querySelectorAll('.submenu-card').forEach(function (card) {
        card.addEventListener('mouseenter', function () {
            this.style.zIndex = '2';
        });
        card.addEventListener('mouseleave', function () {
            this.style.zIndex = '';
        });
    });
</script>
@endsection
