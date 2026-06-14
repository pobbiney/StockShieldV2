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
                        <li class="breadcrumb-item bi"><a href="#">Pick Up</a></li>
                        <li class="breadcrumb-item bi"><a href="#">My Pick Up List  </a></li>
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
                        <p class="h6">  My Pick Up List  </p>
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
                               
                                <table  class="table"  >
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Item Code</th>
                                            <th >Item Name</th>
                                            <th>UoM</th>
                                            <th>Batch Number</th>
                                           
                                            <th>Qty</th>
                                            <th>Unit Cost</th>
                                            <th>Requisition No</th>
                                            
                                            <th>Status</th>
                                            <th>Created By </th>
                                            <th>Approved By</th>
                                         
                                           
                                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                            
                                         
                                            @foreach($listrequest as $lists)
                                            <tr>
                                                <td>{{ $loop->iteration}}  </td>
                                                <td> {{ $lists->itemcode->item_code }}</td>
                                                <td>{{ $lists->itemname->name}}</td>
                                                <td>{{$lists->itemname->unitname->name }}</td>
                                                <td>{{$lists->batch_number}}</td>
                                               
                                                <td> {{$lists->qty}}</td>
                                                <td> {{$lists->amount}}</td>
                                                <td> {{$lists->requisition_no}}</td>
                                                <td> {{$lists->status}}</td>
                                                <td>{{ $lists->staffname->name}}</td>
                                                <td>{{ $lists->authorised->name}}</td>
                                                 
                                                     
                                            </tr> 
                                                
                                            
                                            @endforeach 
                                            
                                    </tbody>
                                </table>
                                 <a href="{{ route('requisition.print', Crypt::encrypt($lists->invoice_number)) }}" target="_" class="btn btn-primary"><i class="fa fa-print"></i> Print Invoice</a>
                             
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

 
 
 
  @include('requisition.reject-request-modal')
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