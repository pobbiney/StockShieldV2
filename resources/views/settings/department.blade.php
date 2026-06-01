<!-- page title -->
@php $pageName = "setup"; $subpageName = "department"; @endphp

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
                        <li class="breadcrumb-item bi"><a href="#">Department</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Add Department </a></li>
                         
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
                             <p class="h6">Add New Department  </p>
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
                   
                    <form enctype="multipart/form-data" action="{{ route('add-department-process') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4">
                               <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="text" name="name" class="form-control"   placeholder="Enter Loan Type">
                                        <label>Name</label>
                                        @error('name') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select class="form-control" name="status">
                                            <option value="" selected disabled>--Choose Option--</option>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                        <label>Status</label>
                                        @error('status') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-success">Add Department </button>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <table  class="display" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th  >Name</th>
                                            <th >Status</th>
                                            <th  >Action</th>
                                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @php
                                            $i=1;
                                            @endphp
                                            @if($list)
                                            @foreach($list as $lists)
                                            <tr>
                                                <td>{{ $i}}</td>
                                                <td>{{ $lists->name}}</td>
                                                <td>{{$lists->status}}</td>
                                                 <td><a class="btn btn-sm btn-success showmodal"   data-url="{{ route('department-id',$lists->id)  }}"  data-bs-toggle="modal" data-bs-target="#standardmodal"   data-bs-placement="top" data-bs-custom-class="adminuiux-success-tooltip" data-bs-title="Click to edit loan type"><i class="fa fa-edit"></i></a></td>
                                            </tr>
                                             @php
                                            $i++;
                                            @endphp
                                            @endforeach
                                            @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>

                </div>
                    
            </div>
    </div>
         
    
</div>
  @include('settings.edit-department-modal')
 @endsection

@section('scripts')
 
       <script>
         $(document).ready(function(){
    $('#myTable').DataTable();

    $('body').on('click', '.showmodal', function(){
        var userUrl = $(this).data('url');
        console.log('Fetching URL:', userUrl); // Debug: Check URL

        $.get(userUrl, function(data){
            console.log('Data received:', data); // Debug: See exact data structure
            
            // Check if elements exist before setting values
            console.log('docID element:', $('#docID').length);
            console.log('docname element:', $('#docname').length);
            console.log('statusname element:', $('#statusname').length);
            
            // Set the values
            $('#docID').val(data.id);
            $('#docname').val(data.name);
            $('#statusname').val(data.status);
            
            // Verify values were set
            console.log('Set docname value:', $('#docname').val());
            console.log('Set statusname value:', $('#statusname').val());
            
            // Show the modal
            $('#standardmodal').modal('show');
        }).fail(function(error) {
            console.log('Error:', error);
        });
    });

    
});


</script>   
@endsection