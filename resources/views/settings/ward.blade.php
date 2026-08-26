@php
    $pageName = 'settings';
    $subpageName = 'ward';
    $activePct = $totalWards > 0 ? round(($activeCount / $totalWards) * 100) : 0;
    $inactivePct = $totalWards > 0 ? round(($inactiveCount / $totalWards) * 100) : 0;
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .ward-page { padding: 0 0.5rem 2rem; }

    .ward-hero {
        background: linear-gradient(135deg, #4338ca 0%, #6366f1 55%, #818cf8 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(99, 102, 241, 0.28);
    }

    .ward-hero::before, .ward-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .ward-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .ward-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }
    .ward-hero-inner { position: relative; z-index: 1; }

    .ward-hero-badge {
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

    .ward-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
    }

    .ward-hero p { color: rgba(255, 255, 255, 0.88); font-size: 0.9rem; margin-bottom: 0; max-width: 540px; }

    .store-chip-banner {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 1rem;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        background: rgba(255, 255, 255, 0.15);
        font-size: 0.82rem;
        font-weight: 600;
    }

    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .stat-card-icon {
        width: 48px; height: 48px; border-radius: 0.875rem;
        display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
    }

    .stat-card.total .stat-card-icon { background: rgba(99, 102, 241, 0.12); color: #6366f1; }
    .stat-card.active .stat-card-icon { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
    .stat-card.inactive .stat-card-icon { background: rgba(100, 116, 139, 0.12); color: #64748b; }
    .stat-card-value { font-size: 2.25rem; font-weight: 800; line-height: 1; margin-bottom: 0.2rem; }
    .stat-card.total .stat-card-value { color: #6366f1; }
    .stat-card.active .stat-card-value { color: #16a34a; }
    .stat-card.inactive .stat-card-value { color: #64748b; }
    .stat-card-label { font-size: 0.82rem; color: #64748b; margin: 0 0 0.85rem; }
    .stat-bar-wrap { height: 4px; background: #f1f5f9; border-radius: 2rem; overflow: hidden; }
    .stat-bar-fill { height: 100%; border-radius: 2rem; }
    .stat-card-meta { font-size: 0.72rem; color: #94a3b8; margin-top: 0.4rem; }

    .ward-shell {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        background: #fff;
    }

    .ward-shell-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
    }

    .ward-shell-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.15rem;
        color: #0f172a;
    }

    .ward-shell-body { padding: 1.5rem; }

    .field-error { color: #dc3545; font-size: 0.78rem; margin-top: 0.35rem; }

    .btn-create-ward {
        display: inline-flex; align-items: center; gap: 0.45rem;
        padding: 0.65rem 1.35rem; border-radius: 0.625rem; border: none;
        background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
        color: #fff; font-weight: 600; font-size: 0.875rem;
    }

    .btn-create-ward:hover { color: #fff; opacity: 0.95; }

    .record-count-badge {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.35rem 0.75rem; border-radius: 2rem;
        background: rgba(99, 102, 241, 0.08); color: #6366f1; font-size: 0.78rem; font-weight: 600;
    }

    #wardTable thead th {
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
        color: #64748b; border-bottom-width: 1px; padding: 0.85rem 1rem; background: #f8fafc;
    }

    #wardTable tbody td { padding: 0.9rem 1rem; vertical-align: middle; border-color: #f1f5f9; font-size: 0.875rem; }

    .ward-badge {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.28rem 0.65rem; border-radius: 2rem; font-size: 0.72rem; font-weight: 600;
    }

    .ward-badge.status-active { background: rgba(22, 163, 74, 0.1); color: #15803d; }
    .ward-badge.status-inactive { background: rgba(100, 116, 139, 0.1); color: #475569; }

    .btn-edit-ward {
        width: 34px; height: 34px; border-radius: 0.5rem; border: 1px solid #e2e8f0;
        background: #fff; color: #6366f1; display: inline-flex; align-items: center; justify-content: center;
    }

    .btn-edit-ward:hover { background: rgba(99, 102, 241, 0.08); border-color: rgba(99, 102, 241, 0.25); }

    .ward-modal-content { border-radius: 1rem !important; overflow: hidden; }
    .ward-modal-header { padding: 1.35rem 1.5rem 1rem; border-bottom: 1px solid #f1f5f9; background: linear-gradient(180deg, #eef2ff 0%, #fff 100%); }
    .ward-modal-header .modal-title { font-family: "SUSE", sans-serif; font-weight: 700; font-size: 1.05rem; color: #0f172a; }
    .ward-modal-footer { padding: 1rem 1.5rem 1.35rem; border-top: 1px solid #f1f5f9; background: #fafafa; }

    .btn-modal-save {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.65rem 1.15rem; border-radius: 0.625rem; border: none;
        background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%); color: #fff; font-weight: 600;
    }

    .btn-modal-cancel {
        padding: 0.65rem 1.15rem; border-radius: 0.625rem; border: 1px solid #cbd5e1;
        background: #fff; color: #64748b; font-weight: 600;
    }

    .empty-ward-state { text-align: center; padding: 2.5rem 1rem; color: #94a3b8; }
    .empty-ward-state i { font-size: 2.5rem; margin-bottom: 0.75rem; display: block; color: #cbd5e1; }
</style>
@endsection

@section('content')
<div class="container-fluid ward-page px-3 px-lg-4 mt-3">

    <div class="ward-hero">
        <div class="ward-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="ward-hero-badge"><i class="bi bi-hospital"></i> Satellite Settings</div>
                    <h2>Manage Wards</h2>
                    <p>Add and edit wards for your satellite store. Wards are used when issuing items internally.</p>
                    @if($activeStore)
                        <span class="store-chip-banner"><i class="bi bi-shop"></i> {{ $activeStore->name }}</span>
                    @endif
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active">Ward</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card total">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="stat-card-label">Total Wards</p>
                        <div class="stat-card-value">{{ number_format($totalWards) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-hospital"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:100%;background:#6366f1"></div></div>
                <p class="stat-card-meta">Wards for this satellite store</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card active">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="stat-card-label">Active</p>
                        <div class="stat-card-value">{{ number_format($activeCount) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $activePct }}%;background:#16a34a"></div></div>
                <p class="stat-card-meta">{{ $activePct }}% of wards are active</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card inactive">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="stat-card-label">Inactive</p>
                        <div class="stat-card-value">{{ number_format($inactiveCount) }}</div>
                    </div>
                    <div class="stat-card-icon"><i class="bi bi-pause-circle-fill"></i></div>
                </div>
                <div class="stat-bar-wrap"><div class="stat-bar-fill" style="width:{{ $inactivePct }}%;background:#94a3b8"></div></div>
                <p class="stat-card-meta">Wards not currently in use</p>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-5">
            <div class="ward-shell">
                <div class="ward-shell-head">
                    <h5><i class="bi bi-plus-circle me-2 text-primary"></i>Add New Ward</h5>
                    <p class="text-secondary small mb-0">Register a ward for {{ $activeStore->name ?? 'this store' }}.</p>
                </div>
                <div class="ward-shell-body">
                    <form action="{{ route('add-ward-process') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="ward_name">Ward Name</label>
                            <input type="text" class="form-control" id="ward_name" name="name"
                                   value="{{ old('name') }}" placeholder="e.g. Maternity Ward" required>
                            @error('name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold" for="ward_status">Status</label>
                            <select class="form-select" id="ward_status" name="status" required>
                                <option value="" disabled selected>Choose status</option>
                                <option value="Active" @selected(old('status') === 'Active')>Active</option>
                                <option value="Inactive" @selected(old('status') === 'Inactive')>Inactive</option>
                            </select>
                            @error('status')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn-create-ward"><i class="bi bi-plus-lg"></i> Add Ward</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="ward-shell">
                <div class="ward-shell-head">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                        <div>
                            <h5><i class="bi bi-list-ul me-2 text-primary"></i>Existing Wards</h5>
                            <p class="text-secondary small mb-0">All wards configured for this satellite store.</p>
                        </div>
                        <span class="record-count-badge"><i class="bi bi-collection"></i> {{ $totalWards }} wards</span>
                    </div>
                </div>
                <div class="ward-shell-body pt-3">
                    @if($list->isEmpty())
                        <div class="empty-ward-state">
                            <i class="bi bi-hospital"></i>
                            <p class="mb-0 fw-semibold">No wards yet</p>
                            <p class="small mb-0">Use the form to add your first ward.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="wardTable" class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list as $ward)
                                        <tr>
                                            <td class="text-secondary">{{ $loop->iteration }}</td>
                                            <td class="fw-semibold">{{ $ward->name }}</td>
                                            <td>
                                                <span class="ward-badge {{ $ward->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                                    {{ $ward->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn-edit-ward btn-open-edit-ward"
                                                        data-id="{{ $ward->id }}"
                                                        data-name="{{ $ward->name }}"
                                                        data-status="{{ $ward->status }}">
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

@include('settings.edit-ward-modal')
@endsection

@section('scripts')
<script>
@if($list->count() > 0)
$(document).ready(function () {
    $('#wardTable').DataTable({
        pageLength: 10,
        order: [[1, 'asc']],
        columnDefs: [{ orderable: false, targets: [3] }],
        language: { search: '', searchPlaceholder: 'Search wards…' },
        dom: '<"d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3"lf>t<"d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3"ip>',
    });
});
@endif

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-open-edit-ward');
    if (!btn) return;

    document.getElementById('wardID').value = btn.dataset.id || '';
    document.getElementById('wardname').value = btn.dataset.name || '';
    document.getElementById('wardstatus').value = btn.dataset.status || 'Active';

    bootstrap.Modal.getOrCreateInstance(document.getElementById('editWardModal')).show();
});
</script>
@endsection
