@php
    $pageName = 'user';
    $subpageName = 'create_account';
    $accountPct = $totalStaff > 0 ? round(($totalAccounts / $totalStaff) * 100) : 0;
    $pendingPct = $totalStaff > 0 ? round(($staffWithoutAccounts / $totalStaff) * 100) : 0;
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .account-page { padding: 0 0.5rem 2rem; }

    .account-hero {
        background: linear-gradient(135deg, #312e81 0%, #4f46e5 55%, #6366f1 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(79, 70, 229, 0.28);
    }

    .account-hero::before,
    .account-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .account-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .account-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .account-hero-inner { position: relative; z-index: 1; }

    .account-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.85rem;
        border-radius: 2rem;
        background: rgba(255, 255, 255, 0.15);
        font-size: 0.78rem;
        font-weight: 600;
        margin-bottom: 0.85rem;
        border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .account-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .account-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 540px;
    }

    .account-hero-actions {
        display: flex;
        gap: 0.65rem;
        flex-wrap: wrap;
        margin-top: 1.25rem;
    }

    .btn-hero-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 2rem;
        border: none;
        background: #fff;
        color: #4f46e5;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        text-decoration: none;
    }

    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        color: #4f46e5;
    }

    .btn-hero-ghost {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.15rem;
        border-radius: 2rem;
        border: 1.5px solid rgba(255, 255, 255, 0.45);
        background: transparent;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.2s;
    }

    .btn-hero-ghost:hover { background: rgba(255, 255, 255, 0.12); color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        animation: statIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.12s; }
    .stat-card:nth-child(3) { animation-delay: 0.19s; }

    @keyframes statIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .stat-card.staff   .stat-card-icon { background: rgba(79, 70, 229, 0.12); color: #4f46e5; }
    .stat-card.pending .stat-card-icon { background: rgba(217, 119, 6, 0.12);  color: #d97706; }
    .stat-card.active  .stat-card-icon { background: rgba(22, 163, 74, 0.12);  color: #16a34a; }

    .stat-card-value {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.staff   .stat-card-value { color: #4f46e5; }
    .stat-card.pending .stat-card-value { color: #d97706; }
    .stat-card.active  .stat-card-value { color: #16a34a; }

    .stat-card-label {
        font-size: 0.82rem;
        color: #64748b;
        margin: 0 0 0.85rem;
    }

    .stat-bar-wrap {
        height: 4px;
        background: #f1f5f9;
        border-radius: 2rem;
        overflow: hidden;
    }

    .stat-bar-fill {
        height: 100%;
        border-radius: 2rem;
        transition: width 1s ease;
    }

    .stat-card.staff   .stat-bar-fill { background: #4f46e5; }
    .stat-card.pending .stat-bar-fill { background: #d97706; }
    .stat-card.active  .stat-bar-fill { background: #16a34a; }

    .stat-card-meta {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.4rem;
    }

    .form-shell {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        background: #fff;
        animation: statIn 0.5s ease 0.25s both;
    }

    .form-shell-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
    }

    .form-shell-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.15rem;
        color: #0f172a;
    }

    .form-shell-body { padding: 1.5rem; }

    .form-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        margin: 0 0 1rem;
        padding-bottom: 0.65rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .form-section-title i {
        width: 28px;
        height: 28px;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        font-size: 0.85rem;
    }

    .form-section + .form-section { margin-top: 1.75rem; }

    .preview-panel {
        border-radius: 1rem;
        border: 1px dashed #c7d2fe;
        background: linear-gradient(180deg, #eef2ff 0%, #f8fafc 100%);
        padding: 1.5rem;
        height: 100%;
        min-height: 320px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        transition: border-color 0.2s, background 0.2s;
    }

    .preview-panel.has-selection {
        border-style: solid;
        border-color: rgba(79, 70, 229, 0.25);
        background: linear-gradient(180deg, #eef2ff 0%, #fff 100%);
    }

    .preview-avatar {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 1rem;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.25);
    }

    .preview-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-name {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .preview-meta {
        font-size: 0.82rem;
        color: #64748b;
        margin-bottom: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        justify-content: center;
    }

    .preview-role-badge {
        margin-top: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.85rem;
        border-radius: 2rem;
        font-size: 0.78rem;
        font-weight: 600;
        background: rgba(79, 70, 229, 0.12);
        color: #4f46e5;
    }

    .preview-role-badge.global {
        background: rgba(22, 163, 74, 0.12);
        color: #16a34a;
    }

    .preview-empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
    }

    .preview-empty-text {
        color: #64748b;
        font-size: 0.875rem;
        max-width: 240px;
        line-height: 1.5;
    }

    .password-wrap { position: relative; }

    .password-toggle {
        position: absolute;
        right: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #94a3b8;
        padding: 0;
        cursor: pointer;
        z-index: 5;
    }

    .password-toggle:hover { color: #4f46e5; }

    .password-wrap .form-control { padding-right: 2.75rem; }

    .field-hint {
        font-size: 0.76rem;
        color: #94a3b8;
        margin-top: 0.35rem;
    }

    .field-error {
        color: #ef4444;
        font-size: 0.76rem;
        margin-top: 0.25rem;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1.75rem;
        padding-top: 1.25rem;
        border-top: 1px solid #f1f5f9;
    }

    .btn-submit-account {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.7rem 1.6rem;
        border-radius: 0.75rem;
        border: none;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 4px 16px rgba(79, 70, 229, 0.35);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-submit-account:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.4);
        color: #fff;
    }

    .btn-submit-account:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .empty-staff-alert {
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        font-size: 0.875rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .role-hint {
        margin-top: 0.5rem;
        padding: 0.65rem 0.85rem;
        border-radius: 0.625rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.78rem;
        color: #64748b;
        display: none;
    }

    .role-hint.visible { display: block; }

    .role-hint.global {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
    }

    .store-map-section {
        display: none;
    }

    .store-map-section.visible {
        display: block;
    }

    .store-map-section.global-role {
        display: block;
    }

    .store-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.65rem;
        max-height: 220px;
        overflow-y: auto;
        padding: 0.25rem;
    }

    .store-option {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.65rem 0.85rem;
        border-radius: 0.625rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.15s, background 0.15s;
        margin: 0;
    }

    .store-option:has(input:checked) {
        border-color: #4f46e5;
        background: #eef2ff;
    }

    .store-option input { cursor: pointer; }

    .store-option span {
        font-size: 0.84rem;
        color: #334155;
    }

    .preview-screens {
        margin-top: 0.85rem;
        width: 100%;
        text-align: left;
    }

    .preview-screens-head {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.78rem;
        font-weight: 700;
        color: #4f46e5;
        margin-bottom: 0.55rem;
    }

    .preview-screen-list {
        list-style: none;
        margin: 0;
        padding: 0;
        max-height: 180px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 0.625rem;
        background: #fff;
    }

    .preview-screen-list li {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.75rem;
        font-size: 0.76rem;
        color: #475569;
        border-bottom: 1px solid #f1f5f9;
    }

    .preview-screen-list li:last-child {
        border-bottom: none;
    }

    .preview-screen-list li i {
        color: #4f46e5;
        font-size: 0.7rem;
        flex-shrink: 0;
    }

    .preview-screens-empty {
        font-size: 0.78rem;
        color: #64748b;
        text-align: center;
    }

    .extra-screens-section {
        display: none;
        width: 100%;
        margin-top: 0.85rem;
        text-align: left;
    }

    .extra-screens-section.visible {
        display: block;
    }

    .extra-screens-section .preview-screens-head {
        color: #d97706;
    }

    .extra-screen-grid {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #fde68a;
        border-radius: 0.625rem;
        background: #fff;
        padding: 0.35rem;
    }

    .extra-screen-option {
        display: flex;
        align-items: flex-start;
        gap: 0.55rem;
        padding: 0.5rem 0.65rem;
        border-radius: 0.5rem;
        border: none;
        background: transparent;
        cursor: pointer;
        margin: 0;
        transition: background 0.15s;
    }

    .extra-screen-option:hover {
        background: #fffbeb;
    }

    .extra-screen-option:has(input:checked) {
        background: #fffbeb;
    }

    .extra-screen-option input {
        margin-top: 0.15rem;
        cursor: pointer;
        flex-shrink: 0;
    }

    .extra-screen-option span {
        font-size: 0.76rem;
        color: #475569;
        line-height: 1.35;
    }

    .extra-screens-hint {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-bottom: 0.45rem;
        line-height: 1.4;
    }

    .staff-autocomplete {
        position: relative;
    }

    .staff-autocomplete-input-wrap {
        position: relative;
    }

    .staff-autocomplete-input-wrap .form-control {
        padding-right: 2.25rem;
    }

    .staff-autocomplete-icon {
        position: absolute;
        right: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }

    .staff-autocomplete-clear {
        position: absolute;
        right: 0.65rem;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #94a3b8;
        padding: 0.15rem 0.35rem;
        line-height: 1;
        cursor: pointer;
        display: none;
    }

    .staff-autocomplete-clear:hover { color: #64748b; }

    .staff-autocomplete.has-value .staff-autocomplete-icon { display: none; }
    .staff-autocomplete.has-value .staff-autocomplete-clear { display: block; }

    .staff-autocomplete-dropdown {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 4px);
        z-index: 30;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
        max-height: 240px;
        overflow-y: auto;
        display: none;
        list-style: none;
        margin: 0;
        padding: 0.35rem;
    }

    .staff-autocomplete-dropdown.open { display: block; }

    .staff-autocomplete-option {
        padding: 0.65rem 0.85rem;
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 0.875rem;
        color: #334155;
        transition: background 0.12s;
    }

    .staff-autocomplete-option:hover,
    .staff-autocomplete-option.active {
        background: #eef2ff;
        color: #4f46e5;
    }

    .staff-autocomplete-empty {
        padding: 0.85rem;
        text-align: center;
        font-size: 0.82rem;
        color: #94a3b8;
    }
</style>
@endsection

@section('content')
<div class="container-fluid account-page px-3 px-lg-4 mt-3">

    {{-- Hero --}}
    <div class="account-hero">
        <div class="account-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="account-hero-badge">
                        <i class="bi bi-person-lock-fill"></i> User Management
                    </div>
                    <h2>Create User Account</h2>
                    <p>Link a staff member to a system login, assign their role, and set initial credentials so they can access Stock Shield.</p>
                    <div class="account-hero-actions">
                        <a href="#accountForm" class="btn-hero-primary">
                            <i class="bi bi-person-plus-fill"></i> New Account
                        </a>
                        <a href="{{ route('user-management-list-create-account') }}" class="btn-hero-ghost">
                            <i class="bi bi-list-ul"></i> View All Accounts
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-flex justify-content-end align-items-start">
                    @include('layouts.partials.breadcrumb', [
                        'variant' => 'dark',
                        'items' => [
                            ['label' => 'Home', 'url' => route('dashboard'), 'icon' => 'bi-house-door-fill'],
                            ['label' => 'User Management', 'url' => route('user-management-list-create-account'), 'icon' => 'bi-people-fill'],
                            ['label' => 'Create Account', 'active' => true, 'icon' => 'bi-person-plus-fill'],
                        ],
                    ])
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card staff">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Total Staff</p>
                        <div class="stat-card-value" data-count="{{ $totalStaff }}">{{ number_format($totalStaff) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-people-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">{{ $totalRoles }} roles available</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card pending">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Pending Accounts</p>
                        <div class="stat-card-value" data-count="{{ $staffWithoutAccounts }}">{{ number_format($staffWithoutAccounts) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $pendingPct }}%"></div></div>
                <p class="stat-card-meta">Staff without login yet</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card active">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Active Accounts</p>
                        <div class="stat-card-value" data-count="{{ $totalAccounts }}">{{ number_format($totalAccounts) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-shield-check"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $accountPct }}%"></div></div>
                <p class="stat-card-meta">{{ $accountPct }}% of staff onboarded</p>
            </div>
        </div>
    </div>
        
    {{-- Form --}}
    <div class="form-shell" id="accountForm">
        <div class="form-shell-head">
            <h5><i class="bi bi-person-badge me-2 text-primary"></i>Account Registration</h5>
            <p class="text-secondary small mb-0">Select a staff member, assign a role, and set their login credentials.</p>
</div>
        <div class="form-shell-body">
            @if($listStaff->isEmpty())
                <div class="empty-staff-alert">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <div>
                        <strong>All staff have accounts.</strong>
                        <div class="mt-1">Every registered staff member already has a user account. Add new staff first, or manage existing accounts from the list page.</div>
                    </div>
                </div>
            @endif
                
            <form id="createAccountForm" method="POST" action="{{ route('create-account-process') }}">
                        @csrf
                <div class="row g-4">
                    <div class="col-lg-7">
                        {{-- Staff & Role --}}
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-person"></i> Staff & Role
                            </div>
                            <div class="row g-3">
                        <div class="col-md-6">
                                    <label class="form-label small fw-semibold" for="staffSearchInput">Staff Member</label>
                                    <div class="staff-autocomplete {{ old('users') ? 'has-value' : '' }}" id="staffAutocomplete">
                                        <div class="staff-autocomplete-input-wrap">
                                            <input type="text"
                                                id="staffSearchInput"
                                                class="form-control"
                                                placeholder="Type staff name…"
                                                autocomplete="off"
                                                value="{{ $selectedStaffName }}"
                                                {{ $listStaff->isEmpty() ? 'disabled' : '' }}>
                                            <i class="bi bi-search staff-autocomplete-icon"></i>
                                            <button type="button" class="staff-autocomplete-clear" id="staffSearchClear" aria-label="Clear selection">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" id="users" name="users" value="{{ old('users') }}">
                                        <ul class="staff-autocomplete-dropdown" id="staffSearchDropdown" role="listbox"></ul>
                                    </div>
                                    @error('users')<div class="field-error">{{ $message }}</div>@enderror
                                    <p class="field-hint">Start typing a name to search staff without accounts.</p>
                        </div>
                        <div class="col-md-6">
                                    <label class="form-label small fw-semibold" for="category">User Role</label>
                                    <select id="category" name="category" class="form-select select2" data-allow-clear="true" {{ $listStaff->isEmpty() ? 'disabled' : '' }}>
                                        <option value="">— Select role —</option>
                                            @foreach ($userCategoryList as $catItem)
                                            <option value="{{ $catItem->cat_id }}"
                                                data-global="{{ $catItem->access_all_stores ? '1' : '0' }}"
                                                {{ old('category') == $catItem->cat_id ? 'selected' : '' }}>
                                                {{ $catItem->cat_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    @error('category')<div class="field-error">{{ $message }}</div>@enderror
                                    <div id="roleHint" class="role-hint"></div>
                                    </div>
                            </div>
                        </div>

                        {{-- Credentials --}}
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-key"></i> Login Credentials
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold" for="email">Email Address</label>
                                    <input readonly class="form-control bg-light" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Auto-filled from staff record">
                                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                                    <p class="field-hint">Pulled from the staff member's personal email.</p>
                        </div>
                        <div class="col-md-6">
                                    <label class="form-label small fw-semibold" for="password">Password</label>
                                    <div class="password-wrap">
                                        <input class="form-control" type="password" id="password" name="password" value="{{ old('password') }}" placeholder="Minimum 8 characters" autocomplete="new-password">
                                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')<div class="field-error">{{ $message }}</div>@enderror
                                    <p class="field-hint">Must be at least 8 characters.</p>
                               </div>
                            </div>
                        </div>

                        {{-- Store Access --}}
                        <div class="form-section store-map-section" id="storeMapSection">
                            <div class="form-section-title">
                                <i class="bi bi-shop"></i> Store Access
                            </div>
                            <div id="storeMapGlobal" class="role-hint global d-none">
                                <i class="bi bi-globe2 me-1"></i> All active stores will be assigned automatically when the account is created.
                            </div>
                            <div id="storeMapPicker">
                                <p class="field-hint mb-2">Select the store(s) this user can access.</p>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <input class="form-check-input" type="checkbox" id="selectAllStores">
                                    <label for="selectAllStores" class="small fw-semibold mb-0">Select all stores</label>
                                </div>
                                <div class="store-grid">
                                    @foreach ($liststore as $store)
                                        <label class="store-option">
                                            <input class="form-check-input store-checkbox" type="checkbox"
                                                name="department_id[]"
                                                value="{{ $store->id }}"
                                                {{ in_array($store->id, old('department_id', [])) ? 'checked' : '' }}>
                                            <span>{{ $store->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('department_id')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <input type="hidden" id="phone" name="phone" value="{{ old('phone') }}">

                        <div class="form-actions">
                            <a href="{{ route('user-management-list-create-account') }}" class="text-secondary small text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Back to account list
                            </a>
                            <button type="submit" class="btn-submit-account" id="submitBtn" {{ $listStaff->isEmpty() ? 'disabled' : '' }}>
                                <i class="bi bi-person-check-fill"></i> Create Account
                            </button>
                                   </div>
                               </div>
                               
                    {{-- Preview --}}
                    <div class="col-lg-5">
                        <div class="preview-panel" id="previewPanel">
                            <div id="previewEmpty">
                                <div class="preview-empty-icon"><i class="bi bi-person-circle"></i></div>
                                <p class="preview-empty-text">Select a staff member to preview their details before creating an account.</p>
                            </div>
                            <div id="previewContent" class="d-none w-100">
                                <div class="preview-avatar" id="previewAvatar">?</div>
                                <div class="preview-name" id="previewName">—</div>
                                <div class="preview-meta" id="previewEmail"><i class="bi bi-envelope"></i> <span>—</span></div>
                                <div class="preview-meta" id="previewPhone"><i class="bi bi-telephone"></i> <span>—</span></div>
                                <div class="preview-meta" id="previewDept"><i class="bi bi-building"></i> <span>—</span></div>
                                <div class="preview-role-badge d-none" id="previewRole">
                                    <i class="bi bi-shield"></i> <span>—</span>
                                </div>
                                <div class="preview-screens d-none" id="previewScreens"></div>
                                <div class="extra-screens-section" id="extraScreensSection">
                                    <div class="preview-screens-head">
                                        <i class="bi bi-plus-circle"></i> Additional screens (this user only)
                                    </div>
                                    <p class="extra-screens-hint">Not included in the role — check to grant access only to this account.</p>
                                    <div id="extraScreensList" class="extra-screen-grid"></div>
                                    @error('extra_link_ids')<div class="field-error">{{ $message }}</div>@enderror
                                    @error('extra_link_ids.*')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </form>
            </div>
    </div>
</div>
 @endsection

@section('scripts')
 <script>
const roleData = @json($roleData);
const staffSearchList = @json($staffSearchList);
const allScreens = @json($allScreens);
const oldExtraLinkIds = @json(array_map('intval', old('extra_link_ids', [])));

const AccountAlert = {
    _base(opts) {
        return Swal.fire({
            icon: opts.icon,
            title: opts.title,
            html: opts.html || opts.text,
            confirmButtonText: opts.confirmButtonText || 'OK',
            customClass: {
                popup: 'staff-swal-popup',
                title: 'staff-swal-title',
                htmlContainer: 'staff-swal-text',
                confirmButton: 'btn staff-swal-confirm ' + (opts.btnClass || 'success'),
            },
            buttonsStyling: false,
        });
    },
    success(title, text) {
        return this._base({ icon: 'success', title, text, btnClass: 'success' });
    },
    error(title, text) {
        return this._base({ icon: 'error', title, text, btnClass: 'error' });
    },
};

function getInitials(name) {
    return (name || '?').split(/\s+/).filter(Boolean).slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function renderRoleScreens(role) {
    const previewScreens = document.getElementById('previewScreens');

    if (!role.screen_names || role.screen_names.length === 0) {
        previewScreens.innerHTML = '<div class="preview-screens-empty"><i class="bi bi-exclamation-circle me-1 text-warning"></i> No screens on this role.</div>';
        return;
    }

    previewScreens.innerHTML =
        '<div class="preview-screens-head"><i class="bi bi-grid"></i> ' + role.screen_names.length + ' from role</div>' +
        '<ul class="preview-screen-list">' +
        role.screen_names.map(function (name) {
            return '<li><i class="bi bi-check-circle-fill"></i><span>' + escapeHtml(name) + '</span></li>';
        }).join('') +
        '</ul>';
}

function getSelectedExtraLinkIds() {
    return Array.from(document.querySelectorAll('input[name="extra_link_ids[]"]:checked'))
        .map(function (input) { return Number(input.value); });
}

function renderExtraScreens(role) {
    const section = document.getElementById('extraScreensSection');
    const container = document.getElementById('extraScreensList');

    if (!role) {
        section.classList.remove('visible');
        container.innerHTML = '';
        return;
    }

    const roleIds = new Set((role.role_link_ids || []).map(Number));
    const available = allScreens.filter(function (screen) {
        return !roleIds.has(Number(screen.id));
    });

    section.classList.add('visible');

    if (!available.length) {
        container.innerHTML = '<p class="extra-screens-hint mb-0 text-center">This role already includes every available screen.</p>';
        renderRoleScreens(role);
        return;
    }

    container.innerHTML = available.map(function (screen) {
        const checked = oldExtraLinkIds.includes(Number(screen.id)) ? ' checked' : '';
        return '<label class="extra-screen-option">' +
            '<input type="checkbox" class="form-check-input" name="extra_link_ids[]" value="' + screen.id + '"' + checked + '>' +
            '<span>' + escapeHtml(screen.name) + '</span>' +
            '</label>';
    }).join('');

    renderRoleScreens(role);
}

function updateRoleHint() {
    const catId = document.getElementById('category').value;
    const hint = document.getElementById('roleHint');
    const previewRole = document.getElementById('previewRole');
    const previewScreens = document.getElementById('previewScreens');
    const storeSection = document.getElementById('storeMapSection');
    const storeGlobal = document.getElementById('storeMapGlobal');
    const storePicker = document.getElementById('storeMapPicker');

    if (!catId || !roleData[catId]) {
        hint.className = 'role-hint';
        hint.textContent = '';
        previewRole.classList.add('d-none');
        previewScreens.classList.add('d-none');
        storeSection.className = 'form-section store-map-section';
        renderExtraScreens(null);
        return;
    }

    const role = roleData[catId];
    hint.className = 'role-hint visible' + (role.global ? ' global' : '');
    hint.innerHTML = role.global
        ? '<i class="bi bi-globe2 me-1"></i> This role has <strong>access to all stores</strong> — stores are assigned automatically.'
        : '<i class="bi bi-shop me-1"></i> Select at least one store below for this user to log in.';

    previewRole.classList.remove('d-none');
    previewRole.classList.toggle('global', role.global);
    previewRole.querySelector('span').textContent = role.name;

    previewScreens.classList.remove('d-none');
    renderExtraScreens(role);

    storeSection.className = 'form-section store-map-section visible' + (role.global ? ' global-role' : '');
    storeGlobal.classList.toggle('d-none', !role.global);
    storePicker.classList.toggle('d-none', role.global);

    if (role.global) {
        document.querySelectorAll('.store-checkbox').forEach(cb => {
            cb.checked = true;
            cb.disabled = true;
        });
    } else {
        document.querySelectorAll('.store-checkbox').forEach(cb => {
            cb.disabled = false;
        });
    }
}

document.getElementById('selectAllStores')?.addEventListener('change', function () {
    document.querySelectorAll('.store-checkbox:not(:disabled)').forEach(cb => {
        cb.checked = this.checked;
    });
});

function updatePreview(data) {
    const panel = document.getElementById('previewPanel');
    const empty = document.getElementById('previewEmpty');
    const content = document.getElementById('previewContent');

    if (!data) {
        panel.classList.remove('has-selection');
        empty.classList.remove('d-none');
        content.classList.add('d-none');
        return;
    }

    panel.classList.add('has-selection');
    empty.classList.add('d-none');
    content.classList.remove('d-none');

    const avatar = document.getElementById('previewAvatar');
    if (data.picture) {
        avatar.innerHTML = '<img src="' + data.picture + '" alt="">';
    } else {
        avatar.textContent = getInitials(data.full_name);
    }

    document.getElementById('previewName').textContent = data.full_name || '—';
    document.getElementById('previewEmail').querySelector('span').textContent = data.personal_email || 'No email on file';
    document.getElementById('previewPhone').querySelector('span').textContent = data.contact_num || 'No phone on file';
    document.getElementById('previewDept').querySelector('span').textContent = data.department || 'No department';
    updateRoleHint();
}

function loadStaffDetails(id) {
    if (!id) {
        document.getElementById('email').value = '';
        document.getElementById('phone').value = '';
        updatePreview(null);
        return;
    }

    fetch('{{ url('get-user-email-process') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: new URLSearchParams({ users: id, _token: '{{ csrf_token() }}' }),
    })
        .then(res => res.ok ? res.json() : Promise.reject())
        .then(data => {
            document.getElementById('email').value = data.personal_email || '';
            document.getElementById('phone').value = data.contact_num || '';
            updatePreview(data);
        })
        .catch(() => {
            AccountAlert.error('Load Failed', 'Could not load staff details. Please try again.');
        });
}

function initStaffAutocomplete() {
    const wrapper = document.getElementById('staffAutocomplete');
    const input = document.getElementById('staffSearchInput');
    const hidden = document.getElementById('users');
    const dropdown = document.getElementById('staffSearchDropdown');
    const clearBtn = document.getElementById('staffSearchClear');

    if (!input || input.disabled) return;

    let activeIndex = -1;
    let filtered = [];

    function setHasValue(hasValue) {
        wrapper.classList.toggle('has-value', hasValue);
    }

    function renderDropdown(items) {
        filtered = items;
        activeIndex = -1;
        dropdown.innerHTML = '';

        if (!items.length) {
            dropdown.innerHTML = '<li class="staff-autocomplete-empty">No matching staff found</li>';
            dropdown.classList.add('open');
            return;
        }

        items.forEach(function (item, index) {
            const li = document.createElement('li');
            li.className = 'staff-autocomplete-option';
            li.textContent = item.name;
            li.setAttribute('role', 'option');
            li.dataset.id = item.id;
            li.dataset.index = index;
            li.addEventListener('mousedown', function (e) {
                e.preventDefault();
                selectStaff(item);
            });
            dropdown.appendChild(li);
        });

        dropdown.classList.add('open');
    }

    function closeDropdown() {
        dropdown.classList.remove('open');
        activeIndex = -1;
    }

    function highlightOption(index) {
        dropdown.querySelectorAll('.staff-autocomplete-option').forEach(function (el, i) {
            el.classList.toggle('active', i === index);
        });
    }

    function selectStaff(item) {
        input.value = item.name;
        hidden.value = item.id;
        setHasValue(true);
        closeDropdown();
        loadStaffDetails(item.id);
    }

    function clearSelection() {
        input.value = '';
        hidden.value = '';
        setHasValue(false);
        closeDropdown();
        loadStaffDetails(null);
        input.focus();
    }

    input.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        hidden.value = '';

        if (!query) {
            setHasValue(false);
            closeDropdown();
            loadStaffDetails(null);
            return;
        }

        const matches = staffSearchList.filter(function (staff) {
            return staff.name.toLowerCase().includes(query);
        }).slice(0, 12);

        renderDropdown(matches);
    });

    input.addEventListener('focus', function () {
        const query = this.value.trim().toLowerCase();
        if (!query) {
            renderDropdown(staffSearchList.slice(0, 12));
            return;
        }

        const matches = staffSearchList.filter(function (staff) {
            return staff.name.toLowerCase().includes(query);
        }).slice(0, 12);

        if (matches.length) renderDropdown(matches);
    });

    input.addEventListener('keydown', function (e) {
        const options = dropdown.querySelectorAll('.staff-autocomplete-option');
        if (!dropdown.classList.contains('open') || !options.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = Math.min(activeIndex + 1, options.length - 1);
            highlightOption(activeIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            highlightOption(activeIndex);
        } else if (e.key === 'Enter' && activeIndex >= 0) {
            e.preventDefault();
            selectStaff(filtered[activeIndex]);
        } else if (e.key === 'Escape') {
            closeDropdown();
        }
    });

    input.addEventListener('blur', function () {
        setTimeout(closeDropdown, 150);
    });

    clearBtn.addEventListener('click', clearSelection);

    if (hidden.value) {
        loadStaffDetails(hidden.value);
    }
}

initStaffAutocomplete();

document.getElementById('category').addEventListener('change', updateRoleHint);

document.getElementById('togglePassword').addEventListener('click', function () {
    const input = document.getElementById('password');
    const icon = this.querySelector('i');
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
});

document.querySelectorAll('.stat-card-value[data-count]').forEach(function (el) {
    const target = parseInt(el.dataset.count, 10);
    if (isNaN(target) || target === 0) return;
    let current = 0;
    const step = Math.ceil(target / 30);
    const timer = setInterval(function () {
        current = Math.min(current + step, target);
        el.textContent = current.toLocaleString();
        if (current >= target) clearInterval(timer);
    }, 30);
});

@if(old('category'))
document.addEventListener('DOMContentLoaded', function () {
    updateRoleHint();
});
@endif
    
@if(session('success_message'))
AccountAlert.success('Account Created', @json(session('success_message')));
@endif

@if(session('error_message'))
AccountAlert.error('Something Went Wrong', @json(session('error_message')));
@endif
    </script>
@endsection
