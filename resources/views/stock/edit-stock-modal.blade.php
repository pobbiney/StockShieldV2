 <div class="modal fade" id="xlmodal" tabindex="-1" aria-labelledby="xlmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <p class="modal-title h5" id="xlmodalLabel">Update Stock Details</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form enctype="multipart/form-data" method="POST" action="{{ route('edit-stock-process') }}" >
                            @csrf
                <div class="modal-body">
                    
                        <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <select class=" form-control" name="item" id="skitem" >
                            <option value="" selected disabled>--Select Item--</option>
                                @foreach ($getItemid as $listitems )
                                    <option value="{{ $listitems->id }}">{{ $listitems->name}}</option>
                                @endforeach
                        </select>
                        
                        @error('item') <small style="color:red"> {{ $message}}</small> @enderror  
                    </div>
                    <div class="col-md-4"></div>
                    <div class="row" style="margin-top: 15px">
                        <div class="col-md-4">
                            <div class="form-group mb-3 position-relative check-valid">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="skbatchnumber" name="batch_number" placeholder="Enter Batch Number">
                                    <label>Batch Number</label>
                                    @error('batch_number') <small style="color:red"> {{ $message}}</small> @enderror
                                </div>
                           </div>
                        </div>
                   
                        <div class="col-md-4">
                            <div class="form-group mb-3 position-relative check-valid">
                                <div class="form-floating">
                                    <input type="text" class="form-control datepicker1"  id="skmanufactured"   name="manufacturing_date" placeholder="Enter Manufacturing Date">
                                    <label>Manufacturing Date</label>
                                    
                                </div>
                           </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group mb-3 position-relative check-valid">
                                <div class="form-floating">
                                    <input type="text" class="form-control datepicker2"  id="skexpiryd"   name="expiry_date" placeholder="Enter Expiry Date">
                                    <label>Expiry Date</label>
                                    @error('expiry_date') <small style="color:red"> {{ $message}}</small> @enderror
                                </div>
                           </div>
                        </div>
                   </div> 
                   <div class="row" style="margin-top: 15px">
                    <div class="col-md-4">
                        <div class="form-group mb-3 position-relative check-valid">
                            <div class="form-floating">
                                <select class="form-control" name="supplier" id="sksupplier">
                                    <option value="" selected disabled>--Choose Option--</option>
                                    @foreach ($listsup as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->supplier }}</option>
                                    @endforeach
                                        
                                </select>
                                <label>Supplier</label>
                                @error('supplier') <small style="color:red"> {{ $message}}</small> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                            <div class="form-group mb-3 position-relative check-valid">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="skpurchaseorder" name="purchase_order" placeholder="Enter Purchase Order">
                                    <label>Purchase Order</label>
                                   
                                </div>
                           </div>
                    </div>
                    <div class="col-md-3">
                            <div class="form-group mb-3 position-relative check-valid">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="skwaybill" name="waybill" placeholder="Enter Waybill">
                                    <label>Waybill</label>
                                    @error('waybill') <small style="color:red"> {{ $message}}</small> @enderror
                                </div>
                           </div>
                    </div>
                    <div class="col-md-2">
                            <div class="form-group mb-3 position-relative check-valid">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="skqty" name="quantity" placeholder="Enter Quantity">
                                    <label>Quantity</label>
                                    @error('quantity') <small style="color:red"> {{ $message}}</small> @enderror
                                </div>
                           </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                            <div class="form-group mb-3 position-relative check-valid">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="award_letter" id="skaward" placeholder="Enter Award Letter Reference">
                                    <label>Award Letter Reference</label>
                                    @error('award_letter') <small style="color:red"> {{ $message}}</small> @enderror
                                </div>
                           </div>
                    </div>
                    <div class="col-md-2">
                            <div class="form-group mb-3 position-relative check-valid">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="skamount" name="amount" placeholder="Enter Item Amount">
                                    <label>Unit Cost </label>
                                    @error('amount') <small style="color:red"> {{ $message}}</small> @enderror
                                </div>
                           </div>
                    </div>
                    <div class="col-md-4">
                         <div class="form-group mb-3 position-relative check-valid">
                            <div class="form-floating">
                                <select class="form-control" name="store" id="skstore">
                                    <option value="" selected disabled>--Choose Option--</option>
                                    @foreach ($getstoreId as $liststore)
                                        <option value="{{ $liststore->id }}">{{ $liststore->name }}</option>
                                    @endforeach
                                        
                                </select>
                                <label>Store</label>
                                @error('store') <small style="color:red"> {{ $message}}</small> @enderror
                            </div>
                        </div>
                    </div>
                     <div class="col-md-4">
                            <div class="form-group mb-3 position-relative check-valid">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="skbarcode" name="bar_code" placeholder="Enter Barcode Number">
                                    <label>Barcode</label>
                                  
                                </div>
                           </div>
                    </div>
                    <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-6">
                        <textarea class="form-control" id="skcomment" name="comment" placeholder="Please Enter Comment if any" ></textarea>
                    </div>
                </div><br/>
                </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-theme">Save changes</button>
                </div>
                <input type="hidden" name="stock_id" id="stockID" />
            </form>
        </div>
    </div>
</div>