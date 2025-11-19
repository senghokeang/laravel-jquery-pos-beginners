 <div class="modal-header py-2 bg-secondary text-light">
     <h5 class="modal-title" style="font-weight: bold">
         {{ isset($data) ? 'Edit' : 'New' }} Product Category
     </h5>
 </div>
 <div class="modal-body">
     <form method="POST" id="submitForm" action="{{ url('product-category/submit') }}">
         @csrf
         @method(isset($data) ? 'PUT' : 'POST')
         <input type="hidden" value="{{ isset($data) ? $data->id : 0 }}" name="id" />
         <div class="row">
             <div class="required mb-3">
                 <label for="autofocus" class="form-label">Name</label>
                 <input id="autofocus" name="name" type="text" class="form-control"
                     value="{{ isset($data) ? $data->name : '' }}" />
             </div>
         </div>
     </form>
 </div>
 <div class="modal-footer">
     <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
         <i class="bi bi-x-lg"></i> Cancel
     </button>
     <button type="submit" class="btn btn-primary" form="submitForm">
         <i class="bi bi-floppy" style="padding-right: 3px;"></i>Save
     </button>
 </div>