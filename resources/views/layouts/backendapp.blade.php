@php

    $staff_query = DB::select('SELECT * FROM staff WHERE staff_id = :id', ['id' => auth()->user()->staff_id]);

    $userCat = auth()->user()->user_cat;
    $userId = auth()->id();
    $storeContext = app(\App\Services\StoreContext::class);
    $activeStore = $storeContext->getActiveStore();
    $isGlobalStoreAccess = $storeContext->hasGlobalStoreAccess();
    $mappedStoreCount = count($storeContext->getMappedStoreIds(auth()->user()));
    $links = DB::select(
        'SELECT DISTINCT user_links.link_id, user_links.page_id, user_links.page_id_sub, user_links.link_url, user_links.link_name, user_links.link_image, user_links.link_parent
         FROM user_links
         WHERE user_links.link_id IN (
             SELECT link_id FROM user_cat_links WHERE cat_id = ?
             UNION
             SELECT link_id FROM user_extra_links WHERE user_id = ?
         )
         ORDER BY user_links.link_name ASC',
        [$userCat, $userId]
    );
    $parents = array();
    $child = array();
    $childrenByParent = [];
    foreach ($links as $row_links) {
        if ($row_links->link_parent == 0) {
            $parents[] = $row_links;
        } else {
            $child[] = $row_links;
            $childrenByParent[$row_links->link_parent][] = $row_links;
        }
    }

    $currentPage = $pageName ?? '';
    $currentSubpage = $subpageName ?? '';
    $sidebarUserInitials = strtoupper(collect(explode(' ', auth()->user()->name))->filter()->take(2)->map(fn ($w) => $w[0])->join(''));
    
@endphp
<!DOCTYPE html>
<html lang="en">
<!-- dir="rtl"-->

