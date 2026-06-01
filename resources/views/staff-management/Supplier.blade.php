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
                        <li class="breadcrumb-item bi"><a href="#">Supplier</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Add New Supplier</a></li>
                         
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
                            
                            <a  class="btn btn-theme  "   data-bs-toggle="modal" data-bs-target="#xlmodal"  >
                               <i class="bi bi-person"></i> Add New Supplier 
                            </a>
                        </div>
                        
                    </div>
                    
                </div>
                <hr/>
                
                <div class="card-body">
                   
                   
                    <div class="table-responsive" style="margin-top: 15px">
                          <div class="col-lg-12">
                                <h4>List All Suppliers</h4>
                                <table  class="display" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Supplier's Code</th>
                                            <th>Company Name</th>
                                            <th>Contact Person</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Reg. No</th>
                                            <th>Action</th>
                                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($list as $listsupplier)
                                            
                                        
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $listsupplier->code}}</td>
                                            <td>{{ $listsupplier->company}}</td>
                                            <td>{{ $listsupplier->supplier}}</td>
                                            <td>{{ $listsupplier->phone}}</td>
                                            <td>{{ $listsupplier->email}}</td>
                                            <td>{{ $listsupplier->registration_number}}</td>
                                            <td><a class="btn btn-sm btn-success showmodal"   data-url="{{ route('supplier-id',$listsupplier->id)  }}"  data-bs-toggle="modal" data-bs-target="#xlmodalE"    ><i class="fa fa-edit"></i></a></td>
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
  @include('staff-management.add-supplier-modal')
  @include('staff-management.edit-supplier-modal')
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