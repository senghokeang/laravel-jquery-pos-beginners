 <div class="modal-header py-2 bg-secondary text-light">
     <h5 class="modal-title" style="font-weight: bold">
         {{ isset($data) ? 'Edit' : 'New' }} Table
     </h5>
 </div>
 <div class="modal-body">
     <form method="POST" id="submitForm" action="{{ url('table/submit') }}">
         @csrf
         @method(isset($data) ? 'PUT' : 'POST')
         <input type="hidden" value="{{ isset($data) ? $data->id : 0 }}" name="id" />
         <div class="row">
             <div class="required mb-3">
                 <label for="autofocus" class="form-label">Name</label>
                 <input id="autofocus" name="name" type="text" class="form-control"
                     value="{{ isset($data) ? $data->name : '' }}" />
             </div>
             <div class="required mb-3">
                 <label for="status" class="form-label">Status</label>
                 <select id="status" name="status" class="form-select">
                     <option value="2" @selected(isset($data) && $data->status == 2)>Free</option>
                     <option value="1" @selected(isset($data) && $data->status == 1)>Busy</option>
                 </select>
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