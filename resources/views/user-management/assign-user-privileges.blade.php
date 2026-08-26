@php
    $pageName = 'user';
    $subpageName = 'assign_priv';
    $privilegePct = $totalScreens > 0 ? round(($totalAssignedLinks / max($totalScreens * max($totalRoles, 1), 1)) * 100) : 0;
    $rolesConfiguredPct = $totalRoles > 0 ? round(($rolesWithPrivileges / $totalRoles) * 100) : 0;
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .priv-page { padding: 0 0.5rem 2rem; }

    .priv-hero {
        background: linear-gradient(135deg, #064e3b 0%, #059669 55%, #34d399 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(5, 150, 105, 0.28);
    }

    .priv-hero::before,
    .priv-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .priv-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .priv-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .priv-hero-inner { position: relative; z-index: 1; }

    .priv-hero-badge {
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

    .priv-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .priv-hero p {
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

    .stat-card.roles  .stat-card-icon { background: rgba(5, 150, 105, 0.12); color: #059669; }
    .stat-card.screens .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-card.configured .stat-card-icon { background: rgba(217, 119, 6, 0.12); color: #d97706; }

    .stat-card-value {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.roles  .stat-card-value { color: #059669; }
    .stat-card.screens .stat-card-value { color: #0d6efd; }
    .stat-card.configured .stat-card-value { color: #d97706; }

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
    .stat-card.roles  .stat-bar-fill { background: #059669; }
    .stat-card.screens .stat-bar-fill { background: #0d6efd; }
    .stat-card.configured .stat-bar-fill { background: #d97706; }

    .stat-card-meta {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.4rem;
    }

    .priv-shell {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        background: #fff;
        animation: statIn 0.5s ease 0.25s both;
    }

    .priv-shell-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .priv-shell-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.15rem;
        color: #0f172a;
    }

    .priv-toolbar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .priv-toolbar .form-select {
        min-width: 220px;
        border-radius: 0.625rem;
    }

    .role-meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.76rem;
        font-weight: 600;
        background: #ecfdf5;
        color: #047857;
    }

    .role-meta-badge.global {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .btn-save-priv {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 0.625rem;
        border: none;
        background: linear-gradient(135deg, #059669, #10b981);
        color: #fff;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 4px 16px rgba(5, 150, 105, 0.35);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-save-priv:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(5, 150, 105, 0.4);
        color: #fff;
    }

    .btn-save-priv:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    .priv-shell-body { padding: 1.5rem; }

    .priv-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 0.75rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.25rem;
    }

    .priv-summary-count {
        font-size: 0.875rem;
        font-weight: 600;
        color: #334155;
    }

    .priv-summary-count span {
        color: #059669;
    }

    .priv-loading {
        display: none;
        text-align: center;
        padding: 2.5rem 1rem;
        color: #64748b;
        font-size: 0.875rem;
    }

    .priv-loading.active { display: block; }

    .priv-empty {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #64748b;
    }

    .priv-empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(5, 150, 105, 0.1);
        color: #059669;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .priv-group {
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        overflow: hidden;
        margin-bottom: 1rem;
        background: #fff;
    }

    .priv-group-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1.15rem;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
        border-bottom: 1px solid #f1f5f9;
    }

    .priv-group-head h6 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 0.9rem;
        margin: 0;
        color: #0f172a;
    }

    .priv-group-select-all {
        font-size: 0.76rem;
        color: #059669;
        cursor: pointer;
        font-weight: 600;
        user-select: none;
    }

    .priv-group-select-all:hover { text-decoration: underline; }

    .priv-item-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 0.5rem;
        padding: 1rem;
    }

    .priv-item {
        display: flex;
        align-items: flex-start;
        gap: 0.55rem;
        padding: 0.65rem 0.85rem;
        border-radius: 0.625rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        cursor: pointer;
        margin: 0;
        transition: border-color 0.15s, background 0.15s;
    }

    .priv-item:has(input:checked) {
        border-color: #059669;
        background: #ecfdf5;
    }

    .priv-item input { margin-top: 0.15rem; cursor: pointer; flex-shrink: 0; }

    .priv-item span {
        font-size: 0.82rem;
        color: #334155;
        line-height: 1.35;
    }

    .field-error {
        color: #ef4444;
        font-size: 0.76rem;
        margin-top: 0.35rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid priv-page px-3 px-lg-4 mt-3">

    {{-- Hero --}}
    <div class="priv-hero">
        <div class="priv-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="priv-hero-badge">
                        <i class="bi bi-shield-lock-fill"></i> User Management
                    </div>
                    <h2>Assign Role Privileges</h2>
                    <p>Choose a user role and control which screens its members can access across Stock Shield.</p>
                </div>
                <div class="col-lg-4 d-none d-lg-flex justify-content-end align-items-start">
                    @include('layouts.partials.breadcrumb', [
                        'variant' => 'dark',
                        'items' => [
                            ['label' => 'Home', 'url' => route('dashboard'), 'icon' => 'bi-house-door-fill'],
                            ['label' => 'User Management', 'url' => route('user-management-add-category'), 'icon' => 'bi-people-fill'],
                            ['label' => 'Privileges', 'active' => true, 'icon' => 'bi-shield-check'],
                        ],
                    ])
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card roles">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">User Roles</p>
                        <div class="stat-card-value" data-count="{{ $totalRoles }}">{{ number_format($totalRoles) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-people-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">Roles available to configure</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card screens">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Available Screens</p>
                        <div class="stat-card-value" data-count="{{ $totalScreens }}">{{ number_format($totalScreens) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-grid-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">Active menu items in the system</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card configured">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Roles Configured</p>
                        <div class="stat-card-value" data-count="{{ $rolesWithPrivileges }}">{{ number_format($rolesWithPrivileges) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-check2-square"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $rolesConfiguredPct }}%"></div></div>
                <p class="stat-card-meta">{{ $rolesConfiguredPct }}% of roles have privileges</p>
            </div>
        </div>
    </div>

    {{-- Main panel --}}
    <form id="privilegeForm">
        @csrf
        <div class="priv-shell">
            <div class="priv-shell-head">
                <div>
                    <h5><i class="bi bi-sliders me-2 text-success"></i>Privilege Assignment</h5>
                    <p class="text-secondary small mb-0">Select a role, tick the screens it should access, then save.</p>
                </div>
                <div class="priv-toolbar">
                    <select class="form-select" name="category" id="category">
                        <option value="" disabled {{ old('category') ? '' : 'selected' }}>— Select role —</option>
                        @foreach ($userCatList as $cat)
                            <option value="{{ $cat->cat_id }}" {{ old('category') == $cat->cat_id ? 'selected' : '' }}>
                                {{ $cat->cat_name }}
                            </option>
                        @endforeach
                    </select>
                    <span id="roleMetaBadge" class="role-meta-badge d-none"></span>
                    <button type="submit" id="assign" class="btn-save-priv" disabled>
                        <i class="bi bi-check2-circle"></i> Save Privileges
                    </button>
                </div>
            </div>

            <div class="priv-shell-body">
                <div id="caterror" class="field-error"></div>

                <div class="priv-summary d-none" id="privSummary">
                    <div class="priv-summary-count">
                        <span id="selectedCount">0</span> of <span id="totalCount">0</span> screens selected
                    </div>
                    <label class="priv-group-select-all mb-0" id="selectAllScreens">
                        <input type="checkbox" class="form-check-input me-1" id="selectAllToggle"> Select all screens
                    </label>
                </div>

                <div class="priv-loading" id="privLoading">
                    <div class="spinner-border text-success spinner-border-sm me-2" role="status"></div>
                    Loading privileges for selected role…
                </div>

                <div id="listarea">
                    <div class="priv-empty" id="privEmpty">
                        <div class="priv-empty-icon"><i class="bi bi-shield-check"></i></div>
                        <h6 class="fw-semibold text-dark mb-1">Select a role to begin</h6>
                        <p class="small mb-0">Choose a user role above to view and assign its screen privileges.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
const roleOptions = @json($roleOptions);

const PrivAlert = {
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

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('input[name="priv_check[]"]:checked').length;
    const total = document.querySelectorAll('input[name="priv_check[]"]').length;
    document.getElementById('selectedCount').textContent = checked;
    document.getElementById('totalCount').textContent = total;

    const selectAll = document.getElementById('selectAllToggle');
    if (selectAll) {
        selectAll.checked = total > 0 && checked === total;
        selectAll.indeterminate = checked > 0 && checked < total;
    }
}

function renderPrivilegeGroups(data) {
    const listarea = document.getElementById('listarea');
    const summary = document.getElementById('privSummary');
    const empty = document.getElementById('privEmpty');

    if (!data.groups || data.groups.length === 0) {
        summary.classList.add('d-none');
        listarea.innerHTML = '<div class="priv-empty"><div class="priv-empty-icon"><i class="bi bi-exclamation-circle"></i></div><h6 class="fw-semibold text-dark mb-1">No screens available</h6><p class="small mb-0">There are no active menu items to assign.</p></div>';
        document.getElementById('assign').disabled = true;
        return;
    }

    summary.classList.remove('d-none');
    if (empty) empty.remove();

    let html = '';

    data.groups.forEach(function (group, groupIndex) {
        html += '<div class="priv-group" data-group="' + groupIndex + '">';
        html += '<div class="priv-group-head">';
        html += '<h6><i class="bi bi-folder2-open me-2 text-success"></i>' + escapeHtml(group.parent_name) + '</h6>';
        html += '<span class="priv-group-select-all" data-group-select="' + groupIndex + '">Select all</span>';
        html += '</div>';
        html += '<div class="priv-item-grid">';

        group.items.forEach(function (item) {
            html += '<label class="priv-item">';
            html += '<input type="checkbox" class="form-check-input priv-check" name="priv_check[]" value="' + item.id + '" data-group="' + groupIndex + '"' + (item.checked ? ' checked' : '') + '>';
            html += '<span>' + escapeHtml(item.name) + '</span>';
            html += '</label>';
        });

        html += '</div></div>';
    });

    listarea.innerHTML = html;
    document.getElementById('assign').disabled = false;
    updateSelectedCount();

    listarea.querySelectorAll('.priv-check').forEach(function (input) {
        input.addEventListener('change', updateSelectedCount);
    });

    listarea.querySelectorAll('[data-group-select]').forEach(function (el) {
        el.addEventListener('click', function () {
            const groupIndex = this.getAttribute('data-group-select');
            const boxes = listarea.querySelectorAll('input[name="priv_check[]"][data-group="' + groupIndex + '"]');
            const allChecked = Array.from(boxes).every(function (cb) { return cb.checked; });
            boxes.forEach(function (cb) { cb.checked = !allChecked; });
            updateSelectedCount();
        });
    });
}

function updateRoleMeta(catId) {
    const badge = document.getElementById('roleMetaBadge');
    const role = roleOptions.find(function (r) { return String(r.id) === String(catId); });

    if (!role) {
        badge.classList.add('d-none');
        return;
    }

    badge.classList.remove('d-none');
    badge.classList.toggle('global', role.global);
    badge.innerHTML = role.global
        ? '<i class="bi bi-globe2"></i> Access all stores'
        : '<i class="bi bi-shop"></i> Store-scoped role';
}

function getEmptyStateHtml() {
    return '<div class="priv-empty"><div class="priv-empty-icon"><i class="bi bi-shield-check"></i></div><h6 class="fw-semibold text-dark mb-1">Select a role to begin</h6><p class="small mb-0">Choose a user role above to view and assign its screen privileges.</p></div>';
}

function loadPrivileges(catId) {
    const loading = document.getElementById('privLoading');
    const assignBtn = document.getElementById('assign');

    if (!catId) {
        document.getElementById('listarea').innerHTML = getEmptyStateHtml();
        document.getElementById('privSummary').classList.add('d-none');
        assignBtn.disabled = true;
        updateRoleMeta(null);
        return;
    }

    loading.classList.add('active');
    assignBtn.disabled = true;
    document.getElementById('caterror').textContent = '';
    updateRoleMeta(catId);

    $.ajax({
        type: 'POST',
        url: '{{ route('get-category-privileges') }}',
        data: $('#privilegeForm').serialize(),
        dataType: 'json',
        success: function (data) {
            loading.classList.remove('active');
            renderPrivilegeGroups(data);
        },
        error: function () {
            loading.classList.remove('active');
            PrivAlert.error('Load Failed', 'Could not load privileges for this role. Please try again.');
        },
    });
}

document.getElementById('category').addEventListener('change', function () {
    loadPrivileges(this.value);
});

document.getElementById('selectAllToggle').addEventListener('change', function () {
    document.querySelectorAll('input[name="priv_check[]"]').forEach(function (cb) {
        cb.checked = document.getElementById('selectAllToggle').checked;
    });
    updateSelectedCount();
});

document.getElementById('privilegeForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const catId = $.trim($('#category').val());
    document.getElementById('caterror').textContent = '';

    if (!catId) {
        document.getElementById('caterror').textContent = 'Please select a role first.';
        return;
    }

    const assignBtn = document.getElementById('assign');
    assignBtn.disabled = true;

    $.ajax({
        type: 'POST',
        url: '{{ route('save-user-privileges') }}',
        data: $('#privilegeForm').serialize(),
        success: function (response) {
            assignBtn.disabled = false;

            if (response === 'ok') {
                PrivAlert.success('Privileges Saved', 'Screen access was updated for this role.');
                loadPrivileges(catId);
            } else if (response === 'unchecked') {
                PrivAlert.error('Nothing Selected', 'Select at least one screen before saving.');
            } else if (response === 'unselected') {
                PrivAlert.error('No Role Selected', 'Choose a role before saving privileges.');
            } else {
                PrivAlert.error('Save Failed', 'Privilege assignment could not be completed.');
            }
        },
        error: function () {
            assignBtn.disabled = false;
            PrivAlert.error('Something Went Wrong', 'Please try again.');
        },
    });
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
loadPrivileges(@json(old('category')));
@endif
</script>
@endsection
