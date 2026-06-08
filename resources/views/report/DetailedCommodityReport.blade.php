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
                        <li class="breadcrumb-item bi"><a href="#">Detailed Commodity Report  </a></li>
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
                        <p class="h6">Detailed Commodity Report </p>
                    </div>
                    <div class="col-auto">
                          {{-- <a href="{{ route('IssuedItemsReport') }}" class="btn btn-danger"  >
                               <i class="bi bi-back"></i> Back
                            </a> --}}
                    </div>
                </div>
            </div>
            <hr/>
            
            <div class="card-body">
                  @if(isset($reportData))
                        @if(count($reportData) > 0)
                            <div class="alert alert-success mt-2">
                                {{ count($reportData) }} result(s) found
                            </div>
                        @else
                            <div class="alert alert-danger mt-2">
                                No results found
                            </div>
                        @endif
                    @endif
                        <form id="form" enctype="multipart/form-data" method="POST" action="{{ route('report.searchCommodityDetails-report') }}">
                            @csrf
                            <div class="row">
                              
                                <div class="col-md-3">
                                    <div class="form-group mb-3 position-relative check-valid">
                                        <div class="form-floating">
                                            <input type="text" class="form-control datepicker1"     name="start_date" placeholder="Enter Start Date">
                                            <label>Start Date</label>
                                             @error('department') <small style="color:red"> {{ $message}}</small> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3 position-relative check-valid">
                                        <div class="form-floating">
                                            <input type="text" class="form-control datepicker2"     name="end_date" placeholder="Enter End Date">
                                            <label>End Date</label>
                                             @error('end_date') <small style="color:red"> {{ $message}}</small> @enderror
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-3">
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

                      @if(isset($reportData) && count($reportData) > 0)
                          <a  href="{{ route('report.commodityreport-print', ['start_date' => request()->start_date, 'end_date' => request()->end_date]) }}" target="_blank" class="btn btn-primary" style=" margin-bottom:10px"><i class="bi bi-printer"></i> Print</a>
                          <div class="table-responsive">
                            
                                <table  class="table table-bordered "  >
                                    <thead>
                                        <tr>
                                            <th colspan="14"> <h2 style="text-align: center">DETAILED  COMMODITIES REPORT <br/> @if(request()->start_date && request()->end_date)
                                                   {{ \Carbon\Carbon::parse(request()->start_date)->format('F Y') }} @endif</h2></th>
                                        </tr>
                                          <tr>
                                            <th>ID</th>
                                            <th>Item Description</th>
                                            <th>UoM</th>
                                            <th>Price</th>
                                            <th>Balance B/F (Qty)</th>
                                            <th>Balance B/F Value GHS</th>
                                            <th>Receipts (Qty)</th>
                                            
                                            <th >Receipt Value GHS</th>
                                            <th >Total Stock  (Qty) </th>
                                            <th >Total Stock  Value GHS</th>
                                            
                                            <th>Issued (Qty)  </th>
                                            <th>Issued Value GHS</th>
                                            <th>Closing Balance (Qty)</th>
                                            <th>Closing Balance Value GHS</th>
                                            
                                            
                                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                          
                                         
                                        @php
                                        $totalBF = 0;
                                        $totalReceipt = 0;
                                        $totalStock = 0;
                                        $totalIssued = 0;
                                        $totalClosing = 0;
                                        @endphp

                                        @foreach($reportData as $item)

                                         
        

                                         <tr>
                                         <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item['item_description'] }}</td>
                                        <td>{{ $item['uom'] }}</td>
                                        <td class="text-end">{{ $item['price'] }}</td>
                                        <td class="text-end">{{ number_format($item['balance_bf_qty']) }}</td>
                                        <td class="text-end">{{ $item['balance_bf_value'] }}</td>
                                        <td class="text-end">{{ number_format($item['receipts_qty']) }}</td>
                                        <td class="text-end">{{ $item['receipts_value'] }}</td>
                                       
                                        <td class="text-end">{{ number_format($item['total_stock_qty']) }}</td>
                                        <td class="text-end">{{ $item['total_stock_value'] }}</td>
                                        <td class="text-end">{{ number_format($item['issued_qty']) }}</td>
                                        <td class="text-end">{{ $item['issued_value'] }}</td>
                                        <td class="text-end  ">{{ number_format($item['closing_balance_qty']) }}</td>
                                        <td class="text-end  ">{{ $item['closing_balance_value'] }}</td>
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
 
 
@endsection