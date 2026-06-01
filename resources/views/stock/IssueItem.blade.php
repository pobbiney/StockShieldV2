 <!-- page title -->
@php $pageName = "stock"; $subpageName = "pending-stock"; @endphp

@extends('layouts.backendapp')
<style>
.select2-container .select2-selection--single {
    height: 60px !important;
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
                        <li class="breadcrumb-item bi"><a href="#">Issue Item  </a></li>
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
                        <p class="h6">Issue Item  </p>
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
             
                <div class="row gx-3 align-items-center" >
                    <div class="col-auto">
                        <div class="avatar avatar-60 rounded bg-theme-1-subtle text-theme-1 theme-green">
                            <i class="bi bi-boxes h4"></i>
                        </div>
                    </div>
                    <div class="col">
                        <p class="text-secondary small mb-1">Available Stock Balance</p>
                        <h5 class="text-dark mb-0"><span class="increamentcount" id="qty"></span> <small class="h6"></small></h5>
                         
                    </div>
                </div>
             
            
          
           <form method="post" enctype="multipart/form-data" action="{{ route('add-itemissue-process') }}">
            @csrf
                <div class="row gx-3 align-items-center">
                    <div class="row" style="margin-top:50px ">
                        
                            <div class="col-md-3">
                                <select class="js-example-basic-single form-control" name="item" id="item_id" >
                                    <option value="" selected disabled>--Select Item--</option>
                                        @foreach ($getItemid as $listitems )
                                            <option value="{{ $listitems->id }}">{{ $listitems->name}}</option>
                                        @endforeach
                                </select>
                        
                                 @error('item') <small style="color:red"> {{ $message}}</small> @enderror  
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="text" name="batch_number" id="batch_number" class="form-control"  readonly
                                        />
                                         <label>Batch Number</label>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select class="form-control" name="store">
                                            <option value="" selected disabled>--Choose Option--</option>
                                            @foreach ($liststore as $liststores)
                                                <option value="{{ $liststores->id }}">{{ $liststores->name }}</option>
                                            @endforeach
                                                
                                        </select>
                                        <label>Issue To</label>
                                        @error('store') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="quantity" placeholder="Enter Qty" >
                                        <label>Qty</label>
                                        @error('quantity') <small style="color:red"> {{ $message}}</small> @enderror
                                    
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="book_no" placeholder="Enter Requisiton Book No" >
                                        <label>Requisition No</label>
                                        @error('book_no') <small style="color:red"> {{ $message}}</small> @enderror
                                    
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                         
                </div>
            </div>
             
            <input type="hidden" name="stock_id" id="stock_id"/>
           </form>

             @if($listitemissue->count() > 0)
             <div class="border-start border-top p-3 bg-l-gradient-light theme-green">
            
                
                    <div class="row" style="margin-top:50px ">
                        <div class="col-md-12">
                            <div class="table-responsive">
                               
                                <table  class="table table-bordered "  >
                                    <thead>
                                        <tr class="bg-l-gradient-light theme-green">
                                            <th>ID</th>
                                            <th>Item Code</th>
                                            <th >Item Name</th>
                                            <th>Batch Number</th>
                                            
                                            <th>Qty</th>
                                            <th>Cost</th>
                                            <th>Requisition No  </th>
                                            <th>Issue To</th>
                                              
                                            <th>Action</th>
                                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                            
                                            
                                            @foreach($listitemissue as $lists)
                                            <tr>
                                                <td>{{ $loop->iteration}}</td>
                                                <td> {{ $lists->itemcode->item_code }}</td>
                                                <td>{{ $lists->itemname->name}}</td>
                                                <td>{{$lists->batch_number}}</td>
                                               
                                                <td>{{$lists->qty}}</td>
                                                <td> {{$lists->amount}}</td>
                                                <td> {{$lists->requisition_no}}</td>
                                                <td> {{$lists->storename->name}}</td>
                                                    <td><a class="btn btn-sm btn-danger delete-btn"  onclick="return confirm( 'Are you sure you want to delete this Item?')" href=" {{ url('IssueItem/'.$lists->id).'/delete' }}"   ><i class="fa fa-trash"></i> </a>
                                                      
                                                    </td>
                                            </tr>
                                                
                                            
                                            @endforeach
                                            
                                    </tbody>
                                </table>
                             
                                <a href="{{ route('stock.IssueapproveAll') }}" class="btn btn-info" onclick="return confirm('Save all pending Issues ?')"><i class="fa fa-save"></i> Save All</a>
                                    
                            </div>
                        </div>
                    </div>
                </div>
                @endif
         </div>
        
        </div>
    </div>
</div>
  
 
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
    $('#item_id').on('change', function () {

        let itemId = $(this).val();

        $.ajax({
            url: "{{ route('get.batch.number') }}",
            type: "POST",
            data: {
                getID: itemId,
                _token: "{{ csrf_token() }}"
            },

            success: function (response) {

                if (response.batch_number) {
                    $('#batch_number').val(response.batch_number);
                    $('#stock_id').val(response.stock_id);
                    $('#qty').text(response.qty);
                } else {
                    $('#batch_number').val('');
                    $('#stock_id').val('');
                     $('#qty').val('');
                }
            }
        });

    });
</script>
    
@endsection