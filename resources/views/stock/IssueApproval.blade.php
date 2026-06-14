 <!-- page title -->
@php $pageName = "stock"; $subpageName = "approve-issues"; @endphp

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
                        <li class="breadcrumb-item bi"><a href="#">Issue Approval  </a></li>
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
                        <p class="h6">Issue Approval  </p>
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
                  @if(isset($listissues))
                            @if($listissues->count() > 0)
                                <div class="alert alert-success mt-2">
                                    {{ $listissues->count() }} result(s) found
                                </div>
                            @else
                                <div class="alert alert-danger mt-2">
                                    No results found
                                </div>
                            @endif
                        @endif

                        
                        <form id="form" enctype="multipart/form-data" method="POST" action="{{ route('stock.search-issues') }}">
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

                        @if(isset($listissues) && $listissues->count() > 0)
                        <form  id="approveForm" method="POST" action="{{ route('approveIssue-process') }}" target="_" >
                            @csrf
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
                                            
                                            
                                            @foreach($listissues as $lists)
                                            <tr>
                                                  
                                                <td>{{ $loop->iteration}}  <input type="hidden"
                                                    name="issue_id[]"
                                                    value="{{ $lists->id }}"></td>
                                                 
                                                <td> {{ $lists->itemcode->item_code }}</td>
                                                <td>{{ $lists->itemname->name}}</td>
                                                <td>{{$lists->batch_number}}</td>
                                               
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
                            @endif
            </div>
        
        </div>
    </div>
</div>
  
 
@endsection

@section('scripts')
 

 
<script>
   @if(session('print_url'))
<script>
    var printWindow = window.open("{{ session('print_url') }}", '_blank');
    if (!printWindow) {
        alert('Please allow popups for this site to open the print page automatically.');
        window.location.href = "{{ session('print_url') }}";  // fallback: open in same tab
    }
</script>
@endif
 
</script>

  @include('stock.reject-issue-modal')
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