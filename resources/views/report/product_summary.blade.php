    <button type="button" class="btn btn-success" style="float: right" onclick="exportToExcel()">
        <i class="bi bi-file-earmark-excel"></i> Export to Excel
    </button>

    <div class="pagetitle">
        <h1>Product Summary</h1>
    </div>
    <section class="section">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <form method="get" id="search_form" action="{{ url('/report/product-summary') }}">
                        <div class="row pt-4">
                            <div class="col-md-10">
                                <div class="row justify-content-start">
                                    <div class="col-lg-3 col-sm-6">
                                        <label for="product_summary_category_id" class="form-label">Category</label>
                                        <select id="product_summary_category_id" name="product_summary_category_id" class="form-select">
                                            <option value="0"
                                                {{ session('product_summary_category_id') == 0 ? 'selected' : '' }}>
                                                ALL
                                            </option>
                                            @foreach ($product_categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ session('product_summary_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <label class="form-label" for="product_summary_fd">From Date</label>
                                        <input type="text" id="product_summary_fd" name="product_summary_fd" value="{{session('product_summary_fd')}}" class="form-control" />
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <label class="form-label" for="product_summary_td">To Date</label>
                                        <input type="text" id="product_summary_td" name="product_summary_td" value="{{session('product_summary_td')}}" class="form-control" />
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
                                <th style="width: 50px">#</th>
                                <th>Product Name</th>
                                <th>Product Category</th>
                                <th class="text-end">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($list->count()>0)
                            @foreach($list as $index => $value)
                            <tr>
                                <th scope="row">{{ $list->perPage() * ($list->currentPage() - 1) + ($index + 1) }}</th>
                                <td>{{ $value->description }}</td>
                                <td>{{ $value->category_name }}</td>
                                <td class="text-end">{{ $value->qty }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="10" class="shadow-none">
                                    No record found
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end">
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
    <script>
        $(document).ready(function() {
            const $start = $("#product_summary_fd");
            const $end = $("#product_summary_td");

            const startPicker = flatpickr($start, {
                altFormat: "d-M-Y",
                altInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    if (dateStr) {
                        endPicker.set('minDate', dateStr);
                    }
                }
            });

            const endPicker = flatpickr($end, {
                altFormat: "d-M-Y",
                altInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    if (dateStr) {
                        startPicker.set('maxDate', dateStr);
                    }
                }
            });

            window.exportToExcel = () => {
                $.ajax({
                    url: "/report/export-product-summary",
                    method: "GET",
                    dataType: "json",
                    success: function(data) {
                        const headers = ['Product Name', 'Product Category', 'Quantity'];
                        const response = [headers, ...data.map(row => [
                            row.description,
                            row.category_name,
                            row.qty
                        ])];

                        XlsxPopulate.fromBlankAsync().then(workbook => {
                            const sheet = workbook.sheet(0);
                            response.forEach((row, i) => {
                                row.forEach((cell, j) => {
                                    sheet.cell(i + 1, j + 1).value(cell);
                                });
                            });
                            return workbook.outputAsync();
                        }).then(blob => {
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement("a");
                            a.href = url;
                            a.download = "Product Summary Report.xlsx";
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        });
                    },
                    error: function(xhr, status, error) {
                        $('.modal-body p', errorModalId).text(xhr.responseJSON.message);
                        errorModal.show();
                    }
                });
            };
        });
    </script>