 <div class="modal fade" id="xlmodal" tabindex="-1" aria-labelledby="xlmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <p class="modal-title h5" id="xlmodalLabel">Add New Stock</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form enctype="multipart/form-data" method="POST" action="{{ route('add-supplier-process') }}" >
                            @csrf
                <div class="modal-body">
                    
                        <div class="row">
                            
                             <div class="col-md-4">
                                        <select class="js-example-basic-single form-control" name="item" style="width: 100%;">
                                            <option value="" selected disabled>--Select Item--</option>
                                              @foreach ($getItemid as $listitems )
                                                  <option value="{{ $listitems->id }}">{{ $listitems->name}}</option>
                                              @endforeach
                                        </select>
                                        
                                        @error('item') <small style="color:red"> {{ $message}}</small> @enderror
                                
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