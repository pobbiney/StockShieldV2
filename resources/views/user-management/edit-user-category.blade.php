<!-- page title -->
@php $pageName = "user"; $subpageName = "user_cat"; @endphp

@extends('layouts.backendapp')

@section('content')
                  
<div class="container-fluid mt-3">
    <div class="bg-theme-1-subtle rounded px-3 py-3">
        <div class="row gx-3 align-items-center">
            <div class="col col-sm mb-2 mb-sm-0">
                <p class="h5">Settings</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item bi"><a href="#">Settings</a></li>
                        <li class="breadcrumb-item bi"><a href="#">User Management</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Update  User Category</a></li>
                         
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
                            <p class="h6">Update   User Category</p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-theme btn-square" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false">
                                <i class="bi bi-code-slash"></i>
                            </button>
                        </div><br><br/>
                        <hr/>
                    </div>
                </div>
                
                <div class="card-body">
                   
                  <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('user-management-add-category-edit-process',$id) }}" method="POST">
                                @csrf
                              <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="category_name" value="{{ $categoryData->cat_name }}">
                                        <label>Category Name</label>
                                        @error('category_name') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                  
                                </div>
                                <div class="form-group position-relative check-valid">
                                    <div class="form-floating">
                                        <select class="form-select" name="status">
                                            <option>-- Select --</option>
                                            <option value="Active" @if ($categoryData->status == "Active") selected @endif>Active</option>
                                            <option value="Inactive" @if ($categoryData->status == "Inactive") selected @endif>Inactive</option>
                                        </select>
                                         <label>Status</label>
                                        @error('status') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                   
                                </div>
                                <div class="text-end" style="margin-top:20px">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-xl-6 d-flex">
                            <div class="card flex-fill">
                                <div class="card-header">
                                    <h5 class="card-title">List Category</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive no-search">
										<table id="myTable" class="display ">
											<thead class="thead-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                $i=1;
                                                @endphp
                                                @foreach ($listCategory as $catItem)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td>{{$catItem->cat_name}}</td>
                                                    <td><a style="color:white;" href="{{ route('user-management-add-category-edit', Crypt::encrypt($catItem->cat_id)) }}" class="btn btn-success"><i class="fa fa-edit"></i></a></td>
                                                </tr>
                                                @php
                                                $i++;
                                                @endphp
                                                @endforeach
                                                
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
               </div>
    </div>
         
    
</div>
 
 @endsection

@section('scripts')
 <script>
 
@if(session('success_message'))
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: "{{ session('success_message') }}",
    showConfirmButton: true,
    timer: 2000
});
@endif
@if(session('error_message'))
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: "{{ session('error_message') }}",
     showConfirmButton: true,
    timer: 2000
});
@endif
</script>

 

 
<script>
     $(document).ready(function () {
      $('#myTable').DataTable();
    });
    </script>
@endsection