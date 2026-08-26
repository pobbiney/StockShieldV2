@php
    $pageName = 'user';
    $subpageName = 'user_cat';
    $activePct = $totalRoles > 0 ? round(($activeCount / $totalRoles) * 100) : 0;
    $globalPct = $totalRoles > 0 ? round(($globalAccessCount / $totalRoles) * 100) : 0;
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .role-page { padding: 0 0.5rem 2rem; }

    .role-hero {
        background: linear-gradient(135deg, #581c87 0%, #7c3aed 55%, #a78bfa 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(124, 58, 237, 0.28);
    }

    .role-hero::before,
    .role-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .role-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .role-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .role-hero-inner { position: relative; z-index: 1; }

    .role-hero-badge {
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

    .role-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .role-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 540px;
    }

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

    .stat-card.total   .stat-card-icon { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }
    .stat-card.global  .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-card.active  .stat-card-icon { background: rgba(22, 163, 74, 0.12);  color: #16a34a; }

    .stat-card-value {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.total  .stat-card-value { color: #7c3aed; }
    .stat-card.global .stat-card-value { color: #0d6efd; }
    .stat-card.active .stat-card-value { color: #16a34a; }

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
    .stat-card.total  .stat-bar-fill { background: #7c3aed; }
    .stat-card.global .stat-bar-fill { background: #0d6efd; }
    .stat-card.active .stat-bar-fill { background: #16a34a; }

    .stat-card-meta {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.4rem;
    }

    .role-shell {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        background: #fff;
        height: 100%;
        animation: statIn 0.5s ease 0.25s both;
    }

    .role-shell-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
    }

    .role-shell-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.15rem;
        color: #0f172a;
    }

    .role-shell-body { padding: 1.5rem; }

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
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
        font-size: 0.85rem;
    }

    .access-toggle-card {
        border: 1px solid #e2e8f0;
        border-radius: 0.875rem;
        padding: 1rem 1.15rem;
        background: #f8fafc;
        transition: border-color 0.2s, background 0.2s;
    }

    .access-toggle-card:has(input:checked) {
        border-color: #93c5fd;
        background: #eff6ff;
    }

    .access-toggle-card label {
        cursor: pointer;
        margin: 0;
        font-size: 0.875rem;
        color: #334155;
        line-height: 1.45;
    }

    .access-toggle-card small {
        display: block;
        color: #64748b;
        font-size: 0.76rem;
        margin-top: 0.25rem;
    }

    .btn-create-role {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.7rem 1.5rem;
        border-radius: 0.75rem;
        border: none;
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        color: #fff;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 4px 16px rgba(124, 58, 237, 0.35);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-create-role:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(124, 58, 237, 0.4);
        color: #fff;
    }

    .field-error {
        color: #ef4444;
        font-size: 0.76rem;
        margin-top: 0.35rem;
    }

    .role-table-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(124, 58, 237, 0.08);
        color: #7c3aed;
        font-size: 0.78rem;
        font-weight: 600;
    }

    #roleTable {
        width: 100% !important;
    }

    #roleTable thead th {
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

    #roleTable tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #roleTable tbody tr:hover { background: #faf5ff; }

    .role-name-cell {
        font-weight: 600;
        color: #0f172a;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .role-badge.global {
        background: rgba(13, 110, 253, 0.1);
        color: #1d4ed8;
    }

    .role-badge.scoped {
        background: rgba(100, 116, 139, 0.1);
        color: #475569;
    }

    .role-badge.status-active {
        background: rgba(22, 163, 74, 0.1);
        color: #15803d;
    }

    .role-badge.status-inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #b91c1c;
    }

    .role-badge.screens {
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
    }

    .btn-edit-role {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        border: none;
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
        transition: background 0.2s, transform 0.2s;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-edit-role:hover {
        background: #7c3aed;
        color: #fff;
        transform: translateY(-1px);
    }

    .role-actions {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .modal-role-header {
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
        background: linear-gradient(180deg, #faf5ff 0%, #fff 100%);
    }

    .modal-role-header .modal-title {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        color: #581c87;
    }

    .modal-role-footer {
        border-top: 1px solid #f1f5f9;
        padding: 1rem 1.5rem;
        background: #f8fafc;
    }

    .btn-modal-save {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.35rem;
        border-radius: 0.625rem;
        border: none;
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 14px rgba(124, 58, 237, 0.3);
    }

    .btn-modal-save:hover:not(:disabled) { color: #fff; opacity: 0.95; }
    .btn-modal-save:disabled { opacity: 0.6; cursor: not-allowed; }

    .btn-modal-cancel {
        padding: 0.65rem 1.15rem;
        border-radius: 0.625rem;
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #64748b;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-modal-cancel:hover { background: #f8fafc; color: #334155; }
</style>
@endsection

@section('content')
<div class="container-fluid role-page px-3 px-lg-4 mt-3">

    {{-- Hero --}}
    <div class="role-hero">
        <div class="role-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="role-hero-badge">
                        <i class="bi bi-tags-fill"></i> User Management
                    </div>
                    <h2>Manage User Roles</h2>
                    <p>Create roles, define store access levels, and manage how users are grouped across Stock Shield.</p>
                </div>
                <div class="col-lg-4 d-none d-lg-flex justify-content-end align-items-start">
                    @include('layouts.partials.breadcrumb', [
                        'variant' => 'dark',
                        'items' => [
                            ['label' => 'Home', 'url' => route('dashboard'), 'icon' => 'bi-house-door-fill'],
                            ['label' => 'User Management', 'url' => route('user-management-add-category'), 'icon' => 'bi-people-fill'],
                            ['label' => 'Roles', 'active' => true, 'icon' => 'bi-tags-fill'],
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
                        <p class="stat-card-label">Total Roles</p>
                        <div class="stat-card-value" data-count="{{ $totalRoles }}">{{ number_format($totalRoles) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-person-badge-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">User categories in the system</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card global">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Global Store Access</p>
                        <div class="stat-card-value" data-count="{{ $globalAccessCount }}">{{ number_format($globalAccessCount) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-globe2"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $globalPct }}%"></div></div>
                <p class="stat-card-meta">Roles with access to all stores</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card active">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Active Roles</p>
                        <div class="stat-card-value" data-count="{{ $activeCount }}">{{ number_format($activeCount) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $activePct }}%"></div></div>
                <p class="stat-card-meta">{{ $activePct }}% of roles are active</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Create form --}}
        <div class="col-lg-5">
            <div class="role-shell">
                <div class="role-shell-head">
                    <h5><i class="bi bi-plus-circle me-2 text-primary"></i>Create New Role</h5>
                    <p class="text-secondary small mb-0">Add a user category and set its store access level.</p>
                </div>
                <div class="role-shell-body">
                    <form action="{{ route('user-management-add-category-process') }}" method="POST">
                        @csrf

                        <div class="form-section-title">
                            <i class="bi bi-pencil-square"></i> Role Details
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="category_name">Role Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name"
                                value="{{ old('category_name') }}" placeholder="e.g. Store Manager">
                            @error('category_name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="access-toggle-card mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="access_all_stores" value="1"
                                    id="access_all_stores" {{ old('access_all_stores') ? 'checked' : '' }}>
                                <label class="form-check-label" for="access_all_stores">
                                    <strong>Access all stores</strong>
                                    <small>For Administrator, HOD, and Assistant HOD roles. These users skip store selection and see all stores automatically.</small>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <a href="{{ route('user-management-privilege') }}" class="text-secondary small text-decoration-none">
                                <i class="bi bi-sliders me-1"></i> Assign privileges after creating
                            </a>
                            <button type="submit" class="btn-create-role">
                                <i class="bi bi-plus-lg"></i> Create Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Roles list --}}
        <div class="col-lg-7">
            <div class="role-shell">
                <div class="role-shell-head">
                    <div class="role-table-head w-100 mb-0">
                        <div>
                            <h5><i class="bi bi-list-ul me-2 text-primary"></i>Existing Roles</h5>
                            <p class="text-secondary small mb-0">All user categories configured in the system.</p>
                        </div>
                        <span class="record-count-badge">
                            <i class="bi bi-collection"></i> {{ $totalRoles }} roles
                        </span>
                    </div>
                </div>
                <div class="role-shell-body pt-3">
                    <div class="table-responsive">
                        <table id="roleTable" class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Role Name</th>
                                    <th>Store Access</th>
                                    <th>Screens</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listCategory as $catItem)
                                    <tr>
                                        <td class="text-secondary">{{ $loop->iteration }}</td>
                                        <td class="role-name-cell">{{ $catItem->cat_name }}</td>
                                        <td>
                                            @if($catItem->access_all_stores)
                                                <span class="role-badge global"><i class="bi bi-globe2"></i> All stores</span>
                                            @else
                                                <span class="role-badge scoped"><i class="bi bi-shop"></i> Store-scoped</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="role-badge screens">
                                                <i class="bi bi-grid"></i>
                                                {{ (int) ($privilegeCounts[$catItem->cat_id] ?? 0) }} screens
                                            </span>
                                        </td>
                                        <td>
                                            <span class="role-badge {{ $catItem->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                                {{ $catItem->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="role-actions">
                                                <button type="button"
                                                    class="btn-edit-role btn-open-edit-role"
                                                    title="Edit role"
                                                    data-edit-url="{{ route('user-management-add-category-edit-process', Crypt::encrypt($catItem->cat_id)) }}"
                                                    data-name="{{ $catItem->cat_name }}"
                                                    data-status="{{ $catItem->status }}"
                                                    data-global="{{ $catItem->access_all_stores ? '1' : '0' }}">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Role Modal --}}
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-role-header">
                <h5 class="modal-title" id="editRoleModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Role
                </h5>
                <p class="text-secondary small mb-0 mt-1">Update role name, status, and store access.</p>
            </div>
            <form id="editRoleForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div id="editRoleError" class="field-error mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="edit_category_name">Role Name</label>
                        <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="edit_status">Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="access-toggle-card mb-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="access_all_stores" value="1" id="edit_access_all_stores">
                            <label class="form-check-label" for="edit_access_all_stores">
                                <strong>Access all stores</strong>
                                <small>Enable for Administrator, HOD, and Assistant HOD roles.</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-role-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-save" id="editRoleSubmit">
                        <i class="bi bi-check2"></i> Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const RoleAlert = {
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

@if($listCategory->count() > 0)
$(document).ready(function () {
    $('#roleTable').DataTable({
        order: [[1, 'asc']],
        pageLength: 10,
        language: { search: '', searchPlaceholder: 'Search roles...' },
        columnDefs: [{ orderable: false, targets: [5] }],
        dom: '<"d-flex justify-content-between align-items-center px-1 pt-1 pb-2"lf>rt<"d-flex justify-content-between align-items-center px-1 py-3"ip>',
    });
});
@endif

@if(session('success_message'))
RoleAlert.success('Role Created', @json(session('success_message')));
@endif

@if(session('error_message'))
RoleAlert.error('Something Went Wrong', @json(session('error_message')));
@endif

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-open-edit-role');
    if (!btn) return;

    const form = document.getElementById('editRoleForm');
    form.action = btn.dataset.editUrl;
    document.getElementById('edit_category_name').value = btn.dataset.name || '';
    document.getElementById('edit_status').value = btn.dataset.status || 'Active';
    document.getElementById('edit_access_all_stores').checked = btn.dataset.global === '1';
    document.getElementById('editRoleError').textContent = '';

    new bootstrap.Modal(document.getElementById('editRoleModal')).show();
});

document.getElementById('editRoleForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const submitBtn = document.getElementById('editRoleSubmit');
    const errorEl = document.getElementById('editRoleError');
    errorEl.textContent = '';
    submitBtn.disabled = true;

    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: new FormData(this),
    })
        .then(function (res) {
            return res.json().then(function (data) {
                return { ok: res.ok, status: res.status, data: data };
            });
        })
        .then(function ({ ok, data }) {
            submitBtn.disabled = false;

            if (ok && data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editRoleModal')).hide();
                RoleAlert.success('Role Updated', data.message).then(function () {
                    window.location.reload();
                });
                return;
            }

            if (data.errors) {
                errorEl.textContent = Object.values(data.errors).flat().join(' ');
                return;
            }

            errorEl.textContent = data.message || 'Could not update role.';
        })
        .catch(function () {
            submitBtn.disabled = false;
            errorEl.textContent = 'Something went wrong. Please try again.';
        });
});
</script>
@endsection
