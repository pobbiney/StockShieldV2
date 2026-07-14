 <div class="modal fade" id="xlmodal" tabindex="-1" aria-labelledby="xlmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <p class="modal-title h5" id="xlmodalLabel">Add Supplier's Details</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form enctype="multipart/form-data" method="POST" action="{{ route('add-supplier-process') }}" >
                            @csrf
                <div class="modal-body">
                    
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="text" name="code" class="form-control"    placeholder="Enter Supplier's Code" value="{{ $supCode}}">
                                        <label>Supplier's Code</label>
                                        @error('code') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="text" name="supplier" class="form-control"     placeholder="Enter Supplier's Name">
                                        <label>Supplier/Contact Person's Name</label>
                                        @error('supplier') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="number" name="phone" class="form-control"     placeholder="Enter Phone Number">
                                        <label>Phone Number</label>
                                        @error('phone') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px">
                            <div class="col-md-6">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="text" name="email" class="form-control"   placeholder="Enter Email Address">
                                        <label>Email Address</label>
                                        <!-- @error('email') <small style="color:red"> {{ $message}}</small> @enderror -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        
                                        <input type="text" name="company" class="form-control"    placeholder="Enter Company Name">
                                        <label>Company Name</label>
                                        @error('company') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px">
                            <div class="col-md-3">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        
                                        <input type="text" name="city" class="form-control"    placeholder="Enter City">
                                        <label>City/Location</label>
                                        @error('city') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        
                                        <input type="text" name="tin_number" class="form-control"    placeholder="Enter TIN Number">
                                        <label>TIN</label>
                                        <!-- @error('tin_number') <small style="color:red"> {{ $message}}</small> @enderror -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        
                                        <input type="text" name="registration_number" class="form-control"    placeholder="Enter Company Registration Number">
                                        <label>Company Registration Number</label>
                                        <!-- @error('registration_number') <small style="color:red"> {{ $message}}</small> @enderror -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px">
                            <div class="col-md-8">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        
                                        <textarea class="form-control" name="address" placeholder="Enter Company's Address"></textarea>
                                        <label>Company Addrees</label>
                                        @error('address') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-theme">Add Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>