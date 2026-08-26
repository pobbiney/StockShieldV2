@php
    $pageName = 'setup';
    $subpageName = 'department';
    $activePct = $totalDepartments > 0 ? round(($activeCount / $totalDepartments) * 100) : 0;
    $inactivePct = $totalDepartments > 0 ? round(($inactiveCount / $totalDepartments) * 100) : 0;
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .dept-page { padding: 0 0.5rem 2rem; }

    .dept-hero {
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 55%, #14b8a6 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(13, 148, 136, 0.28);
    }

    .dept-hero::before,
    .dept-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .dept-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .dept-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .dept-hero-inner { position: relative; z-index: 1; }

    .dept-hero-badge {
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

    .dept-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
    }

    .dept-hero p {
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

    .stat-card.total   .stat-card-icon { background: rgba(13, 148, 136, 0.12); color: #0d9488; }
    .stat-card.active  .stat-card-icon { background: rgba(22, 163, 74, 0.12);  color: #16a34a; }
    .stat-card.inactive .stat-card-icon { background: rgba(100, 116, 139, 0.12); color: #64748b; }

    .stat-card-value {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.total    .stat-card-value { color: #0d9488; }
    .stat-card.active   .stat-card-value { color: #16a34a; }
    .stat-card.inactive .stat-card-value { color: #64748b; }

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
    .stat-card.total    .stat-bar-fill { background: #0d9488; }
    .stat-card.active   .stat-bar-fill { background: #16a34a; }
    .stat-card.inactive .stat-bar-fill { background: #94a3b8; }

    .stat-card-meta {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.4rem;
    }

    .dept-shell {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        background: #fff;
        animation: statIn 0.5s ease 0.25s both;
    }

    .dept-shell-form .dept-shell-head {
        padding: 1rem 1.25rem;
    }

    .dept-shell-form .dept-shell-body {
        padding: 1.15rem 1.25rem 1.25rem;
    }

    .dept-shell-form .form-section-title {
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
    }

    .dept-shell-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
    }

    .dept-shell-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.15rem;
        color: #0f172a;
    }

    .dept-shell-body { padding: 1.5rem; }

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
        background: rgba(13, 148, 136, 0.1);
        color: #0d9488;
        font-size: 0.85rem;
    }

    .field-error {
        color: #dc3545;
        font-size: 0.78rem;
        margin-top: 0.35rem;
    }

    .btn-create-dept {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 1.35rem;
        border-radius: 0.625rem;
        border: none;
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-create-dept:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(13, 148, 136, 0.42);
        color: #fff;
    }

    .dept-table-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        background: rgba(13, 148, 136, 0.08);
        color: #0d9488;
        font-size: 0.78rem;
        font-weight: 600;
    }

    #deptTable thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        border-bottom-width: 1px;
        padding: 0.85rem 1rem;
        background: #f8fafc;
    }

    #deptTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-color: #f1f5f9;
        font-size: 0.875rem;
    }

    .dept-name-cell {
        font-weight: 600;
        color: #0f172a;
    }

    .dept-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.28rem 0.65rem;
        border-radius: 2rem;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .dept-badge.status-active {
        background: rgba(22, 163, 74, 0.1);
        color: #15803d;
    }

    .dept-badge.status-inactive {
        background: rgba(100, 116, 139, 0.1);
        color: #475569;
    }

    .dept-badge.staff-count {
        background: rgba(13, 148, 136, 0.1);
        color: #0f766e;
    }

    .btn-edit-dept {
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #0d9488;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }

    .btn-edit-dept:hover {
        background: rgba(13, 148, 136, 0.08);
        border-color: rgba(13, 148, 136, 0.25);
        color: #0f766e;
    }

    .dept-modal-content { border-radius: 1rem !important; overflow: hidden; }

    .dept-modal-header {
        padding: 1.35rem 1.5rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #f0fdfa 0%, #fff 100%);
    }

    .dept-modal-header .modal-title {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
        color: #0f172a;
    }

    .dept-modal-footer {
        padding: 1rem 1.5rem 1.35rem;
        border-top: 1px solid #f1f5f9;
        background: #fafafa;
    }

    .btn-modal-save {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.15rem;
        border-radius: 0.625rem;
        border: none;
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-modal-save:hover { color: #fff; opacity: 0.95; }

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

    .empty-dept-state {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #94a3b8;
    }

    .empty-dept-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: block;
        color: #cbd5e1;
    }
</style>
@endsection

@section('content')
<div class="container-fluid dept-page px-3 px-lg-4 mt-3">

    {{-- Hero --}}
    <div class="dept-hero">
        <div class="dept-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="dept-hero-badge">
                        <i class="bi bi-gear-fill"></i> Settings
                    </div>
                    <h2>Manage Departments</h2>
                    <p>Create and organize departments used for staff assignment and reporting across Stock Shield.</p>
                </div>
                <div class="col-lg-4 d-none d-lg-flex justify-content-end align-items-start">
                    @include('layouts.partials.breadcrumb', [
                        'variant' => 'dark',
                        'items' => [
                            ['label' => 'Home', 'url' => route('dashboard'), 'icon' => 'bi-house-door-fill'],
                            ['label' => 'Settings', 'url' => route('department'), 'icon' => 'bi-gear-fill'],
                            ['label' => 'Departments', 'active' => true, 'icon' => 'bi-building'],
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
                        <p class="stat-card-label">Total Departments</p>
                        <div class="stat-card-value" data-count="{{ $totalDepartments }}">{{ number_format($totalDepartments) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-building"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%"></div></div>
                <p class="stat-card-meta">Departments configured in the system</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card active">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Active</p>
                        <div class="stat-card-value" data-count="{{ $activeCount }}">{{ number_format($activeCount) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $activePct }}%"></div></div>
                <p class="stat-card-meta">{{ $activePct }}% of departments are active</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card inactive">
                <div class="stat-card-top">
                    <div>
                        <p class="stat-card-label">Inactive</p>
                        <div class="stat-card-value" data-count="{{ $inactiveCount }}">{{ number_format($inactiveCount) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-pause-circle-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $inactivePct }}%"></div></div>
                <p class="stat-card-meta">Departments not currently in use</p>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        {{-- Create form --}}
        <div class="col-lg-5">
            <div class="dept-shell dept-shell-form">
                <div class="dept-shell-head">
                    <h5><i class="bi bi-plus-circle me-2 text-success"></i>Add New Department</h5>
                    <p class="text-secondary small mb-0">Register a department for staff and organizational grouping.</p>
                </div>
                <div class="dept-shell-body">
                    <form action="{{ route('add-department-process') }}" method="POST">
                        @csrf

                        <div class="form-section-title">
                            <i class="bi bi-pencil-square"></i> Department Details
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="dept_name">Department Name</label>
                            <input type="text" class="form-control" id="dept_name" name="name"
                                value="{{ old('name') }}" placeholder="e.g. Procurement">
                            @error('name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="dept_status">Status</label>
                            <select class="form-select" id="dept_status" name="status">
                                <option value="" selected disabled>Choose status</option>
                                <option value="Active" {{ old('status') === 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn-create-dept">
                                <i class="bi bi-plus-lg"></i> Add Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Departments list --}}
        <div class="col-lg-7">
            <div class="dept-shell">
                <div class="dept-shell-head">
                    <div class="dept-table-head w-100 mb-0">
                        <div>
                            <h5><i class="bi bi-list-ul me-2 text-success"></i>Existing Departments</h5>
                            <p class="text-secondary small mb-0">All departments configured in the system.</p>
                        </div>
                        <span class="record-count-badge">
                            <i class="bi bi-collection"></i> {{ $totalDepartments }} departments
                        </span>
                    </div>
                </div>
                <div class="dept-shell-body pt-3">
                    @if($list->isEmpty())
                        <div class="empty-dept-state">
                            <i class="bi bi-building"></i>
                            <p class="mb-0 fw-semibold">No departments yet</p>
                            <p class="small mb-0">Use the form to add your first department.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="deptTable" class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Staff</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list as $dept)
                                        <tr>
                                            <td class="text-secondary">{{ $loop->iteration }}</td>
                                            <td class="dept-name-cell">{{ $dept->name }}</td>
                                            <td>
                                                <span class="dept-badge staff-count">
                                                    <i class="bi bi-people"></i>
                                                    {{ (int) ($staffCounts[$dept->id] ?? 0) }} staff
                                                </span>
                                            </td>
                                            <td>
                                                <span class="dept-badge {{ $dept->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                                    {{ $dept->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn-edit-dept btn-open-edit-dept"
                                                    title="Edit department"
                                                    data-id="{{ $dept->id }}"
                                                    data-name="{{ $dept->name }}"
                                                    data-status="{{ $dept->status }}">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('settings.edit-department-modal')
@endsection

@section('scripts')
<script>
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

@if($list->count() > 0)
$(document).ready(function () {
    $('#deptTable').DataTable({
        pageLength: 10,
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [4] },
        ],
        language: {
            search: '',
            searchPlaceholder: 'Search departments…',
            lengthMenu: 'Show _MENU_',
            info: 'Showing _START_–_END_ of _TOTAL_',
            paginate: { previous: '‹', next: '›' },
        },
        dom: '<"d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3"lf>t<"d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3"ip>',
    });
});
@endif

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-open-edit-dept');
    if (!btn) return;

    document.getElementById('docID').value = btn.dataset.id || '';
    document.getElementById('docname').value = btn.dataset.name || '';
    document.getElementById('statusname').value = btn.dataset.status || 'Active';

    const modalEl = document.getElementById('editDepartmentModal');
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
});
</script>
@endsection
