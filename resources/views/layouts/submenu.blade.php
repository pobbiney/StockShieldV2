<!-- page title -->
@php $pageName = "submenu"; $subpageName = "sub-menu"; @endphp

@extends('layouts.backendapp')

@section('content')
                  
<div class="container-fluid mt-3">
    <div class="bg-theme-1-subtle rounded px-3 py-3">
        <div class="row gx-3 align-items-center">
            <div class="col col-sm mb-2 mb-sm-0">
                <p class="h5">Main Navigation</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item bi"><a href="#">Main menu</a></li>
                       
                        <li class="breadcrumb-item bi"><a href="#">Sub menu</a></li>
                         
                    </ol>
                </nav>
            </div>
            <div class="col-auto ">
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
                            <p class="h6">{{ $parent->link_name }}    </p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-theme btn-square" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false">
                                <i class="bi bi-code-slash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <hr/>
                
                <div class="card-body">  
                    <div class="row gx-3 gx-lg-4 justify-content-center">
                            @if($list->count() > 0)

                            @foreach ($list as $listall)
                                <div class="col-12 col-md-3">
                                    <a href="{{ route($listall->link_url) }}">
                                        <div class="card adminuiux-card shadow-sm text-center mb-3 mb-lg-4 bg-l-gradient-light bg-theme-1">
                                            <div class="card-body">
                                                <i class="{{ $listall->link_image }} display-5 mb-3 d-block" style="color: white"></i>
                                                <h5 class="mb-1">{{ $listall->link_name }}</h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach

                        @else

                            <div class="col-12">
                                <div class="alert alert-danger text-center">
                                    No Sub Menu Found.
                                </div>
                            </div>

                        @endif
                        
                    </div>
                           
                </div>
                    
            </div>
    </div>
         
    
</div>
 
 @endsection

@section('scripts')
 

 
@endsection