<!-- Mirrored from adminuiux.com/adminuiux/adminux/html/adminux-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 02 Mar 2026 20:13:42 GMT -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>StockShield</title>
    <link rel="icon" type="image/png" href="{{asset('backend/assets/img/favicon.png')}}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300..800&amp;family=SUSE:wght@100..800&amp;display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
      <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">
   <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Fraunces:wght@300;400;600&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/searchpanes/2.2.0/css/searchPanes.bootstrap5.min.css">


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

 
    <style>
        :root {
            --adminuiux-content-font: "Open Sans", sans-serif;
            --adminuiux-content-font-weight: 400;
            --adminuiux-title-font: "SUSE", sans-serif;
            --adminuiux-title-font-weight: 600;
        }

        .compact-swal-popup,
        .staff-swal-popup {
            font-size: 16px !important;
            border-radius: 1rem !important;
            padding: 1.5rem 1.75rem 1.35rem !important;
            width: 28rem !important;
            max-width: 92vw !important;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.14) !important;
        }

        .compact-swal-popup .swal2-icon,
        .staff-swal-popup .swal2-icon {
            width: 5em !important;
            height: 5em !important;
            margin: 0.5em auto 1em !important;
            border-width: 0.25em !important;
            line-height: 5em !important;
            overflow: visible !important;
        }

        .compact-swal-popup .swal2-icon .swal2-icon-content,
        .staff-swal-popup .swal2-icon .swal2-icon-content {
            font-size: 3.75em !important;
        }

        .compact-swal-title,
        .staff-swal-title {
            font-family: "SUSE", sans-serif !important;
            font-weight: 700 !important;
            font-size: 1.2rem !important;
            color: #0f172a !important;
            padding: 0 !important;
        }

        .compact-swal-text,
        .staff-swal-text {
            font-size: 0.9rem !important;
            color: #64748b !important;
            line-height: 1.55 !important;
            margin-top: 0.5rem !important;
        }

        .compact-swal-popup .swal2-actions,
        .staff-swal-popup .swal2-actions {
            margin: 1rem 0 0 !important;
            gap: 0.6rem !important;
        }

        .compact-swal-confirm,
        .staff-swal-confirm {
            border-radius: 0.5rem !important;
            padding: 0.5rem 1.15rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #fff !important;
            border: none !important;
            box-shadow: none !important;
        }

        .compact-swal-confirm.success,
        .staff-swal-confirm.success { background: #16a34a !important; }

        .compact-swal-confirm.error,
        .staff-swal-confirm.error { background: #dc3545 !important; }

        .staff-swal-confirm.info { background: #0d6efd !important; }
        .staff-swal-confirm.neutral { background: #64748b !important; }

        .staff-swal-cancel {
            border-radius: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            font-weight: 500 !important;
            font-size: 0.875rem !important;
        }

        /* ── Breadcrumb (ss-bc) ── */
        .ss-bc {
            display: inline-flex;
            max-width: 100%;
            margin-bottom: 0.75rem;
        }

        .ss-bc__list {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.15rem;
            list-style: none;
            margin: 0;
            padding: 0.4rem 0.55rem;
            border-radius: 0.875rem;
        }

        .ss-bc--light .ss-bc__list {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 4px 16px rgba(15, 23, 42, 0.05);
        }

        .ss-bc--dark .ss-bc__list {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .ss-bc__item {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .ss-bc__sep {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            color: #cbd5e1;
            flex-shrink: 0;
            list-style: none;
        }

        .ss-bc__sep svg {
            width: 11px;
            height: 11px;
            opacity: 0.85;
        }

        .ss-bc--dark .ss-bc__sep {
            color: rgba(255, 255, 255, 0.45);
        }

        .ss-bc__link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.32rem 0.65rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.18s ease, color 0.18s ease, transform 0.15s ease;
        }

        .ss-bc--light .ss-bc__link {
            color: #64748b;
        }

        .ss-bc--light .ss-bc__link:hover {
            background: #f1f5f9;
            color: #4f46e5;
        }

        .ss-bc--dark .ss-bc__link {
            color: rgba(255, 255, 255, 0.82);
        }

        .ss-bc--dark .ss-bc__link:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .ss-bc__icon {
            width: 22px;
            height: 22px;
            border-radius: 0.4rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            flex-shrink: 0;
        }

        .ss-bc--light .ss-bc__icon {
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
        }

        .ss-bc--dark .ss-bc__icon {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .ss-bc__item.is-current {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.32rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .ss-bc--light .ss-bc__item.is-current {
            color: #fff;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            box-shadow: 0 2px 10px rgba(79, 70, 229, 0.35);
        }

        .ss-bc--light .ss-bc__item.is-current .ss-bc__icon {
            background: rgba(255, 255, 255, 0.22);
            color: #fff;
        }

        .ss-bc--dark .ss-bc__item.is-current {
            color: #312e81;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        }

        .ss-bc--dark .ss-bc__item.is-current .ss-bc__icon {
            background: rgba(79, 70, 229, 0.12);
            color: #4f46e5;
        }

        .ss-bc__label {
            line-height: 1.2;
            white-space: nowrap;
        }

        /* Legacy bootstrap breadcrumbs → match ss-bc look */
        .adminuiux-content nav[aria-label="breadcrumb"]:not(.ss-bc) {
            display: inline-flex;
            max-width: 100%;
            margin-bottom: 0.65rem;
        }

        .adminuiux-content nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.15rem;
            margin: 0;
            padding: 0.4rem 0.55rem;
            border-radius: 0.875rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 4px 16px rgba(15, 23, 42, 0.05);
            list-style: none;
        }

        .adminuiux-content nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item {
            display: inline-flex;
            align-items: center;
        }

        .adminuiux-content nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item + .breadcrumb-item::before {
            content: none !important;
            display: none !important;
        }

        .adminuiux-content nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item:not(:first-child)::before {
            content: '';
            display: inline-block;
            width: 11px;
            height: 11px;
            margin: 0 0.35rem;
            flex-shrink: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' fill='%23cbd5e1' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            float: none !important;
            padding: 0 !important;
        }

        .adminuiux-content nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item a {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.32rem 0.65rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #64748b;
            text-decoration: none;
            transition: background 0.18s, color 0.18s;
        }

        .adminuiux-content nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item a:hover {
            background: #f1f5f9;
            color: #4f46e5;
        }

        .adminuiux-content nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item.active {
            display: inline-flex;
            align-items: center;
            padding: 0.32rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            box-shadow: 0 2px 10px rgba(79, 70, 229, 0.35);
        }

        [class*="-hero"] nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb,
        .submenu-hero nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        [class*="-hero"] nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item:not(:first-child)::before,
        .submenu-hero nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item:not(:first-child)::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' fill='%23ffffff' fill-opacity='0.45' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        }

        [class*="-hero"] nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item a,
        .submenu-hero nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.82);
        }

        [class*="-hero"] nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item a:hover,
        .submenu-hero nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item a:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        [class*="-hero"] nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item.active,
        .submenu-hero nav[aria-label="breadcrumb"]:not(.ss-bc) .breadcrumb-item.active {
            color: #312e81;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        }

        .bg-theme-1-subtle:has(nav[aria-label="breadcrumb"]) {
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04) !important;
            border-radius: 0.875rem !important;
        }

        .page-title .ss-bc {
            margin-bottom: 0.85rem;
        }

        [class*="-hero"] .ss-bc,
        .submenu-hero .ss-bc {
            margin-bottom: 0.65rem;
        }

        [class*="-hero"] .ss-bc--light .ss-bc__list,
        .submenu-hero .ss-bc--light .ss-bc__list {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* ── StockShield sidebar redesign ── */
        .ss-sidebar {
            border-right: 1px solid rgba(15, 23, 42, 0.06) !important;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
        }

        .ss-sidebar-inner {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding-bottom: 1rem;
        }

        .ss-sidebar-user {
            margin: 0.85rem 0.85rem 0.5rem;
            padding: 1rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #312e81 0%, #4f46e5 55%, #6366f1 100%);
            color: #fff;
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.28);
            position: relative;
            overflow: hidden;
        }

        .ss-sidebar-user::before {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            top: -30px;
            right: -20px;
        }

        .ss-sidebar-user-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .ss-sidebar-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.35);
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .ss-sidebar-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ss-sidebar-user-name {
            font-family: "SUSE", sans-serif;
            font-weight: 700;
            font-size: 0.875rem;
            line-height: 1.25;
            margin-bottom: 0.15rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
        }

        .ss-sidebar-user-role {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.68rem;
            font-weight: 600;
            padding: 0.15rem 0.5rem;
            border-radius: 2rem;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.22);
        }

        .ss-sidebar-store {
            margin: 0 0.85rem 0.75rem;
            padding: 0.55rem 0.75rem;
            border-radius: 0.625rem;
            background: rgba(79, 70, 229, 0.08);
            border: 1px solid rgba(79, 70, 229, 0.12);
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.72rem;
            font-weight: 600;
            color: #4f46e5;
        }

        .ss-sidebar-store i { font-size: 0.85rem; }

        .ss-sidebar-section {
            padding: 0.65rem 1.1rem 0.35rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
        }

        .ss-sidebar-nav {
            list-style: none;
            margin: 0;
            padding: 0 0.65rem;
            flex: 1;
        }

        .ss-nav-item { margin-bottom: 0.2rem; }

        .ss-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.85rem;
            border-radius: 0.75rem;
            text-decoration: none;
            color: #475569;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background 0.2s, color 0.2s, transform 0.15s, box-shadow 0.2s;
            position: relative;
        }

        .ss-nav-link:hover {
            background: rgba(79, 70, 229, 0.08);
            color: #4f46e5;
        }

        .ss-nav-item.is-active .ss-nav-link {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.14) 0%, rgba(99, 102, 241, 0.1) 100%);
            color: #4f46e5;
            font-weight: 600;
            box-shadow: inset 3px 0 0 #4f46e5;
        }

        .ss-nav-icon {
            width: 36px;
            height: 36px;
            border-radius: 0.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
            font-size: 1rem;
            transition: background 0.2s, color 0.2s, transform 0.2s;
        }

        .ss-nav-link:hover .ss-nav-icon,
        .ss-nav-item.is-active .ss-nav-icon {
            background: rgba(79, 70, 229, 0.15);
            color: #4f46e5;
        }

        .ss-nav-item.is-active .ss-nav-icon {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .ss-nav-text {
            flex: 1;
            line-height: 1.3;
            min-width: 0;
        }

        .ss-nav-badge {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.45rem;
            border-radius: 2rem;
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
            flex-shrink: 0;
        }

        .ss-nav-item.is-active .ss-nav-badge {
            background: rgba(79, 70, 229, 0.2);
        }

        .ss-nav-chevron {
            font-size: 0.7rem;
            color: #cbd5e1;
            transition: transform 0.2s, color 0.2s;
        }

        .ss-nav-link:hover .ss-nav-chevron { color: #4f46e5; }

        .ss-sidebar-footer {
            margin: 0.75rem 0.85rem 0;
            padding-top: 0.75rem;
            border-top: 1px solid #e2e8f0;
        }

        .ss-sidebar-footer-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.85rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 500;
            color: #64748b;
            transition: background 0.2s, color 0.2s;
            border: none;
            background: transparent;
            width: 100%;
            cursor: pointer;
        }

        .ss-sidebar-footer-link:hover {
            background: rgba(239, 68, 68, 0.08);
            color: #dc2626;
        }

        .ss-sidebar-footer-link i {
            width: 32px;
            height: 32px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(100, 116, 139, 0.1);
            font-size: 0.9rem;
        }

        .ss-sidebar-footer-link:hover i {
            background: rgba(239, 68, 68, 0.12);
        }

        /* Iconic / collapsed sidebar */
        .adminuiux-sidebar-iconic .ss-nav-text,
        .adminuiux-sidebar-iconic .ss-nav-badge,
        .adminuiux-sidebar-iconic .ss-nav-chevron,
        .adminuiux-sidebar-iconic .ss-sidebar-section,
        .adminuiux-sidebar-iconic .ss-sidebar-user-info,
        .adminuiux-sidebar-iconic .ss-sidebar-store span,
        .adminuiux-sidebar-iconic .ss-sidebar-footer-link span {
            display: none !important;
        }

        .adminuiux-sidebar-iconic .ss-sidebar-user {
            padding: 0.65rem;
            margin-bottom: 0.5rem;
        }

        .adminuiux-sidebar-iconic .ss-sidebar-user-inner {
            justify-content: center;
        }

        .adminuiux-sidebar-iconic .ss-nav-link {
            justify-content: center;
            padding: 0.65rem;
        }

        .adminuiux-sidebar-iconic .ss-sidebar-nav {
            padding: 0 0.35rem;
        }

        /* WhatsApp-style in-app toast notifications */
        .ss-toast-stack {
            position: fixed;
            right: 1.25rem;
            bottom: 1.25rem;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 0;
            max-width: min(380px, calc(100vw - 2rem));
            pointer-events: none;
        }

        .ss-toast-stack > .ss-toast:not(:first-of-type) {
            display: none !important;
        }

        .ss-toast {
            pointer-events: auto;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            background: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18), 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            cursor: pointer;
            animation: ssToastIn 0.35s cubic-bezier(0.22, 1, 0.36, 1);
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .ss-toast-exit {
            animation: ssToastOut 0.25s ease forwards;
        }

        @keyframes ssToastIn {
            from { opacity: 0; transform: translateX(110%) scale(0.95); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }

        @keyframes ssToastOut {
            from { opacity: 1; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(110%); }
        }

        .ss-toast-accent {
            width: 4px;
            flex-shrink: 0;
            background: linear-gradient(180deg, #25d366, #128c7e);
        }

        .ss-toast-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #075e54;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 0.65rem;
            margin-left: 0.15rem;
            overflow: hidden;
        }

        .ss-toast-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ss-toast-body {
            flex: 1;
            min-width: 0;
            padding: 0.65rem 0.85rem 0.75rem 0;
        }

        .ss-toast-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 0.2rem;
        }

        .ss-toast-app {
            font-size: 0.82rem;
            font-weight: 700;
            color: #075e54;
        }

        .ss-toast-time {
            font-size: 0.7rem;
            color: #8696a0;
            white-space: nowrap;
        }

        .ss-toast-title {
            font-size: 0.88rem;
            font-weight: 600;
            color: #111b21;
            margin-bottom: 0.15rem;
        }

        .ss-toast-message {
            font-size: 0.82rem;
            color: #54656f;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .ss-toast-close {
            border: none;
            background: transparent;
            color: #8696a0;
            font-size: 1.1rem;
            line-height: 1;
            padding: 0.35rem 0.5rem;
            margin: 0.25rem 0.25rem 0 0;
            cursor: pointer;
            flex-shrink: 0;
        }

        .ss-toast-close:hover { color: #111b21; }

        .ss-toast-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 0.45rem;
        }

        .ss-toast-open {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.28rem 0.75rem;
            border-radius: 2rem;
            background: #25d366;
            color: #fff !important;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            border: none;
        }

        .ss-toast-open:hover {
            background: #1da851;
            color: #fff !important;
        }

        .ss-toast-persistent {
            border-left: 3px solid #25d366;
        }

        .ss-toast-pill {
            position: fixed;
            right: 1.25rem;
            bottom: 1.25rem;
            z-index: 99998;
            pointer-events: auto;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            max-width: min(320px, calc(100vw - 2rem));
            padding: 0.42rem 0.9rem 0.42rem 0.48rem;
            border: none;
            border-radius: 2rem;
            background: #075e54;
            color: #fff;
            box-shadow: 0 6px 20px rgba(7, 94, 84, 0.35);
            cursor: pointer;
            animation: ssToastPillPulse 1.6s ease-in-out infinite;
        }

        .ss-toast-pill.d-none {
            display: none !important;
            animation: none;
        }

        .ss-toast-pill-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #25d366;
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            animation: ssToastPillDot 1.6s ease-out infinite;
            flex-shrink: 0;
        }

        .ss-toast-pill-count {
            min-width: 1.35rem;
            height: 1.35rem;
            padding: 0 0.3rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.18);
            font-size: 0.72rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ss-toast-pill-title {
            font-size: 0.78rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 14rem;
            text-align: left;
        }

        @keyframes ssToastPillPulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
                box-shadow: 0 6px 20px rgba(7, 94, 84, 0.35);
            }
            50% {
                opacity: 0.78;
                transform: scale(1.03);
                box-shadow: 0 0 0 8px rgba(37, 211, 102, 0.18);
            }
        }

        @keyframes ssToastPillDot {
            0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(37, 211, 102, 0); }
            100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
        }

        /* DataTables — ensure white backgrounds app-wide */
        .adminuiux-content .dataTables_wrapper {
            background: #fff;
        }

        .adminuiux-content .dataTables_wrapper .table-responsive,
        .adminuiux-content .dataTables_wrapper .dataTables_scroll,
        .adminuiux-content .dataTables_wrapper .dataTables_scrollBody {
            background: #fff;
        }

        .adminuiux-content table.dataTable {
            background: #fff;
            border-collapse: collapse;
        }

        .adminuiux-content table.dataTable thead th,
        .adminuiux-content table.dataTable thead td {
            background-color: #f8fafc;
        }

        .adminuiux-content table.dataTable tbody tr {
            background-color: #fff;
        }

        .adminuiux-content table.dataTable tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .adminuiux-content table.dataTable tbody tr:hover {
            background-color: #f8fafc !important;
        }

        .adminuiux-content table.dataTable tbody td {
            background-color: transparent;
        }

        .adminuiux-content .dataTables_wrapper .dataTables_length,
        .adminuiux-content .dataTables_wrapper .dataTables_filter,
        .adminuiux-content .dataTables_wrapper .dataTables_info,
        .adminuiux-content .dataTables_wrapper .dataTables_paginate {
            background: #fff;
            color: #64748b;
        }

        .adminuiux-content .dataTables_wrapper .dataTables_filter input,
        .adminuiux-content .dataTables_wrapper .dataTables_length select {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.35rem 0.75rem;
        }

        .adminuiux-content .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: #fff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.375rem !important;
            color: #475569 !important;
        }

        .adminuiux-content .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .adminuiux-content .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0f172a !important;
            border-color: #0f172a !important;
            color: #fff !important;
        }

        .adminuiux-content .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
    </style>

  <script defer src="{{asset('backend/assets/js/app134b.js')}}"></script><link href="{{asset('backend/assets/css/app134b.css')}}" rel="stylesheet">
  @yield('css')
