<!-- page title -->
@php $pageName = "staff"; $subpageName = "supplier"; @endphp

@extends('layouts.backendapp')

@section('content')
                  
<div class="container-fluid mt-3">
    <div class="bg-theme-1-subtle rounded px-3 py-3">
        <div class="row gx-3 align-items-center">
            <div class="col col-sm mb-2 mb-sm-0">
                <p class="h5">User Management</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item bi"><a href="#">User Management</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Staff</a></li>
                        <li class="breadcrumb-item bi"><a href="#">List All Staff</a></li>
                         
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
                            <p class="h6">Add New Supplier  </p>
                        </div>
                        <div class="col-auto">
                            
                            
                        </div>
                        
                    </div>
                    
                </div>
                <hr/>
                
                <div class="card-body">
                   
                   
                    <div class="table-responsive" style="margin-top: 15px">
                          <div class="col-lg-12">
                                <h4>List All Staff</h4>
                                <table  class="display" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Staff Name</th>
                                            <th>Gender  </th>
                                            <th>Staff ID  </th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Position</th>
                                            <th>Action</th>
                                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($liststaff as $staff)
                                            
                                        
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $staff->surname.' '.$staff->firstname}}</td>
                                            <td>{{ $staff->gender}}</td>
                                            <td>{{ $staff->employee_id}}</td>
                                            <td>{{ $staff->contact_num}}</td>
                                            <td>{{ $staff->personal_email}}</td>
                                            <td>{{ $staff->position}}</td>
                                            <td><a class="btn btn-sm btn-success  "  href="{{ route('edit-staff', Crypt::encrypt($staff->staff_id)) }}" ><i class="fa fa-edit"></i></a></td>
                                        </tr>   
                                        @endforeach  
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                    
            </div>
    </div>
         
    
</div>
 
 @endsection

@section('scripts')
 
<script>
     $(document).ready(function () {
      $('#myTable').DataTable();
    });
    </script>

    <script>
         $(document).ready(function(){
     

    $('body').on('click', '.showmodal', function(){
        var userUrl = $(this).data('url');
        console.log('Fetching URL:', userUrl); // Debug: Check URL

        $.get(userUrl, function(data){
            console.log('Data received:', data); // Debug: See exact data structure
            
            // Check if elements exist before setting values
            console.log('supplierID element:', $('#supplierID').length);
            console.log('supcode element:', $('#supcode').length);
            console.log('sup element:', $('#sup').length);
            console.log('supphone element:', $('#supphone').length);
            console.log('supemail element:', $('#supemail').length);
            console.log('supcom element:', $('#supcom').length);
            console.log('supcity element:', $('#supcity').length);
            console.log('suptin element:', $('#suptin').length);
            console.log('supreg element:', $('#supreg').length);
            console.log('supaddress element:', $('#supaddress').length);
            console.log('supstatus element:', $('#supstatus').length);
            
            
            // Set the values
            $('#supplierID').val(data.id);
            $('#supcode').val(data.code);
            $('#sup').val(data.supplier);
            $('#supphone').val(data.phone);
            $('#supemail').val(data.email);
            $('#supcom').val(data.company);
            $('#supcity').val(data.city);
            $('#suptin').val(data.tin_number);
            $('#supreg').val(data.registration_number);
            $('#supaddress').val(data.address);
            $('#supstatus').val(data.status);
             
            
            // Verify values were set
            console.log('Set supcode value:', $('#supcode').val());
            console.log('Set sup value:', $('#sup').val());
            console.log('Set supphone value:', $('#supphone').val());
            console.log('Set supemail value:', $('#supemail').val());
            console.log('Set supcom value:', $('#supcom').val());
            console.log('Set supcity value:', $('#supcity').val());
            console.log('Set suptin value:', $('#suptin').val());
            console.log('Set supreg value:', $('#supreg').val());
            console.log('Set supaddress value:', $('#supaddress').val());
            console.log('Set supstatus value:', $('#supstatus').val());
            
            // Show the modal
            $('#xlmodalE').modal('show');
        }).fail(function(error) {
            console.log('Error:', error);
        });
    });

    
});


</script>
@endsection