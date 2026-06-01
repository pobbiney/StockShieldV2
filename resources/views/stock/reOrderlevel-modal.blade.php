 <div class="modal fade" id="standardmodal" tabindex="-1" aria-labelledby="standardmodalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" action="{{ route('add-reorderlevel-process') }}">
                @csrf
                <div class="modal-header">
                    <p class="modal-title h5" id="standardmodalLabel">Set ReOrder Level  for <span id="itemname"></span></p> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                     
                      
                    <div class="form-group mb-3 position-relative check-valid">
                            <div class="form-floating">
                                  <input type="text" id="docname"  name="quantity" class="form-control"  placeholder="Enter Quantity">
                                <label>Quantity</label>
                                @error('quantity') <small style="color:red"> {{ $message}}</small> @enderror
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-theme">Save changes</button>
                </div>
                 <input type="hidden" name="item_id" id="itemID">
            </form>
        </div>
    </div>
</div>