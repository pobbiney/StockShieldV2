 <!-- page title -->
@php $pageName = "reports"; $subpageName = "item"; @endphp

@extends('layouts.backendapp')
 
@section('content')
                  
<div class="container-fluid mt-3">
    <div class="bg-theme-1-subtle rounded px-3 py-3">
        <div class="row gx-3 align-items-center">
            <div class="col col-sm mb-2 mb-sm-0">
                <p class="h5">Report Management</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item bi"><a href="#">Report Management</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Report</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Issued Items Report</a></li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12">
    <div class="container mt-4">
        <div class="card adminuiux-card mb-4">
            <div class="card-header">
                <div class="row gx-3 gx-lg-4 align-items-center">
                    <div class="col">
                        <p class="h6">Issued Items Report</p>
                    </div>
                    <div class="col-auto">
                       
                    </div>
                </div>
            </div>
            <hr/>
            
            <div class="card-body">
                  <div class="row gx-3 gx-lg-4">
                     
                        <div class="col-md-3">
                            <div class="card adminuiux-card shadow-sm bg-theme-1 mb-3 mb-lg-4">
                                <div class="card-body">
                                    <div class="row gx-3 align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar avatar-60 position-relative">
                                                <div id="circleprogresswhite"></div>
                                                <div class="avatar avatar-40 h5 position-absolute start-50 top-50 translate-middle bg-white-opacity text-white rounded-circle">
                                                    <i class="bi bi-bar-chart-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            
                                                <a href="{{ route('searchIssueItemByStore') }}" style="color: white;text-decoration:none"><h6>Search By Store</h6></a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card adminuiux-card shadow-sm bg-theme-1 mb-3 mb-lg-4">
                                <div class="card-body">
                                    <div class="row gx-3 align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar avatar-60 position-relative">
                                                <div id="circleprogresswhite"></div>
                                                <div class="avatar avatar-40 h5 position-absolute start-50 top-50 translate-middle bg-white-opacity text-white rounded-circle">
                                                    <i class="bi bi-boxes"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            
                                                <a href="{{ route('searchByIssueItem') }}" style="color: white;text-decoration:none"><h6>Search By Item</h6></a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="card adminuiux-card shadow-sm bg-theme-1 mb-3 mb-lg-4">
                                <div class="card-body">
                                    <div class="row gx-3 align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar avatar-60 position-relative">
                                                <div id="circleprogresswhite"></div>
                                                <div class="avatar avatar-40 h5 position-absolute start-50 top-50 translate-middle bg-white-opacity text-white rounded-circle">
                                                    <i class="bi bi-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            
                                                <a href="{{ route('searchByIssuedDate') }}" style="color: white;text-decoration:none"><h6>Search By Date</h6></a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>   
                         <div class="col-md-3">
                            <div class="card adminuiux-card shadow-sm bg-theme-1 mb-3 mb-lg-4">
                                <div class="card-body">
                                    <div class="row gx-3 align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar avatar-60 position-relative">
                                                <div id="circleprogresswhite"></div>
                                                <div class="avatar avatar-40 h5 position-absolute start-50 top-50 translate-middle bg-white-opacity text-white rounded-circle">
                                                    <i class="bi bi-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            
                                                <a href="{{ route('searchIssuedItemByDateIntev') }}" style="color: white;text-decoration:none"><h6>Search Item By Date Intervals</h6></a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>   
                    
                </div>
                <div class="row">
                     <p class="h6">Items Issued to Departments </p>
                     <hr/>
                </div>
            </div>
        
        </div>
    </div>
</div>
  
 
@endsection

@section('scripts')
 
 
@endsection