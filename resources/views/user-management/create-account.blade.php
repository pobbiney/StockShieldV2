<!-- page title -->
@php $pageName = "user"; $subpageName = "create_account"; @endphp

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
                        <li class="breadcrumb-item bi"><a href="#">Create New User Account</a></li>
                         
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
                            <p class="h6">Create New User Account</p>
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
                   
                    <form id="formValidationExamples" method="POST" action="{{route('create-account-process')}}">
                        @csrf
                         <div class="form-group row">
                    
                        <div class="col-md-6">
                           <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select id="users" name="users" class="form-select select2" data-allow-clear="true">
                                            <option value="">-- SELECT USER --</option>
                                            @foreach ($listStaff as $staff)
                                                <option value="{{$staff->staff_id}}">{{$staff->title}} {{$staff->firstname}} {{$staff->surname}}</option>
                                            @endforeach 
                                        </select>
                                        <label> Name</label>
                                    </div>
                           </div>
                            @error('users')<small style="color: red">{{$message}}</small>@enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select id="category" name="category" class="form-select select2" data-allow-clear="true">
                                            <option value="">-- SELECT CATEGORY --</option>
                                            @foreach ($userCategoryList as $catItem)
                                            <option value="{{$catItem->cat_id}}">{{$catItem->cat_name}}</option> 
                                            @endforeach
                                            
                                        </select>
                                        <label> Category</label>
                                        @error('category')<small style="color: red">{{$message}}</small>@enderror
                                    </div>
                            </div>
                        </div>
                        </div>
                        <div class="form-group row" style="margin-top: 20px">
                    
                        <div class="col-md-6">
                            <div class="form-password-toggle">
                               <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        
                                            <input @readonly(true)  class="form-control" type="text" id="email" name="email" placeholder="" />
                                            <label>User Email</label>
                                    </div>
                               </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-password-toggle">
                               <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                      <input class="form-control" type="password" id="password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="multicol-confirm-password2" />
                                       @error('password')<small style="color: red">{{$message}}</small>@enderror
                                       <label>Password</label>
                                   </div>
                               </div>
                               
                            </div>
                        </div>
                    </div>
                    <input  class="form-control" type="hidden" id="phone" name="phone"  />
                        <!-- Personal Info -->
                    <br>
                        <div class="text-end">
                            <button type="submit" name="submitButton" class="btn btn-success">Create Account</button>
                        </div>
                    </form>
                </div>
                    
            </div>
    </div>
         
    
</div>
 
 @endsection

@section('scripts')
 <script>
     $(document).on('change','#users',function(e){
        e.preventDefault();

        let id = $(this).val();

        $.ajax({
            type:'POST',
            url:'get-user-email-process',
            data:{
                "_token": "{{ csrf_token() }}",
                'users': id
            },
            success:function(data){

                $('#email').val(data.personal_email);
                 $('#phone').val(data.contact_num);
            }
        });

        
    });


    
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