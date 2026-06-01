
@php $pageName = "dashboard"; $subpageName = ""; @endphp

@extends('layouts.backendapp')


@section('content')
 <!-- page title -->
              <div class="container-fluid py-3">
                <div class="row align-items-center page-title">
                  <div class="col col-sm mb-3 mb-sm-0 order-1">
                    <h5 class="mb-0">Personalization</h5>
                    <p class="text-secondary small">Customize color scheme and global structure</p>
                  </div>
                </div>
                <nav aria-label="breadcrumb" class="breadcrumb-theme mt-3 rounded">
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item bi"><a href="adminux-dashboard.html"><i class="bi bi-house-door me-2"></i> AdminUX</a></li>
                    <li class="breadcrumb-item bi active" aria-current="page">Personalization</li>
                  </ol>
                </nav>
              </div>

              <!-- content -->
              <div class="container mt-3" id="main-content">

                <div class="row mb-3 mb-lg-4">
                  <div class="col text-center pb-3">
                    <h2 class="text-gradient mb-1 d-inline-block">Ready to express yourself</h2>
                    <h5 class="text-secondary fw-normal">through customizing according to your branding?</h5>
                  </div>
                </div>

                <div class="row gx-3">
                  <div class="col-12 col-lg-6 mb-3 mb-lg-4 mb-xl-5 pt-1">
                    <div class="row gx-3 align-items-center mb-3 mb-lg-4">
                      <div class="col-auto">
                        <figure class="avatar avatar-160 coverimg rounded">
                          <img src="{{asset('backend/assets/img/modern-ai-image/image-10.jpg')}}" alt="">
                        </figure>
                      </div>
                      <div class="col">
                        <h2 class="fw-bold mb-1">Theme Color</h2>
                        <h4 class="opacity-75 fw-normal">Choose your desired theme color</h4>
                        <p class="small opacity-75">Theme colors in web design are pivotal as they influence user emotions, perceptions, and actions.Theme colors in web design are pivotal as they influence user emotions, perceptions, and actions. </p>
                      </div>
                    </div>
                    <div class="row gx-3 theme-select">
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                            <i class="bi bi-arrow-clockwise"></i>
                          </span>
                          <p class="fs-12 opacity-50">Default</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-blue">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-blue"></span>
                          <p class="fs-12 opacity-50">Blue</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-indigo">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-indigo"></span>
                          <p class="fs-12 opacity-50">Indigo</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-purple">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-purple"></span>
                          <p class="fs-12 opacity-50">Purple</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-pink">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-pink"></span>
                          <p class="fs-12 opacity-50">Pink</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-red">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-red"></span>
                          <p class="fs-12 opacity-50">Red</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-orange">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-orange"></span>
                          <p class="fs-12 opacity-50">Orange</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-yellow">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-yellow"></span>
                          <p class="fs-12 opacity-50">Yellow</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-green">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-green"></span>
                          <p class="fs-12 opacity-50">Green</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-teal">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-teal"></span>
                          <p class="fs-12 opacity-50">Teal</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-pista">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-pista"></span>
                          <p class="fs-12 opacity-50">Pista</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-cyan">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-cyan"></span>
                          <p class="fs-12 opacity-50">Cyan</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-grey">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-grey"></span>
                          <p class="fs-12 opacity-50">Grey</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-brown">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-brown"></span>
                          <p class="fs-12 opacity-50">Brown</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-chocolate">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-chocolate"></span>
                          <p class="fs-12 opacity-50">Choco</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center mb-2" data-title="theme-black">
                          <span class="avatar avatar-40 rounded-circle mb-2 bg-dark"></span>
                          <p class="fs-12 opacity-50">Black</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-6">
                    <div class="card adminuiux-card bg-theme-1-subtle mb-3 mb-lg-4 mb-xl-5">
                      <div class="card-body">
                        <h2 class="fw-bold mb-1">Background Color</h2>
                        <h4 class="opacity-75 fw-normal">Choose your desired theme color</h4>
                        <p class="small opacity-75 mb-3 mb-lg-4">The chosen background and text colors should have sufficient contrast to ensure readability for users with visual impairments. There are classes which will be useful like None, white, bg-r-gradient, bg-gradient-1 to 10 </p>
                        <div class="row gx-3 theme-background">
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-default">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-default"><i class="bi bi-arrow-clockwise"></i></span>
                              <p class="fs-12 opacity-50">None</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-white">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-white"></span>
                              <p class="fs-12 opacity-50">White</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2 active" data-title="bg-r-gradient">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-r-gradient"></span>
                              <p class="fs-12 opacity-50">Theme</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-gradient-1">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-1"></span>
                              <p class="fs-12 opacity-50">Grad-1</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-gradient-2">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-2"></span>
                              <p class="fs-12 opacity-50">Grad-2</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-gradient-3">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-3"></span>
                              <p class="fs-12 opacity-50">Grad-3</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-gradient-4">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-4"></span>
                              <p class="fs-12 opacity-50">Grad-4</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-gradient-5">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-5"></span>
                              <p class="fs-12 opacity-50">Grad-5</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-gradient-6">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-6"></span>
                              <p class="fs-12 opacity-50">Grad-6</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-gradient-7">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-7"></span>
                              <p class="fs-12 opacity-50">Grad-7</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-gradient-8">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-8"></span>
                              <p class="fs-12 opacity-50">Grad-8</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-gradient-9">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-9"></span>
                              <p class="fs-12 opacity-50">Grad-9</p>
                            </div>
                          </div>
                          <div class="col-auto">
                            <div class="gradient-box text-center mb-2" data-title="bg-gradient-10">
                              <span class="avatar avatar-40 rounded-circle mb-2 bg-gradient-10"></span>
                              <p class="fs-12 opacity-50">Grad-10</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- <div class="row gx-3 align-items-center">
                  <div class="col-12 col-lg-6 mb-3">
                    <h2 class="fw-bold mb-1">Background Image</h2>
                    <h4 class="opacity-75 fw-normal">Create depth with images & blur effect</h4>
                    <p class="small opacity-75 mb-3">The chosen background and text colors should have sufficient contrast to ensure readability for users with visual impairments. There are classes which will be useful like None, white, bg-r-gradient, bg-gradient-1 to 10 </p>
                  </div>
                  <div class="col-12 col-lg-12 mb-3 mb-lg-4 mb-xl-5">
                    <div class="row gx-3 theme-background">
                      <div class="col-auto mb-3">
                        <div class="select-box text-center" data-title="bg-none">
                          <span class="avatar avatar-80 rounded mb-2">
                            <i class="bi bi-arrow-clockwise"></i>
                          </span>
                          <p class="fs-12 opacity-50">None</p>
                        </div>
                      </div>
                      <div class="col-auto mb-3">
                        <div class="select-box text-center">
                          <span class="avatar avatar-80 rounded coverimg mb-2">
                            <img src="{{asset('backend/assets/img/background-image/backgorund-image-1.jpg')}}" alt="" class="w-100">
                          </span>
                          <p class="fs-12 opacity-50">Bg 1</p>
                        </div>
                      </div>
                      <div class="col-auto mb-3">
                        <div class="select-box text-center">
                          <span class="avatar avatar-80 rounded coverimg mb-2">
                            <img src="{{asset('backend/assets/img/background-image/backgorund-image-2.jpg')}}" alt="" class="w-100">
                          </span>
                          <p class="fs-12 opacity-50">Bg 2</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center">
                          <span class="avatar avatar-80 rounded coverimg mb-2">
                            <img src="{{asset('backend/assets/img/background-image/backgorund-image-3.jpg')}}" alt="" class="w-100">
                          </span>
                          <p class="fs-12 opacity-50">Bg 3</p>
                        </div>
                      </div>
                      <div class="col-auto mb-3">
                        <div class="select-box text-center">
                          <span class="avatar avatar-80 rounded coverimg">
                            <img src="{{asset('backend/assets/img/background-image/backgorund-image-4.jpg')}}" alt="" class="w-100">
                          </span>
                          <p class="fs-12 opacity-50">Bg 4</p>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="select-box text-center">
                          <span class="avatar avatar-80 rounded coverimg mb-2">
                            <img src="{{asset('backend/assets/img/background-image/backgorund-image-5.jpg')}}" alt="" class="w-100">
                          </span>
                          <p class="fs-12 opacity-50">Bg 5</p>
                        </div>
                      </div>
                      <div class="col-auto mb-3">
                        <div class="select-box text-center">
                          <span class="avatar avatar-80 rounded coverimg mb-2">
                            <img src="{{asset('backend/assets/img/background-image/backgorund-image-7.jpg')}}" alt="" class="w-100">
                          </span>
                          <p class="fs-12 opacity-50">Bg 6</p>
                        </div>
                      </div>
                      <div class="col-auto mb-3">
                        <div class="select-box text-center">
                          <span class="avatar avatar-80 rounded coverimg mb-2">
                            <img src="{{asset('backend/assets/img/background-image/backgorund-image-8.jpg')}}" alt="" class="w-100">
                          </span>
                          <p class="fs-12 opacity-50">Bg 7</p>
                        </div>
                      </div>
                      <div class="col-auto mb-3">
                        <div class="select-box text-center">
                          <span class="avatar avatar-80 rounded coverimg mb-2">
                            <img src="{{asset('backend/assets/img/background-image/backgorund-image-9.jpg')}}" alt="" class="w-100">
                          </span>
                          <p class="fs-12 opacity-50">Bg 8</p>
                        </div>
                      </div>
                      <div class="col-auto mb-3">
                        <div class="select-box text-center">
                          <span class="avatar avatar-80 rounded coverimg mb-2">
                            <img src="{{asset('backend/assets/img/background-image/backgorund-image-10.jpg')}}" alt="" class="w-100">
                          </span>
                          <p class="fs-12 opacity-50">Bg 9</p>
                        </div>
                      </div>
                      <div class="col-auto mb-3">
                        <div class="select-box text-center">
                          <span class="avatar avatar-80 rounded coverimg mb-2">
                            <img src="{{asset('backend/assets/img/background-image/backgorund-image-11.jpg')}}" alt="" class="w-100">
                          </span>
                          <p class="fs-12 opacity-50">Bg 10</p>
                        </div>
                      </div>
                      <div class="col-auto mb-3">
                        <div class="select-box text-center">
                          <span class="avatar avatar-80 rounded coverimg mb-2">
                            <img src="{{asset('backend/assets/img/background-image/backgorund-image-12.jpg')}}" alt="" class="w-100">
                          </span>
                          <p class="fs-12 opacity-50">Bg 11</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> --}}

                <div class="row gx-3 gx-lg-5 align-items-center">
                  <div class="col-12 col-lg-6">
                    <div class="row gx-3 align-items-center">
                      <div class="col-12 col-lg-12 mb-3">
                        <h2 class="fw-bold mb-1">Sidebar </h2>
                        <h4 class="opacity-75 fw-normal">Experience with Fill color & Style</h4>
                        <p class="small opacity-75 mb-3">Take control of your dashboard! Customize sidebar color and layout to match your unique style and preferences.</p>
                      </div>
                      <div class="col-12 col-lg-12 mb-3 mb-lg-4 mb-xl-5">
                        <div class="row gx-3">
                          <div class="col">
                            <h6 class="fw-normal mb-3">Color Fill</h6>
                            <div class="row gx-3" id="personalise-preview-sidebar">
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-fill-none">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                                    <i class="bi bi-arrow-clockwise"></i>
                                  </span>
                                  <p class="small">None</p>
                                </div>
                              </div>
                              <div class="col-auto" data-bs-img='dark'>
                                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-fill-white">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-black"></span>
                                  <p class="small">Black</p>
                                </div>
                              </div>
                              <div class="col-auto" data-bs-img='light'>
                                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-fill-white">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-white"></span>
                                  <p class="small">White</p>
                                </div>
                              </div>
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-fill-bg">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-body-1"></span>
                                  <p class="small">BgTheme</p>
                                </div>
                              </div>
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-fill-theme">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-theme-1"></span>
                                  <p class="small">Theme</p>
                                </div>
                              </div>
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-fill-accent">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-theme-accent-1"></span>
                                  <p class="small">Accent</p>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-auto">
                            <h6 class="fw-normal mb-3">Layout Style</h6>
                            <div class="row gx-3 sidebar-layout">
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-standard">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                                    <i class="bi bi-arrow-clockwise"></i>
                                  </span>
                                  <p class="small">Standard</p>
                                </div>
                              </div>
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-iconic">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                                    <i class="bi bi-bezier h4"></i>
                                  </span>
                                  <p class="small">Iconic</p>
                                </div>
                              </div>
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-boxed">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                                    <i class="bi bi-box h5"></i>
                                  </span>
                                  <p class="small">Boxed</p>
                                </div>
                              </div>
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-sidebar-boxed adminuiux-sidebar-iconic">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                                    <i class="bi bi-bounding-box h5"></i>
                                  </span>
                                  <p class="small">IconicBox</p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-6">
                    <div class="row gx-3 align-items-center">
                      <div class="col-12 col-lg-12 mb-3">
                        <h2 class="fw-bold mb-1">Header </h2>
                        <h4 class="opacity-75 fw-normal">Make stylish with Fill color & Style</h4>
                        <p class="small opacity-75 mb-3">Enhance your workflow with a personalized dashboard. Easily customize header color and layout to create a more efficient and enjoyable user experience.</p>
                      </div>
                      <div class="col-12 col-lg-12 mb-3 mb-lg-4 mb-xl-5">
                        <div class="row gx-3">
                          <div class="col">
                            <h6 class="fw-normal mb-3">Color Fill</h6>
                            <div class="row gx-3" id="personalise-preview-header">
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-header-transparent">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                                    <i class="bi bi-arrow-clockwise"></i>
                                  </span>
                                  <p class="small">None</p>
                                </div>
                              </div>
                              <div class="col-auto" data-bs-img='dark'>
                                <div class="select-box text-center mb-2" data-title="adminuiux-header-fill-white">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-black"></span>
                                  <p class="small">Black</p>
                                </div>
                              </div>
                              <div class="col-auto" data-bs-img='light'>
                                <div class="select-box text-center mb-2" data-title="adminuiux-header-fill-white">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-white"></span>
                                  <p class="small">White</p>
                                </div>
                              </div>
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-header-fill-bg">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-body-1"></span>
                                  <p class="small">BgTheme</p>
                                </div>
                              </div>
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-header-fill-theme">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-theme-1"></span>
                                  <p class="small">Theme</p>
                                </div>
                              </div>
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-header-fill-accent">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-theme-accent-1"></span>
                                  <p class="small">Accent</p>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-auto">
                            <h6 class="fw-normal mb-3">Layout Style</h6>
                            <div class="row gx-3" id="header-layout">
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-header-standard">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                                    <i class="bi bi-arrow-clockwise"></i>
                                  </span>
                                  <p class="small">None</p>
                                </div>
                              </div>
                              <div class="col-auto">
                                <div class="select-box text-center mb-2" data-title="adminuiux-header-boxed">
                                  <span class="avatar avatar-40 rounded-circle mb-2 bg-default">
                                    <i class="bi bi-box h5"></i>
                                  </span>
                                  <p class="small">Boxed</p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row gx-3 gx-lg-5">
                  <div class="col-12 col-md-6 mb-4 mb-xl-5">

                    <h2 class="fw-bold mb-1">Light & Dark Mode</h2>
                    <h4 class="opacity-75 fw-normal mb-3 mb-lg-4">There is elegance in the darkness</h4>
                  
                    <!-- light-dark layout -->
                    <div class="row align-items-center no-rtl">
                      <div class="col-auto ">Light</div>
                      <div class="col-auto px-0">
                        <div class="form-check form-switch sunmoon">
                          <input class="form-check-input" type="checkbox" role="switch" id="btn-layout-modes-dark">
                          <label class="form-check-label" for="btn-layout-modes-dark">
                            <i class="bi bi-brightness-low sun"></i>
                            <i class="bi bi-moon-stars moon"></i>
                          </label>
                        </div>
                      </div>
                      <div class="col-auto">Dark</div>
                    </div>
                  </div>
                  <div class="col-12 col-md-6 mb-4 mb-xl-5">

                    <h2 class="fw-bold mb-1">Text Direction</h2>
                    <h4 class="opacity-75 fw-normal mb-3 mb-lg-4">Give respect to different languages</h4>
                    
                    <!-- rtl -->
                    <div class="row align-items-center no-rtl">
                      <!-- avoid rtl direction in some cases when rtl selected by adding "no-rtl" class -->
                      <div class="col-auto ">LTR</div>
                      <div class="col-auto px-0">
                        <div class="form-check form-switch rtlcheck">
                          <input class="form-check-input" type="checkbox" role="switch" id="btn-layout-dir-rtl">
                          <label class="form-check-label" for="btn-layout-dir-rtl">
                            <i class="bi bi-justify-left ltrjustify"></i>
                            <i class="bi bi-justify-right rtljustify"></i>
                          </label>
                        </div>
                      </div>
                      <div class="col-auto">RTL</div>
                    </div>
                  </div>
                </div>

              </div>

              
              </div>
              
               @endsection

@section('scripts')
    
@endsection