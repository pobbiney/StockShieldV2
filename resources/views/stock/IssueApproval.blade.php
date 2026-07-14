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
                    <table  class="display" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            
                                         
                                            @foreach($listissues as $lists)
                                            <tr>
                                                <td>{{ $loop->iteration}}</td>
                                                <td> Request has been made by {{ $lists->staffname->name}} with Requisition Number <b>{{ $lists->requisition_no}}</b></td>
                                                <td><a href="{{ route('viewIssues', Crypt::encrypt($lists->requisition_no)) }}" class="btn btn-success"><i class="fa fa-eye"></i> Open Request </a></td>
                                               
                                                     
                                            </tr> 
                                                
                                            
                                            @endforeach 
                                            
                                    </tbody>
                                </table>
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