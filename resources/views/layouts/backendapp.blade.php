@php

    $staff_query = DB::select('SELECT * FROM staff WHERE staff_id = :id', ['id' => auth()->user()->staff_id]);

    $userCat = auth()->user()->user_cat;
    $links = DB::select('SELECT user_links.link_id, user_links.page_id,user_links.page_id_sub, user_links.link_url, user_links.link_name, user_links.link_image, user_links.link_parent FROM user_cat_links INNER JOIN user_links ON user_cat_links.link_id = user_links.link_id WHERE user_cat_links.cat_id = :id ORDER BY user_links.link_name ASC',['id' => $userCat]);
    $parents = array();
    $child = array();
    foreach ($links as $row_links) {
        if ($row_links->link_parent == 0) {
            $parents[] = $row_links;
        } else {
            $child[] = $row_links;
        }
    }

    
@endphp
<!DOCTYPE html>
<html lang="en">
<!-- dir="rtl"-->

<!-- Mirrored from adminuiux.com/adminuiux/adminux/html/adminux-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 02 Mar 2026 20:13:42 GMT -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

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
                <button class="btn btn-link btn-square btn-icon btn-link-header dropdown-toggle position-relative no-caret" type="button" data-bs-toggle="offcanvas" data-bs-target="#view-notification" aria-expanded="false">
                    <i data-feather="bell"></i>
                    <span class="position-absolute top-0 end-0 badge rounded-pill bg-danger p-1">
                        <small>{{ $reorderItemsCount}} +</small>
                        <span class="visually-hidden">unread messages</span>
                    </span>
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
                    <!-- Standard sidebar -->
<div class="adminuiux-sidebar shadow-sm">
    <div class="adminuiux-sidebar-inner">
        <div class="px-3 not-iconic mt-2">
            <div class="row gx-3 gx-lg-4">
                <div class="col align-self-center menu-name">
                    <h6>Main navigation</h6>
                </div>
                <div class="col-auto">
                    <a class="collapsed btn btn-link btn-square" data-bs-toggle="collapse" data-bs-target="#usersidebarprofile" aria-expanded="false" role="button" aria-controls="usersidebarprofile">
                        <i class="bi bi-person-circle"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- user information -->
        <div class="px-3 text-center not-iconic collapse" id="usersidebarprofile">
            <div class="avatar avatar-100 rounded-circle shadow-sm my-3 bg-white">
                <figure class="avatar avatar-90 rounded-circle coverimg">
                    @if(Auth::user()->staff)
                    <img src="{{ asset(Auth::user()->staff->picture) }}" alt="" id="userphotoonboarding" style="display: none;">
                    @endif
                </figure>
            </div>
            <h5 class="mb-0" id="usernamedisplay">{{auth()->user()->name}}</h5>
            <p class="text-secondary small mb-3">{{auth()->user()->getUserCategory()}}</p>
        </div>

        <!-- user menu navigation -->
        <ul class="nav flex-column menu-active-line">
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="{{route('dashboard')}}">
                    <i class="menu-icon bi bi-speedometer2"></i>
                     <div class="col menu-name @if ($pageName == "dashboard") active  @endif">Dashboard</div>
                </a>
            </li>
            @foreach ($parents as $parent)
            <li class="nav-item  " class="@if ($pageName == $parent->page_id) active  @endif">
                <a href="{{ route('submenu', Crypt::encrypt($parent->link_id)) }}" class="nav-link  ">
                    <i class="{{$parent->link_image}}"></i>
                    <div class="col menu-name">{{$parent->link_name}}</div>
                </a> 
                {{-- <ul class="dropdown-menu">
                    @foreach ($child as $sub)
									   @if ($parent->link_id == $sub->link_parent)
                    <li class="nav-item">
                        <a class="nav-link" href="{{route($sub->link_url)}}">
                            <i class="menu-icon bi bi-plus"></i>
                            <div class="@if ($subpageName == $sub->page_id_sub) active @endif">{{ $sub->link_name}}</div>
                        </a>
                    </li>
                    @endif
				    @endforeach
                  
                </ul> --}}
            </li>
             @endforeach
        </ul>

        <!-- applications -->
        
       

    </div>
</div>
                        <main class="adminuiux-content has-sidebar" onclick="contentClick()">

                             @yield('content')
                        </main>
                </div>

                <!-- notification -->
                <div class="offcanvas offcanvas-end shadow border-0 maxwidth-300" tabindex="-1" id="view-notification" data-bs-scroll="true" data-bs-backdrop="false">
    <div class="offcanvas-header border-bottom">
        <div class="flex-grow-1">
            <h6 class="mb-0">Notifications</h6>
            <p class="text-secondary">{{ $reorderItemsCount }} new updates</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    {{-- <div class="small text-center px-3 py-2 bg-theme-1-subtle text-theme-1 border-bottom">
        <div class="input-group">
            <input type="text" class="form-control daterangepickers">
            <span class="input-group-text text-secondary">
                <i class="bi bi-calendar-week"></i>
            </span>
        </div>
    </div> --}}
    <div class="offcanvas-body">
         
        @foreach($reorderItems as $item)

        @if($item->total_qty  == $item->reorder_level)
        <div class="alert alert-warning mb-2">
            <div class="row gx-3">
                <div class="col-auto">
                    <figure class="avatar avatar-30 rounded-circle bg-warning text-white">
                        <i class="bi bi-bell"></i>
                    </figure>
                </div>
                <div class="col">
                    <p class="small mb-2"><b> {{ $item->name }} </b> reached re-order level please re-stock to continue issuing.</p>

                    {{-- <div class="row gx-3 align-items-center">
                        <div class="col">
                            <p class="text-secondary small">4 days ago</p>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:void(0)" class="btn btn-sm btn-square btn-link theme-red"><i class="bi bi-trash"></i></a>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
         @elseif($item->total_qty  < $item->reorder_level)
        
       <div class="alert alert-danger mb-2">
            <div class="row gx-3">
                <div class="col-auto">
                    <figure class="avatar avatar-30 rounded-circle bg-warning text-white">
                        <i class="bi bi-bell"></i>
                    </figure>
                </div>
                <div class="col">
                    <p class="small mb-2"><b>{{ $item->name }}</b> is below re-order level please   re-stock to continue issuing.</p>

                    {{-- <div class="row gx-3 align-items-center">
                        <div class="col">
                            <p class="text-secondary small">4 days ago</p>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:void(0)" class="btn btn-sm btn-square btn-link theme-red"><i class="bi bi-trash"></i></a>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
       @endif

      @endforeach
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

<script>
    @if(session('message_success'))
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: "{{ session('message_success') }}",
    showConfirmButton: true,
    timer: 5000
});
@endif
@if(session('message_error'))
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: "{{ session('message_error') }}",
     showConfirmButton: true,
    timer: 5000
});
@endif
</script>
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">

<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
     @yield('scripts')
    </body>


<!-- Mirrored from adminuiux.com/adminuiux/adminux/html/adminux-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 02 Mar 2026 20:15:06 GMT -->
</html>