<!-- page title -->

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
                            <p class="h6">{{$parent->link_name}}    </p>
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
                           @foreach($submenus as $submenu)
                                <div class="col-12 col-md-3">
                                    <a href="{{route($submenu->link_url)}}">
                                        <div class="card adminuiux-card shadow-sm text-center mb-3 mb-lg-4 bg-l-gradient-light bg-theme-1">
                                            <div class="card-body">
                                                <i class="{{ $submenu->link_image}} display-5 mb-3 d-block" style="color: white"></i>
                                                <h5 class="mb-1">{{ $submenu->link_name}}</h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                           
                         @endforeach
                         
                        
                    </div>
                           
                </div>
                    
            </div>
    </div>
         
    
</div>
 
 @endsection

@section('scripts')
 

 
@endsection