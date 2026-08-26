@php
    $pageName = 'user';
    $subpageName = 'list_user';
    $currentStatus = old('status', $userData->status ?? 'Active');
    $isBlocked = $currentStatus === 'Inactive';
    $selectedStoreIds = array_map('intval', old('department_id', $userStoreIds));
    $initialExtraIds = old('extra_link_ids', $userExtraLinkIds);
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
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        transition: transform 0.2s, box-shadow 0.2s;
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
    }

    .btn-hero-ghost:hover { background: rgba(255, 255, 255, 0.12); color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: statIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.12s; }
    .stat-card:nth-child(3) { animation-delay: 0.19s; }

    @keyframes statIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 0.75rem;
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

    .stat-card.status  .stat-card-icon { background: rgba(79, 70, 229, 0.12); color: #4f46e5; }
    .stat-card.status.blocked .stat-card-icon { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .stat-card.screens .stat-card-icon { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
    .stat-card.stores  .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }

    .stat-card-value {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 0.2rem;
    }

    .stat-card.status  .stat-card-value { color: #4f46e5; }
    .stat-card.status.blocked .stat-card-value { color: #ef4444; }
    .stat-card.screens .stat-card-value { color: #16a34a; }
    .stat-card.stores  .stat-card-value { color: #d97706; }

    .stat-card-label {
        font-size: 0.82rem;
        color: #64748b;
        margin: 0;
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
        border: 1px solid rgba(79, 70, 229, 0.25);
        background: linear-gradient(180deg, #eef2ff 0%, #fff 100%);
        padding: 1.5rem;
        height: 100%;
        min-height: 320px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
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

    .preview-avatar img { width: 100%; height: 100%; object-fit: cover; }

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

    .preview-status-badge {
        margin-top: 0.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .preview-status-badge.active {
        background: rgba(22, 163, 74, 0.1);
        color: #15803d;
    }

    .preview-status-badge.blocked {
        background: rgba(239, 68, 68, 0.1);
        color: #b91c1c;
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

    .store-map-section { display: none; }
    .store-map-section.visible { display: block; }
    .store-map-section.global-role { display: block; }

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
        margin: 0;
        transition: border-color 0.15s, background 0.15s;
    }

    .store-option:has(input:checked) {
        border-color: #4f46e5;
        background: #eef2ff;
    }

    .store-option input { cursor: pointer; }
    .store-option span { font-size: 0.84rem; color: #334155; }

    .preview-screens { margin-top: 0.85rem; width: 100%; text-align: left; }

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
        max-height: 140px;
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

    .preview-screen-list li:last-child { border-bottom: none; }
    .preview-screen-list li i { color: #4f46e5; font-size: 0.7rem; flex-shrink: 0; }

    .extra-screens-section {
        display: none;
        width: 100%;
        margin-top: 0.85rem;
        text-align: left;
    }

    .extra-screens-section.visible { display: block; }
    .extra-screens-section .preview-screens-head { color: #d97706; }

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
        cursor: pointer;
        margin: 0;
        transition: background 0.15s;
    }

    .extra-screen-option:hover,
    .extra-screen-option:has(input:checked) { background: #fffbeb; }

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

    .block-account-card {
        border-radius: 0.875rem;
        border: 1px solid #e2e8f0;
        padding: 1rem 1.15rem;
        background: #f8fafc;
        transition: border-color 0.2s, background 0.2s;
    }

    .block-account-card.is-blocked {
        border-color: #fecaca;
        background: #fef2f2;
    }

    .block-account-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .block-account-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #0f172a;
        margin-bottom: 0.15rem;
    }

    .block-account-desc {
        font-size: 0.78rem;
        color: #64748b;
        margin: 0;
    }

    .form-switch-lg .form-check-input {
        width: 2.75rem;
        height: 1.4rem;
        cursor: pointer;
    }

    .form-switch-lg .form-check-input:checked {
        background-color: #ef4444;
        border-color: #ef4444;
    }
</style>
@endsection

@section('content')
<div class="container-fluid account-page px-3 px-lg-4 mt-3">

    <div class="account-hero">
        <div class="account-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="account-hero-badge">
                        <i class="bi bi-person-gear"></i> User Management
                    </div>
                    <h2>Edit User Account</h2>
                    <p>Update role, credentials, store access, and screen permissions for <strong>{{ $userData->name }}</strong>.</p>
                    <div class="account-hero-actions">
                        <a href="#editAccountForm" class="btn-hero-primary">
                            <i class="bi bi-pencil-square"></i> Edit Details
                        </a>
                        <a href="{{ route('user-management-list-create-account') }}" class="btn-hero-ghost">
                            <i class="bi bi-arrow-left"></i> Back to Accounts
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-flex justify-content-end align-items-start">
                    @include('layouts.partials.breadcrumb', [
                        'variant' => 'dark',
                        'items' => [
                            ['label' => 'Home', 'url' => route('dashboard'), 'icon' => 'bi-house-door-fill'],
                            ['label' => 'Accounts', 'url' => route('user-management-list-create-account'), 'icon' => 'bi-people-fill'],
                            ['label' => 'Edit', 'active' => true, 'icon' => 'bi-pencil-square'],
                        ],
                    ])
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card status {{ $isBlocked ? 'blocked' : '' }}" id="statStatusCard">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Account Status</p>
                        <div class="stat-card-value" id="statStatusValue">{{ $isBlocked ? 'Blocked' : 'Active' }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi {{ $isBlocked ? 'bi-slash-circle-fill' : 'bi-shield-check' }}" id="statStatusIcon"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card screens">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Role Screens</p>
                        <div class="stat-card-value" id="statRoleScreens">{{ $currentRoleScreens }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-grid-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stores">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Stores Mapped</p>
                        <div class="stat-card-value" id="statStoreCount">{{ $storeCount }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-shop"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-shell" id="editAccountForm">
        <div class="form-shell-head">
            <h5><i class="bi bi-person-badge me-2 text-primary"></i>Account Settings</h5>
            <p class="text-secondary small mb-0">Change role, reset password, assign extra screens, or block this account.</p>
        </div>
        <div class="form-shell-body">
            @if ($errors->any())
                <div class="alert alert-danger mx-0 mb-3">
                    <strong>Could not save changes:</strong>
                    <ul class="mb-0 mt-1 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="editAccountFormEl" method="POST" action="{{ route('user-management-edit-user-account-process', $id) }}">
                @csrf
                <div class="row g-4">
                    <div class="col-lg-7">

                        {{-- User info --}}
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-person"></i> User Details
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold" for="user">Display Name</label>
                                    <input class="form-control" type="text" id="user" name="user"
                                        value="{{ old('user', $userData->name) }}">
                                    @error('user')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold" for="email">Email Address</label>
                                    <input readonly class="form-control bg-light" type="email" id="email" name="email"
                                        value="{{ old('email', $userData->email) }}">
                                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                                    <p class="field-hint">Login email cannot be changed here.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Role --}}
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-shield"></i> Role & Access
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-semibold" for="category">User Role</label>
                                    <select id="category" name="category" class="form-select select2" data-allow-clear="true">
                                        <option value="">— Select role —</option>
                                        @foreach ($userCategoryList as $catItem)
                                            <option value="{{ $catItem->cat_id }}"
                                                data-global="{{ $catItem->access_all_stores ? '1' : '0' }}"
                                                {{ (string) old('category', $userData->user_cat) === (string) $catItem->cat_id ? 'selected' : '' }}>
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
                                <i class="bi bi-key"></i> Password
                            </div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label small fw-semibold" for="password">New Password</label>
                                    <div class="password-wrap">
                                        <input class="form-control" type="password" id="password" name="password"
                                            value="" placeholder="Leave blank to keep current password" autocomplete="new-password">
                                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')<div class="field-error">{{ $message }}</div>@enderror
                                    <p class="field-hint">Only fill in if you want to reset the password (minimum 8 characters).</p>
                                </div>
                            </div>
                        </div>

                        {{-- Block account --}}
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-lock"></i> Account Control
                            </div>
                            <div class="block-account-card {{ $isBlocked ? 'is-blocked' : '' }}" id="blockAccountCard">
                                <div class="block-account-row">
                                    <div>
                                        <div class="block-account-label">Block Account</div>
                                        <p class="block-account-desc">
                                            Sets <code>users.status</code> to <strong>Inactive</strong> in the database.
                                            Blocked users cannot log in.
                                        </p>
                                    </div>
                                    <div class="form-check form-switch form-switch-lg mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="blockAccountToggle" {{ $isBlocked ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <input type="hidden" name="status" id="accountStatusField"
                                    value="{{ $currentStatus === 'Inactive' ? 'Inactive' : 'Active' }}">
                                @error('status')<div class="field-error mt-2">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Store access --}}
                        <div class="form-section store-map-section visible" id="storeMapSection">
                            <div class="form-section-title">
                                <i class="bi bi-shop"></i> Store Access
                            </div>
                            <div id="storeMapGlobal" class="role-hint global d-none">
                                <i class="bi bi-globe2 me-1"></i> All active stores are assigned automatically for this role.
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
                                                {{ in_array((int) $store->id, $selectedStoreIds, true) ? 'checked' : '' }}>
                                            <span>{{ $store->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('department_id')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('user-management-list-create-account') }}" class="text-secondary small text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Back to account list
                            </a>
                            <button type="submit" class="btn-submit-account">
                                <i class="bi bi-check-lg"></i> Save Changes
                            </button>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <div class="col-lg-5">
                        <div class="preview-panel">
                            <div class="preview-avatar" id="previewAvatar">?</div>
                            <div class="preview-name" id="previewName">{{ $userData->name }}</div>
                            <div class="preview-meta" id="previewEmail">
                                <i class="bi bi-envelope"></i> <span>{{ $userData->email }}</span>
                            </div>
                            <div class="preview-meta" id="previewPhone">
                                <i class="bi bi-telephone"></i> <span>{{ $userData->phone ?? '—' }}</span>
                            </div>
                            <div class="preview-status-badge {{ $isBlocked ? 'blocked' : 'active' }}" id="previewStatusBadge">
                                <i class="bi {{ $isBlocked ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                <span>{{ $isBlocked ? 'Blocked' : 'Active' }}</span>
                            </div>
                            <div class="preview-role-badge" id="previewRole">
                                <i class="bi bi-shield"></i> <span>—</span>
                            </div>
                            <div class="preview-screens" id="previewScreens"></div>
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
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const roleData = @json($roleData);
const allScreens = @json($allScreens);
const savedExtraLinkIds = @json(array_map('intval', $initialExtraIds));
const previewData = @json($previewData);

const EditAccountAlert = {
    _base(opts) {
        return Swal.fire({
            icon: opts.icon,
            title: opts.title,
            text: opts.text,
            confirmButtonText: 'OK',
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
        previewScreens.innerHTML = '<div class="extra-screens-hint text-center"><i class="bi bi-exclamation-circle me-1 text-warning"></i> No screens on this role.</div>';
        document.getElementById('statRoleScreens').textContent = '0';
        return;
    }

    document.getElementById('statRoleScreens').textContent = role.screen_names.length;

    previewScreens.innerHTML =
        '<div class="preview-screens-head"><i class="bi bi-grid"></i> ' + role.screen_names.length + ' from role</div>' +
        '<ul class="preview-screen-list">' +
        role.screen_names.map(function (name) {
            return '<li><i class="bi bi-check-circle-fill"></i><span>' + escapeHtml(name) + '</span></li>';
        }).join('') +
        '</ul>';
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
        const checked = savedExtraLinkIds.includes(Number(screen.id)) ? ' checked' : '';
        return '<label class="extra-screen-option">' +
            '<input type="checkbox" class="form-check-input" name="extra_link_ids[]" value="' + screen.id + '"' + checked + '>' +
            '<span>' + escapeHtml(screen.name) + '</span>' +
            '</label>';
    }).join('');

    renderRoleScreens(role);
}

function updateStoreCount() {
    const globalRole = document.getElementById('storeMapGlobal').classList.contains('d-none') === false;
    if (globalRole) {
        document.getElementById('statStoreCount').textContent = 'All';
        return;
    }
    const count = document.querySelectorAll('.store-checkbox:checked').length;
    document.getElementById('statStoreCount').textContent = count;
}

function updateRoleHint() {
    const catId = document.getElementById('category').value;
    const hint = document.getElementById('roleHint');
    const previewRole = document.getElementById('previewRole');
    const storeSection = document.getElementById('storeMapSection');
    const storeGlobal = document.getElementById('storeMapGlobal');
    const storePicker = document.getElementById('storeMapPicker');

    if (!catId || !roleData[catId]) {
        hint.className = 'role-hint';
        hint.textContent = '';
        previewRole.classList.add('d-none');
        document.getElementById('previewScreens').innerHTML = '';
        storeSection.className = 'form-section store-map-section';
        renderExtraScreens(null);
        return;
    }

    const role = roleData[catId];
    hint.className = 'role-hint visible' + (role.global ? ' global' : '');
    hint.innerHTML = role.global
        ? '<i class="bi bi-globe2 me-1"></i> This role has <strong>access to all stores</strong> — stores are assigned automatically.'
        : '<i class="bi bi-shop me-1"></i> Select at least one store for this user to log in.';

    previewRole.classList.remove('d-none');
    previewRole.classList.toggle('global', role.global);
    previewRole.querySelector('span').textContent = role.name;

    renderExtraScreens(role);

    storeSection.className = 'form-section store-map-section visible' + (role.global ? ' global-role' : '');
    storeGlobal.classList.toggle('d-none', !role.global);
    storePicker.classList.toggle('d-none', role.global);

    if (role.global) {
        document.querySelectorAll('.store-checkbox').forEach(function (cb) {
            cb.checked = true;
            cb.disabled = true;
        });
    } else {
        document.querySelectorAll('.store-checkbox').forEach(function (cb) {
            cb.disabled = false;
        });
    }

    updateStoreCount();
}

function initPreview() {
    const avatar = document.getElementById('previewAvatar');
    if (previewData.picture) {
        avatar.innerHTML = '<img src="' + previewData.picture + '" alt="">';
    } else {
        avatar.textContent = getInitials(previewData.full_name);
    }

    document.getElementById('previewName').textContent = previewData.full_name || '—';
    document.getElementById('previewEmail').querySelector('span').textContent = previewData.personal_email || '—';
    document.getElementById('previewPhone').querySelector('span').textContent = previewData.contact_num || '—';
}

function syncAccountStatusField() {
    const isBlocked = document.getElementById('blockAccountToggle').checked;
    document.getElementById('accountStatusField').value = isBlocked ? 'Inactive' : 'Active';
}

function updateBlockUI(isBlocked) {
    syncAccountStatusField();
    const card = document.getElementById('blockAccountCard');
    const statCard = document.getElementById('statStatusCard');
    const statValue = document.getElementById('statStatusValue');
    const statIcon = document.getElementById('statStatusIcon');
    const badge = document.getElementById('previewStatusBadge');

    card.classList.toggle('is-blocked', isBlocked);
    statCard.classList.toggle('blocked', isBlocked);
    statValue.textContent = isBlocked ? 'Blocked' : 'Active';
    statIcon.className = 'bi ' + (isBlocked ? 'bi-slash-circle-fill' : 'bi-shield-check');

    badge.className = 'preview-status-badge ' + (isBlocked ? 'blocked' : 'active');
    badge.innerHTML = isBlocked
        ? '<i class="bi bi-slash-circle"></i><span>Blocked</span>'
        : '<i class="bi bi-check-circle"></i><span>Active</span>';
}

document.getElementById('blockAccountToggle').addEventListener('change', function () {
    updateBlockUI(this.checked);
});

document.getElementById('editAccountFormEl').addEventListener('submit', function () {
    syncAccountStatusField();

    // Disabled store checkboxes are not posted — re-enable before submit so mapping is preserved.
    document.querySelectorAll('.store-checkbox').forEach(function (cb) {
        cb.disabled = false;
    });
});

document.getElementById('category').addEventListener('change', updateRoleHint);

document.getElementById('selectAllStores')?.addEventListener('change', function () {
    document.querySelectorAll('.store-checkbox:not(:disabled)').forEach(function (cb) {
        cb.checked = this.checked;
    }.bind(this));
    updateStoreCount();
});

document.querySelectorAll('.store-checkbox').forEach(function (cb) {
    cb.addEventListener('change', updateStoreCount);
});

document.getElementById('togglePassword').addEventListener('click', function () {
    const input = document.getElementById('password');
    const icon = this.querySelector('i');
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
});

initPreview();
updateRoleHint();
updateBlockUI(document.getElementById('blockAccountToggle').checked);

@if(session('success_message'))
EditAccountAlert.success('Account Updated', @json(session('success_message')));
@endif

@if(session('error_message'))
EditAccountAlert.error('Something Went Wrong', @json(session('error_message')));
@endif
</script>
@endsection
