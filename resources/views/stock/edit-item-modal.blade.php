  <!-- modal-lg Modal -->
    <div class="modal fade" id="lgmodal" tabindex="-1" aria-labelledby="lgmodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title h5" id="lgmodalLabel">Update Item Details</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            <form enctype="multipart/form-data" method="POST" action="{{ route('update-item-process') }}" >
                            @csrf
                <div class="modal-body">
                   <div class="row">
                            <div class="col-lg-4">
                               <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <input type="text" name="name" class="form-control" id="itemname"   placeholder="Enter Item">
                                        <label>Name</label>
                                        @error('name') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select class="form-control" name="category_id" id="itemcatname">
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
                                        <select class="form-control" name="unit_of_measure_id" id="itemunitname">
                                            <option value="" selected disabled>--Choose Option--</option>
                                            @foreach ($listunit as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                            @endforeach
                                             
                                        </select>
                                        <label>Unit of measure</label>
                                        @error('unit_of_measure_id') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px">
                                    
                                 
                            <div class="col-md-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select class="form-control" name="store_id" id="itemstorename">
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
                                         <input type="number" id="itemsreorder" name="re_order_level" class="form-control"/>
                                        <label>Reorder Level</label>
                                        @error('re_order_level') <small style="color:red"> {{ $message}}</small> @enderror
                                    </div>
                                </div>
                               
                            </div>
                             <div class="col-lg-4">
                                <div class="form-group mb-3 position-relative check-valid">
                                    <div class="form-floating">
                                        <select class="form-control" name="status" id="statusname">
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
                    <button type="submit" class="btn btn-theme">Save changes</button>
                </div>
                <input type="hidden" name="item_id" id="itemID"/>
            </form>
            
            </div>
        </div>
    </div>