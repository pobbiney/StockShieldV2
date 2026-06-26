 <!-- page title -->
@php $pageName = "reports"; $subpageName = "item"; @endphp

@extends('layouts.backendapp')
 
@section('content')
                  
<div class="container-fluid mt-3">
    <div class="bg-theme-1-subtle rounded px-3 py-3">
        <div class="row gx-3 align-items-center">
            <div class="col col-sm mb-2 mb-sm-0">
                <p class="h5">Report Management</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item bi"><a href="#">Report Management</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Report</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Stock Report  </a></li>
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
                        <p class="h6">Stock Report </p>
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
                  @if(isset($liststock))
                            @if($liststock->count() > 0)
                                <div class="alert alert-success mt-2">
                                    {{ $liststock->count() }} result(s) found
                                </div>
                            @else
                                <div class="alert alert-danger mt-2">
                                    No results found
                                </div>
                            @endif
                        @endif
                        <form id="form" enctype="multipart/form-data" method="POST" action="{{ route('report.stock-report') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3 position-relative check-valid">
                                        <div class="form-floating">
                                            <select class="form-control " name="department">
                                                    <option value="" selected disabled>--Select Department--</option>
                                            @foreach ($liststores as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                            
                                            </select>
                                            @error('department') <small style="color:red"> {{ $message}}</small> @enderror
                                        </div>
                                    </div>
                                </div>
                              
                                 
                                <div class="col-md-1">
                                    <button type="submit" name="find" id="find" class="btn btn-success btn-lg" ><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        </form>
                        <hr/>

                        @if(isset($liststock) && $liststock->count() > 0)
                         
                          <div class="table-responsive">
                            
                                <table  class="table table-bordered "  >
                                    <thead>
                                        <tr class="bg-l-gradient-light theme-green">
                                          
                                            <th>ID</th>
                                            
                                            <th>ITEM CODE</th>
                                            <th >ITEM NAME</th>
                                            <th>UoM</th>
                                            <th>STOCK LEVEL</th>
                                            <th>UNIT COST</th>
                                            <th>TOTAL AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            
                                            
                                            @foreach($liststock as $lists)
                                            @php

                                                $qty = $lists->approveStock->sum('qty');

                                                $unitCost = $lists->approveStock->avg('amount');

                                                $totalAmount = $lists->approveStock->sum(function($stock){
                                                    return $stock->qty * $stock->amount;
                                                });

                                                $batchNumbers = $lists->approveStock
                                                                    ->pluck('batch_number')
                                                                    ->implode(', ');

                                                $expiryDates = $lists->approveStock
                                                                    ->pluck('expiry_date')
                                                                    ->implode(', ');

                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $lists->item_code }}</td>
                                                <td>{{ $lists->name }}</td>
                                                <td>{{ $lists->unitname->name ?? '' }}</td>
                                                <td>{{ $qty ?? 0 }}</td>
                                                <td>{{ number_format($unitCost ?? 0, 2) }}</td>
                                                <td>{{ number_format($totalAmount ?? 0, 2) }}</td>
                                            </tr>
                                            @endforeach
                                            
                                    </tbody>
                                </table>
                             
                                   
                            </div>
                            
                         
                            @endif
            </div>
        
        </div>
    </div>
</div>
  
 
@endsection

@section('scripts')
 

 
<script>
function openPrintTab() {
    document.getElementById('approveForm').target = '_blank';
    document.getElementById('approveForm').submit();
}
</script>
@endsection