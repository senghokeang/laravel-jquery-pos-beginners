<div style="text-align: center;">
    <img src="{{url('./images/sourkea.png')}}" height="100px" width="250px" />>
    <i style="font-size: 11px; display: block">
        Address: #111, Steet 999, Somroung Teav, Sen Sok, Phnom Penh. Tel: 089456111, 098456111
    </i>
    <h1 style="padding: 0px; margin: 0px; font-size: 30px">Invoice</h1>
</div>
<hr style="margin-top: 0px; padding-top: 0px" />
<table style="width: 100%; font-size: 12px">
    <thead>
        <tr>
            <td width="80px" style="text-align: right">Table No:</td>
            <td style="text-align: left">{{ $data->name }}</td>
            <td width="80px" style="text-align: right">Invoice #:</td>
            <td style="text-align: left">{{ $data->invoice_no }}</td>
        </tr>
        <tr>
            <td style="width: 60px; text-align: right">Cashier:</td>
            <td style="text-align: left; width: 100px">{{$data->createdBy?->username}}</td>
            <td style="width: 60px; text-align: right">Date:</td>
            <td style="text-align: left; width: 100px">{{ date('d-M-Y H:i:s', strtotime($data->created_at)) }}</td>
        </tr>
    </thead>
</table>
<table style="width: 100%; margin-top: 10px" border="0" cellspacing="0" cellpadding="2px">
    <thead>
        <tr style="background: darkgray">
            <th width="20px">No</th>
            <th style="text-align: left">Description</th>
            <th style="width: 8%; text-align: center">Qty</th>
            <th style="width: 16%; text-align: right">U.P ($)</th>
            <th style="width: 12%; text-align: right">Disc (%)</th>
            <th style="width: 18%; text-align: right">Total ($)
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data->order_detail_temps as $index=>$value)
        <tr style="font-size: 11px">
            <td align="center">{{ $index + 1 }}</td>
            <td align="left">{{ $value->description }}</td>
            <td align="center">{{ $value->qty }}</td>
            <td align="right">{{ number_format($value->unit_price,2) }}</td>
            <td align="right">{{ $value->discount }}</td>
            <td align="right">
                {{ number_format($value->unit_price * $value->qty * (1 - $value->discount / 100),2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<hr />
<table style="font-size: 14px; width: 100%;">
    <tbody>
        @if($data->discount > 0)
        <tr>
            <td style="text-align: right">
                Discount ({{ $data->discount }}%) :
            </td>
            <td style="text-align: right;">
                {{ number_format($data->total * $data->discount / 100,2) }}
            </td>
        </tr>
        @endif
        <tr>
            <th style="text-align: right">Total Amount ($) :</th>
            <th style="text-align: right; width: 100px;">{{ number_format($data->net_amount,2) }}</th>
        </tr>
    </tbody>
</table>