 <!-- page title -->
@php $pageName = "request"; $subpageName = "pending-stock"; @endphp

@extends('layouts.backendapp')
<style>
.select2-container .select2-selection--single {
    height: 45px !important;
    padding: 5px 10px;
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 45px !important;
}

.select2-container {
    width: 100% !important;
}
</style>
@section('content')
                  
<div class="container-fluid mt-3">
    <div class="bg-theme-1-subtle rounded px-3 py-3">
        <div class="row gx-3 align-items-center">
            <div class="col col-sm mb-2 mb-sm-0">
                <p class="h5">Stock Management</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item bi"><a href="#">Stock Management</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Issues</a></li>
                        <li class="breadcrumb-item bi"><a href="#">My Requisitions  </a></li>
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
                        <p class="h6">My Requisitions  </p>
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
             
          
          
                <div class="row gx-3 align-items-center">
                    <div class="row" style="margin-top:50px ">
                        <div class="col-md-12">
                            <div class="table-responsive">
                               <form enctype="multipart/form-data" method="POST" action="{{ route('issue-request-process') }}" >
                                @csrf
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>UoM</th>
                                            <th>Qty Requested</th>
                                            <th>Qty to Issue</th>
                                            <th>Requisition No</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($listrequest as $lists)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                                <input type="hidden" name="request_id[]" value="{{ $lists->id }}">
                                            </td>
                                            <td>{{ $lists->itemcode->item_code }}</td>
                                            <td>{{ $lists->itemname->name }}</td>
                                            <td>{{ $lists->itemname->unitname->name }}</td>
                                            <td><b>{{ $lists->qty_requested }}</b></td>
                                            <td>
                                                <input type="number" name="qty[{{ $lists->id }}]" value="{{ old('qty.' . $lists->id, $lists->qty_requested) }}" class="form-control" min="1">
                                            </td>
                                            <td>{{ $lists->requisition_no }}</td>
                                            <td>{{ $lists->status }}</td>
                                            <td>{{ $lists->staffname->name }}</td>
                                            <td>
                                                <a href="" class="btn btn-sm btn-danger showmodal" data-url="{{ route('request-item-id', $lists->id) }}" data-bs-toggle="modal" data-bs-target="#standardmodal">
                                                    <i class="fa fa-times"></i> Reject
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                              @if($listrequest->isNotEmpty())
                                    <button type="submit" class="btn btn-info" onclick="return confirm('Are you sure you want to issue item(s)?')">
                                        <i class="fa fa-check-circle"></i> Issue Item
                                    </button>
                                @endif
                               </form>    
                                
                          
                        </div>
                    </div>
                </div>
            </div>
         </div>
        
        </div>
    </div>
</div>
 
 
@endsection

 
 
 
@include('stock.reject-request-modal')
@section('scripts')
 
       <script>
         $(document).ready(function(){
   

    $('body').on('click', '.showmodal', function(){
        var userUrl = $(this).data('url');
        console.log('Fetching URL:', userUrl); // Debug: Check URL

        $.get(userUrl, function(data){
            console.log('Data received:', data); // Debug: See exact data structure
            
            // Check if elements exist before setting values
            console.log('itemID element:', $('#itemID').length);
            console.log('itemname element:', $('#itemname').length);
           
            
            
            // Set the values
            $('#itemID').val(data.id);
            $('#itemname').text(data.name);
            
           
            
            // Verify values were set
            console.log('Set itemname value:', $('#itemname').val());
            
            
            // Show the modal
            $('#standardmodal').modal('show');
        }).fail(function(error) {
            console.log('Error:', error);
        });
    });

    
});


</script>
    
@endsection