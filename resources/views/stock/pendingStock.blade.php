 <!-- page title -->
@php $pageName = "stock"; $subpageName = "pending-stock"; @endphp

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
                        <li class="breadcrumb-item bi"><a href="#">Stock</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Pending Stock</a></li>
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
                        <p class="h6">Pending Stock</p>
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
                               
                                <table  class="display" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Item Code</th>
                                            <th >Item Name</th>
                                            <th>Batch Number</th>
                                            <th>Expiry Date</th>
                                            <th>Qty</th>
                                            <th>Cost</th>
                                            <th>Purchase Order</th>
                                            <th>Supplier</th>
                                            <th>Action</th>
                                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                            
                                            
                                            @foreach($liststock as $lists)
                                            <tr>
                                                <td>{{ $loop->iteration}}</td>
                                                <td> {{ $lists->itemcode->item_code }}</td>
                                                <td>{{ $lists->itemname->name}}</td>
                                                <td>{{$lists->batch_number}}</td>
                                                <td>{{$lists->expiry_date}}</td>
                                                <td>{{$lists->qty}}</td>
                                                <td> {{$lists->amount}}</td>
                                                <td> {{$lists->purchase_order}}</td>
                                                <td> {{$lists->supname->supplier}}</td>
                                                    <td><a class="btn btn-sm btn-danger delete-btn"  onclick="return confirm( 'Are you sure you want to delete this Item?')" href=" {{ url('stockEntry/'.$lists->id).'/delete' }}"   ><i class="fa fa-trash"></i> </a>
                                                      <a class="btn btn-sm btn-primary showmodal"  data-url="{{ route('stock-id',$lists->id)  }}"  data-bs-toggle="modal" data-bs-target="#xlmodal"  ><i class="fa fa-edit"></i> </a>
                                                    </td>
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
    </div>
</div>
  @include('stock.edit-stock-modal')
 
@endsection

@section('scripts')
 

 
<script>

    
    // Make sure jQuery and Select2 are loaded
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function($) {
            // Check if select2 function exists
            if ($.fn.select2) {
                $('.js-example-basic-single').select2({
                    placeholder: "Select Item",
                    allowClear: true,
                    width: '100%'
                });
                console.log('Select2 initialized successfully');
            } else {
                console.log('Select2 plugin not found');
            }
        });
    } else {
        console.log('jQuery not found');
    }
</script>
 <script>
         $(document).ready(function(){
     

    $('body').on('click', '.showmodal', function(){
        var userUrl = $(this).data('url');
        console.log('Fetching URL:', userUrl); // Debug: Check URL

        $.get(userUrl, function(data){
            console.log('Data received:', data); // Debug: See exact data structure
            
            // Check if elements exist before setting values
            console.log('stockID element:', $('#stockID').length);
            console.log('skbatchnumber element:', $('#skbatchnumber').length);
            console.log('skitem element:', $('#skitem').length);
            console.log('skmanufactured element:', $('#skmanufactured').length);
            console.log('sksupplier element:', $('#sksupplier').length);
            console.log('skpurchaseorder element:', $('#skpurchaseorder').length);
            console.log('skwaybill element:', $('#skwaybill').length);
            console.log('skqty element:', $('#skqty').length);
            console.log('skaward element:', $('#skaward').length);
            console.log('skamount element:', $('#skamount').length);
            console.log('skstore element:', $('#skstore').length);
            console.log('skbarcode element:', $('#skbarcode').length);
            
            
            // Set the values
            $('#stockID').val(data.id);
            $('#skbatchnumber').val(data.batch_number);
            $('#skitem').val(data.item_id);
            $('#skmanufactured').val(data.manufacturing_date);
            $('#skexpiryd').val(data.expiry_date);
            $('#sksupplier').val(data.supplier_id);
            $('#skpurchaseorder').val(data.purchase_order);
            $('#skwaybill').val(data.waybill);
            $('#skqty').val(data.qty);
            $('#skaward').val(data.award_letter);
            $('#skamount').val(data.amount);
            $('#skstore').val(data.store_id);
            $('#skbarcode').val(data.barcode);
             
            
            // Verify values were set
            console.log('Set skbatchnumber value:', $('#skbatchnumber').val());
            console.log('Set skitem value:', $('#skitem').val());
            console.log('Set skmanufactured value:', $('#skmanufactured').val());
            console.log('Set skexpiryd value:', $('#skexpiryd').val());
            console.log('Set sksupplier value:', $('#sksupplier').val());
            console.log('Set skpurchaseorder value:', $('#skpurchaseorder').val());
            console.log('Set skwaybill value:', $('#skwaybill').val());
            console.log('Set skqty value:', $('#skqty').val());
            console.log('Set skaward value:', $('#skaward').val());
            console.log('Set skamount value:', $('#skamount').val());
            console.log('Set skstore value:', $('#skstore').val());
            console.log('Set skbarcode value:', $('#skbarcode').val());
            
            // Show the modal
            $('#xlmodal').modal('show');
        }).fail(function(error) {
            console.log('Error:', error);
        });
    });

    
});


</script>

    
@endsection