</head>

    <body class="main-bg main-bg-opac adminuiux-header-standard theme-blue adminuiux-header-transparent adminuiux-sidebar-fill-white adminuiux-sidebar-standard bg-r-gradient scrollup" data-theme="theme-blue" data-sidebarfill="adminuiux-sidebar-fill-white" data-sidebarlayout="adminuiux-sidebar-standard" data-bs-spy="scroll" data-bs-target="#list-example" data-bs-smooth-scroll="true" tabindex="0" data-headerlayout="adminuiux-header-standard" data-bggradient="bg-r-gradient"
        data-headerfill="adminuiux-header-transparent">
        <!-- Pageloader -->
<div class="pageloader">
    <div class="container h-100">
        <div class="row justify-content-center align-items-center text-center h-100">
            <div class="col-12 mb-auto pt-4"></div>
            <div class="col-auto">
                <img src="{{asset('backend/assets/img/logo.svg')}}" alt="" class="height-100 mb-3">
                <p class="h3 mb-0"><span class="text-gradient">Stock Shield</span></p>
                <p class="small text-secondary mb-3"><span class="">Admin Dashboard </span></p>
                <div class="loader6 mb-2 mx-auto" style="border-color: var(--adminuiux-theme-2);"></div>
            </div>
            <div class="col-12 mt-auto pb-4">
                {{-- <p class="text-secondary">Petal of flower being ready to <span class="text-gradient">blossom</span>...</p> --}}
            </div>
        </div>
    </div>
