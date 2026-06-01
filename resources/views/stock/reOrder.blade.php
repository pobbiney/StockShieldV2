<!-- page title -->
@php $pageName = "stock"; $subpageName = "reorder"; @endphp

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
                        <li class="breadcrumb-item bi"><a href="#">Item</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Set Reorder Level</a></li>
                         
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
                             <p class="h6">Set Reorder Level  </p>
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
                    
                        <div class="row"  >
                            <div class="col-lg-12">
                                <h4>List All Items</h4>
                                <table  class="display" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Item Code</th>
                                            <th >Item Name</th>
                                            <th>Category</th>
                                            <th>Unit Of Issue</th>
                                            <th >Store</th>
                                            <th>ReOrder Level</th>
                                            <th  >Action</th>
                                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                          
                                            @if($list)
                                            @foreach($list as $lists)
                                            <tr>
                                                <td>{{ $loop->iteration}}</td>
                                                <td> {{ $lists->item_code }}</td>
                                                <td>{{ $lists->name}}</td>
                                                <td>{{$lists->categoryname->name}}</td>
                                                <td>{{$lists->unitname->name}}</td>
                                                <td>{{$lists->storename->name}}</td>
                                                <td> @if ($lists->reorder_level == NULL)
                                                      <span class="badge text-bg-danger">Reorder level not set</span>
                                                      @else
                                                     <b> {{ $lists->reorder_level }}</b>
                                                      @endif


                                                </td>
                                                 <td><a class="btn btn-sm btn-success showmodal"  data-url="{{ route('item-id',$lists->id)  }}" data-bs-toggle="modal" data-bs-target="#standardmodal"     ><i class="fa fa-refresh"></i> Set Re-order level</a></td>
                                            </tr>
                                             
                                          
                                            @endforeach
                                            @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                     

                </div>
                    
            </div>
    </div>
         
    
</div>
  
 @endsection
  @include('stock.reOrderlevel-modal')
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