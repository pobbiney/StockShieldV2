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
                               
                                <table  class="display" id="myTable">
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
                                           
                                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                            
                                         
                                            @foreach($listrequest as $lists)
                                            <tr>
                                                <td>{{ $loop->iteration}}</td>
                                                <td> {{ $lists->itemcode->item_code }}</td>
                                                <td>{{ $lists->itemname->name}}</td>
                                                <td>{{$lists->itemname->unitname->name }}</td>
                                                <td>{{$lists->batch_number}}</td>
                                               
                                                <td>{{$lists->qty}}</td>
                                                <td> {{$lists->amount}}</td>
                                                <td> {{$lists->requisition_no}}</td>
                                                <td> 
                                                    <span class="badge text-bg-success">{{$lists->status}}</span>
                                                      
                                                  
                                                    
                                                </td>
                                                <td>{{ $lists->staffname->name}}</td>
                                                     
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
 
 
@endsection

@section('scripts')
 
 

    
@endsection