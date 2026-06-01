<!-- page title -->
@php $pageName = "user"; $subpageName = "assign_priv"; @endphp

@extends('layouts.backendapp')

@section('content')
                  
<div class="container-fluid mt-3">
    <div class="bg-theme-1-subtle rounded px-3 py-3">
        <div class="row gx-3 align-items-center">
            <div class="col col-sm mb-2 mb-sm-0">
                <p class="h5">Settings</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item bi"><a href="#">Settings</a></li>
                        <li class="breadcrumb-item bi"><a href="#">User Management</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Assign User Privileges</a></li>
                         
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
                            <p class="h6">Assign User Privileges    </p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-theme btn-square" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false">
                                <i class="bi bi-code-slash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                   
                      <div class="table-responsive">
                <form id="formValidationExamples" class="row g-6" action="#" method="POST">
                    @csrf
        
                    <table class="table table-striped table-bordered" style="width:100%">
                                    
                        <tr>
                            <td ><label style="margin-left: 20px">Category:</label></td>
                            <td><select class="form-select" name="category" id="category">
                                    <option value="" disabled selected>--SELECT CATEGORY--</option>
                                    @foreach ($userCatList as $userCatList)
                                        <option value="{{ $userCatList->cat_id }}" @if (old('category') == $userCatList->cat_id) selected @endif>{{$userCatList->cat_name}}</option>
                                    @endforeach
                                </select>
                                <span id="caterror"></span>
                            </td>
                            <td><div><button type="submit" id="assign" class="btn btn-success"><i class="fa fa-check"></i></button></td>
                        </tr>
                    </table>
                    <br> <br>
                    <div align="center">
                        <p id="confirmation" style="text-align:center"></p>
                        {{-- <p align="center" style="display: none; color: limegreen;" id="wait"><img src="{{ asset('assets/img/spinner-grey.gif')}}" > saving privileges. Please wait....</p> --}}
                        <p align="center" style="display: none; color: limegreen;" id="wait_fetch"><img src="{{ asset('backend/assets/img/spinner-grey.gif')}}" > Fetching privileges for selected category. Please wait....</p>
                    </div>
                    @if ($parents != null)
                    <div id="listarea">
                        <table class="table table-bordered" style="width:100%">
                            @foreach ($parents as $mainlink)
                                <tr>
                                    <td colspan="2"> <b><small><?php echo $mainlink->link_name; ?></small></b> </td>
                                </tr>
                                    @foreach ($child as $subs)
                                        @if ($mainlink->link_id == $subs->link_parent)
                                            <tr>
                                                
                                                <td style="width: 60px;">
                                                    <div class="form-check form-check-md">
                                                            <input class="form-check-input" type="checkbox" name="priv_check[]" id="priv_check" value="{{$subs->link_id}}">
                                                    </div>
                                                </td>
                                                <td><small>{{$subs->link_name}}</small></td>
                                            </tr>
                                        @endif
                                    @endforeach
                            @endforeach
                        </table>
                    </div>
                    @endif
                </form>
            </div>
                </div>
                    
            </div>
    </div>
         
    
</div>
 
 @endsection

@section('scripts')
<script>
    $(document).on("change","#category",function(){

    var dropvalue = $("#category").val();

    $("#wait_fetch").css("display", "block");

    $.ajax({
        type: "POST",
        url: "{{ route('get-category-privileges') }}",
        data: $('#formValidationExamples').serialize(),
        success:function(data) {

            $('#listarea').html(data);
            $("#assign").removeAttr('disabled');
            $("#wait_fetch").css("display","none");

        }

    });
    });
</script>

<script>
$(document).on("click", "#assign", function(e){
    e.preventDefault();

    $("#caterror").empty();
    var user_cat = $.trim($("#category").val());
    if(user_cat.length == 0){
        $("#caterror").html('<p><small style="color:red;">Choose an option</small></p>');
        return false; // stop execution if no category selected
    }

    $("#wait").css("display","block");
    $("#assign").attr("disabled", "disabled");

    $.ajax({
        type: "POST",
        url: "{{ route('save-user-privileges') }}",
        data: $('#formValidationExamples').serialize(),
        success: function(response) {

            $("#wait").css("display","none");
            $("#assign").removeAttr('disabled');

            if(response == "d_fail"){
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'User privilege assignment failed'
                });

            } else if(response == "ok"){
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'User privileges were assigned successfully'
                });

            } else if(response == "unchecked"){
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Privilege assignment failed. No option was checked before assigning privileges'
                });

            } else if(response == "unselected"){
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Privilege assignment failed. No user category was selected'
                });
            }

        },
        error: function(xhr, status, error){
            $("#wait").css("display","none");
            $("#assign").removeAttr('disabled');
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong! Please try again.'
            });
        }
    });

    return false;
});
 
 
</script>
<script>
     $(document).ready(function () {
      $('#myTable').DataTable();
    });
    </script>
@endsection