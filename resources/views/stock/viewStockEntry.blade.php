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
                        <li class="breadcrumb-item bi"><a href="#">Stock Approval</a></li>
                         
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
                             <p class="h6">Stock Approval  </p>
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
                            <h4>List of All Pending Stocks </h4>
                               
                                <table  class="display" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Item Code</th>
                                            <th >Item Name</th>
                                            <th>Batch Number</th>
                                            <th>Expiry Date</th>
                                            <th>Received Date</th>

                                            <th>Qty</th>
                                            <th>Cost</th>
                                            <th>Purchase Order</th>
                                            <th>Vendor</th>
                                            <th>Store</th>
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
                                                <td>{{$lists->created_at}}</td>
                                                <td>{{$lists->qty}}</td>
                                                <td> {{ number_format($lists->amount, 2) }}</td>
                                                <td> {{$lists->purchase_order}}</td>
                                                <td> {{$lists->supname->company}}</td>
                                                <td> {{$lists->storename->name}}</td>
                                                    <td><a class="btn btn-sm btn-success delete-btn"  onclick="return confirm('Are you sure you want to Approve this ?')"
                                                    href="{{ route('stock.stockApproval', $lists->id) }}"  ><i class="fa fa-check-circle"></i> Approve</a>

                                              
                                                      
                                            </tr>
                                                
                                            
                                            @endforeach
                                            
                                    </tbody>
                                </table>
                                <a href="{{ route('stock.approveAll',$lists->store_id) }}" class="btn btn-info" onclick="return confirm('Approve all pending stock ?')"> Approve All</a>
                               
                                
                            </div>
                   </div>
                </div>
                    
            </div>
    </div>
         
    
</div>
 
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
@endsection