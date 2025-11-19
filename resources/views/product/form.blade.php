 <div class="modal-header py-2 bg-secondary text-light">
     <h5 class="modal-title" style="font-weight: bold">
         {{ isset($data) ? 'Edit' : 'New' }} Table
     </h5>
 </div>
 <div class="modal-body">
     <form method="POST" id="submitForm" enctype="multipart/form-data" action="{{ url('product/submit') }}">
         @csrf
         @method(isset($data) ? 'PUT' : 'POST')
         <input type="hidden" value="{{ isset($data) ? $data->id : 0 }}" name="id" />
         <div class="row">
             <div class="mb-3">
                 <label class="form-label">Image</label>
                 <div style="position: relative; width: 40%;">
                     <i class="bi bi-x-circle fs-3 m-0 p-0 text-danger"
                         style="position: absolute; right: 5px;top: -2px; cursor: pointer;" onclick="removeImage"></i>
                     <img id="img_preview"
                         src="{{ url(isset($data) && $data->image ? 'storage/' . $data->image : 'images/default.png') }}"
                         style="width: 100%;cursor: pointer;" class="img-thumbnail" onclick="$('#image').click()" />
                     <input type="hidden" style="display: none" value="0" name="is_deleted_image"
                         id="is_deleted_image" />
                 </div>
                 <input type="file" id="image" name="image" style="display: none;"
                     accept=".jpg,.jpeg,.bmp,.gif,.png,.webp" />
             </div>
             <div class="required mb-3">
                 <label for="autofocus" class="form-label">Name</label>
                 <input id="autofocus" name="name" type="text" class="form-control"
                     value="{{ isset($data) ? $data->name : '' }}" />
             </div>
             <div class="required mb-3">
                 <label for="product_category_id" class="form-label">Category</label>
                 <select id="product_category_id" name="product_category_id" class="form-select">
                     @foreach ($product_categories as $category)
                     <option value="{{ $category->id }}"
                         {{ isset($data) && $data->product_category_id == $category->id ? 'selected' : '' }}>
                         {{ $category->name }}
                     </option>
                     @endforeach
                 </select>
             </div>
             <div class="required mb-3">
                 <label class="form-label">Unit Price</label>
                 <div class="input-group" for="unit_price">
                     <span class="input-group-text">$</span>
                     <input id="unit_price" name="unit_price" type="text" class="form-control"
                         value="{{ isset($data) ? $data->unit_price : '' }}" />
                 </div>
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