@php
    $pageName = 'user';
    $subpageName = 'list_user';
    $activePct = $totalAccounts > 0 ? round(($activeAccounts / $totalAccounts) * 100) : 0;
    $showTable = $userList !== null;
    $resultCount = $showTable ? $userList->count() : 0;
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .accounts-page { padding: 0 0.5rem 2rem; }

    .accounts-hero {
        background: linear-gradient(135deg, #312e81 0%, #4f46e5 55%, #6366f1 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(79, 70, 229, 0.28);
    }

    .accounts-hero::before,
    .accounts-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .accounts-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .accounts-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .accounts-hero-inner { position: relative; z-index: 1; }

    .accounts-hero-badge {
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

    .accounts-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .accounts-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 540px;
    }

    .accounts-hero-actions {
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

    .stat-card.total   .stat-card-icon { background: rgba(79, 70, 229, 0.12); color: #4f46e5; }
    .stat-card.active  .stat-card-icon { background: rgba(22, 163, 74, 0.12);  color: #16a34a; }
    .stat-card.roles   .stat-card-icon { background: rgba(217, 119, 6, 0.12);  color: #d97706; }

    .stat-card-value {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.total  .stat-card-value { color: #4f46e5; }
    .stat-card.active .stat-card-value { color: #16a34a; }
    .stat-card.roles  .stat-card-value { color: #d97706; }

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

    .stat-bar-fill { height: 100%; border-radius: 2rem; transition: width 1s ease; }
    .stat-card.total  .stat-bar-fill { background: #4f46e5; }
    .stat-card.active .stat-bar-fill { background: #16a34a; }
    .stat-card.roles  .stat-bar-fill { background: #d97706; }

    .stat-card-meta {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.4rem;
    }

    .accounts-shell {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        background: #fff;
        animation: statIn 0.5s ease 0.25s both;
    }

    .accounts-shell-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .accounts-shell-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.15rem;
        color: #0f172a;
    }

    .accounts-toolbar {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    .accounts-toolbar .form-select {
        min-width: 220px;
        border-radius: 0.625rem;
    }

    .btn-filter-accounts {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1rem;
        border-radius: 0.625rem;
        border: none;
        background: #eef2ff;
        color: #4f46e5;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-filter-accounts:hover { background: #e0e7ff; color: #4338ca; }

    .btn-clear-filter {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.55rem 0.85rem;
        border-radius: 0.625rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-clear-filter:hover { background: #f8fafc; color: #334155; }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(79, 70, 229, 0.08);
        color: #4f46e5;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .accounts-shell-body { padding: 0; }

    #accountsTable {
        width: 100% !important;
        margin: 0 !important;
    }

    #accountsTable thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.9rem 1rem;
        background: #f8fafc;
        white-space: nowrap;
    }

    #accountsTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #accountsTable tbody tr:hover { background: #f8fafc; }

    .account-name {
        font-weight: 600;
        color: #0f172a;
    }

    .account-email {
        font-size: 0.82rem;
        color: #64748b;
    }

    .account-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .account-badge.role {
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
    }

    .account-badge.role-global {
        background: rgba(13, 110, 253, 0.1);
        color: #1d4ed8;
    }

    .account-badge.status-active {
        background: rgba(22, 163, 74, 0.1);
        color: #15803d;
    }

    .account-badge.status-inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #b91c1c;
    }

    .btn-edit-account {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        border: none;
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        text-decoration: none;
        transition: background 0.2s, transform 0.2s;
    }

    .btn-edit-account:hover {
        background: #4f46e5;
        color: #fff;
        transform: translateY(-1px);
    }

    .accounts-empty {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #64748b;
    }

    .accounts-empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .field-error {
        color: #ef4444;
        font-size: 0.76rem;
        padding: 0 1.5rem 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid accounts-page px-3 px-lg-4 mt-3">

    {{-- Hero --}}
    <div class="accounts-hero">
        <div class="accounts-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="accounts-hero-badge">
                        <i class="bi bi-people-fill"></i> User Management
                    </div>
                    <h2>User Accounts</h2>
                    <p>View and manage login accounts across all roles. Filter by role or create new accounts for staff.</p>
                    <div class="accounts-hero-actions">
                        <a href="{{ route('user-management-create-account') }}" class="btn-hero-primary">
                            <i class="bi bi-person-plus-fill"></i> Create Account
                        </a>
                        <a href="{{ route('storemapping') }}" class="btn-hero-ghost">
                            <i class="bi bi-shop"></i> Store Mapping
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-flex justify-content-end align-items-start">
                    @include('layouts.partials.breadcrumb', [
                        'variant' => 'dark',
                        'items' => [
                            ['label' => 'Home', 'url' => route('dashboard'), 'icon' => 'bi-house-door-fill'],
                            ['label' => 'User Management', 'url' => route('user-management-list-create-account'), 'icon' => 'bi-people-fill'],
                            ['label' => 'Accounts', 'active' => true, 'icon' => 'bi-person-lock-fill'],
                        ],
                    ])
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card total">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Total Accounts</p>
                        <div class="stat-card-value" data-count="{{ $totalAccounts }}">{{ number_format($totalAccounts) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-person-lock-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">Registered system users</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card active">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Active Accounts</p>
                        <div class="stat-card-value" data-count="{{ $activeAccounts }}">{{ number_format($activeAccounts) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $activePct }}%"></div></div>
                <p class="stat-card-meta">{{ $inactiveAccounts }} inactive</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card roles">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Roles In Use</p>
                        <div class="stat-card-value" data-count="{{ $rolesInUse }}">{{ number_format($rolesInUse) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-tags-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $totalAccounts > 0 ? min(100, round(($rolesInUse / max($listCategory->count(), 1)) * 100)) : 0 }}%"></div></div>
                <p class="stat-card-meta">Roles with assigned users</p>
            </div>
        </div>
    </div>

    {{-- Accounts table --}}
    <div class="accounts-shell">
        <div class="accounts-shell-head">
            <div>
                <h5><i class="bi bi-table me-2 text-primary"></i>Account Directory</h5>
                <p class="text-secondary small mb-0">
                    @if($selectedCategory)
                        Showing accounts for selected role
                    @else
                        Showing all user accounts
                    @endif
                </p>
            </div>
            <form id="filterAccountsForm" class="accounts-toolbar" action="{{ route('user-management-get-accounts') }}" method="POST">
                @csrf
                <select id="category" name="category" class="form-select">
                    <option value="">All roles</option>
                    @foreach ($listCategory as $catItem)
                        <option value="{{ $catItem->cat_id }}" {{ (string) $selectedCategory === (string) $catItem->cat_id ? 'selected' : '' }}>
                            {{ $catItem->cat_name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn-filter-accounts">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                @if($selectedCategory)
                    <a href="{{ route('user-management-list-create-account') }}" class="btn-clear-filter">
                        <i class="bi bi-x-lg"></i> Clear
                    </a>
                @endif
                <span class="record-count-badge">
                    <i class="bi bi-collection"></i> {{ $resultCount }} shown
                </span>
            </form>
        </div>

        @error('category')<div class="field-error">{{ $message }}</div>@enderror

        <div class="accounts-shell-body">
            @if($showTable && $userList->count() > 0)
                <div class="table-responsive">
                    <table id="accountsTable" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userList as $account)
                                @php
                                    $role = $roleMeta[$account->user_cat] ?? null;
                                @endphp
                                <tr>
                                    <td class="text-secondary">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="account-name">{{ $account->name }}</div>
                                        <div class="account-email">{{ $account->email }}</div>
                                    </td>
                                    <td>
                                        <span class="account-badge role {{ $role && $role->access_all_stores ? 'role-global' : '' }}">
                                            @if($role && $role->access_all_stores)
                                                <i class="bi bi-globe2"></i>
                                            @else
                                                <i class="bi bi-shield"></i>
                                            @endif
                                            {{ $account->getUserCategory() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="account-badge {{ $account->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                            {{ $account->status }}
                                        </span>
                                    </td>
                                    <td class="text-secondary small">
                                        {{ $account->created_at ? \Carbon\Carbon::parse($account->created_at)->format('M j, Y') : '—' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('user-management-edit-user-account', Crypt::encrypt($account->id)) }}"
                                            class="btn-edit-account" title="Edit account">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="accounts-empty">
                    <div class="accounts-empty-icon"><i class="bi bi-inbox"></i></div>
                    <h6 class="fw-semibold text-dark mb-1">No accounts found</h6>
                    <p class="small mb-3">
                        @if($selectedCategory)
                            No users are assigned to this role yet.
                        @else
                            No user accounts exist in the system.
                        @endif
                    </p>
                    <a href="{{ route('user-management-create-account') }}" class="btn-hero-primary" style="display:inline-flex;">
                        <i class="bi bi-person-plus-fill"></i> Create Account
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const AccountListAlert = {
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

document.getElementById('filterAccountsForm').addEventListener('submit', function (e) {
    const category = document.getElementById('category').value;
    if (!category) {
        e.preventDefault();
        window.location.href = '{{ route('user-management-list-create-account') }}';
    }
});

@if($showTable && $userList->count() > 0)
$(document).ready(function () {
    $('#accountsTable').DataTable({
        order: [[1, 'asc']],
        pageLength: 10,
        language: { search: '', searchPlaceholder: 'Search accounts...' },
        columnDefs: [{ orderable: false, targets: [5] }],
        dom: '<"d-flex justify-content-between align-items-center px-3 pt-3 pb-2"lf>rt<"d-flex justify-content-between align-items-center px-3 py-3"ip>',
    });
});
@endif

@if(session('success_message'))
AccountListAlert.success('Success', @json(session('success_message')));
@endif

@if(session('error_message'))
AccountListAlert.error('Error', @json(session('error_message')));
@endif
</script>
@endsection
