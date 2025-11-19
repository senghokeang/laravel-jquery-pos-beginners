 <div class="modal-header py-2">
     <h5 class="modal-title" style="font-weight: bold">Make Payment</h5>
 </div>
 <div class="modal-body p-2" style="background: white">
     <table style="width: 100%" cellspacing="0" cellpadding="2px" class="table table-striped mb-0">
         <thead>
             <tr class="table-dark">
                 <th width="20px">No</th>
                 <th style="text-align: left">Description</th>
                 <th style="width: 8%; text-align: center">Qty</th>
                 <th style="width: 16%; text-align: right">U.P($)</th>
                 <th style="width: 15%; text-align: right">Disc(%)</th>
                 <th style="width: 18%; text-align: right">Total($)</th>
             </tr>
         </thead>
         <tbody>
             @foreach ($data->order_detail_temps as $index=>$value)
             <tr>
                 <td align="center">{{ $index + 1 }}</td>
                 <td align="left">{{ $value->description }}</td>
                 <td align="center">{{ $value->qty }}</td>
                 <td align="right">{{ number_format($value->unit_price,2) }}</td>
                 <td align="center">{{ $value->discount }}</td>
                 <td align="right">{{ number_format($value->unit_price * $value->qty * (1 - $value->discount / 100),2)}}</td>
             </tr>
             @endforeach
         </tbody>
     </table>
     <table class="mt-2" style="font-size: 14px; width: 100%;" cellpadding="5px">
         <tbody>
             @if($data->discount > 0)
             <tr>
                 <td style="text-align: right">
                     Discount ({{ $data->discount }}%) :
                 </td>
                 <td style="text-align: right;">
                     {{ number_format(($data->total * $data->discount) / 100,2) }}
                 </td>
             </tr>
             @endif
             <tr>
                 <th style="text-align: right">Total ($) :</th>
                 <th style="text-align: right; width: 100px;">{{ number_format($data->total * (1 - $data->discount/100),2) }}</th>
             </tr>
         </tbody>
     </table>
 </div>
 <div class="modal-footer pt-1" style="display: block">
     <div class="row">
         <div class="col-md-8">
             <form method="POST" id="paymentForm" action="{{ url('cashier/make-payment') }}">
                 @csrf
                 <input type="hidden" name="table_id" value="{{$data->id}}" />
                 <label class="form-label required" for="autofocus">Receive Amount</label>
                 <span id="receive_amount_error" class="col-md-12 text-danger"></span>
                 <div class="row">
                     <div class="col-md-8">
                         <div class="input-group mb-1">
                             <span class="input-group-text">$</span>
                             <input type="text" class="form-control" id="autofocus" name="receive_amount" />
                             <div class="input-group-append">
                                 <button class="btn btn-success" style="border-radius: 0px" type="button"
                                     onclick="$('input[name=receive_amount]').val(`{{number_format($data->total * (1 - $data->discount/100),2)}}`)">
                                     <i class="bi bi-check-lg"></i>
                                 </button>
                             </div>
                         </div>
                     </div>
                 </div>
             </form>
         </div>
         <div class="col-md-4">
             <div class="text-end align-text-bottom pt-lg-4">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                     Cancel</button>&nbsp;
                 <button type="submit" class="btn btn-primary" form="paymentForm">
                     Confirm
                 </button>
             </div>
         </div>
     </div>
 </div>