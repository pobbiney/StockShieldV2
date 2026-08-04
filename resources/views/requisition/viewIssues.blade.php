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
                        <li class="breadcrumb-item bi"><a href="#">Requsition</a></li>
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
                                <form  id="approveForm" method="POST" action="{{ route('approveIssue-process') }}"   >
                            @csrf
                          <div class="table-responsive">
                            
                                <table  class="table table-bordered"  >
                                    <thead>
                                        <tr class="bg-l-gradient-light theme-green">
                                          
                                            <th>ID</th>
                                            
                                            <th>Item Code</th>
                                            <th >Item Name</th>
                                            <th>Batch Number</th>
                                            <th>Current Stock Balance</th>
                                            <th>Qty Requested</th>
                                            <th>Qty</th>
                                            <th>Cost</th>
                                            <th>Requisition No  </th>
                                            <th>Issue To</th>
                                            <th>Action</th>
                                              
                                            
                                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                            
                                            
                                            @foreach($listissues as $lists)
                                            <tr>
                                                  
                                                <td>{{ $loop->iteration}}  <input type="hidden"
                                                    name="issue_id[]"
                                                    value="{{ $lists->id }}"></td>
                                                 
                                                <td> {{ $lists->itemcode->item_code }}</td>
                                                <td>{{ $lists->itemname->name}}</td>
                                                <td>{{$lists->batch_number}}</td>
                                                <td><b>{{ $itembalance[$lists->batch_number]->qty ?? 'N/A' }}</b></td>
                                                  <td>
                                                   @if($lists->qty_requested == NULL)
                                                   <b>{{ $lists->qty}}</b>
                                                   @else
                                                   <b>$lists->qty_requested</b>
                                                   @endif
                                                </td>
                                                <td style="width: 120px"><input type="number" name="qty[{{ $lists->id }}]" class="form-control" value="{{$lists->qty}}"/></td>
                                                <td> {{ number_format($lists->amount,2)}}</td>
                                                <td> {{$lists->requisition_no}}</td>
                                                <td> {{$lists->storename->name}}</td>
                                                <td><a href="" class="btn btn-sm btn-danger showmodal"  data-url="{{ route('issue-item-id',$lists->id)  }}" data-bs-toggle="modal" data-bs-target="#standardmodal" ><i class="fa fa-times"></i> Reject</a></td>
                                                
                                            </tr>
                                                
                                            
                                            @endforeach
                                            
                                    </tbody>
                                </table>
                             
                                @if($listissues->isNotEmpty())
                                    <input type="hidden" name="store_id" value="{{ $listissues->first()->issue_to }}"/>
                                    
                                    <button type="submit" class="btn btn-info" 
                                        onclick="return confirm('Are you sure you want to approve all Issues?')">
                                        <i class="fa fa-check-circle"></i> Approve All
                                    </button>
                                @endif  
                            </div>
                             
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