<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from dompet.dexignlab.com/codeigniter/demo/page_login by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 01 Mar 2026 22:15:22 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
    <meta name="keywords" content="" />
	<meta name="author" content="" />
	<meta name="robots" content="" />
    <meta name="viewport" content="width=device-width,initial-scale=1">
	<meta name="description" content="Mobile Loan Application Platform" />
	<meta property="og:title" content="Mobile Loan Application Platform" />
	<meta property="og:description" content="Mobile Loan Application Platform Developed By Speedlines Technology." />
	<meta property="og:image" content="" />
	<meta name="format-detection" content="telephone=no">
    <title>Mobile Loan Application Platform</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('frontend/public/assets/images/favicon.png')}}">
    <link href="{{asset('frontend/public/assets/css/style.css')}}" rel="stylesheet">
	
</head>

<body class="vh-100">
	<div class="authincation h-100">
        <div class="container-fluid h-100">
            <div class="row h-100">
				<div class="col-lg-6 col-md-12 col-sm-12 mx-auto align-self-center">
					<div class="login-form">
						<div class="text-center">
							<h3 class="title">Sign In</h3>
							<p>Sign in to your account to start using Dompact</p>
						</div>
                              @if (session('message_success'))
								<p class="alert alert-success" align="center" style="color:green"><b>{{session('message_success')}}</b></p>
								@endif

								@if (session('message_error'))
								<p class="alert alert-danger" align="center" style="color: red">{{session('message_error')}}</p>
								@endif
						<form enctype="multipart/form-data" action="{{ route('frontend-login') }}" method="POST">
                            @csrf
							<div class="mb-4">
								<label class="mb-1 text-dark">Email</label>
								<input type="email" class="form-control form-control" name="email"  placeholder="Enter Email">
                                @error('email')<small style="color:red;">{{$message}}</small>@enderror
							</div>
							<div class="mb-4 position-relative">
								<label class="mb-1 text-dark">Password</label>
								<input type="password" name="password" class="form-control form-control"  placeholder="Enter Password">
								<span class="show-pass eye">
								
									<i class="fa fa-eye-slash"></i>
									<i class="fa fa-eye"></i>
								
								</span>
                                @error('password')<small style="color:red;">{{$message}}</small>@enderror
							</div>
							<div class="form-row d-flex justify-content-between mt-4 mb-2">
								{{-- <div class="mb-4">
									<div class="form-check custom-checkbox mb-3">
										<input type="checkbox" class="form-check-input" id="customCheckBox1" required="">
										<label class="form-check-label mt-1" for="customCheckBox1">Remember my preference</label>
									</div>
								</div> --}}
								<div class="mb-4">
									<a href="{{ url('forgot-password') }}" class="btn-link text-primary">Forgot Password?</a>
								</div>
							</div>
							<div class="text-center mb-4">
								<button type="submit" class="btn btn-primary btn-block">Sign In</button>
							</div>
							 
							
							{{-- <div class="mb-3">
								<ul class="d-flex align-self-center justify-content-center">
									<li><a target="_blank" href="https://www.facebook.com/" class="fab fa-facebook-f btn-facebook"></a></li>
									<li><a target="_blank" href="https://www.google.com/" class="fab fa-google-plus-g btn-google-plus mx-2"></a></li>
									<li><a target="_blank" href="https://www.linkedin.com/" class="fab fa-linkedin-in btn-linkedin me-2"></a></li>
									<li><a target="_blank" href="https://twitter.com/" class="fab fa-twitter btn-twitter"></a></li>
								</ul>
							</div> --}}
							<p class="text-center">Not registered?  
								<a class="btn-link text-primary" href="{{ url('register') }}">Register</a>
							</p>
						</form>
					</div>
				</div>
                <div class="col-xl-6 col-lg-6">
					<div class="pages-left h-100">
						<div class="login-content">
							<a href="index.html"><img src="{{asset('frontend/public/assets/images/logo-full.png')}}" class="mb-3" alt=""></a>
							
							<p>Your true value is determined by how much more you give in value than you take in payment. ...</p>
						</div>
						<div class="login-media text-center">
							<img src="{{asset('frontend/public/assets/images/login.png')}}" alt="">
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
<!--**********************************
	Scripts
***********************************-->
<!-- Required vendors -->
<script src="{{asset('frontend/public/assets/vendor/global/global.min.js')}}"></script>
<script src="{{asset('frontend/public/assets/js/custom.min.js')}}"></script>
<script src="{{asset('frontend/public/assets/js/dlabnav-init.js')}}"></script>
</body>

<!-- Mirrored from dompet.dexignlab.com/codeigniter/demo/page_login by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 01 Mar 2026 22:15:24 GMT -->
</html>