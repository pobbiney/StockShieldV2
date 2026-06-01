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
                        <li class="breadcrumb-item bi"><a href="#">Received  Items Report  </a></li>
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
                        <p class="h6">Received  Items Report </p>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('ReceivedStockByDate') }}" class="btn btn-info"  >
                               <i class="bi bi-calendar-date"></i> Search By Date
                            </a>
                            <a  href="{{ route('searchByItem') }}" class="btn btn-success"   >
                               <i class="bi bi-box-arrow-right"></i> Search By Item
                            </a>
                            <a href="{{ route('searchByItemDate') }}" class="btn btn-warning"   >
                               <i class="bi bi-calendar-date"></i> Search  Item By Date Intervals
                            </a>
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
                        <form id="form" enctype="multipart/form-data" method="POST" action="{{ route('report.stockreceived-report') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3 position-relative check-valid">
                                        <div class="form-floating">
                                            <select class="form-control " name="department">
                                                    <option value="" selected disabled>--Select Store--</option>
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
                          <a href="{{ route('report.received-stock-print', request()->department) }}" target="_blank" class="btn btn-primary" style=" margin-bottom:10px"><i class="bi bi-printer"></i> Print</a>
                          <div class="table-responsive">
                            
                                <table  class="table table-bordered "  >
                                    <thead>
                                        <tr class="bg-l-gradient-light theme-green">
                                          
                                            <th>ID</th>
                                            
                                            <th>ITEM CODE</th>
                                            <th >ITEM NAME</th>
                                            <th>UoM</th>
                                            <th>BATCH NO</th>
                                            <th>VENDOR</th>
                                            <th>PO</th>
                                            <th>WAYBILL REF.</th>
                                            <th>EXPIRY DATE</th>
                                            <th>CONTRACT REF.</th>
                                            <th>RECEIVED BY</th>
                                            <th>UNIT COST</th>
                                            <th>QTY</th>
                                            
                                            <th>TOTAL AMOUNT</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                            
                                             @php  $grandTotal = 0; $totalqty = 0; @endphp
                                            @foreach($liststock as $lists)
                                            
   
                                            @php
                                                $lineTotal = $lists->amount * $lists->qty;
                                                $grandTotal += $lineTotal;
                                                $totalqty += $lists->qty;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $lists->itemname->item_code }}</td>
                                                <td>{{ $lists->itemname->name }}</td>
                                                <td>{{ $lists->itemname->unitname->name ?? ''}}</td>
                                                <td>{{ $lists->batch_number ?? '' }}</td>
                                                
                                                <td>{{ $lists->supname->company ?? '' }}</td>
                                                <td>{{ $lists->purchase_order ?? ''}} </td>
                                                <td>{{ $lists->waybill }}</td>
                                                <td>{{ $lists->expiry_date }}</td>
                                                <td>{{ $lists->award_letter }}</td>
                                                <td>{{ $lists->staffname->name }}</td>
                                                <td>{{ $lists->amount ?? ''}}</td>
                                                <td>{{ $lists->qty ?? ''}} </td>
                                                
                                                <td>{{ number_format($lineTotal,2)}}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="11"><b style="float: right">TOTAL</b></td>
                                                <td></td>
                                                <td><b>{{ $totalqty }}</b></td>
                                                
                                                <td><b>{{ number_format($grandTotal,2)}}</b></td>
                                            </tr>
                                            
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
 
 
@endsection