</div>
            <!-- standard header -->
<header class="adminuiux-header">
    <!-- Fixed navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">

            <!-- main sidebar toggle -->
            <button class="btn btn-link btn-square sidebar-toggler" type="button" onclick="initSidebar()">
                <i class="sidebar-svg" data-feather="menu"></i>
            </button>

            <!-- logo -->
            <a class="navbar-brand" href="#">
                <img data-bs-img="light" src="{{asset('backend/assets/img/logo-light.svg')}}" alt="">
                <img data-bs-img="dark" src="{{asset('backend/assets/img/logo.svg')}}" alt="">
                <div class="">
                    <span class="h4 text-gradient">Stock<span class="fw-bold">Shield</span></span>
                    <p class="company-tagline">Best Stock Tracker</p>
                </div>
            </a>

            <!-- search -->
             

            <!-- menu -->
           

            <!-- right icons button -->
            <div class="ms-auto">
                @if(!$isGlobalStoreAccess && $activeStore)
                    <span class="badge bg-theme-1-subtle text-theme-1 border me-2 d-none d-md-inline-flex align-items-center gap-1 px-3 py-2">
                        <i class="bi bi-shop"></i> {{ $activeStore->name }}
                    </span>
                @endif

                @if(!$isGlobalStoreAccess && $mappedStoreCount > 1)
                    <a href="{{ route('switch-store') }}" class="btn btn-link btn-square btn-link-header" title="Switch Store">
                        <i class="bi bi-arrow-left-right"></i>
                    </a>
                @endif

                <!-- global search toggle -->
                <button class="btn btn-link btn-square btn-icon btn-link-header d-lg-none" type="button" onclick="openSearch()">
                    <i data-feather="search"></i>
                </button>

                <!-- dark mode -->
                <button class="btn btn-link btn-square btnsunmoon btn-link-header" id="btn-layout-modes-dark-page">
                    <i class="sun mx-auto" data-feather="sun"></i>
                    <i class="moon mx-auto" data-feather="moon"></i>
                </button>

                
              

                <!-- notification dropdown -->
                <button class="btn btn-link btn-square btn-icon btn-link-header dropdown-toggle position-relative no-caret" type="button" data-bs-toggle="offcanvas" data-bs-target="#view-notification" aria-expanded="false" id="notificationBellBtn">
                    <i data-feather="bell"></i>
                    <span class="position-absolute top-0 end-0 badge rounded-pill bg-danger p-1 d-none" id="notif-badge">
                        <small id="notif-badge-count">0</small>
                        <span class="visually-hidden">unread action notifications</span>
                    </span>
                    @if(($stockAlertCount ?? 0) > 0)
                    <span class="position-absolute top-0 start-0 badge rounded-pill bg-warning p-1" style="transform: translate(-30%, -20%);" title="Stock alerts for your store">
                        <small>{{ $stockAlertCount }}</small>
                    </span>
                    @endif
                </button>

                <!-- profile dropdown -->
                <div class="dropdown d-inline-block">
                    <a class="dropdown-toggle btn btn-link btn-square btn-link-header style-none no-caret px-0" id="userprofiledd" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                        <div class="row gx-0 d-inline-flex">
                            <div class="col-auto align-self-center">
                                <figure class="avatar avatar-28 rounded-circle coverimg align-middle">
                                @if(Auth::user()->staff && Auth::user()->staff->picture)
                                    <img src="{{ asset(Auth::user()->staff->picture) }}" alt="" id="userphotoonboarding2">
                                @else
                                    <span class="avatar-text rounded-circle d-flex align-items-center justify-content-center bg-primary text-white"
                                        style="width: 28px; height: 28px; font-size: 12px; font-weight: 600;">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                @endif
                            </figure>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end width-300 px-0 pt-0" aria-labelledby="userprofiledd">
                        <div class="p-3 bg-r-gradient rounded mb-2">
                            <a href="#" class="dropdown-item">
                                <div class="row gx-3">
                                    <div class="col-auto ">
                                        <figure class="avatar avatar-50 rounded-circle coverimg align-middle">
                                             @if(Auth::user()->staff && Auth::user()->staff->picture)
                                    <img src="{{ asset(Auth::user()->staff->picture) }}" alt="" id="userphotoonboarding2">
                                @else
                                    <span class="avatar-text rounded-circle d-flex align-items-center justify-content-center bg-primary text-white"
                                        style="width: 28px; height: 28px; font-size: 12px; font-weight: 600;">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                @endif
                                        </figure>
                                    </div>
                                    <div class="col align-self-center ">
                                        <h5 class="mb-1">{{auth()->user()->name}}</h5>
                                        <p class="small"><i class="bi bi-trophy me-2"></i> {{auth()->user()->getUserCategory()}}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="px-2">
                            <div>
                                <a class="dropdown-item" href="{{ route('user-profile') }}"><i data-feather="user" class="avatar avatar-18 me-1"></i> My Profile</a>
                            </div>
                            <div>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <div class="row g-0">
                                        <div class="col align-self-center"><i data-feather="layout" class="avatar avatar-18 me-1"></i>
                                            My Dashboard
                                        </div>
                                        
                                    </div>
                                </a>
                            </div>
                             
                            
                            <div>
                                <a class="dropdown-item" href="{{ route('user-profile') }}">
                                    <i data-feather="settings" class="avatar avatar-18 me-1"></i> Account Setting
                                </a>
                            </div>
                            <div>
                                 <form action="{{ route('logout-authentication-process') }}" method="POST">
									@csrf
                                <button type="submit" class="dropdown-item theme-red" href="adminux-login.html">
                                    <i data-feather="power" class="avatar avatar-18 me-1"></i> Logout
                                </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>
