<style>
    .table>:not(caption)>*>* {
        padding: 0.5rem 0.5rem !important;
    }
</style>
<div class="card-header p-2 d-none d-lg-block">
    <div class="row row-cols-4 g-1">
        <div>
            <button class="btn btn-secondary btn w-100" onclick="ajaxPopup('cashier/table/0')" title="Select Table"
                id="table_id">{{ $data ? $data->name : 'Table' }}</button>
        </div>
        <div>
            <button disabled title="Change Table" id="change_table"
                onclick="ajaxPopup('cashier/table/{{ $data?->id }}')" class="btn btn-primary w-100 btn">Change</button>
        </div>
        <div>
            <button class="btn btn-warning w-100 btn" title="Print" onclick="ajaxPrint('cashier/print-invoice',{table_id:`{{ $data?->id }}`})" @disabled(!$data || $data->order_detail_temps->count() == 0)
                >Print</button>
        </div>
        <div>
            <button class="btn btn-success w-100 btn" title="Payment" @disabled(!$data || $data->order_detail_temps->count() == 0)
                onclick="ajaxPopup('cashier/make-payment')">Payment</button>
        </div>
    </div>
</div>
<div class="card-body p-0" style="overflow-y: scroll">
    <table class="table">
        <thead>
            <tr class="table-dark">
                <th style="width: 10px" class="pb-1">
                    <input type="checkbox" id="selectAll" onchange="selectAll(event)"
                        style="width: 18px; height: 18px; margin-top: 3px" />
                </th>
                <th>Desc</th>
                <th style="width: 75px">QTY</th>
                <th class="text-end" style="width: 80px">
                    U.P ($)
                </th>
                <th class="text-end" style="width: 75px">DC(%)</th>
                <th class="text-end" style="width: 90px">
                    Total ($)
                </th>
                <th style="width: 10px"></th>
            </tr>
        </thead>
        @if ($data && $data->order_detail_temps->count() > 0)
        <tbody>
            @foreach ($data->order_detail_temps as $value)
            <tr>
                <td class="pb-0">
                    <input type="checkbox" value="{{ $value->id }}" onchange="checkItem()" class="item"
                        style="width: 18px; height: 18px; margin-top: 3px" />
                </td>
                <td> {{ $value->description }} </td>
                <td>
                    <input type="number" style="border: none; appearance: none; background: #e9ecef;"
                        class="form-control p-0 text-center" value="{{ $value->qty }}" min="1"
                        onchange="ajaxSubmit('cashier/update-order-qty',{id:`{{ $value->id }}`,qty: this.value})" />
                </td>
                <td class="text-end">
                    {{ number_format($value->unit_price, 2) }}
                </td>
                <td class="text-end">
                    <input type="number" style="border: none; appearance: none;background: #e9ecef;"
                        class="form-control p-0 text-center w-100" value="{{ $value->discount }}" min="0"
                        max="100"
                        onchange="ajaxSubmit('cashier/update-detail-discount',{id:`{{ $value->id }}`,discount: this.value})" />
                </td>
                <td class="text-end">
                    {{ number_format($value->unit_price * $value->qty * (1 - $value->discount / 100), 2) }}
                </td>
                <td>
                    <i class="bi bi-trash" style="color: red; cursor: pointer"
                        data-record-url="{{ url('cashier/delete-order-product') }}"
                        data-record-id="{{ $value->id }}" title="Delete" data-bs-toggle="modal"
                        data-bs-target="#confirmDelete"></i>
                </td>
            </tr>
            @endforeach
        </tbody>
        @endif
    </table>
</div>
@if ($data && $data->order_detail_temps->count() > 0)
<div class="card-footer p-1 text-dark" style="background: whitesmoke">
    <table class="table mb-0" style="background: whitesmoke">
        <tbody>
            <tr>
                <td class="text-end" style="width: 50px">Discount (%) :</td>
                <td style="width: 100px">
                    <input type="number"
                        style="border: none; appearance: none;background: #e9ecef;;max-width: 50px;"
                        class="form-control p-0 text-center w-100" value="{{ $data->discount }}" min="0"
                        max="100" onchange="ajaxSubmit('cashier/update-discount',{discount: this.value})" />
                </td>
                <th class="text-end" style="width: 100px">Total ($) :</th>
                <th class="text-end text-danger" style="width: 50px;">
                    {{ number_format($data->net_amount,2) }}
                </th>
            </tr>
        </tbody>
    </table>
</div>
@endif