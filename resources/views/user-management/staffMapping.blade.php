<!-- page title -->
@php $pageName = "user"; $subpageName = "map"; @endphp

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
                        <li class="breadcrumb-item bi"><a href="#">Map staff to Department</a></li>
                         
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
                            <p class="h6">Map staff to Department</p>
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
                   
                    <form id="formValidationExamples" class="row g-6" action="{{route('map-department-process')}}" method="POST">
                     @csrf
                     
                      <div class="form-group row">
                           <div class="col-md-6">
                               <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select  name="staff_id" class="form-select select2" data-allow-clear="true">
                                            <option value="" selected disabled>-- SELECT STAFF --</option>
                                            @foreach ($liststaff as $list)
                                                <option value="{{ $list->staff_id }}"
                                                    {{ session('mapped_staff_id') == $list->staff_id ? 'selected' : '' }}>
                                                    {{ $list->surname.' '.$list->firstname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    
                                        @error('staff_id')<small style="color: red">{{$message}}</small>@enderror
                                   </div>
                               </div>
                           </div>
                          
                      </div> 
                      <div class="row" >
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 50px">#</th>
                                        <th scope="col" style="width: 120px"><input class="form-check-input" type="checkbox"  id="selectAll"> Select All</th>
                                        <th scope="col">Department</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $mappedDepts = session('mapped_departments', []) @endphp
                                    @foreach ($listdept as $listde)
                                        
                                    
                                    <tr>
                                        <td>{{ $loop->iteration}}</td>
                                        <td><input class="form-check-input" type="checkbox" 
                                        name="department_id[]" 
                                        value="{{ $listde->id }}"
                                        {{ in_array($listde->id, $mappedDepts) ? 'checked' : '' }}
                                         ></td>
                                        <td>{{ $listde->name}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-success">Map Staff</button>
                        </div>

                     
                      
                  </form>
                     
                </div>
                    
            </div>
    </div>
         
    
</div>
 
 @endsection

@section('scripts')

<script>
     document.getElementById('selectAll').addEventListener('click', function () {
        let checkboxes = document.querySelectorAll('input[name="department_id[]"]');
        
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = document.getElementById('selectAll').checked;
        });
    });
    </script>
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

 
@endsection