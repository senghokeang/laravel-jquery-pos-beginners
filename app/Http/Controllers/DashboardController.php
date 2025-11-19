<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // Top Products
        $top_products = Order::join('order_details', 'order_details.order_id', '=', 'orders.id')
            ->selectRaw('order_details.description,sum(order_details.qty) AS qty')
            ->where(DB::raw('DATE_FORMAT(orders.created_at,"%Y-%m-%d")'), DB::raw('DATE_FORMAT(CURDATE(),"%Y-%m-%d")'))
            ->groupBy('order_details.description')
            ->orderBy('qty', 'desc')
            ->take(10)->get();

        // Sale by Categories
        $sale_categories = Order::join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('product_categories', 'product_categories.id', '=', 'order_details.product_category_id')
            ->select(
                DB::raw("product_categories.name,sum((order_details.qty * order_details.unit_price*order_details.discount/100) + (order_details.qty * order_details.unit_price * (1-order_details.discount/100) * orders.discount/100)) as discount, sum(order_details.qty * order_details.unit_price) as total")
            )
            ->where(DB::raw('DATE_FORMAT(orders.created_at,"%Y-%m-%d")'), DB::raw('DATE_FORMAT(CURDATE(),"%Y-%m-%d")'))
            ->groupBy(DB::raw('product_categories.name'))
            ->orderBy('product_categories.name')
            ->get();

        // Last 15 days total sale amount
        $days = 15;
        $data = Order::select(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') AS dd, sum(net_amount) AS total"))
            ->where(DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'), '>=', DB::raw('DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY),"%Y-%m-%d")'))
            ->groupBy('dd')
            ->orderBy('dd')
            ->get()->toArray();

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $total = 0;
            foreach ($data as $row) {
                if ($row['dd'] == $date) {
                    $total = $row['total'];
                    break;
                }
            }
            array_push($result, [
                'date' => date('d-M', strtotime($date)),
                'total' => $total
            ]);
        }

        return view('dashboard', compact('result', 'top_products', 'sale_categories'));
    }
}
