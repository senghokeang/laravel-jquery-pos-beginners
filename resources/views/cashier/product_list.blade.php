@foreach ($list as $data)
<div class="col" style="cursor: pointer" onclick="ajaxSubmit('cashier/add-to-order', { id: `{{$data->id}}` })">
    <div class="card h-100 mb-0" style="background: white">
        <div class="card-img-top"
            style="background: url({{ url($data->image ? 'storage/' . $data->image : 'images/default.png') }}) no-repeat center; height:80px">
        </div>
        <div class="card-body" style="font-size: 14px; padding: 3px">
            <p class="card-text text-center mb-1">{{ $data->name }}</p>
            <p class="card-text text-center mb-1" style="color: red">
                ${{ number_format($data->unit_price, 2) }}
            </p>
        </div>
    </div>
</div>
@endforeach