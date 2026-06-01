<!DOCTYPE html>
<html lang="en">
<!-- dir="rtl"-->

<!-- Mirrored from adminuiux.com/adminuiux/adminux/html/adminux-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 02 Mar 2026 20:18:22 GMT -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>Stock Shield</title>
    <link rel="icon" type="image/png" href="assets/img/favicon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300..800&amp;family=SUSE:wght@100..800&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --adminuiux-content-font: "Open Sans", sans-serif;
            --adminuiux-content-font-weight: 400;
            --adminuiux-title-font: "SUSE", sans-serif;
            --adminuiux-title-font-weight: 600;
        }
    </style>

<script defer src="{{asset('backend/assets/js/app134b.js')}}"></script><link href="{{asset('backend/assets/css/app134b.css')}}" rel="stylesheet"></head>

    <body class="main-bg main-bg-opac adminuiux-header-standard theme-blue adminuiux-header-transparent adminuiux-sidebar-fill-white adminuiux-sidebar-standard bg-r-gradient scrollup" data-theme="theme-blue" data-sidebarfill="adminuiux-sidebar-fill-white" data-sidebarlayout="adminuiux-sidebar-standard" data-bs-spy="scroll" data-bs-target="#list-example" data-bs-smooth-scroll="true" tabindex="0" data-headerlayout="adminuiux-header-standard" data-bggradient="bg-r-gradient"
        data-headerfill="adminuiux-header-transparent"><!-- -->
        <!-- Pageloader -->
<div class="pageloader">
    <div class="container h-100">
        <div class="row justify-content-center align-items-center text-center h-100">
            <div class="col-12 mb-auto pt-4"></div>
            <div class="col-auto">
                <img src="assets/img/logo.svg" alt="" class="height-100 mb-3">
                <p class="h3 mb-0"><span class="text-gradient">Stock Shield</span></p>
                <p class="small text-secondary mb-3"><span class="">Admin Login</span></p>
                <div class="loader6 mb-2 mx-auto" style="border-color: var(--adminuiux-theme-2);"></div>
            </div>
            <div class="col-12 mt-auto pb-4">
                
            </div>
        </div>
    </div>
</div> <!-- standard header -->
            <!-- standard header -->
<header class="adminuiux-header z-index-5">
    <nav class="navbar ">
        <div class="container-fluid">
            <!-- logo -->
            <a class="navbar-brand text-white" href="#">
                <img data-bs-img="light" src="{{asset('backend/assets/img/logo-light.svg')}}" alt="" class="me-2">
                <img data-bs-img="dark" src="{{asset('backend/assets/img/logo.svg')}}" alt="" class="me-2">
                <div class="d-inline-block">
                    <span class="h4 text-white">STOCK<span class="fw-bold text-white">SHIELD</span></span>
                    <p class="company-tagline text-white">Best Stock Shield</p>
                </div>
            </a>

            <div class=" ms-auto "></div>
            <!-- right icons button -->
            <div class="ms-auto">

            </div>
        </div>
    </nav>
</header>
                <main class="flex-shrink-0 pt-0 z-index-1">
                    <div class="container">
                        <div class="auth-wrapper">
                            <!--Page body-->
                            <div class="coverimg h-100 w-100 top-0 start-0 position-absolute z-index-0">
                                <img src="{{asset('backend/assets/img/background-image/backgorund-image-13.jpg')}}" alt="" />
                            </div>
                            <!-- login wrap -->
                            <div class="row justify-content-center minheight-dynamic" style="--mih-dynamic: calc(100vh - 135px)">
                                <div class="col-12 col-md-8 col-xl-6">
                                    <div class="h-100 py-4 px-md-3">
                                        <div class="row h-100 align-items-center justify-content-center mt-md-3">
                                            <div class="col-12 col-sm-8 col-md-11 col-xl-11 col-xxl-10 login-box">
                                                 @if (session('login_error_message'))
                                                        <p class="alert alert-danger" align="center">{{session('login_error_message')}}</p>
                                                 @endif
                                                <form enctype="multipart/form-data" action="{{ route('authentication-process') }}" method="POST">
                                                    @csrf
                                                    <div class="card adminuiux-card shadow-sm mb-2">
                                                        <div class="card-body">
                                                            <div class="text-center mb-4">
                                                                <h2 class="mb-1 text-theme-1">Login</h2>
                                                                <p class="text-secondary">Provide your credentials to gain access</p>
                                                            </div>
                                                            <div class="form-floating mb-3">
                                                                <input type="email" class="form-control" name="email" placeholder="Enter email address"   autofocus="">
                                                                <label for="emailadd">Email Address</label>
                                                                @error('email') <small style="color:red;">{{$message}}</small>@enderror
                                                            </div>
                                                            <div class="position-relative">
                                                                <div class="form-floating mb-3">
                                                                    <input type="password" class="form-control" name="password" placeholder="Enter your password" >
                                                                    <label for="passwd">Password</label>
                                                                    @error('password') <small style="color:red;">{{$message}}</small>@enderror
                                                                </div>
                                                                <button class="btn btn-square btn-link text-theme-1 position-absolute end-0 top-0 mt-2 me-2 ">
                                                                    <i class="fa fa-eye"></i>
                                                                </button>
                                                            </div>
                                                            <div class="row gx-3 align-items-center mb-3">
                                                                <div class="col">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" name="rememberme" id="rememberme">
                                                                        <label class="form-check-label" for="rememberme">Remember me</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <a href="adminux-forgot-password.html" class="btn btn-link">Forgot Password?</a>
                                                                </div>
                                                            </div>
                                                            <div class="row gx-3 align-items-center mb-4">
                                                                <div class="col">
                                                                    <button class="btn btn-lg btn-theme w-100 " type="submit">Login</button>
                                                                </div>
                                                            
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                <!-- standard footer -->
                <!-- standard index footer -->
<footer class="adminuiux-footer mt-auto bg-theme-1">
    <div class="container-fluid text-center">
        <span class="small">Copyright @2026, <a href="#" target="_blank" class="text-white">Developed By Speedlines Technology</a>  
        </span>
    </div>
</footer>

<!-- theming action-->
<div class="position-fixed bottom-0 end-0 m-3 z-index-5">
    <button class="btn btn-square btn-accent shadow rounded-circle" type="button" data-bs-toggle="offcanvas" data-bs-target="#theming" aria-controls="theming"><i class="fa fa-dashboard" style="color:white"></i></button>
    <br>
    <button class="btn btn-theme btn-square shadow mt-2 d-none rounded-circle" id="backtotop"><i class="bi bi-arrow-up"></i></button>
</div> <!-- theming -->
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
                        <i class="fa fa-refresh"></i>
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
                    <span class="avatar avatar-40 rounded-circle mb-2 bg-default"><i class="fa fa-refresh"></i></span>
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

        
         
    </div>
</div> <!-- Page Level js -->
                        <script src="{{asset('backend/assets/js/adminux/adminux-auth.js')}}" type="text/javascript"></script>
    </body>


<!-- Mirrored from adminuiux.com/adminuiux/adminux/html/adminux-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 02 Mar 2026 20:18:22 GMT -->
</html>