<div class="modal-header py-2 text-bg-secondary">
    <h5 class="modal-title" style="font-weight: bold">Select Table</h5>
</div>
<div class="modal-body">
    <div class="row row-cols-3 row-cols-sm-4 row-cols-xl-5 g-2">
        @foreach ($list as $value)
        <div>
            <div class="p-3 fs-2 text-center fw-bold w-100 {{ $value->status == 1 ? 'text-bg-danger' : ($value->status == 2 ? 'text-bg-secondary' : 'text-bg-success') }}"
                style="cursor: pointer" onclick="selectTable(`{{ $value->id }}`, `{{ $old_table_id }}`)">
                {{ $value->name }}
            </div>
        </div>
        @endforeach
    </div>
</div>
<div class="modal-footer">
    <div class="px-2 py-1 text-bg-secondary" style="text-align: right">Free</div>
    <div class="px-2 py-1 text-bg-danger" style="text-align: right">Busy</div>
    <div class="px-2 py-1 text-bg-success" style="text-align: right">Printed</div>
</div>