@php
    $pageName = "staff";
    $subpageName = "add_staff";
    $departments = $list->keyBy('id');
    $malePct   = $totalStaff > 0 ? round(($maleCount / $totalStaff) * 100) : 0;
    $femalePct = $totalStaff > 0 ? round(($femaleCount / $totalStaff) * 100) : 0;
@endphp

@extends('layouts.backendapp')

@section('page-alerts')
@endsection

@section('css')
<style>
    .staff-page {
        padding: 0 0.5rem 2rem;
    }

    /* ── Hero ── */
    .staff-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 60%, #4f8ef7 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        border: none;
        box-shadow: 0 8px 32px rgba(13, 110, 253, 0.25);
    }

    .staff-hero::before,
    .staff-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        display: block;
    }

    .staff-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .staff-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .staff-hero-inner { position: relative; z-index: 1; }

    .staff-hero-badge {
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
        color: #fff;
    }

    .staff-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        margin-bottom: 0.4rem;
        letter-spacing: -0.02em;
        color: #fff;
    }

    .staff-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 520px;
    }

    .staff-hero-actions {
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
        color: #0d6efd;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        color: #0d6efd;
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

    .staff-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .staff-hero .breadcrumb-item a:hover { color: #fff; }
    .staff-hero .breadcrumb-item.active { color: #fff; }

    /* ── Stat cards ── */
    .stat-card {
        border-radius: 1.125rem;
        padding: 1.4rem 1.5rem;
        height: 100%;
        position: relative;
        overflow: hidden;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        color: inherit;
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

    .stat-card::after { display: none; }

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

    .stat-card.total  .stat-card-icon { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
    .stat-card.male   .stat-card-icon { background: rgba(8, 145, 178, 0.12);  color: #0891b2; }
    .stat-card.female .stat-card-icon { background: rgba(190, 24, 93, 0.12); color: #be185d; }

    .stat-card-value {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.2rem;
    }

    .stat-card.total  .stat-card-value { color: #0d6efd; }
    .stat-card.male   .stat-card-value { color: #0891b2; }
    .stat-card.female .stat-card-value { color: #be185d; }

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

    .stat-card.total  .stat-bar-fill { background: #0d6efd; }
    .stat-card.male   .stat-bar-fill { background: #0891b2; }
    .stat-card.female .stat-bar-fill { background: #be185d; }

    .stat-bar-fill {
        height: 100%;
        border-radius: 2rem;
        transition: width 1s ease;
    }

    .stat-card-meta {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.4rem;
    }

    .stat-pct-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 2rem;
    }

    .stat-card.male   .stat-pct-badge { background: rgba(8, 145, 178, 0.12);  color: #0891b2; }
    .stat-card.female .stat-pct-badge { background: rgba(190, 24, 93, 0.12); color: #be185d; }

    /* Gender ratio strip */
    .gender-ratio-strip {
        background: var(--adminuiux-card-bg, #fff);
        border-radius: 1rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.75rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        animation: statIn 0.5s ease 0.25s both;
    }

    .gender-ratio-bar {
        height: 10px;
        border-radius: 2rem;
        overflow: hidden;
        display: flex;
        background: #f1f5f9;
        margin: 0.6rem 0 0.4rem;
    }

    .gender-ratio-male   { background: linear-gradient(90deg, #0891b2, #22d3ee); transition: width 1s ease; }
    .gender-ratio-female { background: linear-gradient(90deg, #be185d, #ec4899); transition: width 1s ease; }

    .gender-ratio-legend {
        display: flex;
        justify-content: space-between;
        font-size: 0.78rem;
        color: var(--adminuiux-text-secondary, #64748b);
    }

    .legend-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 4px;
    }

    /* ── Table card ── */
    .staff-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        animation: statIn 0.5s ease 0.32s both;
    }

    .staff-table-head {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .staff-table-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.15rem;
        color: #0f172a;
    }

    .record-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        background: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
        font-size: 0.78rem;
        font-weight: 600;
        border: none;
    }

    #staffTable thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        border: none;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.9rem 1rem;
        background: #f8fafc;
        white-space: nowrap;
    }

    #staffTable tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }

    #staffTable tbody tr {
        transition: background 0.15s ease;
        background: #fff;
    }

    #staffTable tbody tr:nth-child(even) {
        background: #fafafa;
    }

    #staffTable tbody tr:hover {
        background: #f8fafc !important;
    }

    /* DataTables controls */
    .staff-table-card .dataTables_wrapper .dataTables_length,
    .staff-table-card .dataTables_wrapper .dataTables_filter,
    .staff-table-card .dataTables_wrapper .dataTables_info,
    .staff-table-card .dataTables_wrapper .dataTables_paginate {
        color: #64748b;
    }

    .staff-table-card .dataTables_wrapper .dataTables_filter input {
        border: 1.5px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.35rem 0.75rem;
        outline: none;
    }

    .staff-table-card .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #94a3b8;
        box-shadow: none;
    }

    .staff-table-card .dataTables_wrapper .page-item.active .page-link {
        background: #0f172a;
        border-color: #0f172a;
    }

    .staff-table-card .dataTables_wrapper .page-link {
        color: #475569;
    }

    .staff-avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .staff-avatar-initial {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        font-weight: 700;
        color: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .staff-avatar-initial.c0 { background: linear-gradient(135deg, #0d6efd, #6610f2); }
    .staff-avatar-initial.c1 { background: linear-gradient(135deg, #0891b2, #0d9488); }
    .staff-avatar-initial.c2 { background: linear-gradient(135deg, #be185d, #9333ea); }
    .staff-avatar-initial.c3 { background: linear-gradient(135deg, #d97706, #dc2626); }

    .staff-name { font-weight: 600; color: #0f172a; font-size: 0.875rem; }
    .staff-sub  { font-size: 0.75rem; color: #94a3b8; }

    .gender-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.28rem 0.7rem;
        border-radius: 2rem;
        font-size: 0.73rem;
        font-weight: 600;
    }

    .gender-badge.male   { background: rgba(8, 145, 178, 0.12); color: #0891b2; }
    .gender-badge.female { background: rgba(190, 24, 93, 0.12); color: #be185d; }

    .dept-pill {
        display: inline-block;
        padding: 0.22rem 0.65rem;
        border-radius: 0.4rem;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.78rem;
        font-weight: 500;
    }

    .staff-id-badge {
        font-family: "DM Mono", monospace;
        font-size: 0.78rem;
        font-weight: 500;
        color: #64748b;
        background: #f8fafc;
        padding: 0.2rem 0.55rem;
        border-radius: 0.35rem;
        border: 1px solid #e2e8f0;
    }

    .btn-edit-staff {
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid rgba(13, 110, 253, 0.25);
        color: #0d6efd;
        background: rgba(13, 110, 253, 0.06);
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-edit-staff:hover {
        background: #0d6efd;
        color: #fff;
        transform: scale(1.08);
    }

    .btn-delete-staff {
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid rgba(220, 53, 69, 0.25);
        color: #dc3545;
        background: rgba(220, 53, 69, 0.06);
        transition: all 0.2s;
        cursor: pointer;
        padding: 0;
    }

    .btn-delete-staff:hover {
        background: #dc3545;
        color: #fff;
        transform: scale(1.08);
    }

    .btn-delete-staff.disabled,
    .btn-delete-staff:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: #f1f5f9;
        color: #94a3b8;
        border-color: #e2e8f0;
        transform: none;
    }

    .staff-actions {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    /* ── Empty state ── */
    .staff-empty {
        text-align: center;
        padding: 4rem 2rem;
    }

    .staff-empty-visual {
        position: relative;
        display: inline-block;
        margin-bottom: 1.5rem;
    }

    .staff-empty-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(13,110,253,0.1), rgba(102,16,242,0.1));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #0d6efd;
        margin: 0 auto;
    }

    .staff-empty-dot {
        position: absolute;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 3px solid #fff;
    }

    .staff-empty-dot.d1 { background: #22d3ee; top: 0; right: -4px; }
    .staff-empty-dot.d2 { background: #ec4899; bottom: 8px; left: -8px; }

    /* ── Modal ── */
    .modal-staff .modal-dialog {
        max-width: 1140px;
        max-height: calc(100vh - 1.5rem);
        margin: 0.75rem auto;
        display: flex;
        align-items: stretch;
    }

    .modal-staff .modal-content {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.18);
        max-height: calc(100vh - 1.5rem);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        width: 100%;
    }

    .modal-staff #addStaffForm,
    .modal-staff #editStaffForm {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
    }

    .modal-staff-header {
        flex-shrink: 0;
        background: #fff;
        color: inherit;
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .modal-staff .modal-body {
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1 1 auto;
        min-height: 0;
        max-height: none;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 1.5rem;
    }

    /* Bootstrap scrollable + form wrapper fix */
    .modal-staff .modal-dialog.modal-dialog-scrollable .modal-content {
        max-height: calc(100vh - 1.5rem);
    }

    .modal-staff .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-staff .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .modal-staff .modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .modal-staff-header .modal-title {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1.15rem;
        color: #0f172a;
    }

    .modal-staff-header .btn-close {
        filter: none;
        opacity: 0.6;
    }

    .modal-photo-zone {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, rgba(13,110,253,0.05), rgba(102,16,242,0.05));
        border-radius: 1rem;
        margin-bottom: 1.5rem;
        border: 1px dashed rgba(13, 110, 253, 0.25);
    }

    .modal-photo-ring {
        position: relative;
        flex-shrink: 0;
    }

    .modal-photo-ring::before {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0d6efd, #6610f2);
    }

    .modal-photo-preview {
        position: relative;
        z-index: 1;
        width: 72px;
        height: 72px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        cursor: pointer;
    }

    .modal-section {
        background: #f8fafc;
        border-radius: 0.875rem;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
    }

    .modal-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        margin-bottom: 1rem;
    }

    .modal-section-title i { color: #0d6efd; }

    .modal-staff .form-control,
    .modal-staff .form-select {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.875rem;
        padding: 0.6rem 0.85rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .modal-staff .form-control:focus,
    .modal-staff .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
    }

    .modal-staff-footer {
        padding: 1rem 1.75rem;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.65rem;
        z-index: 5;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.06);
    }

    .btn-modal-clear {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.25rem;
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #475569;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-modal-clear:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #334155;
    }

    .btn-modal-save {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.5rem;
        border-radius: 0.625rem;
        border: none;
        background: #198754;
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 14px rgba(25, 135, 84, 0.3);
        transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
        cursor: pointer;
    }

    .btn-modal-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(25, 135, 84, 0.4);
        color: #fff;
        background: #157347;
    }

    .field-error { color: #ef4444; font-size: 0.76rem; margin-top: 0.25rem; }

    .modal-staff-header.edit-header {
        background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%);
        border-bottom: 1px solid #bbf7d0;
    }

    .modal-staff-header.edit-header .modal-title { color: #15803d; }
</style>
@endsection

@section('content')

<div class="container-fluid staff-page px-3 px-lg-4 mt-3">

    {{-- Hero --}}
    <div class="staff-hero">
        <div class="staff-hero-inner">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="staff-hero-badge">
                        <i class="bi bi-people-fill"></i> Staff Management
                    </div>
                    <h2>Manage Your Team</h2>
                    <p>Register staff, track gender distribution, and manage employee records across all departments.</p>
                    <div class="staff-hero-actions">
                        <button type="button" class="btn-hero-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                            <i class="bi bi-person-plus-fill"></i> Add New Staff
                        </button>
                        <a href="{{ route('user-management-create-account') }}" class="btn-hero-ghost">
                            <i class="bi bi-person-lock"></i> Create Account
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0" style="--bs-breadcrumb-divider:'›';">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Staff</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="stat-card total">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-people-fill"></i></div>
                </div>
                <div class="stat-card-value" data-count="{{ $totalStaff }}">{{ number_format($totalStaff) }}</div>
                <p class="stat-card-label">Total Staff Registered</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:100%"></div>
                </div>
                <p class="stat-card-meta">All active staff records</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card male">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-gender-male"></i></div>
                    <span class="stat-pct-badge">{{ $malePct }}%</span>
                </div>
                <div class="stat-card-value" data-count="{{ $maleCount }}">{{ number_format($maleCount) }}</div>
                <p class="stat-card-label">Male Staff</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $malePct }}%"></div>
                </div>
                <p class="stat-card-meta">{{ $malePct }}% of total workforce</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card female">
                <div class="stat-card-top">
                    <div class="stat-card-icon"><i class="bi bi-gender-female"></i></div>
                    <span class="stat-pct-badge">{{ $femalePct }}%</span>
                </div>
                <div class="stat-card-value" data-count="{{ $femaleCount }}">{{ number_format($femaleCount) }}</div>
                <p class="stat-card-label">Female Staff</p>
                <div class="stat-bar-wrap">
                    <div class="stat-bar-fill" style="width:{{ $femalePct }}%"></div>
                </div>
                <p class="stat-card-meta">{{ $femalePct }}% of total workforce</p>
            </div>
        </div>
    </div>

    {{-- Gender ratio bar --}}
    @if($totalStaff > 0)
    <div class="gender-ratio-strip">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="fw-semibold small">Gender Distribution</span>
            <span class="text-secondary small">{{ $totalStaff }} staff members</span>
        </div>
        <div class="gender-ratio-bar">
            @if($malePct > 0)<div class="gender-ratio-male" style="width:{{ $malePct }}%"></div>@endif
            @if($femalePct > 0)<div class="gender-ratio-female" style="width:{{ $femalePct }}%"></div>@endif
        </div>
        <div class="gender-ratio-legend">
            <span><span class="legend-dot" style="background:#0891b2"></span>Male {{ $maleCount }} ({{ $malePct }}%)</span>
            <span><span class="legend-dot" style="background:#be185d"></span>Female {{ $femaleCount }} ({{ $femalePct }}%)</span>
        </div>
    </div>
    @endif

    {{-- Staff table --}}
    <div class="staff-table-card mb-5">
        <div class="staff-table-head">
            <div>
                <h5>Registered Staff</h5>
                <span class="record-count-badge">
                    <i class="bi bi-database"></i>
                    {{ $totalStaff }} record{{ $totalStaff !== 1 ? 's' : '' }}
                </span>
            </div>
            <button type="button" class="btn btn-theme btn-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="bi bi-plus-lg me-1"></i> Add Staff
            </button>
        </div>

        <div class="p-0">
            @if($liststaff->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0 w-100" id="staffTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Staff Member</th>
                                <th>Gender</th>
                                <th>Staff ID</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Contact</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($liststaff as $staff)
                                @php
                                    $initials = strtoupper(substr($staff->firstname ?? '', 0, 1) . substr($staff->surname ?? '', 0, 1));
                                    $deptName = $departments[$staff->department_id]->name ?? '—';
                                    $colorIdx = $loop->index % 4;
                                    $hasAccount = in_array((int) $staff->staff_id, $staffWithAccounts, true);
                                    $staffName = trim(($staff->title ?? '') . ' ' . $staff->firstname . ' ' . $staff->surname);
                                @endphp
                                <tr>
                                    <td class="text-secondary">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($staff->picture)
                                                <img src="{{ asset($staff->picture) }}" alt="" class="staff-avatar-sm">
                                            @else
                                                <span class="staff-avatar-initial c{{ $colorIdx }}">{{ $initials }}</span>
                                            @endif
                                            <div>
                                                <div class="staff-name">{{ trim(($staff->title ?? '') . ' ' . $staff->firstname . ' ' . $staff->surname) }}</div>
                                                <div class="staff-sub">{{ $staff->personal_email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="gender-badge {{ strtolower($staff->gender) }}">
                                            <i class="bi bi-gender-{{ strtolower($staff->gender) === 'male' ? 'male' : 'female' }}"></i>
                                            {{ $staff->gender }}
                                        </span>
                                    </td>
                                    <td><span class="staff-id-badge">{{ $staff->employee_id }}</span></td>
                                    <td><span class="dept-pill">{{ $deptName }}</span></td>
                                    <td>{{ $staff->position }}</td>
                                    <td class="text-secondary">{{ $staff->contact_num }}</td>
                                    <td>
                                        <div class="staff-actions">
                                            <button type="button"
                                                    class="btn-edit-staff btn-open-edit-modal"
                                                    title="Edit staff"
                                                    data-staff-id="{{ Crypt::encrypt($staff->staff_id) }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @if($hasAccount)
                                                <button type="button"
                                                        class="btn-delete-staff disabled"
                                                        title="Cannot delete — staff has a user account"
                                                        data-has-account="1"
                                                        data-staff-name="{{ $staffName }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                        class="btn-delete-staff btn-confirm-delete"
                                                        title="Delete staff"
                                                        data-delete-url="{{ route('delete-staff-process', Crypt::encrypt($staff->staff_id)) }}"
                                                        data-staff-name="{{ $staffName }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="staff-empty">
                    <div class="staff-empty-visual">
                        <div class="staff-empty-circle"><i class="bi bi-people"></i></div>
                        <span class="staff-empty-dot d1"></span>
                        <span class="staff-empty-dot d2"></span>
                    </div>
                    <h5 class="fw-bold mb-2">No staff registered yet</h5>
                    <p class="text-secondary mb-4 mx-auto" style="max-width:360px">
                        Your team list is empty. Add your first staff member to start managing accounts and store assignments.
                    </p>
                    <button type="button" class="btn btn-theme px-4" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                        <i class="bi bi-person-plus-fill me-2"></i>Add First Staff Member
                    </button>
                </div>
            @endif
        </div>
    </div>

</div>

<form id="deleteStaffForm" method="POST" action="" class="d-none">
    @csrf
</form>

{{-- Add Staff Modal --}}
<div class="modal fade modal-staff" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form enctype="multipart/form-data" action="{{ route('add-staff-process') }}" method="POST" id="addStaffForm">
                @csrf

                <div class="modal-header modal-staff-header">
                    <div>
                        <h5 class="modal-title" id="addStaffModalLabel">Add New Staff Member</h5>
                        <p class="mb-0 small opacity-75">Register a new employee into StockShield</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">

                    {{-- Photo --}}
                    <div class="modal-photo-zone">
                        <label for="imageUpload" class="modal-photo-ring mb-0">
                            <img id="preview" src="{{ asset('backend/assets/img/user.png') }}" alt="Photo" class="modal-photo-preview">
                        </label>
                        <input type="file" id="imageUpload" name="image" accept="image/*" hidden>
                        <div>
                            <p class="fw-semibold mb-1 small">Profile Photo</p>
                            <p class="text-secondary small mb-2">JPG or PNG recommended</p>
                            <label for="imageUpload" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-camera me-1"></i> Choose Photo
                            </label>
                        </div>
                    </div>

                    {{-- Personal --}}
                    <div class="modal-section">
                        <div class="modal-section-title">
                            <i class="bi bi-person-vcard-fill"></i> Personal Information
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="title" class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
                                <select class="form-select" name="title" id="title">
                                    <option value="" disabled selected>Choose title</option>
                                    <option value="Mr" @selected(old('title') == 'Mr')>Mr</option>
                                    <option value="Mrs" @selected(old('title') == 'Mrs')>Mrs</option>
                                    <option value="Miss" @selected(old('title') == 'Miss')>Miss</option>
                                    <option value="Dr" @selected(old('title') == 'Dr')>Dr</option>
                                    <option value="Prof" @selected(old('title') == 'Prof')>Prof</option>
                                </select>
                                @error('title')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="surname" class="form-label small fw-semibold">Surname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="surname" id="surname" value="{{ old('surname') }}" placeholder="Surname">
                                @error('surname')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="firstname" class="form-label small fw-semibold">Firstname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="firstname" id="firstname" value="{{ old('firstname') }}" placeholder="Firstname">
                                @error('firstname')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="othername" class="form-label small fw-semibold">Othername</label>
                                <input type="text" class="form-control" name="othername" id="othername" value="{{ old('othername') }}" placeholder="Optional">
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" name="gender" id="gender">
                                    <option value="" disabled selected>Choose gender</option>
                                    <option value="Male" @selected(old('gender') == 'Male')>Male</option>
                                    <option value="Female" @selected(old('gender') == 'Female')>Female</option>
                                </select>
                                @error('gender')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="phone" class="form-label small fw-semibold">Phone <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Phone number">
                                @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8">
                                <label for="email" class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" placeholder="name@company.com">
                                @error('email')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label small fw-semibold">Residential Address</label>
                                <textarea class="form-control" name="address" id="address" rows="2" placeholder="Full address">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Employment --}}
                    <div class="modal-section mb-0">
                        <div class="modal-section-title">
                            <i class="bi bi-briefcase-fill"></i> Employment Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="staff_number" class="form-label small fw-semibold">Staff ID <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="staff_number" id="staff_number" value="{{ old('staff_number') }}" placeholder="Employee ID">
                                @error('staff_number')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="position" class="form-label small fw-semibold">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="position" id="position" value="{{ old('position') }}" placeholder="e.g. Store Manager">
                                @error('position')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="department" class="form-label small fw-semibold">Department <span class="text-danger">*</span></label>
                                <select class="form-select" name="department" id="department">
                                    <option value="" disabled selected>Choose department</option>
                                    @foreach($list as $listdep)
                                        <option value="{{ $listdep->id }}" @selected(old('department') == $listdep->id)>{{ $listdep->name }}</option>
                                    @endforeach
                                </select>
                                @error('department')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer modal-staff-footer">
                    <button type="button" class="btn-modal-clear" id="clearStaffForm">
                        <i class="bi bi-arrow-counterclockwise"></i> Clear
                    </button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-check-lg"></i> Save Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Staff Modal --}}
<div class="modal fade modal-staff" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form enctype="multipart/form-data" action="" method="POST" id="editStaffForm">
                @csrf
                <input type="hidden" name="_edit_form" value="1">
                <input type="hidden" name="staff_encrypted_id" id="edit_staff_encrypted_id" value="{{ old('staff_encrypted_id') }}">

                <div class="modal-header modal-staff-header edit-header">
                    <div>
                        <h5 class="modal-title" id="editStaffModalLabel">Edit Staff Member</h5>
                        <p class="mb-0 small text-secondary">Update staff details</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="modal-photo-zone">
                        <label for="editImageUpload" class="modal-photo-ring mb-0">
                            <img id="editPreview" src="{{ asset('backend/assets/img/user.png') }}" alt="Photo" class="modal-photo-preview">
                        </label>
                        <input type="file" id="editImageUpload" name="image" accept="image/*" hidden>
                        <div>
                            <p class="fw-semibold mb-1 small">Profile Photo</p>
                            <p class="text-secondary small mb-2">Leave unchanged to keep current photo</p>
                            <label for="editImageUpload" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-camera me-1"></i> Change Photo
                            </label>
                        </div>
                    </div>

                    <div class="modal-section">
                        <div class="modal-section-title">
                            <i class="bi bi-person-vcard-fill"></i> Personal Information
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="edit_title" class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
                                <select class="form-select" name="title" id="edit_title">
                                    <option value="" disabled>Choose title</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Miss">Miss</option>
                                    <option value="Dr">Dr</option>
                                    <option value="Prof">Prof</option>
                                </select>
                                @error('title')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="edit_surname" class="form-label small fw-semibold">Surname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="surname" id="edit_surname" placeholder="Surname">
                                @error('surname')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="edit_firstname" class="form-label small fw-semibold">Firstname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="firstname" id="edit_firstname" placeholder="Firstname">
                                @error('firstname')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="edit_othername" class="form-label small fw-semibold">Othername</label>
                                <input type="text" class="form-control" name="othername" id="edit_othername" placeholder="Optional">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_gender" class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" name="gender" id="edit_gender">
                                    <option value="" disabled>Choose gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                @error('gender')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="edit_phone" class="form-label small fw-semibold">Phone <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="phone" id="edit_phone" placeholder="Phone number">
                                @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8">
                                <label for="edit_email" class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="edit_email" placeholder="name@company.com">
                                @error('email')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="edit_address" class="form-label small fw-semibold">Residential Address</label>
                                <textarea class="form-control" name="address" id="edit_address" rows="2" placeholder="Full address"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-section mb-0">
                        <div class="modal-section-title">
                            <i class="bi bi-briefcase-fill"></i> Employment Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="edit_staff_number" class="form-label small fw-semibold">Staff ID <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="staff_number" id="edit_staff_number" placeholder="Employee ID">
                                @error('staff_number')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="edit_position" class="form-label small fw-semibold">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="position" id="edit_position" placeholder="e.g. Store Manager">
                                @error('position')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="edit_department" class="form-label small fw-semibold">Department <span class="text-danger">*</span></label>
                                <select class="form-select" name="department" id="edit_department">
                                    <option value="" disabled>Choose department</option>
                                    @foreach($list as $listdep)
                                        <option value="{{ $listdep->id }}">{{ $listdep->name }}</option>
                                    @endforeach
                                </select>
                                @error('department')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer modal-staff-footer">
                    <button type="button" class="btn-modal-clear" id="clearEditStaffForm">
                        <i class="bi bi-arrow-counterclockwise"></i> Clear
                    </button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-check-lg"></i> Update Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const defaultAvatar = @json(asset('backend/assets/img/user.png'));
const editStaffBaseUrl = @json(url('edit-staff-process'));
const staffFetchBaseUrl = @json(url('staff-id'));

const StaffAlert = {
    _base(opts) {
        return Swal.fire(Object.assign({
            width: '28rem',
            padding: '1.5rem 1.75rem 1.35rem',
            buttonsStyling: false,
            customClass: {
                popup: 'staff-swal-popup',
                title: 'staff-swal-title',
                htmlContainer: 'staff-swal-text',
                confirmButton: 'btn staff-swal-confirm ' + (opts.btnClass || 'neutral'),
                cancelButton: 'btn btn-light border staff-swal-cancel',
            },
            showClass: { popup: 'swal2-show' },
            hideClass: { popup: 'swal2-hide' },
        }, opts));
    },
    success(title, text) {
        return this._base({
            icon: 'success',
            title: title,
            text: text,
            btnClass: 'success',
            confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Done',
            timer: 2800,
            timerProgressBar: true,
            showConfirmButton: true,
        });
    },
    error(title, text) {
        return this._base({
            icon: 'error',
            title: title,
            text: text,
            btnClass: 'error',
            confirmButtonText: '<i class="bi bi-x-lg me-1"></i> Close',
        });
    },
    info(title, html) {
        return this._base({
            icon: 'info',
            title: title,
            html: html,
            btnClass: 'info',
            confirmButtonText: 'Got it',
        });
    },
    confirmDelete(name, onConfirm) {
        return this._base({
            icon: 'warning',
            title: 'Delete Staff?',
            html: `Are you sure you want to delete <strong>${name}</strong>?<br><small class="text-secondary">This action cannot be undone.</small>`,
            btnClass: 'error',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, delete',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed && onConfirm) onConfirm();
        });
    },
};

let editStaffSnapshot = null;

function populateEditForm(data) {
    document.getElementById('editStaffForm').action = editStaffBaseUrl + '/' + data.encrypted_id;
    document.getElementById('edit_staff_encrypted_id').value = data.encrypted_id;
    document.getElementById('editPreview').src = data.picture || defaultAvatar;
    document.getElementById('editImageUpload').value = '';
    document.getElementById('edit_title').value = data.title || '';
    document.getElementById('edit_surname').value = data.surname || '';
    document.getElementById('edit_firstname').value = data.firstname || '';
    document.getElementById('edit_othername').value = data.othername || '';
    document.getElementById('edit_gender').value = data.gender || '';
    document.getElementById('edit_phone').value = data.phone || '';
    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_address').value = data.address || '';
    document.getElementById('edit_staff_number').value = data.staff_number || '';
    document.getElementById('edit_position').value = data.position || '';
    document.getElementById('edit_department').value = data.department || '';
    editStaffSnapshot = { ...data };
}

function openEditModal(staffId) {
    fetch(staffFetchBaseUrl + '/' + staffId)
        .then(function (res) {
            if (!res.ok) throw new Error('Staff not found');
            return res.json();
        })
        .then(function (data) {
            populateEditForm(data);
            new bootstrap.Modal(document.getElementById('editStaffModal')).show();
        })
        .catch(function () {
            StaffAlert.error('Load Failed', 'Could not load staff details. Please try again.');
        });
}

document.getElementById('imageUpload').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
            document.getElementById('preview').src = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('editImageUpload').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
            document.getElementById('editPreview').src = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('clearStaffForm').addEventListener('click', function () {
    const form = document.getElementById('addStaffForm');
    form.reset();
    document.getElementById('preview').src = defaultAvatar;
    document.getElementById('imageUpload').value = '';
});

document.getElementById('clearEditStaffForm').addEventListener('click', function () {
    if (editStaffSnapshot) populateEditForm(editStaffSnapshot);
});

document.querySelectorAll('.btn-open-edit-modal').forEach(function (btn) {
    btn.addEventListener('click', function () {
        openEditModal(this.dataset.staffId);
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

@if($liststaff->count() > 0)
$(document).ready(function () {
    $('#staffTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 10,
        language: { search: '', searchPlaceholder: 'Search staff members...' },
        dom: '<"d-flex justify-content-between align-items-center px-3 pt-3 pb-2"lf>rt<"d-flex justify-content-between align-items-center px-3 py-3"ip>',
    });
});
@endif

@if($errors->any() && old('_edit_form'))
document.addEventListener('DOMContentLoaded', function () {
    const encId = @json(old('staff_encrypted_id'));
    if (encId) {
        document.getElementById('editStaffForm').action = editStaffBaseUrl + '/' + encId;
    }
    new bootstrap.Modal(document.getElementById('editStaffModal')).show();
    document.getElementById('edit_title').value = @json(old('title'));
    document.getElementById('edit_surname').value = @json(old('surname'));
    document.getElementById('edit_firstname').value = @json(old('firstname'));
    document.getElementById('edit_othername').value = @json(old('othername'));
    document.getElementById('edit_gender').value = @json(old('gender'));
    document.getElementById('edit_phone').value = @json(old('phone'));
    document.getElementById('edit_email').value = @json(old('email'));
    document.getElementById('edit_address').value = @json(old('address'));
    document.getElementById('edit_staff_number').value = @json(old('staff_number'));
    document.getElementById('edit_position').value = @json(old('position'));
    document.getElementById('edit_department').value = @json(old('department'));
});
@elseif($errors->any())
document.addEventListener('DOMContentLoaded', function () {
    new bootstrap.Modal(document.getElementById('addStaffModal')).show();
});
@endif

@if(session('open_edit_staff') && !$errors->any())
document.addEventListener('DOMContentLoaded', function () {
    openEditModal(@json(session('open_edit_staff')));
});
@endif

@if(session('message_success'))
StaffAlert.success('Success!', @json(session('message_success')));
@endif

@if(session('message_error'))
StaffAlert.error('Oops!', @json(session('message_error')));
@endif

document.querySelectorAll('.btn-confirm-delete').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const name = this.dataset.staffName;
        const url  = this.dataset.deleteUrl;
        StaffAlert.confirmDelete(name, function () {
            const form = document.getElementById('deleteStaffForm');
            form.action = url;
            form.submit();
        });
    });
});

document.querySelectorAll('.btn-delete-staff[data-has-account]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        StaffAlert.info(
            'Cannot Delete',
            `<strong>${this.dataset.staffName}</strong> has a user account linked.<br><small class="text-secondary">Remove the account from User Management before deleting this staff record.</small>`
        );
    });
});
</script>
@endsection
