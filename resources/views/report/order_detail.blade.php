<div class="modal-header py-2 text-bg-secondary">
    <h4 class="modal-title" style="font-weight: bold">Order Detail</h4>
</div>
<div class="modal-body">
    <table class="table">
        <tbody>
            <tr>
                <td width="80px" style="text-align: right">Table No:</td>
                <td style="text-align: left">{{ $data->table_name }}</td>
                <td width="80px" style="text-align: right">Invoice #:</td>
                <td style="text-align: left">{{ $data->invoice_no }}</td>
            </tr>
            <tr>
                <td style="width: 60px; text-align: right">Cashier:</td>
                <td style="text-align: left; width: 100px" class="text-capitalize">{{ $data->cashier }}</td>
                <td style="width: 60px; text-align: right">Date:</td>
                <td style="text-align: left; width: 100px">{{ date('d-M-Y H:i:s',strtotime($data->created_at)) }}</td>
            </tr>
        </tbody>
    </table>
    @php
    $grand_total = 0;
    @endphp
    <table class="table">
        <thead>
            <tr class="table-dark">
                <th>No</th>
                <th>Descripiton</th>
                <th class="text-center">QTY</th>
                <th class="text-end">Unit Price ($)</th>
                <th class="text-end">Discount (%)</th>
                <th class="text-end">Total ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->order_details as $index=>$value)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $value->description }}</td>
                <td class="text-center">{{ $value->qty }}</td>
                <td class="text-end">{{ number_format($value->unit_price,2)}}</td>
                <td class="text-end">{{ $value->discount }}</td>
                <td class="text-end">
                    {{ number_format($value->unit_price * $value->qty * (1 - $value->discount / 100), 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <hr />
    <table class="table">
        <tbody>
            @if($data->discount > 0)
            <tr>
                <td style="text-align: right">
                    Discount ({{ $data->discount }}%) :
                </td>
                <td style="text-align: right;">
                    {{ number_format($data->total * $data->discount / 100, 2) }}
                </td>
            </tr>
            @endif
            <tr>
                <th style="text-align: right">Total Amount($) :</th>
                <th style="text-align: right; width: 100px;">{{ number_format($data->net_amount,2) }}</th>
            </tr>
            <tr>
                <td style="text-align: right">Receive Amount($) :</td>
                <td style="text-align: right">{{ number_format($data->receive_amount,2) }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
        <i class="bi bi-x-lg"></i> Cancel
    </button>
</div>