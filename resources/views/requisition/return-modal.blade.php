<div class="modal fade" id="standardmodal" tabindex="-1" aria-labelledby="standardmodalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" action="{{ route('add-retrun-item-process') }}">
                @csrf
                <div class="modal-header">
                    <p class="modal-title h5" id="standardmodalLabel">Return Item</p> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="form-group mb-3 position-relative check-valid">
                            <div class="form-floating">
                                    <select class="form-control" name="return_status">
                                        <option value="" selected disabled>--Choose Status--</option>
                                        <option value="All">All</option>
                                        <option value="Part">Part</option>
                                    </select>
                                <label>Status</label>
                                @error('return_status') <small style="color:red"> {{ $message}}</small> @enderror
                            </div>
                    </div>
                     <div class="form-group mb-3 position-relative check-valid">
                        <label>Quantity</label>
                            <div class="form-floating">
                                <input type="number" name="quantity" id="itemqty" class="form-control">
                            </div>
                            
                                @error('quantity') <small style="color:red"> {{ $message}}</small> @enderror
                     </div>
                     
                      
                    <div class="form-group mb-3 position-relative check-valid">
                        <label>Comment</label>
                            <div class="form-floating">
                                   <textarea class="form-control" name="comment"></textarea>
                                <label>Reasons</label>
                                @error('comment') <small style="color:red"> {{ $message}}</small> @enderror
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-theme">Save Changes</button>
                </div>
                 <input type="hidden" id="item_id" name="item_id"/>
                 <input type="hidden" id="itembatch" name="batch_number"/>
            </form>
        </div>
    </div>
</div>