<!-- page title -->
@php $pageName = "user"; $subpageName = "mapping"; @endphp

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
                        <li class="breadcrumb-item bi"><a href="#">Map staff to Store</a></li>
                         
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
                            <p class="h6">Map staff to Store</p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-theme btn-square" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false">
                                <i class="bi bi-code-slash"></i>
                            </button>
                        </div><br><br/>
                        <hr/>
                    </div>
                </div>
                
                <div class="card-body">

                    @if (session('message_success'))
                        <div class="alert alert-success">{{ session('message_success') }}</div>
                    @endif
                    @if (session('message_error'))
                        <div class="alert alert-danger">{{ session('message_error') }}</div>
                    @endif
                   
                    <form id="formValidationExamples" class="row g-6" action="{{route('map-store-process')}}" method="POST">
                     @csrf
                     
                      <div class="form-group row">
                           <div class="col-md-6">
                               <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                         <select id="staffSelect" name="staff_id" class="form-select select2" data-allow-clear="true">
                                            <option value="" selected disabled>-- SELECT STAFF --</option>
                                            @foreach ($liststaff as $list)
                                                <option value="{{ $list->staff_id }}"
                                                    >
                                                    {{ $list->surname.' '.$list->firstname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    
                                        @error('staff_id')<small style="color: red">{{$message}}</small>@enderror
                                   </div>
                               </div>
                           </div>
                           <div class="col-md-6">
                               <div id="staffRoleInfo" class="alert alert-info d-none mb-0">
                                   <strong>Role:</strong> <span id="staffRoleName"></span>
                               </div>
                           </div>
                          
                      </div> 
                      <div id="globalAccessNotice" class="alert alert-warning d-none">
                          This user role has access to all stores. All active stores will be assigned automatically on save.
                      </div>
                      <div class="row" id="storeMappingTable">
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 50px">#</th>
                                        <th scope="col" style="width: 120px"><input class="form-check-input" type="checkbox"  id="selectAll"> Select All</th>
                                        <th scope="col">Store</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach ($liststore as $listde)
                                        
                                    
                                    <tr>
                                        <td>{{ $loop->iteration}}</td>
                                        <td><input class="form-check-input store-checkbox" type="checkbox" 
                                        name="department_id[]" 
                                        value="{{ $listde->id }}"
                                        
                                         ></td>
                                        <td>{{ $listde->name}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-success">Map Staff</button>
                        </div>

                     
                      
                  </form>
                     
                </div>
                    
            </div>
    </div>
         
    
</div>
 
 @endsection

@section('scripts')

<script>
    const selectAllCheckbox = document.getElementById('selectAll');
    const storeCheckboxes = () => document.querySelectorAll('.store-checkbox');
    const globalAccessNotice = document.getElementById('globalAccessNotice');
    const storeMappingTable = document.getElementById('storeMappingTable');
    const staffRoleInfo = document.getElementById('staffRoleInfo');
    const staffRoleName = document.getElementById('staffRoleName');

    function setStoreCheckboxesDisabled(disabled) {
        storeCheckboxes().forEach(function (checkbox) {
            checkbox.disabled = disabled;
        });
        selectAllCheckbox.disabled = disabled;
    }

    selectAllCheckbox.addEventListener('click', function () {
        storeCheckboxes().forEach(function (checkbox) {
            if (!checkbox.disabled) {
                checkbox.checked = selectAllCheckbox.checked;
            }
        });
    });

    document.getElementById('staffSelect').addEventListener('change', function () {
        let staffId = this.value;

        if (!staffId) return;

        fetch(`{{ url('/get-staff-stores') }}/${staffId}`)
            .then(response => response.json())
            .then(data => {
                const mappedIds = data.mapped_ids || [];

                staffRoleInfo.classList.remove('d-none');
                staffRoleName.textContent = data.role_name || 'No role assigned';

                storeCheckboxes().forEach(cb => {
                    cb.checked = false;
                });

                if (data.access_all_stores) {
                    globalAccessNotice.classList.remove('d-none');
                    setStoreCheckboxesDisabled(true);
                    storeCheckboxes().forEach(cb => {
                        cb.checked = true;
                    });
                    selectAllCheckbox.checked = true;
                    return;
                }

                globalAccessNotice.classList.add('d-none');
                setStoreCheckboxesDisabled(false);

                mappedIds.forEach(id => {
                    let cb = document.querySelector(`input[name="department_id[]"][value="${id}"]`);
                    if (cb) cb.checked = true;
                });
            });
    });

    @if(session('mapped_staff_id'))
    document.addEventListener('DOMContentLoaded', function () {
        const staffSelect = document.getElementById('staffSelect');
        staffSelect.value = '{{ session('mapped_staff_id') }}';
        staffSelect.dispatchEvent(new Event('change'));

        @if(session('mapped_departments'))
        const mapped = @json(session('mapped_departments'));
        setTimeout(function () {
            document.querySelectorAll('.store-checkbox').forEach(cb => {
                cb.checked = mapped.includes(parseInt(cb.value, 10)) || mapped.includes(cb.value);
            });
        }, 400);
        @endif
    });
    @endif
</script>
 
@endsection
