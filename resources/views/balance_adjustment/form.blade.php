 <div class="modal-header py-2 bg-secondary text-light">
     <h5 class="modal-title" style="font-weight: bold">
         {{ isset($data) ? 'Edit' : 'New' }} Balance Adjustment
     </h5>
 </div>
 <div class="modal-body">
     <form method="POST" id="submitForm" action="{{ url('balance-adjustment/submit') }}">
         @csrf
         @method(isset($data) ? 'PUT' : 'POST')
         <input type="hidden" value="{{ isset($data) ? $data->id : 0 }}" name="id" />
         <div class="row">
             <div class="required mb-3">
                 <label class="form-label">Unit Price</label>
                 <div class="input-group" for="autofocus">
                     <span class="input-group-text">$</span>
                     <input id="autofocus" name="amount" type="text" class="form-control"
                         value="{{ isset($data) ? $data->amount : '' }}" />
                 </div>
             </div>
             <div class="required mb-3">
                 <label for="type_id" class="form-label">Type</label>
                 <select id="type_id" name="type_id" class="form-select">
                     <option value="1"
                         {{ isset($data) && $data->type_id == 1 ? 'selected' : '' }}>
                         Credit (+)
                     </option>
                     <option value="2"
                         {{ isset($data) && $data->type_id == 2 ? 'selected' : '' }}>
                         Debit (-)
                     </option>
                 </select>
             </div>
             <div class="required mb-3">
                 <label for="adjusted_date" class="form-label">Adjust Date</label>
                 <input id="adjusted_date" name="adjusted_date" type="text" class="form-control"
                     value="{{ isset($data) ? Date('Y-m-d', strtotime($data->adjusted_date)) : Date('Y-m-d') }}" />
             </div>
             <div class="required mb-3">
                 <label for="remark" class="form-label">Remark</label>
                 <textarea id="remark" name="remark" class="form-control">{{ isset($data) ? $data->remark : '' }}</textarea>
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
 <script>
     $(document).ready(function() {
         let oldSelectedDate = null;
         flatpickr($('#adjusted_date'), {
             altFormat: "d-M-Y",
             altInput: true,
             onChange: function(selectedDates, dateStr) {
                 if (dateStr)
                     oldSelectedDate = dateStr;
             },
             onClose: function(selectedDates, dateStr, instance) {
                 if (!dateStr)
                     instance.setDate(oldSelectedDate ?? new Date());
             }
         });
     });
 </script>