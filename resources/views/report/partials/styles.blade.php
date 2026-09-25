<style>
    .rp-page { padding: 0 0.5rem 2rem; }

    .rp-hero {
        background: linear-gradient(135deg, #312e81 0%, #4f46e5 55%, #818cf8 100%);
        border-radius: 1.25rem;
        padding: 2rem 2rem 2.25rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(79, 70, 229, 0.28);
    }

    .rp-hero::before,
    .rp-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .rp-hero::before { width: 220px; height: 220px; top: -70px; right: -50px; }
    .rp-hero::after  { width: 140px; height: 140px; bottom: -40px; left: 8%; }

    .rp-hero-inner { position: relative; z-index: 1; }

    .rp-hero-badge {
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

    .rp-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: clamp(1.35rem, 3vw, 1.85rem);
        margin-bottom: 0.4rem;
    }

    .rp-hero p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.9rem;
        margin-bottom: 0;
        max-width: 640px;
    }

    .rp-hero .breadcrumb-item a { color: rgba(255, 255, 255, 0.65); }
    .rp-hero .breadcrumb-item.active { color: #fff; }

    .rp-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        background: #fff;
        margin-bottom: 1.5rem;
    }

    .rp-card-head {
        padding: 1.15rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .rp-card-head h5 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
    }

    .rp-card-body { padding: 1.5rem; }

    .rp-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .rp-nav a {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.85rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        transition: all 0.15s ease;
    }

    .rp-nav a:hover,
    .rp-nav a.active {
        background: rgba(79, 70, 229, 0.1);
        border-color: rgba(79, 70, 229, 0.25);
        color: #4f46e5;
    }

    .rp-alert {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .rp-alert.success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .rp-alert.error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    .rp-filter-grid {
        display: flex;
        flex-wrap: wrap;
        align-items: end;
        gap: 1rem;
    }

    .rp-filter-grid > * {
        flex: 1 1 200px;
        min-width: 180px;
    }

    .rp-filter-grid > *:has(.btn-rp-search) {
        flex: 0 1 180px;
        min-width: 160px;
    }

    .rp-filter-grid .form-label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.4rem;
    }

    .rp-select-wrap { position: relative; }

    .rp-select-wrap > i {
        position: absolute;
        left: 0.9rem;
        top: 50%;
        transform: translateY(-50%);
        color: #4f46e5;
        font-size: 1rem;
        pointer-events: none;
        z-index: 2;
    }

    .rp-select-wrap:has(select:not(.js-example-basic-single))::after {
        content: '\F282';
        font-family: 'bootstrap-icons';
        position: absolute;
        right: 0.95rem;
        top: 50%;
        transform: translateY(-50%);
        color: #4f46e5;
        pointer-events: none;
        font-size: 0.85rem;
    }

    .rp-filter-grid .form-select,
    .rp-filter-grid .form-control,
    .rp-select {
        appearance: none;
        width: 100%;
        height: 48px;
        padding: 0 1rem;
        border: 1.5px solid #c7d2fe;
        border-radius: 0.85rem;
        background: #eef2ff;
        color: #1e1b4b;
        font-size: 0.92rem;
        font-weight: 600;
        box-shadow: inset 0 1px 2px rgba(79, 70, 229, 0.06);
    }

    .rp-select-wrap .form-select,
    .rp-select-wrap .form-control,
    .rp-select-wrap .rp-select {
        padding-left: 2.55rem;
        padding-right: 2.4rem;
    }

    .rp-filter-grid .form-select:focus,
    .rp-filter-grid .form-control:focus,
    .rp-select:focus {
        outline: none;
        border-color: #4f46e5;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    }

    .rp-page .select2-container { width: 100% !important; }

    .rp-select-wrap .select2-container .select2-selection--single {
        padding-left: 2.2rem !important;
    }

    .rp-page .select2-container .select2-selection--single {
        height: 48px !important;
        padding: 0 0.75rem !important;
        display: flex !important;
        align-items: center;
        border: 1.5px solid #c7d2fe !important;
        border-radius: 0.85rem !important;
        background: #eef2ff !important;
        box-shadow: inset 0 1px 2px rgba(79, 70, 229, 0.06);
    }

    .rp-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 46px !important;
        color: #1e1b4b !important;
        font-weight: 600;
        padding-left: 0 !important;
    }

    .rp-page .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 8px;
    }

    .rp-page .select2-container--default.select2-container--focus .select2-selection--single,
    .rp-page .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #4f46e5 !important;
        background: #fff !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    }

    .btn-rp-search {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0 1.4rem;
        border-radius: 0.85rem;
        border: none;
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: #fff;
        font-size: 0.92rem;
        font-weight: 700;
        height: 48px;
        min-width: 160px;
        box-shadow: 0 8px 18px rgba(79, 70, 229, 0.28);
    }

    .btn-rp-search:hover {
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        color: #fff;
    }

    .btn-rp-print {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 1rem;
        border-radius: 0.625rem;
        border: none;
        background: #0f172a;
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 1rem;
    }

    .btn-rp-print:hover { color: #fff; opacity: 0.9; }

    .btn-rp-back {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.9rem;
        border-radius: 0.625rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-rp-back:hover { background: #f8fafc; color: #334155; }

    .rp-table thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.85rem 1rem;
        background: #f8fafc;
        white-space: nowrap;
    }

    .rp-table tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    .rp-table tbody tr:nth-child(even) { background: #fafafa; }
    .rp-table tbody tr:hover { background: #eef2ff !important; }

    .rp-table tfoot th,
    .rp-table tfoot td {
        background: #f1f5f9;
        font-weight: 700;
        padding: 0.85rem 1rem;
    }

    .rp-table-title th {
        text-align: center;
        background: #eef2ff !important;
        color: #312e81;
        font-size: 1rem;
        padding: 1rem !important;
        text-transform: none;
        letter-spacing: normal;
    }

    .rp-hub-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1rem;
    }

    .rp-hub-card {
        display: block;
        padding: 1.25rem;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        text-decoration: none;
        color: inherit;
        transition: all 0.15s ease;
        height: 100%;
    }

    .rp-hub-card:hover {
        border-color: rgba(79, 70, 229, 0.35);
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.12);
        transform: translateY(-2px);
        color: inherit;
    }

    .rp-hub-card.store { border-top: 3px solid #4f46e5; }
    .rp-hub-card.dept  { border-top: 3px solid #059669; }

    .rp-hub-icon {
        width: 44px;
        height: 44px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 0.75rem;
    }

    .rp-hub-card.store .rp-hub-icon { background: rgba(79, 70, 229, 0.12); color: #4f46e5; }
    .rp-hub-card.dept  .rp-hub-icon { background: rgba(5, 150, 105, 0.12); color: #059669; }

    .rp-hub-card h6 { font-weight: 700; margin-bottom: 0.25rem; font-size: 0.92rem; }
    .rp-hub-card p  { margin: 0; font-size: 0.78rem; color: #64748b; }

    .rp-section-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        margin: 1.5rem 0 1rem;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        padding: 0.4rem 0.75rem;
    }
</style>
