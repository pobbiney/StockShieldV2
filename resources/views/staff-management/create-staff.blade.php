<!-- page title -->
@php $pageName = "staff"; $subpageName = "add_staff"; @endphp

@extends('layouts.backendapp')
<style>
.upload-container{
    display:flex;
    justify-content:center;
    margin-top:20px;
}

.upload-box{
    width:250px;
    height:250px;
    border:2px dashed #ccc;
    border-radius:10px;
    cursor:pointer;
    text-align:center;
    overflow:hidden;
    position:relative;
}

.upload-box img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.upload-text{
    position:absolute;
    bottom:5px;
    width:100%;
    font-size:12px;
    background:rgba(0,0,0,0.5);
    color:white;
}
</style>

@section('content')
                  
<div class="container-fluid mt-3">
    <div class="bg-theme-1-subtle rounded px-3 py-3">
        <div class="row gx-3 align-items-center">
            <div class="col col-sm mb-2 mb-sm-0">
                <p class="h5">Staff Management</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item bi"><a href="#">Staff Management</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Staff</a></li>
                        <li class="breadcrumb-item bi"><a href="#">Add New Staff</a></li>
                         
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
                            <p class="h6">Add New Staff  </p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-theme btn-square" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false">
                                <i class="bi bi-code-slash"></i>
                            </button>
                        </div>
                    </div>
                    <hr/>
                </div>
                
                <div class="card-body">
                   
                    <form enctype="multipart/form-data" action="{{ route('add-staff-process') }}" method="POST">
                        @csrf
                       
                         <div id="step-1" class="tab-pane px-0 " role="tabpanel" aria-labelledby="step-1">
                            <div class="row gx-3 gx-lg-4 align-items-center">
                                <div class="col-12 col-lg-3 text-center mb-3">
                                    <div class="upload-container">

                                        <label for="imageUpload" class="upload-box">
                                            <img id="preview" src="{{ asset('backend/assets/img/user.png') }}" alt="Profile Preview">
                                            <div class="upload-text">Click to upload photo</div>
                                        </label>

                                        <input type="file" id="imageUpload" name="image" accept="image/*" hidden>

                                    </div>
                                    <p class="h5">Upload Photo</p>
                                </div>
                                <div class="col">
                                    <!-- Details -->
                                    <p class="h6 py-2 mb-2">Personal Details</p>
                                    <div class="row gx-3 gx-lg-4">
                                        <div class="col-12 col-md-6 col-lg-6 col-xl-4">
                                            <div class="form-floating mb-3">
                                                <div class="form-floating">
                                                    <select class="form-control" name="title" id="statusname">
                                                        <option value="" selected disabled>--Choose  Title--</option>
                                                        <option value="Mr">Mr</option>
                                                        <option value="Mrs">Mrs</option>
                                                        <option value="Miss">Miss</option>
                                                        <option value="Dr">Dr</option>
                                                        <option value="Prof">Prof</option>
                                                    </select>
                                                        
                                                    @error('title') <small style="color:red"> {{ $message}}</small> @enderror
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6 col-xl-4">
                                            <div class="form-floating mb-3">
                                                <input type="text" name="surname" class="form-control" placeholder="Enter Surname"  >
                                                <label for="investmentmname2">Surname</label>
                                                @error('surname') <small style="color:red"> {{ $message}}</small> @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6 col-xl-4">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control"   placeholder="Enter Othername"  >
                                                <label for="phoneon1">Othername</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6 col-xl-4">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" name="firstname"  placeholder="Enter Firstname"  >
                                                <label for="emailaddresson2">Firstname</label>
                                                @error('firstname') <small style="color:red"> {{ $message}}</small> @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6 col-xl-4">
                                            <div class="form-floating mb-3">
                                                <div class="form-floating">
                                                    <select class="form-control" name="gender"  >
                                                        <option value="" selected disabled>--Choose  Gender--</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                            >
                                                    </select>
                                                        
                                                    @error('gender') <small style="color:red"> {{ $message}}</small> @enderror
                                                </div>
                                                
                                            </div>
                                        </div>
                                        
                                        
                                    </div>

                                        
                                    <div class="row gx-3 gx-lg-4">
                                        <div class="col-8">
                                            <div class="form-floating mb-3">
                                                    <input type="email" class="form-control" name="email"  placeholder="Enter Email Address"  >
                                                <label for="describe2">Email Address</label>
                                                 @error('email') <small style="color:red"> {{ $message}}</small> @enderror
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-floating mb-3">
                                                    <input type="number" class="form-control"  name="phone" placeholder="Enter Phone Number"  >
                                                <label for="describe2">Phone Number</label>
                                                 @error('phone') <small style="color:red"> {{ $message}}</small> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row gx-3 gx-lg-4">
                                        <div class="col-12">
                                            <div class="form-floating mb-3">
                                                <textarea class="form-control height-150" name="address" placeholder="Enter Residential Address"></textarea>
                                                <label for="describe3">Residential Address</label>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Describe Services and Facilities -->
                                    <p class="h6 py-2 mb-2">Employee's Information</p>
                                    <div class="row gx-3 gx-lg-4">
                                        <div class="col-4">
                                            <div class="form-floating mb-3">
                                                <input type="number" class="form-control" name="staff_number"  placeholder="Enter Staff ID"  >
                                                <label for="describe3">Staff ID</label>
                                                 @error('staff_number') <small style="color:red"> {{ $message}}</small> @enderror
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" name="position"   placeholder="Enter Position"  >
                                                <label for="describe3">Position</label>
                                                 @error('position') <small style="color:red"> {{ $message}}</small> @enderror
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-floating mb-3">
                                                 
                                                <select class="form-control" name="department">
                                                    <option value="" selected disabled>--Choose Department--</option>
                                                    @foreach ($list as $listdep)
                                                         <option value="{{ $listdep->id }}">{{ $listdep->name }}</option>
                                                    @endforeach
                                                </select>
                                                   
                                                 @error('department') <small style="color:red"> {{ $message}}</small> @enderror
                                            </div>
                                        </div>
                                    </div>
                                      <div class="mb-3">
                                         <button type="submit" class="btn btn-success">Add Staff </button>
                                     </div>

                                </div>
                            </div>
                        </div>          
                    </form>

                </div>
                    
            </div>
    </div>
         
    
</div>

 @endsection

@section('scripts')

<script>
document.getElementById("imageUpload").addEventListener("change", function(event){

    const file = event.target.files[0];

    if(file){
        const reader = new FileReader();

        reader.onload = function(e){
            document.getElementById("preview").src = e.target.result;
        }

        reader.readAsDataURL(file);
    }

});
</script>
       <script>
         

@if(session('message_success'))
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: "{{ session('message_success') }}",
    showConfirmButton: true,
    timer: 2000
});
@endif
@if(session('message_error'))
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: "{{ session('message_error') }}",
});
@endif
</script>
<script>
     $(document).ready(function () {
      $('#myTable').DataTable();
    });
    </script>
@endsection