</header>
                <div class="adminuiux-wrap">

                    <!-- Standard sidebar -->
<div class="adminuiux-sidebar ss-sidebar shadow-sm">
    <div class="adminuiux-sidebar-inner ss-sidebar-inner">

        {{-- User card --}}
        <div class="ss-sidebar-user not-iconic">
            <div class="ss-sidebar-user-inner">
                <div class="ss-sidebar-avatar">
                    @if(Auth::user()->staff && Auth::user()->staff->picture)
                        <img src="{{ asset(Auth::user()->staff->picture) }}" alt="">
                    @else
                        {{ $sidebarUserInitials }}
                    @endif
                </div>
                <div class="ss-sidebar-user-info">
                    <div class="ss-sidebar-user-name" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</div>
                    <span class="ss-sidebar-user-role">
                        <i class="bi bi-shield-check"></i>
                        {{ auth()->user()->getUserCategory() }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Active store --}}
        @if(!$isGlobalStoreAccess && $activeStore)
            <div class="ss-sidebar-store not-iconic">
                <i class="bi bi-shop"></i>
                <span>{{ $activeStore->name }}</span>
            </div>
        @elseif($isGlobalStoreAccess)
            <div class="ss-sidebar-store not-iconic">
                <i class="bi bi-globe2"></i>
                <span>All stores</span>
            </div>
        @endif

        <div class="ss-sidebar-section not-iconic">Main Menu</div>

        <ul class="ss-sidebar-nav nav flex-column menu-active-line">
            <li class="ss-nav-item {{ $currentPage === 'dashboard' ? 'is-active' : '' }}">
                <a class="ss-nav-link" href="{{ route('dashboard') }}">
                    <span class="ss-nav-icon"><i class="bi bi-speedometer2"></i></span>
                    <span class="ss-nav-text">Dashboard</span>
                </a>
            </li>

            @foreach ($parents as $parent)
                @php
                    $iconClass = trim(preg_replace('/\bmenu-icon\b/', '', $parent->link_image ?? 'bi bi-circle'));
                    $iconClass = $iconClass !== '' ? $iconClass : 'bi bi-circle';
                    $childCount = count($childrenByParent[$parent->link_id] ?? []);
                    $isParentActive = $currentPage === $parent->page_id;
                    if ($currentPage === 'submenu' && request()->routeIs('submenu')) {
                        try {
                            $activeSubmenuId = Crypt::decrypt((string) request()->route('id'));
                            $isParentActive = (int) $activeSubmenuId === (int) $parent->link_id;
                        } catch (\Throwable $e) {
                            $isParentActive = false;
                        }
                    }
                @endphp
                <li class="ss-nav-item {{ $isParentActive ? 'is-active' : '' }}">
                    <a href="{{ route('submenu', Crypt::encrypt($parent->link_id)) }}" class="ss-nav-link">
                        <span class="ss-nav-icon"><i class="{{ $iconClass }}"></i></span>
                        <span class="ss-nav-text">{{ $parent->link_name }}</span>
                        @if($childCount > 0)
                            <span class="ss-nav-badge not-iconic">{{ $childCount }}</span>
                            <i class="bi bi-chevron-right ss-nav-chevron not-iconic"></i>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="ss-sidebar-footer not-iconic">
            @if(!$isGlobalStoreAccess && $mappedStoreCount > 1)
                <a href="{{ route('switch-store') }}" class="ss-sidebar-footer-link mb-1">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Switch Store</span>
                </a>
            @endif
            <form action="{{ route('logout-authentication-process') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="ss-sidebar-footer-link">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>

    </div>
</div>
                        <main class="adminuiux-content has-sidebar" onclick="contentClick()">

                             @yield('content')
                        </main>
                </div>

                <div id="ss-toast-stack" class="ss-toast-stack" aria-live="polite" aria-atomic="false"></div>
                <button type="button" id="ss-toast-pill" class="ss-toast-pill d-none" aria-live="polite" aria-hidden="true">
                    <span class="ss-toast-pill-dot" aria-hidden="true"></span>
                    <span class="ss-toast-pill-count">0</span>
                    <span class="ss-toast-pill-title">Notifications</span>
                </button>

                <!-- notification -->
                <div class="offcanvas offcanvas-end shadow border-0 maxwidth-300" tabindex="-1" id="view-notification" data-bs-scroll="true" data-bs-backdrop="false">
    <div class="offcanvas-header border-bottom">
        <div class="flex-grow-1">
            <h6 class="mb-0">Notifications</h6>
            <p class="text-secondary mb-0"><span id="action-notif-summary">Loading...</span></p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div id="desktop-notif-permission" class="px-3 py-2 bg-theme-1-subtle border-bottom d-none">
            <p class="small mb-2">Enable desktop alerts to get prompted when action is required.</p>
            <button type="button" class="btn btn-sm btn-theme" id="enableDesktopNotifBtn">
                <i class="bi bi-bell"></i> Enable Desktop Alerts
            </button>
        </div>

        <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between">
            <strong class="small text-uppercase text-secondary">Action Required</strong>
            <button type="button" class="btn btn-link btn-sm p-0" id="markAllNotifReadBtn">Mark all read</button>
        </div>
        <div id="action-notifications-list" class="px-2 py-2">
            <p class="text-secondary small px-2 py-3 mb-0">Loading notifications...</p>
        </div>

        @include('layouts.partials.stock-alerts-offcanvas')
    </div>
</div>

                    <!-- themes -->
                    <!-- theming offcanvas-->
