{{-- page title --}}
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

.status-badge {
    font-size: 0.75rem;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 500;
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
                        <li class="breadcrumb-item bi"><a href="#">Requisition</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Request Item</a></li>
                    </ol>
                </nav>
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
                        <p class="h6">Item Request</p>
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

                <form method="post" action="{{ route('add-request-process') }}">
                    @csrf
                    <div class="row gx-3 align-items-center">
                        <div class="row" style="margin-top:20px">

                            <div class="col-md-5">
                                <select class="js-example-basic-single form-control" name="item" id="item_id" required>
                                    <option value="" selected disabled>--Select Item--</option>
                                    @foreach ($getItemid as $listitems)
                                        <option value="{{ $listitems->id }}">{{ $listitems->name }}</option>
                                    @endforeach
                                </select>
                                @error('item') <small style="color:red"> {{ $message }}</small> @enderror
                            </div>
<<<<<<< HEAD

=======
                             <div class="col-md-1">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="text" name="uom" id="uom" class="form-control"  readonly
                                        />
                                         <label>UoM</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="text" name="batch_number" id="batch_number" class="form-control"  readonly
                                        />
                                         <label>Batch Number</label>
                                    </div>
                                </div>
                            </div>
                             
>>>>>>> 3b001aeea4c5ceae9e7bb892e0440c524eabe236
                            <div class="col-md-2">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="text" name="uom" id="uom" class="form-control" readonly>
                                        <label>UoM</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="quantity" id="quantity" placeholder="Enter Qty" min="1" required>
                                        <label>Qty</label>
                                        @error('quantity') <small style="color:red"> {{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>

                @if($listitemissue->count() > 0)
                <div class="border-start border-top p-3 bg-l-gradient-light theme-green">

                    <div class="row" style="margin-top:20px">
                        <div class="col-md-12">
                            <div class="table-responsive">

                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="bg-l-gradient-light theme-green">
                                            <th>ID</th>
                                            <th>Item Code</th>
<<<<<<< HEAD
                                            <th>Item Name</th>
                                            <th>UoM</th>
                                            <th>Qty Requested</th>
                                            <th>Status</th>
=======
                                            <th >Item Name</th>
                                            <th>UoM</th>
                                            <th>Batch Number</th>
                                            
                                            <th>Qty</th>
                                            <th>Unit Cost</th>
                                          
                                           
                                              
>>>>>>> 3b001aeea4c5ceae9e7bb892e0440c524eabe236
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<<<<<<< HEAD
                                        @foreach($listitemissue as $lists)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $lists->itemcode->item_code }}</td>
                                            <td>{{ $lists->itemname->name }}</td>
                                            <td>{{ $lists->itemname->unitname->name }}</td>
                                            <td>{{ $lists->qty_requested }}</td>
                                            <td>
                                                @php
                                                    $statusMap = [
                                                        'pending' => ['label' => 'Pending DHOD Approval', 'class' => 'bg-warning text-dark'],
                                                        'approved'     => ['label' => 'Approved',              'class' => 'bg-info text-white'],
                                                        'issued'       => ['label' => 'Issued',                'class' => 'bg-primary text-white'],
                                                        'completed'    => ['label' => 'Completed',             'class' => 'bg-success text-white'],
                                                        'rejected'     => ['label' => 'Rejected',              'class' => 'bg-danger text-white'],
                                                    ];
                                                    $statusInfo = $statusMap[$lists->status] ?? ['label' => ucfirst($lists->status), 'class' => 'bg-secondary text-white'];
                                                @endphp
                                                <span class="status-badge {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                                            </td>
                                            <td>
                                                @if($lists->status === 'pending_dhod')
                                                <a class="btn btn-sm btn-danger delete-btn"
                                                   onclick="return confirm('Are you sure you want to delete this Item?')"
                                                   href="{{ url('Requisition/'.$lists->id).'/delete' }}">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                                @else
                                                <span class="text-muted small">Locked</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
=======
                                            
                                            
                                            @foreach($listitemissue as $lists)
                                            <tr>
                                                <td>{{ $loop->iteration}}</td>
                                                <td> {{ $lists->itemcode->item_code }}</td>
                                                <td>{{ $lists->itemname->name}}</td>
                                                <td>{{$lists->itemname->unitname->name}}</td>
                                                <td>{{$lists->batch_number}}</td>
                                               
                                                <td>{{$lists->qty_requested}}</td>
                                                <td> {{$lists->amount}}</td>
                                               
                                                 
                                                    <td><a class="btn btn-sm btn-danger delete-btn"  onclick="return confirm( 'Are you sure you want to delete this Item?')" href=" {{ url('Requisition/'.$lists->id).'/delete' }}"   ><i class="fa fa-trash"></i> </a>
                                                      
                                                    </td>
                                            </tr>
                                                
                                            
                                            @endforeach
                                            
>>>>>>> 3b001aeea4c5ceae9e7bb892e0440c524eabe236
                                    </tbody>
                                </table>

                                @php
                                    $hasPendingItems = $listitemissue->where('status', 'pending')->count() > 0;
                                @endphp

                                @if($hasPendingItems)
                                <a href="{{ route('requisition.SubmitRequest') }}"
                                   class="btn btn-info"
                                   onclick="return confirm('Are you sure you want to save for approval?')">
                                    <i class="fa fa-save"></i> Submit Request
                                </a>
                                @endif

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
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function ($) {
            if ($.fn.select2) {
                $('.js-example-basic-single').select2({
                    placeholder: "Select Item",
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    }
</script>

<<<<<<< HEAD
<script>
    $('#item_id').on('change', function () {
        let itemId = $(this).val();

        if (!itemId) {
            $('#uom').val('');
            return;
        }

        $.ajax({
            url: "{{ route('get.item.uom') }}",
            type: "POST",
            data: {
                item_id: itemId,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                $('#uom').val(response.uom_name ?? '');
            },
            error: function () {
                $('#uom').val('');
=======
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
                    $('#store_id').val(response.store_id);
                    $('#stock_id').val(response.stock_id);
                    $('#qty').text(response.qty);
                    $('#uom').val(response.uom_name); // now shows the actual UOM name
                } else {
                    $('#batch_number').val('');
                    $('#stock_id').val('');
                    $('#store_id').val('');
                    $('#uom').val('');
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Stock Available',
                        text: response.message || 'No stock available for this item',
                        confirmButtonColor: '#d33'
                    });
                    $('#item_id').val('').trigger('change');
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while checking stock.'
                });
>>>>>>> 3b001aeea4c5ceae9e7bb892e0440c524eabe236
            }
        });
    });
</script>

@endsection