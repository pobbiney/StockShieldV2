<!-- page title -->
@php $pageName = "stock"; $subpageName = "itemcat"; @endphp

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
                        <li class="breadcrumb-item bi"><a href="#">Add New Item</a></li>
                         
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
                             <p class="h6">Add New Item  </p>
                        </div>
                        <div class="col-auto">
                            {{-- <a data-bs-toggle="modal" data-bs-target="#standardmodal"  class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false">
                                <i class="bi bi-upload"></i> Bulk Upload
                            </a>
                            <a href="" class="btn btn-info" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false">
                                <i class="bi bi-download"></i>   Download Template
                            </a>
                            <a href="" class="btn btn-warning" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false">
                                <i class="bi bi-download"></i>   Download Instructions
                            </a> --}}
                        </div>
                    </div>
                </div>
                <hr/>

                 
                
                <div class="card-body">
                   
                    <form enctype="multipart/form-data" action="{{ route('add-item-process') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4">
                               <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="text" name="name" class="form-control"   placeholder="Enter Name">
                                        <label>Name</label>
                                        @error('name') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select class="form-control" name="category_id">
                                            <option value="" selected disabled>--Choose Option--</option>
                                            @foreach ($listcat as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                             
                                        </select>
                                        <label>Category</label>
                                        @error('category_id') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select class="form-control" name="unit_of_measure_id">
                                            <option value="" selected disabled>--Choose Option--</option>
                                            @foreach ($listunit as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                            @endforeach
                                             
                                        </select>
                                        <label>Unit of Measure</label>
                                        @error('unit_of_measure_id') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px">
                                    
                                 
                            <div class="col-md-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select class="form-control" name="store_id">
                                            <option value="" selected disabled>--Choose Option--</option>
                                            @foreach ($getstoreid as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                             
                                        </select>
                                        <label>Store</label>
                                        @error('store_id') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                             <div class="col-lg-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                         <input type="number" name="re_order_level" class="form-control"/>
                                        <label>Reorder Level</label>
                                        @error('re_order_level') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                               
                            </div>
                             <div class="col-lg-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select class="form-control" name="status">
                                            <option value="" selected disabled>--Choose Option--</option>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                        <label>Status</label>
                                        @error('status') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                        <div class="mb-3">
                                    <button type="submit" class="btn btn-success">Add Item </button>
                        </div>
                        <div class="row" style="margin-top: 50px">
                            <div class="col-lg-12">
                                <h4>List All Items</h4>
                                <table  class="display" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Item Code</th>
                                            <th >Item Name</th>
                                            <th>Category</th>
                                            <th>UoM</th>
                                            <th >Store</th>
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
                                                 <td><a class="btn btn-sm btn-success showmodal"  data-url="{{ route('item-id',$lists->id)  }}" data-bs-toggle="modal" data-bs-target="#lgmodal"     data-bs-placement="top" data-bs-custom-class="adminuiux-success-tooltip" data-bs-title="Click to edit Item"><i class="fa fa-edit"></i></a></td>
                                            </tr>
                                             
                                          
                                            @endforeach
                                            @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>

                </div>
                    
            </div>
    </div>
         
    
</div>
  @include('stock.edit-item-modal')
  @include('stock.bulkupload')
 @endsection

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
            console.log('itemcatname element:', $('#itemcatname').length);
            console.log('itemunitname element:', $('#itemunitname').length);
            console.log('itemstorename element:', $('#itemstorename').length);
             console.log('itemsreorder element:', $('#itemsreorder').length);
            console.log('statusname element:', $('#statusname').length);
            
            // Set the values
            $('#itemID').val(data.id);
            $('#itemname').val(data.name);
            $('#itemcatname').val(data.cat_id);
            $('#itemunitname').val(data.unit_id);
            $('#itemstorename').val(data.store_id);
            $('#itemsreorder').val(data.reorder_level);
            $('#statusname').val(data.status);
            
            // Verify values were set
            console.log('Set itemname value:', $('#itemname').val());
            console.log('Set itemcatname value:', $('#itemcatname').val());
            console.log('Set itemunitname value:', $('#itemunitname').val());
            console.log('Set itemstorename value:', $('#itemstorename').val());
            console.log('Set itemsreorder value:', $('#itemsreorder').val());
            console.log('Set statusname value:', $('#statusname').val());
            
            // Show the modal
            $('#lgmodal').modal('show');
        }).fail(function(error) {
            console.log('Error:', error);
        });
    });

    
});


</script>   
@endsection