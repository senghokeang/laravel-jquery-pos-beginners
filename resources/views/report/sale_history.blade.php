    <button type="button" class="btn btn-success" style="float: right" onclick="exportToExcel()">
        <i class="bi bi-file-earmark-excel"></i> Export to Excel
    </button>

    <div class="pagetitle">
        <h1>Sale History</h1>
    </div>
    <section class="section">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <form method="get" id="search_form" action="{{ url('/report/sale-history') }}">
                        <div class="row pt-4">
                            <div class="col-md-10">
                                <div class="row justify-content-start">
                                    <div class="col-lg-3 col-sm-6">
                                        <label class="form-label" for="sale_history_invoice_no">Invoice#</label>
                                        <input type="text" id="sale_history_invoice_no"
                                            name="sale_history_invoice_no" class="form-control"
                                            value="{{ session('sale_history_invoice_no') }}" placeholder="Search..." />
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <label class="form-label" for="sale_history_fd">From Date</label>
                                        <input type="text" id="sale_history_fd" name="sale_history_fd"
                                            value="{{ session('sale_history_fd') }}" class="form-control" />
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <label class="form-label" for="sale_history_td">To Date</label>
                                        <input type="text" id="sale_history_td" name="sale_history_td"
                                            value="{{ session('sale_history_td') }}" class="form-control" />
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
                    <table class="table">
                        <thead>
                            <tr class="table-dark">
                                <th class="text-center">
                                    Total Amount
                                </th>
                                <th class="text-center">
                                    Total Discount
                                </th>
                                <th class="text-center">
                                    Net Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th class="text-center text-primary">
                                    ${{ number_format($sale_summary->grand_total, 2) }}
                                </th>
                                <th class="text-center text-danger">
                                    ${{ number_format($sale_summary->total_discount, 2) }}
                                </th>
                                <th class="text-center text-success">
                                    ${{ number_format($sale_summary->net_amount, 2) }}
                                </th>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-striped">
                        <thead>
                            <tr class="table-dark">
                                <th style="width: 50px">#</th>
                                <th style="cursor: pointer"
                                    onclick="ajaxLoad(`{{ url('report/sale-history?sale_history_field=orders.invoice_no&sale_history_order=' . (session('sale_history_order') == 'asc' ? 'desc' : 'asc')) }}`)">
                                    Invoice No
                                    <i
                                        class="text-secondary {{ session('sale_history_field') == 'orders.invoice_no' ? (session('sale_history_order') == 'desc' ? 'bi bi-sort-alpha-down-alt' : 'bi bi-sort-alpha-down') : 'bi bi-arrow-down-up' }}"></i>
                                </th>
                                <th style="cursor: pointer" class="text-center"
                                    onclick="ajaxLoad(`{{ url('report/sale-history?sale_history_field=tables.name&sale_history_order=' . (session('sale_history_order') == 'asc' ? 'desc' : 'asc')) }}`)">
                                    Table No
                                    <i
                                        class="text-secondary {{ session('sale_history_field') == 'tables.name' ? (session('sale_history_order') == 'desc' ? 'bi bi-sort-alpha-down-alt' : 'bi bi-sort-alpha-down') : 'bi bi-arrow-down-up' }}"></i>
                                </th>
                                <th style="cursor: pointer" class="text-end"
                                    onclick="ajaxLoad(`{{ url('report/sale-history?sale_history_field=orders.grand_total&sale_history_order=' . (session('sale_history_order') == 'asc' ? 'desc' : 'asc')) }}`)">
                                    Total Amount
                                    <i
                                        class="text-secondary {{ session('sale_history_field') == 'orders.grand_total' ? (session('sale_history_order') == 'desc' ? 'bi bi-sort-alpha-down-alt' : 'bi bi-sort-alpha-down') : 'bi bi-arrow-down-up' }}"></i>
                                </th>
                                <th style="cursor: pointer" class="text-end"
                                    onclick="ajaxLoad(`{{ url('report/sale-history?sale_history_field=orders.total_discount&sale_history_order=' . (session('sale_history_order') == 'asc' ? 'desc' : 'asc')) }}`)">
                                    Discount Amount
                                    <i
                                        class="text-secondary {{ session('sale_history_field') == 'orders.total_discount' ? (session('sale_history_order') == 'desc' ? 'bi bi-sort-alpha-down-alt' : 'bi bi-sort-alpha-down') : 'bi bi-arrow-down-up' }}"></i>
                                </th>
                                <th style="cursor: pointer" class="text-end"
                                    onclick="ajaxLoad(`{{ url('report/sale-history?sale_history_field=orders.net_amount&sale_history_order=' . (session('sale_history_order') == 'asc' ? 'desc' : 'asc')) }}`)">
                                    Net Amount
                                    <i
                                        class="text-secondary {{ session('sale_history_field') == 'orders.net_amount' ? (session('sale_history_order') == 'desc' ? 'bi bi-sort-alpha-down-alt' : 'bi bi-sort-alpha-down') : 'bi bi-arrow-down-up' }}"></i>
                                </th>
                                <th style="cursor: pointer"
                                    onclick="ajaxLoad(`{{ url('report/sale-history?sale_history_field=orders.created_at&sale_history_order=' . (session('sale_history_order') == 'asc' ? 'desc' : 'asc')) }}`)">
                                    Date
                                    <i
                                        class="text-secondary {{ session('sale_history_field') == 'orders.created_at' ? (session('sale_history_order') == 'desc' ? 'bi bi-sort-alpha-down-alt' : 'bi bi-sort-alpha-down') : 'bi bi-arrow-down-up' }}"></i>
                                </th>
                                <th>
                                    Cashier
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($list->count() > 0)
                            @foreach ($list as $index => $value)
                            <tr>
                                <th scope="row">
                                    {{ $list->perPage() * ($list->currentPage() - 1) + ($index + 1) }}
                                </th>
                                <td><button class="btn btn-link p-0"
                                        onclick="ajaxPopup('report/show-order-detail/{{ $value->id }}',true)">{{ $value->invoice_no }}</button>
                                </td>
                                <td class="text-center">{{ $value->table_name }}</td>
                                <td class="text-end">${{ $value->grand_total }}</td>
                                <td class="text-end">${{ $value->total_discount }}</td>
                                <td class="text-end">${{ number_format($value->net_amount, 2) }}</td>
                                <td>{{ date('d-M-Y H:i:s', strtotime($value->created_at)) }}</td>
                                <td class="text-capitalize">{{ $value->cashier }}</td>
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
            const $start = $("#sale_history_fd");
            const $end = $("#sale_history_td");

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
                    url: "/report/export-sale-history",
                    method: "GET",
                    dataType: "json",
                    success: function(data) {
                        const headers = ['Invoice No', 'Table Name', 'Grand Total', 'Total Discount', 'Net Amount', 'Order Date', 'Cashier'];
                        const response = [headers, ...data.map(row => [
                            row.invoice_no,
                            row.table_name,
                            row.grand_total,
                            row.total_discount,
                            row.net_amount,
                            row.order_date,
                            row.cashier,
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
                            a.download = "Sale History Report.xlsx";
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        });
                    },
                    error: function(xhr, status, error) {
                        showError(xhr.responseJSON.message);
                    }
                });
            };
        });
    </script>