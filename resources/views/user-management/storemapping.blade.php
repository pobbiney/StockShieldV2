@php
    $pageName = 'user';
    $subpageName = 'mapping';
    $totalStaff = $liststaff->count();
    $totalStores = $liststore->count();
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .sm-page { padding: 0 0.5rem 2rem; }

    .sm-hero {
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #2dd4bf 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(13, 148, 136, 0.28);
    }

    .sm-hero::before,
    .sm-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .sm-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .sm-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .sm-hero-inner { position: relative; z-index: 1; }

    .sm-hero-badge {
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

    .sm-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .sm-hero p { color: rgba(255, 255, 255, 0.88); font-size: 0.9rem; margin-bottom: 0; max-width: 560px; }
    .sm-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .sm-hero .breadcrumb-item.active { color: #fff; }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: smStatIn 0.5s ease both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }

    @keyframes smStatIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .stat-card-icon {
        width: 48px; height: 48px; border-radius: 0.875rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; margin-bottom: 1rem;
    }

    .stat-card.staff .stat-card-icon { background: rgba(13, 148, 136, 0.12); color: #0d9488; }
    .stat-card.stores .stat-card-icon { background: rgba(79, 70, 229, 0.12); color: #4f46e5; }
    .stat-card.mapped .stat-card-icon { background: rgba(234, 88, 12, 0.12); color: #ea580c; }

    .stat-card-value { font-size: 2rem; font-weight: 800; line-height: 1; margin-bottom: 0.2rem; }
    .stat-card.staff .stat-card-value { color: #0d9488; }
    .stat-card.stores .stat-card-value { color: #4f46e5; }
    .stat-card.mapped .stat-card-value { color: #ea580c; }
    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0; }

    .sm-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        background: #fff;
        animation: smStatIn 0.5s ease 0.2s both;
    }

    .sm-card-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .sm-card-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
    }

    .sm-card-body { padding: 1.5rem; }

    .sm-staff-select-wrap {
        background: linear-gradient(135deg, #f0fdfa 0%, #ecfeff 100%);
        border: 1px solid #99f6e4;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .sm-staff-select-wrap label {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #0f766e;
        margin-bottom: 0.5rem;
    }

    .sm-staff-select-wrap .form-select {
        border-radius: 0.75rem;
        border: 1.5px solid #5eead4;
        padding: 0.75rem 1rem;
        font-weight: 500;
        background-color: #fff;
    }

    .sm-staff-select-wrap .form-select:focus {
        border-color: #0d9488;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
    }

    .sm-role-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1rem;
        border-radius: 2rem;
        background: rgba(79, 70, 229, 0.1);
        color: #4338ca;
        font-size: 0.85rem;
        font-weight: 600;
        height: 100%;
        min-height: 48px;
    }

    .sm-notice {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem 1.15rem;
        border-radius: 0.875rem;
        font-size: 0.875rem;
        margin-bottom: 1.25rem;
    }

    .sm-notice.warning {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        color: #92400e;
    }

    .sm-notice.info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
    }

    .sm-notice-icon {
        width: 36px; height: 36px; border-radius: 0.625rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 1rem;
    }

    .sm-notice.warning .sm-notice-icon { background: rgba(234, 179, 8, 0.2); color: #b45309; }
    .sm-notice.info .sm-notice-icon { background: rgba(37, 99, 235, 0.15); color: #2563eb; }

    .sm-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .sm-select-all {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.9rem;
        border-radius: 2rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        user-select: none;
        transition: background 0.15s, border-color 0.15s;
    }

    .sm-select-all:hover { background: #f0fdfa; border-color: #99f6e4; }
    .sm-select-all input { cursor: pointer; }

    .sm-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(13, 148, 136, 0.1);
        color: #0d9488;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .sm-store-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.85rem;
    }

    .sm-store-item {
        position: relative;
        display: block;
        cursor: pointer;
        margin: 0;
    }

    .sm-store-item input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .sm-store-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.1rem;
        border-radius: 0.875rem;
        border: 2px solid #e2e8f0;
        background: #fff;
        transition: border-color 0.2s, background 0.2s, box-shadow 0.2s, transform 0.2s;
        height: 100%;
    }

    .sm-store-item input:checked + .sm-store-card {
        border-color: #0d9488;
        background: linear-gradient(135deg, #f0fdfa 0%, #ecfeff 100%);
        box-shadow: 0 4px 16px rgba(13, 148, 136, 0.15);
    }

    .sm-store-item input:disabled + .sm-store-card {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .sm-store-item:not(:has(input:disabled)):hover .sm-store-card {
        border-color: #5eead4;
        transform: translateY(-2px);
    }

    .sm-store-icon {
        width: 40px; height: 40px; border-radius: 0.625rem;
        display: flex; align-items: center; justify-content: center;
        background: rgba(13, 148, 136, 0.1);
        color: #0d9488;
        font-size: 1.1rem;
        flex-shrink: 0;
        transition: background 0.2s, color 0.2s;
    }

    .sm-store-item input:checked + .sm-store-card .sm-store-icon {
        background: #0d9488;
        color: #fff;
    }

    .sm-store-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.3;
    }

    .sm-store-check {
        margin-left: auto;
        width: 22px; height: 22px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 0.7rem;
        color: transparent;
        transition: border-color 0.2s, background 0.2s, color 0.2s;
    }

    .sm-store-item input:checked + .sm-store-card .sm-store-check {
        border-color: #0d9488;
        background: #0d9488;
        color: #fff;
    }

    .btn-sm-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.7rem 1.5rem;
        border-radius: 2rem;
        border: none;
        background: linear-gradient(135deg, #0f766e, #0d9488);
        color: #fff;
        font-size: 0.875rem;
        font-weight: 700;
        box-shadow: 0 4px 16px rgba(13, 148, 136, 0.35);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-sm-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(13, 148, 136, 0.4);
        color: #fff;
    }

    .btn-sm-submit:disabled {
        opacity: 0.55;
        transform: none;
        cursor: not-allowed;
    }

    .sm-flash {
        border-radius: 0.875rem;
        padding: 0.85rem 1.1rem;
        margin-bottom: 1.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sm-flash.success { background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46; }
    .sm-flash.error   { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }

    .sm-empty {
        text-align: center;
        padding: 2.5rem 1.5rem;
        color: #64748b;
        border: 2px dashed #e2e8f0;
        border-radius: 1rem;
        background: #f8fafc;
    }

    .sm-empty i { font-size: 2rem; color: #94a3b8; margin-bottom: 0.75rem; display: block; }
</style>
@endsection

@section('content')
<div class="container-fluid sm-page px-3 px-lg-4 mt-3">
    <div class="sm-hero">
        <div class="sm-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="sm-hero-badge">
                        <i class="bi bi-shop-window"></i> Store Mapping
                    </div>
                    <h2>Map Staff to Stores</h2>
                    <p>
                        Assign store access to staff members. Users with global store roles receive all active stores automatically.
                    </p>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('user-management-list-create-account') }}" class="text-decoration-none">User Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Store Mapping</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-card staff">
                <div class="stat-card-icon"><i class="bi bi-people"></i></div>
                <div class="stat-card-value">{{ number_format($totalStaff) }}</div>
                <p class="stat-card-label">Staff Members</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card stores">
                <div class="stat-card-icon"><i class="bi bi-shop"></i></div>
                <div class="stat-card-value">{{ number_format($totalStores) }}</div>
                <p class="stat-card-label">Active Stores</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card mapped">
                <div class="stat-card-icon"><i class="bi bi-check2-square"></i></div>
                <div class="stat-card-value" id="selectedStoreCount">0</div>
                <p class="stat-card-label">Stores Selected</p>
            </div>
        </div>
    </div>

    <div class="sm-card mb-5">
        <div class="sm-card-head">
            <h5><i class="bi bi-diagram-3 me-1 text-success"></i> Assign Store Access</h5>
            <span class="sm-count-badge">
                <i class="bi bi-layers"></i>
                {{ $totalStores }} store{{ $totalStores !== 1 ? 's' : '' }} available
            </span>
        </div>

        <div class="sm-card-body">
            @if (session('message_success'))
                <div class="sm-flash success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('message_success') }}
                </div>
            @endif
            @if (session('message_error'))
                <div class="sm-flash error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('message_error') }}
                </div>
            @endif

            <form id="storeMappingForm" action="{{ route('map-store-process') }}" method="POST">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-lg-7">
                        <div class="sm-staff-select-wrap h-100">
                            <label for="staffSelect"><i class="bi bi-person-badge me-1"></i> Select Staff Member</label>
                            <select id="staffSelect" name="staff_id" class="form-select select2" data-allow-clear="true" required>
                                <option value="" selected disabled>Choose a staff member…</option>
                                @foreach ($liststaff as $list)
                                    <option value="{{ $list->staff_id }}">
                                        {{ trim($list->surname . ' ' . $list->firstname) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('staff_id')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    <div class="col-lg-5 d-flex align-items-end">
                        <div id="staffRoleInfo" class="sm-role-chip w-100 d-none">
                            <i class="bi bi-shield-check"></i>
                            <span><strong>Role:</strong> <span id="staffRoleName">—</span></span>
                        </div>
                    </div>
                </div>

                <div id="globalAccessNotice" class="sm-notice warning d-none">
                    <div class="sm-notice-icon"><i class="bi bi-globe2"></i></div>
                    <div>
                        <strong>Global store access</strong>
                        <div class="small mt-1 mb-0">This user role has access to all stores. All active stores will be assigned automatically on save.</div>
                    </div>
                </div>

                <div id="storeMappingTable">
                    <div class="sm-toolbar">
                        <label class="sm-select-all mb-0" for="selectAll">
                            <input class="form-check-input m-0" type="checkbox" id="selectAll">
                            Select all stores
                        </label>
                        <span class="sm-count-badge" id="storeSelectionLabel">
                            <i class="bi bi-check2"></i>
                            <span id="selectionText">No stores selected</span>
                        </span>
                    </div>

                    @if($liststore->count() > 0)
                        <div class="sm-store-grid mb-4">
                            @foreach ($liststore as $listde)
                                <label class="sm-store-item">
                                    <input class="store-checkbox"
                                           type="checkbox"
                                           name="department_id[]"
                                           value="{{ $listde->id }}">
                                    <span class="sm-store-card">
                                        <span class="sm-store-icon"><i class="bi bi-shop"></i></span>
                                        <span class="sm-store-name">{{ $listde->name }}</span>
                                        <span class="sm-store-check"><i class="bi bi-check-lg"></i></span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="sm-empty mb-4">
                            <i class="bi bi-inbox"></i>
                            <p class="mb-0 fw-semibold">No stores configured</p>
                            <p class="small mb-0">Add stores in Settings before mapping staff.</p>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn-sm-submit" id="mapSubmitBtn" disabled>
                            <i class="bi bi-diagram-3"></i> Map Staff to Stores
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const selectAllCheckbox = document.getElementById('selectAll');
    const staffSelect = document.getElementById('staffSelect');
    const globalAccessNotice = document.getElementById('globalAccessNotice');
    const staffRoleInfo = document.getElementById('staffRoleInfo');
    const staffRoleName = document.getElementById('staffRoleName');
    const selectedStoreCount = document.getElementById('selectedStoreCount');
    const selectionText = document.getElementById('selectionText');
    const mapSubmitBtn = document.getElementById('mapSubmitBtn');

    function storeCheckboxes() {
        return document.querySelectorAll('.store-checkbox');
    }

    function updateSelectionStats() {
        const checked = Array.from(storeCheckboxes()).filter(cb => cb.checked);
        const count = checked.length;
        const total = storeCheckboxes().length;

        selectedStoreCount.textContent = count.toLocaleString();
        selectionText.textContent = count === 0
            ? 'No stores selected'
            : count + ' of ' + total + ' store' + (total !== 1 ? 's' : '') + ' selected';

        if (total > 0) {
            selectAllCheckbox.checked = count === total;
            selectAllCheckbox.indeterminate = count > 0 && count < total;
        }

        mapSubmitBtn.disabled = !staffSelect.value;
    }

    function setStoreCheckboxesDisabled(disabled) {
        storeCheckboxes().forEach(function (checkbox) {
            checkbox.disabled = disabled;
        });
        selectAllCheckbox.disabled = disabled;
    }

    selectAllCheckbox.addEventListener('change', function () {
        storeCheckboxes().forEach(function (checkbox) {
            if (!checkbox.disabled) {
                checkbox.checked = selectAllCheckbox.checked;
            }
        });
        updateSelectionStats();
    });

    storeCheckboxes().forEach(function (checkbox) {
        checkbox.addEventListener('change', updateSelectionStats);
    });

    staffSelect.addEventListener('change', function () {
        const staffId = this.value;

        if (!staffId) {
            staffRoleInfo.classList.add('d-none');
            globalAccessNotice.classList.add('d-none');
            setStoreCheckboxesDisabled(false);
            storeCheckboxes().forEach(cb => { cb.checked = false; });
            updateSelectionStats();
            return;
        }

        fetch(`{{ url('/get-staff-stores') }}/${staffId}`)
            .then(response => response.json())
            .then(data => {
                const mappedIds = (data.mapped_ids || []).map(String);

                staffRoleInfo.classList.remove('d-none');
                staffRoleName.textContent = data.role_name || 'No role assigned';

                storeCheckboxes().forEach(cb => { cb.checked = false; });

                if (data.access_all_stores) {
                    globalAccessNotice.classList.remove('d-none');
                    setStoreCheckboxesDisabled(true);
                    storeCheckboxes().forEach(cb => { cb.checked = true; });
                    updateSelectionStats();
                    return;
                }

                globalAccessNotice.classList.add('d-none');
                setStoreCheckboxesDisabled(false);

                mappedIds.forEach(id => {
                    const cb = document.querySelector(`input[name="department_id[]"][value="${id}"]`);
                    if (cb) cb.checked = true;
                });

                updateSelectionStats();
            });
    });

    @if(session('mapped_staff_id'))
    document.addEventListener('DOMContentLoaded', function () {
        staffSelect.value = '{{ session('mapped_staff_id') }}';
        staffSelect.dispatchEvent(new Event('change'));

        @if(session('mapped_departments'))
        const mapped = @json(session('mapped_departments'));
        setTimeout(function () {
            storeCheckboxes().forEach(cb => {
                cb.checked = mapped.includes(parseInt(cb.value, 10)) || mapped.includes(cb.value);
            });
            updateSelectionStats();
        }, 400);
        @endif
    });
    @endif

    updateSelectionStats();
})();
</script>
@endsection
