<!-- Laravel POS With jQuery @ https://laravelcenter.com -->
<div class="pagetitle">
    <h1>Dashboard</h1>
</div>
@php
$total_amount = 0;
$total_discount = 0;
$net_amount = 0;
$pie_labels = [];
$pie_series = [];
if (isset($sale_categories) && $sale_categories->count() > 0) {
foreach ($sale_categories as $value) {
$total_amount += $value->total;
$total_discount += $value->discount;
array_push($pie_labels, $value->name);
array_push($pie_series, $value->total - $value->discount);
}
$net_amount = $total_amount - $total_discount;
}
@endphp
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="me-2">
                        <div class="display-6 text-white">
                            $ {{ number_format($total_amount, 2) }}
                        </div>
                        <div class="card-text fs-6 mt-2">Daily Total Sale</div>
                    </div>
                    <div style="color: lightblue"><i class="bi bi-cash-stack display-4"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow bg-danger text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="me-2">
                        <div class="display-6 text-white">$ {{ number_format($total_discount, 2) }}</div>
                        <div class="card-text fs-6 mt-2">Daily Total Disount</div>
                    </div>
                    <div style="color: lightgray;"><i class="bi bi-percent display-4"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow bg-success text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="me-2">
                        <div class="display-6 text-white">$ {{ number_format($net_amount, 2) }}</div>
                        <div class="card-text fs-6 mt-2">Daily Net Amount</div>
                    </div>
                    <div style="color: lightblue"><i class="bi bi-coin display-4"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow mb-3">
            <div class="card-header bg-dark text-white py-2">
                <i class="fas fa-chart-area me-1"></i>
                Daily Sale Report By Categories
            </div>
            <div class="card-body py-1">
                <div id="pie_chart"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <table class="table table-hover shadow align-middle bg-white">
            <thead>
                <tr class="table-dark">
                    <th style="width: 50px" class="text-center">#</th>
                    <th>Product Name</th>
                    <th class="text-center">QTY</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($top_products as $index => $value)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $value->description }}</td>
                    <td class="text-center">
                        {{ $value->qty }}
                    </td>
                </tr>
                @endforeach
                @for ($i = 1; $i <= 10 - $top_products->count(); $i++)
                    <tr>
                        <td class="text-center">{{ $top_products->count() + $i }}</td>
                        <td></td>
                        <td class="text-center"></td>
                    </tr>
                    @endfor
            </tbody>
        </table>
    </div>
</div>
<div class="col mb-5">
    <div class="card shadow mb-3">
        <div class="card-header bg-dark text-white py-2">
            <i class="fas fa-chart-bar me-1"></i>
            15 Days Total Sale Amount
        </div>
        <div class="card-body">
            <div id="bar_chart"></div>
        </div>
    </div>
</div>
<script>
    // Pie Chart
    var options = {
        fill: {
            colors: ['#3366cc', '#660066', '#006600', '#cc0066', '#996633', '#006666', '#993399', '#999966',
                '#ffcc99', '#33cc33', '#cccc00'
            ]
        },
        series: <?php echo json_encode($pie_series); ?>,
        chart: {
            height: 400,
            type: 'pie',
        },
        labels: <?php echo json_encode($pie_labels); ?>,
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val.toFixed(2) + "%"
            },
            dropShadow: {}
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function(value) {
                    try {
                        if (typeof value !== "number") {
                            if (value && !isNaN(value)) {
                                value = parseFloat(value);
                            } else {
                                return value;
                            }
                        }
                        var formatter = new Intl.NumberFormat("en-US", {
                            style: "decimal",
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        });
                        return "$" + formatter.format(value);
                    } catch (ex) {
                        return "$" + value;
                    }
                }
            }
        },
    };

    var chart = new ApexCharts(document.querySelector("#pie_chart"), options);
    chart.render();

    // Bar Chart

    var options = {
        series: [{
            name: 'Net Amount',
            data: <?php echo json_encode(array_column($result, 'total')); ?>
        }],
        chart: {
            height: 350,
            type: 'bar',
        },
        plotOptions: {
            bar: {
                borderRadius: 10,
                dataLabels: {
                    position: 'top', // top, center, bottom
                },
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return "$" + val;
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#304758"]
            }
        },

        xaxis: {
            categories: <?php echo json_encode(array_column($result, 'date')); ?>,
            position: 'top',
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            crosshairs: {
                fill: {
                    type: 'gradient',
                    gradient: {
                        colorFrom: '#D8E3F0',
                        colorTo: '#BED1E6',
                        stops: [0, 100],
                        opacityFrom: 0.4,
                        opacityTo: 0.5,
                    }
                }
            },
            tooltip: {
                enabled: true,
            }
        },
        yaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false,
            },
            labels: {
                show: false,
                formatter: function(val) {
                    return "$" + val;
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#bar_chart"), options);
    chart.render();
</script>