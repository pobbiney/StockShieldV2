<!-- page title -->
@php $pageName = "dashboard"; $subpageName = ""; @endphp

@extends('layouts.backendapp')

@section('content')
                            <div class="container-fluid py-3">
                                <div class="row gx-3 gx-lg-4 align-items-center page-title">
                                    <div class="col col-sm mb-3 mb-sm-0 order-1">
                                        <h5 class="mb-0">My Dashboard</h5>
                                        <p class="text-secondary small">This is my personal dasbboard</p>
                                    </div>
                                    <div class="col-12 col-sm-auto order-3 order-sm-2">
                                        <div class="input-group input-group-md width-250">
                                            <input type="text" class="form-control bg-transparent" value="" id="daterangepickerranges">
                                            <span class="input-group-text text-theme-1 bg-transparent" id="titlecalendar" onclick="this.previousElementSibling.click()"><i class="bi bi-calendar-event"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-auto ps-0 position-relative order-2 order-sm-3 mb-3 mb-sm-0">
                                        <div class="dropdown d-inline-block">
                                            <a class="btn btn-link btn-square no-caret dropdown-toggle" href="#" role="button" id="filterintitle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                                <i class="bi bi-filter"></i>
                                            </a>
                                            <div class="dropdown-menu width-300" aria-labelledby="filterintitle">
                                                <div class="p-1 mb-2">
                                                    <div class="input-group input-group-md rounded" style="--mw-dynamic:234px">
                                                        <span class="input-group-text text-theme-1"><i class="bi bi-box"></i></span>
                                                        <select class="form-control choices" id="titltfilterlist" multiple>
                                                            <option value="San Francisco">San Francisco</option>
                                                            <option value="New York">New York</option>
                                                            <option value="London">London</option>
                                                            <option value="Chicago">Chicago</option>
                                                            <option value="India" selected="">India</option>
                                                            <option value="Sydney">Sydney</option>
                                                            <option value="Seattle">Seattle</option>
                                                            <option value="Los Angeles">Los Angeles</option>
                                                            <option value="Indonesia">Indonesia</option>
                                                            <option value="Los Angeles">Los Angeles</option>
                                                            <option value="Chicago">Chicago</option>
                                                            <option value="India">India</option>
                                                        </select>
                                                    </div>
                                                    <div class="invalid-feedback">You have already selected maximum option allowed. (This is Configurable)</div>
                                                </div>
                                                <div class="p-1">
                                                    <h6 class="mb-0">Orders:</h6>
                                                    <p class="text-secondary small">1256 orders last week</p>
                                                </div>
                                                <ul class="list-group list-group-flush bg-transparent border-0 mb-2">
                                                    <li class="list-group-item">
                                                        <div class="row gx-3 gx-lg-4">
                                                            <div class="col">Online Orders</div>
                                                            <div class="col-auto">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox" role="switch" id="titleswitch1">
                                                                    <label class="form-check-label" for="titleswitch1"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="row gx-3 gx-lg-4">
                                                            <div class="col">Offline Orders</div>
                                                            <div class="col-auto">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox" role="switch" id="titleswitch2" checked="">
                                                                    <label class="form-check-label" for="titleswitch2"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <div class="p-1">
                                                    <div class="row gx-3 gx-lg-4">
                                                        <div class="col"><button class="btn btn-outline-secondary border ddclose">cancel</button></div>
                                                        <div class="col-auto">
                                                            <button class="btn btn-theme">Save</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="adminux-company-help-center.html" class="btn btn-link btn-square" data-bs-toggle="tooltip" data-bs-placement="top" id="stylise">
                                            <i class="bi bi-life-preserver"></i>
                                        </a>
                                        <a href="https://1.envato.market/7N7Br" target="_blank" class="btn btn-link btn-square" data-bs-toggle="tooltip" data-bs-placement="top">
                                            <span class="bi bi-basket position-relative">
                                                <span class="position-absolute top-0 start-100 p-1 bg-danger border border-light rounded-circle">
                                                    <span class="visually-hidden">New alerts</span>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- content -->
                            <div class="container mt-3" id="main-content">
                                <!-- welcome bar -->
                                <div class="row gx-3 gx-lg-4 align-items-center">

                                    <!-- welcome message -->
                                    <div class="col-12 col-md mb-3 mb-lg-4">
                                        <p class="h2 fw-normal mb-0">Welcome,</p>
                                        <h1 class="display-3 fw-medium text-gradient">Stock Shield</h1>

                                        <!-- Swiper daily quote -->
                                        <div class="swiper mt-4 swipernav">
                                            <div class="swiper-wrapper">
                                                {{-- <div class="swiper-slide">
                                                    <div class="row gx-3 gx-xl-4">
                                                        <div class="col-auto">
                                                            <i class="bi bi-cake fs-5 avatar avatar-50 rounded-circle bg-theme-1-subtle text-theme-1 theme-pink"></i>
                                                        </div>
                                                        <div class="col">
                                                            <a href="https://www.adminuiux.com/adminuiux/adminux/html/adminuiux-profile-professional.html" class="style-none rounded-5 bg-theme-l-gradient p-1 d-inline-block mb-1 me-2">
                                                                <figure class="avatar avatar-30 rounded-circle me-1 coverimg"><img src="assets/img/modern-ai-image/user-4.jpg" alt=""></figure>
                                                                James Wang <i class="bi bi-chat-right-dots vm mx-2"></i>
                                                            </a>
                                                            <a href="https://www.adminuiux.com/adminuiux/adminux/html/adminuiux-profile-professional.html" class="style-none rounded-5 bg-theme-l-gradient p-1 d-inline-block mb-1 me-2">
                                                                <figure class="avatar avatar-30 rounded-circle me-1 coverimg"><img src="assets/img/modern-ai-image/user-2.jpg" alt=""></figure>
                                                                Millie Tyson <i class="bi bi-chat-right-dots vm mx-2"></i>
                                                            </a>
                                                            <p class="text-secondary">Your 5 partner and our CEO's <span class="fw-bold">birthday</span> today.</p>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                 @foreach($reorderItems as $item)

                                                 @if($item->total_qty  == $item->reorder_level)
                                                <div class="swiper-slide">
                                                    <div class="row gx-3 gx-xl-4">
                                                        <div class="col-auto">
                                                            <i class="bi bi-quote fs-4 avatar avatar-50 rounded-circle bg-theme-1-subtle text-theme-1 theme-grey"></i>
                                                        </div>
                                                        <div class="col">
                                                            <h5 class="mb-1">{{ $item->name }}</h5>
                                                            <p class="text-secondary">reached re-order level please re-stock to continue issuing</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @elseif($item->total_qty  < $item->reorder_level)
                                                <div class="swiper-slide">
                                                    <div class="row gx-3 gx-xl-4">
                                                        <div class="col-auto">
                                                            <i class="bi bi-quote fs-4 avatar avatar-50 rounded-circle bg-theme-1-subtle text-theme-1 theme-grey"></i>
                                                        </div>
                                                        <div class="col">
                                                            <h5 class="mb-1">{{ $item->name }}</h5>
                                                            <p class="text-secondary">is below re-order level please   re-stock to continue issuing</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                 @endif

                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <!-- rank and progress -->
                                    <div class="col-12 col-md-auto col-xl-4 col-xxl-3 ms-auto align-self-center">
                                        <div class="row gx-3 gx-lg-4 mb-3 mb-lg-4">
                                            <div class="col col-md col-lg text-center">
                                                <i class="bi bi-trophy h5 avatar avatar-50 bg-theme-r-gradient theme-purple text-white rounded-circle mb-2"></i>
                                                <h3 class="increamentcount mb-0">{{ $reorderItemsCount }}</h3>
                                                <p class="small text-secondary text-truncated">Re-orderlevel </p>
                                            </div>
                                            <div class="col col-md col-lg text-center">
                                                <i class="bi bi-award h5 avatar avatar-50 bg-theme-r-gradient theme-orange text-white rounded-circle mb-2"></i>
                                                <h3 class="increamentcount mb-0">{{ $count }}</h3>
                                                <p class="small text-secondary text-truncated">Expiry</p>
                                            </div>
                                            <div class="col col-md col-lg text-center">
                                                <i class="bi bi-clipboard-check h5 avatar avatar-50 bg-theme-r-gradient theme-teal text-white rounded-circle mb-2"></i>
                                                <h3 class="increamentcount mb-0">1356</h3>
                                                <p class="small text-secondary text-truncated">Tasks Done</p>
                                            </div>
                                        </div>
                                        {{-- <div class="row gx-3 gx-lg-4 align-items-center mb-3 mb-lg-4">
                                            <div class="col-auto">
                                                <i class="bi bi-star h5 avatar avatar-50 bg-theme-1 text-white theme-yellow rounded-circle"></i>
                                            </div>
                                            <div class="col">
                                                <p class="mb-2">Earn 500 points</p>
                                                <div class="progress height-dynamic mb-1 bg-theme-1-subtle" style="--h-dynamic:5px">
                                                    <div class="progress-bar bg-theme-1" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <p class="text-secondary small"><a href="https://www.adminuiux.com/adminuiux/adminux/html/profile-settings.html" class="style-none">Complete your profile</a></p>
                                            </div>
                                        </div> --}}
                                    </div>

                                    <!-- Tips swiper message -->
                                    <div class="col-auto col-xl-3 d-none d-xxl-block h-100 mb-3 mb-lg-4">
                                        <div class="card adminuiux-card shadow-sm h-100 bg-r-gradient theme-yellow">
                                            <div class="card-header">
                                                <div class="row gx-3 gx-lg-4 align-items-center">
                                                    <div class="col">
                                                        <h6>
                                                            <i class="bi bi-lightbulb text-warning me-1"></i>
                                                           {{ $count }} Product(s) to Expiry in 3 months
                                                        </h6>
                                                    </div>
                                                     
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <!-- image swiper -->
                                                <div class="swiper swipernav width-200 h-100 text-center">
                                                    <div class="swiper-wrapper">
                                                        @foreach ($notifications as $listnote)
                                                            
                                                        
                                                        <div class="swiper-slide">
                                                            <i class="bi bi-chat-right-dots h4 text-success mb-3 d-block"></i>
                                                            <h6>{{ $listnote->name }}</h6>
                                                            <p class="small text-secondary">Will expire in about <b>{{ $listnote->days_left }} day(s)</b> time kindly take note.</p>
                                                        </div>
                                                        @endforeach
                                                        
                                                         
                                                         
                                                    </div>
                                                </div>
                                                <!-- image swiper ends -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- content -->
                                <div class="row gx-3 gx-lg-4">
                                    <!-- summary blocks -->
                                    <div class="col-12 col-md-6 col-lg-6 col-xxl-3">
                                        <div class="card adminuiux-card shadow-sm mb-3 mb-lg-4">
                                            <div class="card-body">
                                                <div class="row gx-3 gx-lg-4 align-items-center">
                                                    <div class="col-auto">
                                                        <div class="position-relative">
                                                            <div id="circleprogressblue" class="avatar avatar-60"></div>
                                                            <div class="avatar avatar-40 h5 bg-theme-1-subtle text-theme-1 rounded-circle position-absolute top-50 start-50 translate-middle">
                                                                <i class="bi bi-calendar2-check"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <p class="text-secondary small mb-1">Task Completed</p>
                                                        <h5>60<small>%</small></h5>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="dropdown d-inline-block">
                                                            <a class="text-secondary no-caret" data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static" role="button">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="javascript:void(0)">Edit</a></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0)">Move</a></li>
                                                                <li><a class="dropdown-item text-danger" href="javascript:void(0)">Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 col-xxl-3">
                                        <div class="card adminuiux-card shadow-sm mb-3 mb-lg-4">
                                            <div class="card-body">
                                                <div class="row gx-3 gx-lg-4 align-items-center">
                                                    <div class="col-auto">
                                                        <div class="position-relative">
                                                            <div id="circleprogressyellow" class="avatar avatar-60"></div>
                                                            <div class="avatar avatar-40 h5 bg-theme-1-subtle text-theme-1 theme-yellow rounded-circle position-absolute top-50 start-50 translate-middle">
                                                                <i class="bi bi-building"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <p class="text-secondary small mb-1">Construction</p>
                                                        <h5>12550<small>USD</small></h5>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="dropdown d-inline-block">
                                                            <a class="text-secondary no-caret" data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static" role="button">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="javascript:void(0)">Edit</a></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0)">Move</a></li>
                                                                <li><a class="dropdown-item text-danger" href="javascript:void(0)">Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 col-xxl-3">
                                        <div class="card adminuiux-card shadow-sm mb-3 mb-lg-4">
                                            <div class="card-body">
                                                <div class="row gx-3 gx-lg-4 align-items-center">
                                                    <div class="col-auto">
                                                        <div class="avatar avatar-60 h5 bg-theme-1-subtle text-theme-1 theme-red rounded-circle">
                                                            <i class="bi bi-emoji-heart-eyes"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <p class="text-secondary small mb-1">Event Joined</p>
                                                        <h5>1525<small>k</small></h5>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="dropdown d-inline-block">
                                                            <a class="text-secondary no-caret" data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static" role="button">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="javascript:void(0)">Edit</a></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0)">Move</a></li>
                                                                <li><a class="dropdown-item text-danger" href="javascript:void(0)">Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 col-xxl-3">
                                        <div class="card adminuiux-card shadow-sm mb-3 mb-lg-4">
                                            <div class="card-body">
                                                <div class="row gx-3 gx-lg-4 align-items-center">
                                                    <div class="col-auto">
                                                        <div class="avatar avatar-60 h5 bg-theme-1 theme-green text-white rounded-circle">
                                                            <i class="bi bi-thermometer-sun"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <p class="text-secondary small mb-1">Temperature</p>
                                                        <h5>45 <small><sup>0</sup>C, Room-32</small></h5>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="dropdown d-inline-block">
                                                            <a class="text-secondary no-caret" data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static" role="button">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="javascript:void(0)">Edit</a></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0)">Move</a></li>
                                                                <li><a class="dropdown-item text-danger" href="javascript:void(0)">Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row gx-3 gx-lg-4 mb-3 mb-lg-4">
                                    <div class="col text-center py-3">
                                        <h4>The sort <span class="text-gradient">summary</span> may help you</h4>
                                        <p class="text-secondary">Keep yourself updated, No matter how much workload is.</p>
                                    </div>
                                </div>

                                <div class="row gx-3 gx-lg-4">
                                    <div class="col-12 col-md-6 col-lg-6 col-xxl-3 mb-3 mb-lg-4">
                                        <!-- finance card -->
                                        <div class="card adminuiux-card shadow-sm bg-l-gradient-light theme-blue">
                                            <div class="card-header">
                                                <div class="row gx-3 gx-lg-4 align-items-center">
                                                    <div class="col">
                                                        <h6>
                                                            <i class="bi bi-cash h5 me-1 avatar avatar-40 bg-theme-1-subtle text-theme-1 rounded me-2"></i>
                                                            Finance
                                                        </h6>
                                                    </div>
                                                    <div class="col-auto">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="swiper swipernav mb-3 mb-lg-4">
                                                    <div class="swiper-wrapper">
                                                        <div class="swiper-slide width-240">
                                                            <div class="card adminuiux-card shadow-sm mb-2 theme-blue bg-theme-r-gradient overflow-hidden">
                                                                <div class="coverimg top-0 start-0 h-100 w-100 position-absolute z-index-0 opacity-25">
                                                                    <img src="assets/img/modern-ai-image/peacoke-2.jpg" alt="" style="display: none;">
                                                                </div>
                                                                <div class="card-body position-relative z-index-1">
                                                                    <div class="row gx-3 align-items-center mb-4">
                                                                        <div class="col-auto align-self-center">
                                                                            <i class="bi bi-amazon fs-4"></i>
                                                                        </div>
                                                                        <div class="col text-end">
                                                                            <p class="fs-12">
                                                                                <span class="opacity-50 small">City Bank</span><br>
                                                                                <span class="">Credit Card</span>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <p class="fw-medium h6 mb-3">
                                                                        000 0000 0001 546598
                                                                    </p>
                                                                    <div class="row gx-3 gx-lg-4">
                                                                        <div class="col-auto fs-12">
                                                                            <p class="mb-0 opacity-50 small">Expiry</p>
                                                                            <p>09/023</p>
                                                                        </div>
                                                                        <div class="col text-end fs-12">
                                                                            <p class="mb-0 opacity-50 small">Card Holder</p>
                                                                            <p>AdminUIUX</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row amount-data">
                                                                <div class="col">
                                                                    <p class="opacity-50 small mb-1">Expense</p>
                                                                    <p>1500.00 <small class="text-success">18.0% <i class="bi bi-arrow-up"></i></small></p>
                                                                </div>
                                                                <div class="col-auto text-end">
                                                                    <p class="opacity-50 small mb-1">Limit Remain</p>
                                                                    <p>13500.00</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="swiper-slide width-240">
                                                            <div class="card adminuiux-card shadow-sm theme-pink bg-theme-r-gradient mb-2 overflow-hidden">
                                                                <div class="coverimg top-0 start-0 h-100 w-100 position-absolute z-index-0 opacity-25">
                                                                    <img src="assets/img/modern-ai-image/flamingo-2.jpg" alt="" style="display: none;">
                                                                </div>
                                                                <div class="card-body position-relative z-index-1">
                                                                    <div class="row gx-3 align-items-center mb-4">
                                                                        <div class="col-auto align-self-center">
                                                                            <i class="bi bi-apple fs-4"></i>
                                                                        </div>
                                                                        <div class="col text-end">
                                                                            <p class="fs-12">
                                                                                <span class="opacity-50 small">City Bank</span><br>
                                                                                <span class="">Credit Card</span>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <p class="fw-medium h6 mb-3">
                                                                        000 0000 0001 546598
                                                                    </p>
                                                                    <div class="row gx-3 gx-lg-4">
                                                                        <div class="col-auto fs-12">
                                                                            <p class="mb-0 opacity-50 small">Expiry</p>
                                                                            <p>09/023</p>
                                                                        </div>
                                                                        <div class="col text-end fs-12">
                                                                            <p class="mb-0 opacity-50 small">Card Holder</p>
                                                                            <p>AdminUIUX</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row amount-data">
                                                                <div class="col">
                                                                    <p class="text-secondary small mb-1">Expense</p>
                                                                    <p>3650.00 <small class="text-danger">11.0% <i class="bi bi-arrow-down"></i></small></p>
                                                                </div>
                                                                <div class="col-auto text-end">
                                                                    <p class="text-secondary small mb-1">Limit Remain</p>
                                                                    <p>35500.00</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="swiper-slide width-240">
                                                            <div class="card adminuiux-card shadow-sm theme-yellow bg-r-gradient mb-2">
                                                                <div class="card-body">
                                                                    <div class="row gx-3 align-items-center mb-4">
                                                                        <div class="col-auto align-self-center">
                                                                            <i class="bi bi-amazon fs-4"></i>
                                                                        </div>
                                                                        <div class="col text-end">
                                                                            <p class="fs-12">
                                                                                <span class="opacity-50 small">City Bank</span><br>
                                                                                <span class="">Credit Card</span>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <p class="fw-medium h6 mb-3">
                                                                        000 0000 0001 546598
                                                                    </p>
                                                                    <div class="row gx-3 gx-lg-4">
                                                                        <div class="col-auto fs-12">
                                                                            <p class="mb-0 opacity-50 small">Expiry</p>
                                                                            <p>09/023</p>
                                                                        </div>
                                                                        <div class="col text-end fs-12">
                                                                            <p class="mb-0 opacity-50 small">Card Holder</p>
                                                                            <p>AdminUIUX</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row amount-data">
                                                                <div class="col">
                                                                    <p class="text-secondary small mb-1">Expense</p>
                                                                    <p>1500.00 <small class="text-success">18.0 <i class="bi bi-arrow-up"></i></small></p>
                                                                </div>
                                                                <div class="col-auto text-end">
                                                                    <p class="text-secondary small mb-1">Limit Remain</p>
                                                                    <p>13500.00</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card adminuiux-card">
                                                    <div class="card-body">
                                                        <div class="row gx-3 align-items-center">
                                                            <div class="col-auto">
                                                                <div class="avatar avatar-50 h5 bg-theme-1-subtle text-theme-1 rounded-circle">
                                                                    <i class="bi bi-receipt"></i>
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <p class="text-secondary small mb-1">Billed Amount</p>
                                                                <h5>1525 <small>USD</small></h5>
                                                            </div>
                                                            <div class="col-auto">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer justify-content-center text-center">
                                                <a href="https://www.adminuiux.com/adminuiux/adminux/html/finance-dashboard.html" class="btn btn-sm btn-link">Visit Finance Dashboard <i class="bi bi-arrow-right vm"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 col-xxl-3 mb-4">
                                        <!-- Inventory card -->
                                        <div class="card adminuiux-card shadow-sm bg-l-gradient-light theme-yellow h-100">
                                            <div class="card-header">
                                                <div class="row gx-3 gx-lg-4 align-items-center">
                                                    <div class="col">
                                                        <h6>
                                                            <i class="bi bi-box h5 me-1 avatar avatar-40 bg-theme-1-subtle text-theme-1 rounded me-2"></i>
                                                            Inventory
                                                        </h6>
                                                    </div>
                                                    <div class="col-auto">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-auto">
                                                        <div class="rounded bg-theme-1 text-white p-3">
                                                            <p class="opacity-75 small mb-1">
                                                                Annual<br />Income
                                                            </p>
                                                            <h5>$124k</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center">
                                                        <p class="text-secondary small mb-0">United States</p>
                                                        <p>45<small>% Sales</small></p>

                                                        <div class="mt-3">
                                                            <div class="progress height-dynamic mb-1 bg-theme-1-subtle" style="--h-dynamic:5px">
                                                                <div class="progress-bar bg-theme-1" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <p class="small text-secondary">Targeted orders <span class="float-end">153k</span></p>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-auto">
                                                        <div class="rounded bg-theme-1-subtle p-3">
                                                            <p class="opacity-75 small mb-1">Annual<br />Income</p>
                                                            <h5>$124k</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center">
                                                        <p class="text-secondary small mb-0">United Kingdom</p>
                                                        <p>15<small>% Sales</small></p>

                                                        <div class="mt-3">
                                                            <div class="progress height-dynamic mb-1 bg-theme-1-subtle" style="--h-dynamic:5px">
                                                                <div class="progress-bar bg-theme-1" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <p class="small text-secondary">Targeted orders <span class="float-end">53k</span></p>
                                                    </div>
                                                </div>
                                                <div class="row gx-3 gx-lg-4">
                                                    <div class="col col-md text-center">
                                                        <i class="bi bi-box h5 avatar avatar-30 text-theme-1 theme-green mb-2"></i>
                                                        <h4 class="mb-0">1265</h4>
                                                        <p class="small text-secondary">In Stock</p>
                                                    </div>
                                                    <div class="col col-md text-center">
                                                        <i class="bi bi-truck h5 avatar avatar-30 text-theme-1 mb-2"></i>
                                                        <h4 class="mb-0">365</h4>
                                                        <p class="small text-secondary">Delivered</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer justify-content-center text-center">
                                                <a href="https://www.adminuiux.com/adminuiux/adminux/html/inventory-dashboard.html" class="btn btn-sm btn-link">Visit Inventory Dash <i class="bi bi-arrow-right vm"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 col-xxl-3 mb-4">
                                        <!-- Network card -->
                                        <div class="card adminuiux-card shadow-sm bg-l-gradient-light theme-red h-100">
                                            <div class="card-header">
                                                <div class="row gx-3 gx-lg-4 align-items-center">
                                                    <div class="col">
                                                        <h6>
                                                            <i class="bi bi-hdd-rack h5 me-1 avatar avatar-40 bg-theme-1-subtle text-theme-1 rounded me-2"></i>
                                                            Network
                                                        </h6>
                                                    </div>
                                                    <div class="col-auto">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="height-100 mb-2">
                                                    <canvas id="smallchart2"></canvas>
                                                </div>
                                                <p class="mb-1">Server CPU <span class="text-secondary">#0514-R3D</span></p>
                                                <p class="text-secondary">45% 3.2 MHz</p>
                                                <div class="card adminuiux-card mt-3">
                                                    <div class="card-body">
                                                        <div class="row gx-3 align-items-center">
                                                            <div class="col-auto">
                                                                <div class="position-relative">
                                                                    <div id="circleprogressred" class="avatar avatar-50"></div>
                                                                    <div class="avatar avatar-30 h5 bg-theme-1-subtle text-theme-1 theme-red rounded-circle position-absolute start-50 top-50 translate-middle">
                                                                        <i class="bi bi-bug"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <p class="text-secondary small mb-1">Ticket Created</p>
                                                                <p>651<small> and 250 yesterday</small></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer border-top py-0">
                                                        <div class="row gx-3 ">
                                                            <div class="col py-2">
                                                                <p class="text-secondary small mb-1">Resolved</p>
                                                                <p class="text-success">432</p>
                                                            </div>
                                                            <div class="col border-start py-2">
                                                                <p class="text-secondary small mb-1">In Progress</p>
                                                                <p class="text-theme-1 theme-yellow">50</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer justify-content-center text-center">
                                                <a href="https://www.adminuiux.com/adminuiux/adminux/html/network-dashboard.html" class="btn btn-sm btn-link">Visit Network Dashboard <i class="bi bi-arrow-right vm"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 col-xxl-3 mb-4">
                                        <!-- Social card -->
                                        <div class="card adminuiux-card shadow-sm bg-l-gradient-light theme-green h-100">
                                            <div class="card-header">
                                                <div class="row gx-3 gx-lg-4 align-items-center">
                                                    <div class="col">
                                                        <h6>
                                                            <i class="bi bi-people h5 me-1 avatar avatar-40 bg-theme-1-subtle text-theme-1 rounded me-2"></i> Social
                                                        </h6>
                                                    </div>
                                                    <div class="col-auto">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row gx-3 align-items-center mb-3">
                                                    <div class="col-auto">
                                                        <figure class="coverimg rounded width-100 height-80 mb-0">
                                                            <img src="assets/img/modern-ai-image/user-5.jpg" alt="" class="mw-100" />
                                                        </figure>
                                                    </div>
                                                    <div class="col">
                                                        <p class="text-secondary small mb-0">Boosted Post</p>
                                                        <h4>2,545,05</h4>
                                                        <p class="text-secondary small">People Reached</p>
                                                    </div>
                                                </div>
                                                <div class="row gx-3 gx-lg-4 align-items-center">
                                                    <div class="col-12 mb-2">
                                                        <p class="text-secondary small">Buy now or Share now! Do support </p>
                                                    </div>
                                                    <div class="col">
                                                        <p class="small text-secondary mb-0">Likes</p>
                                                        <h5 class="mb-0">65.15 k</h5>
                                                    </div>
                                                    <div class="col">
                                                        <p class="small text-secondary mb-0">Retweet</p>
                                                        <h5 class="mb-0">8.2 k</h5>
                                                    </div>
                                                    <div class="col">
                                                        <p class="small text-secondary mb-0">Clicks</p>
                                                        <h5 class="mb-0">52.01 k</h5>
                                                    </div>
                                                </div>

                                                <div class="card adminuiux-card mt-3 bg-theme-r-gradient text-white">
                                                    <div class="card-body">
                                                        <div class="row gx-3 align-items-center mb-2">
                                                            <div class="col-auto">
                                                                <figure class="avatar avatar-50 coverimg rounded">
                                                                    <img src="assets/img/modern-ai-image/pet-4.jpg" alt="" />
                                                                </figure>
                                                            </div>
                                                            <div class="col">
                                                                <p>Learn about software and Framework. All...</p>
                                                            </div>
                                                        </div>
                                                        <div class="row gx-3 gx-lg-4 align-items-center">
                                                            <div class="col-auto">
                                                                <p class="mb-0">125</p>
                                                                <p class="small">Reached</p>
                                                            </div>
                                                            <div class="col-auto">
                                                                <p class="mb-0">35</p>
                                                                <p class="small">Likes</p>
                                                            </div>
                                                            <div class="col text-end">
                                                                <button class="btn btn-sm btn-light">Boost</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer justify-content-center text-center">
                                                <a href="https://www.adminuiux.com/adminuiux/adminux/html/social-dashboard.html" class="btn btn-sm btn-link">Visit Social Dashboard <i class="bi bi-arrow-right vm"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                               
                             
                            </div>
@endsection

@section('scripts')
    
@endsection