<div class="offcanvas offcanvas-end shadow border-0" tabindex="-1" id="theming" data-bs-scroll="true" data-bs-backdrop="false" aria-labelledby="theminglabel">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title" id="theminglabel">Personalize</h5>
            <p class="text-secondary small">Make it more like your own</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <h6 class="offcanvas-title">Colors</h6>
        <p class="text-secondary small mb-4">Change colors of templates</p>

        <div class="row mb-4 theme-select">
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                        <i class="bi bi-arrow-clockwise"></i>
                    </span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-blue">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-theme-1 theme-blue"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-indigo">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-indigo"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-purple">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-purple"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-pink">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-pink"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-red">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-red"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-orange">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-orange"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-yellow">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-yellow"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-green">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-green"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-teal">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-teal"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-cyan">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-cyan"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-grey">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-grey"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-brown">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-brown"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-chocolate">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-chocolate"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="theme-black">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-dark"></span>
                </div>
            </div>
        </div>

        <h6 class="offcanvas-title">Backgrounds</h6>
        <p class="text-secondary small mb-4">Change color for background</p>
        <div class="row mb-4 theme-background">
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-default">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-default"><i class="bi bi-arrow-clockwise"></i></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-white">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-white"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-r-gradient">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-r-gradient"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-gradient-1">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-1"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-gradient-2">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-2"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-gradient-3">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-3"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-gradient-4">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-4"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-gradient-5">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-5"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-gradient-6">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-6"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-gradient-7">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-7"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-gradient-8">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-8"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-gradient-9">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-9"></span>
                </div>
            </div>
            <div class="col-auto">
                <div class="gradient-box text-center mb-2" data-title="bg-gradient-10">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-10"></span>
                </div>
            </div>
        </div>

        <h6 class="offcanvas-title">Sidebar Layout</h6>
        <p class="text-secondary small mb-4">Change sidebar layout style</p>

        <div class="row mb-4 sidebar-layout">
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-standard" data-bs-toggle="tooltip" title="None">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                        <i class="bi bi-arrow-clockwise"></i>
                    </span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-iconic" data-bs-toggle="tooltip" title="Iconic">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                        <i class="bi bi-bezier h4"></i>
                    </span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-boxed" data-bs-toggle="tooltip" title="Boxed">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                        <i class="bi bi-box h5"></i>
                    </span>
                </div>
            </div>
            <div class="col-auto">
                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-boxed adminuiux-sidebar-iconic" data-bs-toggle="tooltip" title="Iconic+Boxed">
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                        <i class="bi bi-bounding-box h5"></i>
                    </span>
                </div>
            </div>

        </div>

        <div class="text-center mb-4">
            <a href="{{ route('theme-settings') }}" class="btn btn-sm btn-outline-theme">More options <i class="bi bi-arrow-right-short"></i></a>
        </div>
    </div>
</div>

 
<!-- standard footer -->
<footer class="adminuiux-footer has-adminuiux-sidebar mt-auto bg-theme-1">
    <div class="container-fluid">
        <div class="row gx-3 gx-lg-4">
            <div class="col-12 col-md col-lg py-2">
                <span class="small">Copyright @2026,  designed by
                    <a href="" target="_blank" class="text-white">Speedlines Technology</a>  
                </span>
            </div>
            
        </div>
    </div>
</footer>

<!-- theming action-->
<div class="position-fixed bottom-0 end-0 m-3 z-index-5">
    <button class="btn btn-square btn-theme shadow rounded-circle" type="button" data-bs-toggle="offcanvas" data-bs-target="#theming" aria-controls="theming"><i class="bi bi-palette"></i></button>
    <br>
    <button class="btn btn-theme btn-square shadow mt-2 d-none rounded-circle" id="backtotop"><i class="bi bi-arrow-up"></i></button>
</div>

                            <!-- Page Level js -->
    <script src="{{asset('backend/assets/js/adminux/adminux-dashboard.js')}}"></script>
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>

<!-- code highlighter -->
<link rel="stylesheet" href="{{('backend/cdnjs.cloudflare.com/ajax/libs/highlight.js/11.10.0/styles/base16/circus.min.css')}}">
<script src="{{('backend/cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js')}}"></script>
<script>
    document.querySelectorAll('.code').forEach(el => {
        // then highlight each
        hljs.highlightElement(el);
    });
</script>

                    <!-- Page Level js -->
                    <script src="{{asset('backend/assets/js/component/component-smartwizard.js')}}"></script>
                    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
                $(document).ready(function () {
        $('#myTable').DataTable();
        });
        </script>

        <script>
$(document).ready(function () {

    const table = $('#assetsTable').DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[0, 'asc']],
        language: {
            search: "",
            searchPlaceholder:  " search ",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ assets",
            emptyTable: "No assets found"
        },
        columnDefs: [
            { orderable: false, targets: [] }
        ]
    });

    // Category filter
    $('#filterCategory').on('change', function () {
        table.column(3).search(this.value).draw();
    });

    // Status filter
    $('#filterStatus').on('change', function () {
        table.column(7).search(this.value).draw();
    });

    // Location filter
    $('#filterLocation').on('change', function () {
        table.column(6).search(this.value).draw();
    });

    // Reset all filters
    $('#resetFilters').on('click', function () {
        $('#filterCategory, #filterStatus, #filterLocation').val('');
        table.search('').columns().search('').draw();
    });

});
</script>

@unless(View::hasSection('page-alerts'))
<script>
    @if(session('message_success'))
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: @json(session('message_success')),
    width: '28rem',
    padding: '1.5rem 1.75rem 1.35rem',
    buttonsStyling: false,
    customClass: {
        popup: 'compact-swal-popup',
        title: 'compact-swal-title',
        htmlContainer: 'compact-swal-text',
        confirmButton: 'btn compact-swal-confirm success',
    },
    showConfirmButton: true,
    timer: 3500,
    timerProgressBar: true,
});
@endif
@if(session('message_error'))
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: @json(session('message_error')),
    width: '28rem',
    padding: '1.5rem 1.75rem 1.35rem',
    buttonsStyling: false,
    customClass: {
        popup: 'compact-swal-popup',
        title: 'compact-swal-title',
        htmlContainer: 'compact-swal-text',
        confirmButton: 'btn compact-swal-confirm error',
    },
    showConfirmButton: true,
    timer: 4500,
    timerProgressBar: true,
});
@endif
</script>
@endunless
 <script>
        $(document).ready(function () {

    $('.datepicker1').daterangepicker({
        singleDatePicker: true,
        autoApply: true,
        linkedCalendars: false,
        showDropdowns: true,
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('.datepicker1').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });

});
</script>
 <script>
        $(document).ready(function () {

    $('.datepicker2').daterangepicker({
        singleDatePicker: true,
        autoApply: true,
        linkedCalendars: false,
        showDropdowns: true,
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('.datepicker2').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });

});
</script>

