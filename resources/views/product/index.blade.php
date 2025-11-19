<!-- Laravel POS With jQuery @ https://laravelcenter.com -->
<button type="button" class="btn btn-primary" style="float: right" onclick="ajaxPopup(`{{ url('product/form') }}`)">
    <i class="bi bi-plus-circle"></i> Add New
</button>

<div class="pagetitle">
    <h1>Product</h1>
</div>
<section class="section">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <form method="get" id="search_form" action="{{ url('/product') }}">
                    <div class="row pt-4">
                        <div class="col-md-10">
                            <div class="row justify-content-start">
                                <div class="col-lg-3 col-sm-6">
                                    <label class="form-label" for="product_name">Name</label>
                                    <input type="text" id="product_name" name="product_name" class="form-control"
                                        value="{{ session('product_name') }}" placeholder="Search..." />
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <label for="product_category" class="form-label">Category</label>
                                    <select id="product_category" name="product_category" class="form-select">
                                        <option value="0"
                                            {{ session('product_category') == 0 ? 'selected' : '' }}>
                                            ALL
                                        </option>
                                        @foreach ($product_categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ session('product_category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 align-self-end">
                            <button type="submit" class="btn btn-secondary pt-1" style="float: right">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
                <hr class="text-secondary" />
                <table class="table table-striped">
                    <thead>
                        <tr class="table-dark">
                            <th width="50px">#</th>
                            <th width="100px">Image</th>
                            <th style="cursor: pointer"
                                onclick="ajaxLoad(`{{ url('product?product_field=name&product_order=' . (session('product_order') == 'asc' ? 'desc' : 'asc')) }}`)">
                                Name
                                <i
                                    class="text-secondary {{ session('product_field') == 'name' ? (session('product_order') == 'desc' ? 'bi bi-sort-alpha-down-alt' : 'bi bi-sort-alpha-down') : 'bi bi-arrow-down-up' }}"></i>
                            </th>
                            <th style="cursor: pointer"
                                onclick="ajaxLoad(`{{ url('product?product_field=category_name&product_order=' . (session('product_order') == 'asc' ? 'desc' : 'asc')) }}`)">
                                Category
                                <i
                                    class="text-secondary {{ session('product_field') == 'category_name' ? (session('product_order') == 'desc' ? 'bi bi-sort-alpha-down-alt' : 'bi bi-sort-alpha-down') : 'bi bi-arrow-down-up' }}"></i>
                            </th>
                            <th class="text-end" style="cursor: pointer"
                                onclick="ajaxLoad(`{{ url('product?product_field=unit_price&product_order=' . (session('product_order') == 'asc' ? 'desc' : 'asc')) }}`)">
                                Unit Price
                                <i
                                    class="text-secondary {{ session('product_field') == 'unit_price' ? (session('product_order') == 'desc' ? 'bi bi-sort-alpha-down-alt' : 'bi bi-sort-alpha-down') : 'bi bi-arrow-down-up' }}"></i>
                            </th>
                            <th style="cursor: pointer"
                                onclick="ajaxLoad(`{{ url('product?product_field=created_at&product_order=' . (session('product_order') == 'asc' ? 'desc' : 'asc')) }}`)">
                                Created at
                                <i
                                    class="text-secondary {{ session('product_field') == 'created_at' ? (session('product_order') == 'desc' ? 'bi bi-sort-alpha-down-alt' : 'bi bi-sort-alpha-down') : 'bi bi-arrow-down-up' }}"></i>
                            </th>
                            <th class="text-center" width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($list) && count($list) > 0)
                        @foreach ($list as $index => $value)
                        <tr>
                            <th style="vertical-align: middle;text-align: center">
                                {{ $list->perPage() * ($list->currentPage() - 1) + ($index + 1) }}
                            </th>
                            <td>
                                <img src="{{ url($value->image ? 'storage/' . $value->image : 'images/default.png') }}"
                                    style="height: 40px;" />
                            </td>
                            <td style="vertical-align: middle">{{ $value->name }}</td>
                            <td style="vertical-align: middle">{{ $value->category_name }}</td>
                            <td class="text-end" style="vertical-align: middle">
                                ${{ number_format($value->unit_price, 2) }}
                            </td>
                            <td style="vertical-align: middle">
                                {{ date('d-M-Y H:i:s', strtotime($value->created_at)) }}
                            </td>
                            <td style="vertical-align: middle;text-align: center;">
                                <i class="bi bi-trash3-fill text-danger" role="button"
                                    data-record-url="{{ url('product/delete') }}"
                                    data-record-id="{{ $value->id }}" title="Delete"
                                    data-bs-toggle="modal" data-bs-target="#confirmDelete"></i>
                                <a title="Edit"
                                    href="javascript:ajaxPopup('{{ url('product/form/' . $value->id) }}')">
                                    <i class="bi bi-pencil-square text-success ps-3" role="button"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr v-else>
                            <td colspan="10" class="shadow-none">
                                No record found
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination justify-content-end">
                            {{ $list->links() }}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>