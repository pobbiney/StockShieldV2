<!-- page title -->
@php $pageName = "user"; $subpageName = "list_user"; @endphp

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
                        <li class="breadcrumb-item bi"><a href="#">List User Accounts</a></li>
                         
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
                            <p class="h6">List User Accounts</p>
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
                   
                    <form id="formValidationExamples" class="row g-6" action="{{route('user-management-get-accounts')}}" method="POST">
                     @csrf
                      <div class="form-group row">
                           <div class="col-md-6">
                               <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select id="category" name="category" class="form-select select2" data-allow-clear="true">
                                            <option value="">-- SELECT CATEGORY --</option>
                                            @foreach ($listCategory as $catItem)
                                                <option value="{{$catItem->cat_id}}">{{$catItem->cat_name}}</option> 
                                            @endforeach
                                        </select>
                                    
                                        @error('category')<small style="color: red">{{$message}}</small>@enderror
                                   </div>
                               </div>
                           </div>
                          <div class="col-md-6"  >
                             <div class="form-group mb-3 position-relative check-valid">
                                <button type="submit" name="submitButton" class="btn btn-success"><i class="fa fa-list"></i></button>
                             </div>
                          </div>
                      </div>
                     
                      @if ($userList != null)
                      <div class="table-responsive" style="margin-top: 30px">                                 
                            <table id="myTable" class="display"  >
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Category</th>
                                        <th>Action</th>
                                        <th>Creation Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userList as $userList)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><b>{{$userList->name}}</b></td>
                                            <td>{{$userList->email}}</td>
                                            <td>{{$userList->getUserCategory()}}</td>
                                            <td><a style="color: white;" href="{{route('user-management-edit-user-account',Crypt::encrypt($userList->id))}}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a></td>
                                            <td>{{$userList->created_at}}</td>
                                        </tr>   
                                    @endforeach
                                </tbody>
                            </table>
                      </div>
                      @else
                          <p class="alert alert-danger" align="center" style="margin-top: 30px">NO RECORDS FOUND</p>
                      @endif
                  
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