<script>

(function () {
    const userId = {{ auth()->id() ?? 0 }};
    const storageKey = 'lastSeenNotificationId_' + userId;
    let lastSeenId = parseInt(localStorage.getItem(storageKey) || '0', 10);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const notifIconUrl = "{{ asset('backend/assets/img/favicon.png') }}";
    const approveRequestUrl = @json(route('ApproveRequest'));
    let audioContext = null;
    const dismissedToastKey = 'dismissedToastIds_' + userId;
    const dismissedToastIds = new Set(
        JSON.parse(sessionStorage.getItem(dismissedToastKey) || '[]')
    );
    const TOAST_AUTO_HIDE_MS = 10000;
    let latestUnreadNotes = [];

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    function resolveActionUrl(note) {
        if (!note) {
            return '#';
        }

        if (note.action_route === 'ApproveRequest' || note.type === 'requisition_submitted') {
            return approveRequestUrl;
        }

        if (note.action_url && note.action_url !== '#') {
            return note.action_url;
        }

        return '#';
    }

    function playNotificationSound() {
        try {
            if (!audioContext) {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }

            if (audioContext.state === 'suspended') {
                audioContext.resume();
            }

            const now = audioContext.currentTime;

            function tone(freq, start, duration, volume) {
                const osc = audioContext.createOscillator();
                const gain = audioContext.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                gain.gain.setValueAtTime(0.0001, now + start);
                gain.gain.exponentialRampToValueAtTime(volume, now + start + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + start + duration);
                osc.connect(gain);
                gain.connect(audioContext.destination);
                osc.start(now + start);
                osc.stop(now + start + duration + 0.05);
            }

            tone(880, 0, 0.11, 0.22);
            tone(1174, 0.13, 0.16, 0.18);
            tone(880, 0.32, 0.12, 0.14);
        } catch (e) {
            // Ignore if audio is blocked by the browser.
        }
    }

    function rememberUnreadNotes(notifications) {
        latestUnreadNotes = (notifications || []).filter(function (note) {
            return note && note.is_unread;
        }).sort(function (a, b) {
            return Number(b.id) - Number(a.id);
        });
    }

    function hasLiveToast() {
        return !!document.querySelector('#ss-toast-stack .ss-toast:not(.ss-toast-exit)');
    }

    function updateToastPill() {
        const pill = document.getElementById('ss-toast-pill');
        if (!pill) {
            return;
        }

        const count = latestUnreadNotes.length;
        const countEl = pill.querySelector('.ss-toast-pill-count');
        const titleEl = pill.querySelector('.ss-toast-pill-title');

        if (count === 0 || hasLiveToast()) {
            pill.classList.add('d-none');
            pill.setAttribute('aria-hidden', 'true');
            return;
        }

        const latest = latestUnreadNotes[0];
        if (countEl) {
            countEl.textContent = count > 99 ? '99+' : String(count);
        }
        if (titleEl) {
            titleEl.textContent = (latest && latest.title)
                ? latest.title
                : (count === 1 ? '1 notification' : count + ' notifications');
        }

        pill.classList.remove('d-none');
        pill.setAttribute('aria-hidden', 'false');
    }

    function persistDismissedToasts() {
        sessionStorage.setItem(dismissedToastKey, JSON.stringify(Array.from(dismissedToastIds)));
    }

    function clearToastAutoHide(toast) {
        if (toast && toast._autoHideTimer) {
            clearTimeout(toast._autoHideTimer);
            toast._autoHideTimer = null;
        }
    }

    function scheduleToastAutoHide(toast) {
        clearToastAutoHide(toast);
        if (!toast) {
            return;
        }

        toast._autoHideTimer = setTimeout(function () {
            dismissToast(toast, true);
        }, TOAST_AUTO_HIDE_MS);
    }

    function openNotificationsPanel() {
        const offcanvasEl = document.getElementById('view-notification');
        if (!offcanvasEl || typeof bootstrap === 'undefined') {
            return;
        }

        bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl).show();
    }

    function dismissToast(toast, rememberDismissal) {
        if (!toast || toast.classList.contains('ss-toast-exit')) {
            return;
        }

        clearToastAutoHide(toast);

        if (rememberDismissal && toast.dataset.notifId) {
            dismissedToastIds.add(toast.dataset.notifId);
            persistDismissedToasts();
        }

        toast.classList.add('ss-toast-exit');
        setTimeout(function () {
            toast.remove();
            updateToastPill();
        }, 260);
    }

    function removeToastById(id) {
        const toast = document.querySelector('.ss-toast[data-notif-id="' + id + '"]');
        dismissToast(toast, false);
    }

    function showPersistentToast(note, playSound) {
        const stack = document.getElementById('ss-toast-stack');
        if (!stack || !note || !note.is_unread) {
            return false;
        }

        const notifId = String(note.id);
        if (dismissedToastIds.has(notifId)) {
            return false;
        }

        document.querySelectorAll('#ss-toast-stack .ss-toast').forEach(function (existing) {
            if (existing.dataset.notifId !== notifId) {
                dismissToast(existing, true);
            }
        });

        if (document.querySelector('.ss-toast[data-notif-id="' + notifId + '"]')) {
            return false;
        }

        const actionUrl = resolveActionUrl(note);
        const toast = document.createElement('div');
        toast.className = 'ss-toast ss-toast-persistent';
        toast.dataset.notifId = notifId;
        toast.dataset.tag = 'ss-action-' + notifId;
        toast.innerHTML = `
            <div class="ss-toast-accent"></div>
            <div class="ss-toast-icon">
                <img src="${notifIconUrl}" alt="">
            </div>
            <div class="ss-toast-body">
                <div class="ss-toast-head">
                    <span class="ss-toast-app">Stock Shield</span>
                    <span class="ss-toast-time">now</span>
                </div>
                <div class="ss-toast-title">${escapeHtml(note.title)}</div>
                <div class="ss-toast-message">${escapeHtml(note.message)}</div>
                <div class="ss-toast-actions">
                    <a href="${actionUrl}" class="ss-toast-open">Open</a>
                </div>
            </div>
            <button type="button" class="ss-toast-close" aria-label="Close notification">&times;</button>
        `;

        toast.querySelector('.ss-toast-close').addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            dismissToast(toast, true);
        });

        toast.addEventListener('click', function (e) {
            if (e.target.closest('.ss-toast-close') || e.target.closest('.ss-toast-open')) {
                return;
            }

            e.preventDefault();
            openNotificationsPanel();
        });

        stack.prepend(toast);
        updateToastPill();
        scheduleToastAutoHide(toast);

        if (playSound) {
            playNotificationSound();
        }

        return true;
    }

    function syncUnreadToasts(notifications) {
        rememberUnreadNotes(notifications);
        const unreadIds = new Set(latestUnreadNotes.map(function (note) {
            return String(note.id);
        }));

        document.querySelectorAll('.ss-toast[data-notif-id]').forEach(function (toast) {
            if (!unreadIds.has(toast.dataset.notifId)) {
                dismissToast(toast, false);
            }
        });

        dismissedToastIds.forEach(function (id) {
            if (!unreadIds.has(id)) {
                dismissedToastIds.delete(id);
            }
        });
        persistDismissedToasts();
        updateToastPill();
    }

    function showNativeNotification(note) {
        if (!('Notification' in window)) {
            return;
        }

        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }

        if (Notification.permission !== 'granted') {
            return;
        }

        const actionUrl = resolveActionUrl(note);
        const notification = new Notification(note.title, {
            body: note.message,
            icon: notifIconUrl,
            badge: notifIconUrl,
            tag: 'ss-action-' + note.id,
            renotify: true,
            requireInteraction: true,
            silent: false,
            timestamp: Date.now(),
        });

        notification.onclick = function () {
            window.focus();
            window.location.href = actionUrl;
            notification.close();
        };
    }

    function notifyUser(note, playSound) {
        const added = showPersistentToast(note, playSound);
        if (added) {
            showNativeNotification(note);
        }
    }

    function updateNotificationBadge(count) {
        const badge = document.getElementById('notif-badge');
        const countEl = document.getElementById('notif-badge-count');
        if (!badge || !countEl) return;

        if (count > 0) {
            countEl.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }

    function updateActionSummary(count) {
        const summary = document.getElementById('action-notif-summary');
        if (summary) {
            summary.textContent = count === 1 ? '1 action required' : count + ' actions required';
        }
    }

    function renderActionNotifications(items) {
        const container = document.getElementById('action-notifications-list');
        if (!container) return;

        const pending = (items || []).filter(function (note) {
            return note.is_unread;
        });

        if (!pending.length) {
            container.innerHTML = '<p class="text-secondary small px-2 py-3 mb-0">No pending actions right now.</p>';
            return;
        }

        container.innerHTML = pending.map(function (note) {
            const unreadClass = note.is_unread ? 'border-primary border-opacity-25 bg-theme-1-subtle' : '';
            const actionUrl = resolveActionUrl(note);
            return `
                <div class="alert alert-light mb-2 action-notif-item ${unreadClass}" data-id="${note.id}">
                    <div class="d-flex gap-2">
                        <figure class="avatar avatar-30 rounded-circle bg-primary text-white flex-shrink-0">
                            <i class="bi bi-exclamation-circle"></i>
                        </figure>
                        <div class="flex-grow-1">
                            <p class="small fw-semibold mb-1">${escapeHtml(note.title)}</p>
                            <p class="small mb-2 text-secondary">${escapeHtml(note.message)}</p>
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <span class="text-secondary" style="font-size:11px;">${note.created_human || ''}</span>
                                <a href="${actionUrl}" class="btn btn-sm btn-outline-theme action-notif-open" data-id="${note.id}">Open</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function loadActionNotifications() {
        $.get("{{ route('notifications.index') }}", function (response) {
            const items = response.notifications || [];
            renderActionNotifications(items);
            syncUnreadToasts(items);
            updateNotificationBadge(response.unread_total || 0);
            updateActionSummary(response.unread_total || 0);
        });
    }

    function markNotificationRead(id) {
        if (!id) return;

        $.ajax({
            url: "{{ url('notifications') }}/" + id + "/read",
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            complete: function () {
                loadActionNotifications();
            }
        });
    }

    function checkForNewRequests() {
        $.ajax({
            url: "{{ route('check-notifications') }}",
            type: 'GET',
            data: { last_id: lastSeenId },
            success: function (response) {
                if (response.count > 0) {
                    const notes = response.notifications || [];
                    const newest = notes[notes.length - 1];
                    notes.forEach(function (note) {
                        if (newest && String(note.id) !== String(newest.id)) {
                            dismissedToastIds.add(String(note.id));
                        }
                    });
                    persistDismissedToasts();
                    if (newest) {
                        notifyUser(newest, true);
                    }
                }

                syncUnreadToasts(response.unread_notifications || []);
                renderActionNotifications(response.unread_notifications || []);

                lastSeenId = response.latest_id || lastSeenId;
                localStorage.setItem(storageKey, lastSeenId);
                updateNotificationBadge(response.unread_total || 0);
                updateActionSummary(response.unread_total || 0);
            }
        });
    }

    function refreshDesktopPermissionBanner() {
        const banner = document.getElementById('desktop-notif-permission');
        if (!banner || !('Notification' in window)) return;

        if (Notification.permission === 'default') {
            banner.classList.remove('d-none');
        } else {
            banner.classList.add('d-none');
        }
    }

    $(document).on('click', '.action-notif-open, .ss-toast-open', function () {
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
    });

    $('#markAllNotifReadBtn').on('click', function () {
        $.ajax({
            url: "{{ route('notifications.read-all') }}",
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function () {
                document.querySelectorAll('.ss-toast[data-notif-id]').forEach(function (toast) {
                    dismissToast(toast, false);
                });
                dismissedToastIds.clear();
                persistDismissedToasts();
                latestUnreadNotes = [];
                updateToastPill();
                loadActionNotifications();
            }
        });
    });

    $('#enableDesktopNotifBtn').on('click', function () {
        if (!('Notification' in window)) return;

        Notification.requestPermission().then(function () {
            refreshDesktopPermissionBanner();
        });
    });

    document.getElementById('ss-toast-pill')?.addEventListener('click', function () {
        openNotificationsPanel();
    });

    $('#notificationBellBtn').on('click', function () {
        loadActionNotifications();
    });

    $('#view-notification').on('show.bs.offcanvas', function () {
        loadActionNotifications();
    });

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            loadActionNotifications();
            checkForNewRequests();
        }
    });

    refreshDesktopPermissionBanner();
    document.addEventListener('click', function () {
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
    }, { once: true });
    loadActionNotifications();
    checkForNewRequests();
    setInterval(checkForNewRequests, 10000);
})();
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">

<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
     @yield('scripts')
    </body>


<!-- Mirrored from adminuiux.com/adminuiux/adminux/html/adminux-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 02 Mar 2026 20:15:06 GMT -->
</html>