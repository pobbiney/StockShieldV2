<!-- page title -->
@php $pageName = "stock"; $subpageName = "stock-approval"; @endphp

@extends('layouts.backendapp')

@section('content')
                  
<div class="container-fluid mt-3">
    <div class="bg-theme-1-subtle rounded px-3 py-3">
        <div class="row gx-3 align-items-center">
            <div class="col col-sm mb-2 mb-sm-0">
                <p class="h5">Stock Management</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item bi"><a href="#">Stock Management</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Stock</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Return Item</a></li>
                         
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
                             <p class="h6">Return Item </p>
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
                   
                   <div class="row">
                      <div class="form-group mb-3 position-relative check-valid">
                            <div class="form-floating">
                                <input type="text" class="form-control"  id="tableSearch"  placeholder="Search By Store">
                                <label>Search Item Here</label>
                                
                            </div>
                        </div>

                          <div class="table-responsive" style="margin-top: 50px">
                            <h4>Return Items Awaiting Approval </h4>
                               
                                    <table  class="display" id="myTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Item Code</th>
                                                <th >Item Name</th>
                                                <th>Batch Number</th>
                                                <th>Expiry Date</th>
                                                <th>Qty Returned</th>
                                                <th>Cost</th>
                                                <th>Purchase Order</th>
                                                <th>Vendor</th>
                                                <th>Received On</th>
                                                <th>Comment</th>
                                                <th>Action</th>
                                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                                
                                                
                                                @foreach($listItem as $lists)
                                                <tr>
                                                    <td>{{ $loop->iteration}}</td>
                                                    <td> {{ $lists->itemcode->item_code }}</td>
                                                    <td>{{ $lists->itemcode->name}}</td>
                                                    <td>{{$lists->batch_number}}</td>
                                                    <td>{{$lists->stockdetails->expiry_date}}</td>
                                                    <td>{{$lists->quantity}}</td>
                                                    <td>{{$lists->stockdetails->amount}}</td>
                                                    <td>{{$lists->stockdetails->purchase_order}}</td>
                                                    <td>{{$lists->stockdetails->supname->company}}</td>
                                                    <td>{{$lists->stockdetails->created_at}}</td>
                                                    <td>{{$lists->manager_comment ??  $lists->hod_comment}}</td>
                                                    <td><a class="btn btn-sm btn-success showmodal"   data-url="{{ route('return-item-approval-id',$lists->id)  }}" data-bs-toggle="modal" data-bs-target="#standardmodal" ><i class="fa fa-check"></i> Approve</a></td>
                                                    
                                                        
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
@include('requisition.return-item-approval-modal')
 @endsection

@section('scripts')
    <script>
        document.querySelector('input[type="text"]').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#myTable tbody tr');

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(searchValue) ? '' : 'none';
        });
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
            console.log('itemID element:', $('#itemID').length);
         
            console.log('itemqty element:', $('#itemqty').length);
            console.log('itembatch element:', $('#itembatch').length);
           
            
            
            // Set the values
            $('#itemID').val(data.id);
           
            $('#itemqty').val(data.quantity);
            $('#itembatch').val(data.batch_number);
            
           
            
            // Verify values were set
           
             console.log('Set itemqty value:', $('#itemqty').val());
             console.log('Set itembatch value:', $('#itembatch').val());
            
            
            // Show the modal
            $('#standardmodal').modal('show');
        }).fail(function(error) {
            console.log('Error:', error);
        });
    });

    
});


</script>
@endsection