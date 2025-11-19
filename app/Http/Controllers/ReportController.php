<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function saleSummary(Request $request)
    {
        session()->put('sale_summary_fd', $request->get('sale_summary_fd', session('sale_summary_fd', date('Y-m-d'))));
        session()->put('sale_summary_td', $request->get('sale_summary_td', session('sale_summary_td', date('Y-m-d'))));

        $list = Order::join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('product_categories', 'product_categories.id', '=', 'order_details.product_category_id')
            ->select(DB::raw("product_categories.name,sum((order_details.qty * order_details.unit_price*order_details.discount/100) + (order_details.qty * order_details.unit_price * (1-order_details.discount/100) * orders.discount/100)) as discount, sum(order_details.qty * order_details.unit_price) as total"))
            ->when(session('sale_summary_fd'), function ($query) {
                $query->where('orders.created_at', '>=', date('Y-m-d 00:00:00', strtotime(session('sale_summary_fd'))));
            })
            ->when(session('sale_summary_td'), function ($query) {
                $query->where('orders.created_at', '<=', date('Y-m-d 23:59:59', strtotime(session('sale_summary_td'))));
            })
            ->groupBy(DB::raw('product_categories.name'))
            ->orderBy('product_categories.name', 'DESC')
            ->get();

        // return back to compoment
        return view('report.sale_summary', compact('list'));
    }

    public function productSummary(Request $request)
    {
        // get param value
        session()->put('product_summary_category_id', $request->get('product_summary_category_id', session('product_summary_category_id', 0)));
        session()->put('product_summary_fd', $request->get('product_summary_fd', session('product_summary_fd', date('Y-m-d'))));
        session()->put('product_summary_td', $request->get('product_summary_td', session('product_summary_td', date('Y-m-d'))));

        // select from table with filter, sort, and paginate
        $list = Order::join('order_details', 'order_details.order_id', '=', 'orders.id')->join('product_categories', 'product_categories.id', '=', 'order_details.product_category_id')
            ->selectRaw('order_details.description,order_details.product_category_id,product_categories.name AS category_name,sum(order_details.qty) AS qty')
            ->when(session('product_summary_fd'), function ($query) {
                $query->where('orders.created_at', '>=', date('Y-m-d 00:00:00', strtotime(session('product_summary_fd'))));
            })
            ->when(session('product_summary_td'), function ($query) {
                $query->where('orders.created_at', '<=', date('Y-m-d 23:59:59', strtotime(session('product_summary_td'))));
            })
            ->when(session('product_summary_category_id'), function ($query) {
                $query->where('order_details.product_category_id',  session('product_summary_category_id'));
            })
            ->groupBy('order_details.description', 'order_details.product_category_id', 'product_categories.name')
            ->orderBy(DB::raw('sum(order_details.qty)'), 'DESC')
            ->paginate(50);

        // product category list
        $product_categories = ProductCategory::all(['id', 'name']);
        // return back to compoment
        return view('report.product_summary', compact('list', 'product_categories'));
    }

    public function exportProductSummary()
    {
        // select from table with filter, sort, and paginate
        $list = Order::join('order_details', 'order_details.order_id', '=', 'orders.id')->join('product_categories', 'product_categories.id', '=', 'order_details.product_category_id')
            ->selectRaw('order_details.description,order_details.product_category_id,product_categories.name AS category_name,sum(order_details.qty) AS qty')
            ->when(session('product_summary_fd'), function ($query) {
                $query->where('orders.created_at', '>=', date('Y-m-d 00:00:00', strtotime(session('product_summary_fd'))));
            })
            ->when(session('product_summary_td'), function ($query) {
                $query->where('orders.created_at', '<=', date('Y-m-d 23:59:59', strtotime(session('product_summary_td'))));
            })
            ->when(session('product_summary_category_id'), function ($query) {
                $query->where('order_details.product_category_id',  session('product_summary_category_id'));
            })
            ->groupBy('order_details.description', 'order_details.product_category_id', 'product_categories.name')
            ->orderBy(DB::raw('sum(order_details.qty)'), 'DESC')
            ->get();

        // return back to compoment
        return response()->json($list);
    }

    public function saleHistory(Request $request)
    {
        // get param value
        session()->put('sale_history_invoice_no', $request->get('sale_history_invoice_no', session('sale_history_invoice_no')));
        session()->put('sale_history_fd', $request->get('sale_history_fd', session('sale_history_fd', date('Y-m-d'))));
        session()->put('sale_history_td', $request->get('sale_history_td', session('sale_history_td', date('Y-m-d'))));
        session()->put('sale_history_field', $request->get('sale_history_field', session('sale_history_field', 'orders.created_at')));
        session()->put('sale_history_order', $request->get('sale_history_order', session('sale_history_order', 'desc')));

        $list = Order::join('tables', 'tables.id', '=', 'orders.table_id')
            ->join('users', 'users.id', '=', 'orders.created_by_id')
            ->select(
                'orders.invoice_no',
                'tables.name AS table_name',
                'orders.grand_total',
                'orders.total_discount',
                'orders.net_amount',
                'orders.id',
                'orders.created_at',
                DB::raw('users.username AS cashier')
            )
            ->when(session('sale_history_fd'), function ($query) {
                $query->where('orders.created_at', '>=', date('Y-m-d 00:00:00', strtotime(session('sale_history_fd'))));
            })
            ->when(session('sale_history_td'), function ($query) {
                $query->where('orders.created_at', '<=', date('Y-m-d 23:59:59', strtotime(session('sale_history_td'))));
            })
            ->when(session('sale_history_invoice_no'), function ($query) {
                $query->where('orders.invoice_no', 'like', '%' . session('sale_history_invoice_no') . '%');
            })
            ->orderBy(session('sale_history_field'), session('sale_history_order'))
            ->paginate(50);

        $sale_summary = $this->saleHistorySummary($request);
        // return back to compoment
        return view('report.sale_history', compact('list', 'sale_summary'));
    }

    private function saleHistorySummary()
    {
        $data = Order::select(DB::raw("sum(grand_total) as grand_total, sum(total_discount) as total_discount, sum(net_amount) as net_amount"))
            ->when(session('sale_history_fd'), function ($query) {
                $query->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime(session('sale_history_fd'))));
            })
            ->when(session('sale_history_td'), function ($query) {
                $query->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime(session('sale_history_td'))));
            })
            ->when(session('sale_history_invoice_no'), function ($query) {
                $query->where('invoice_no', 'like', '%' . session('sale_history_invoice_no') . '%');
            })->first();

        return $data;
    }

    public function exportSaleHistory()
    {
        $data = Order::join('tables', 'tables.id', '=', 'orders.table_id')
            ->join('users', 'users.id', '=', 'orders.created_by_id')
            ->select(
                'orders.invoice_no',
                'tables.name AS table_name',
                DB::raw('DATE_FORMAT(orders.created_at, "%d-%b-%Y %H:%i:%s") AS order_date'),
                'orders.grand_total',
                'orders.total_discount',
                'orders.net_amount',
                'orders.created_by_id',
                DB::raw('users.username AS cashier')
            )
            ->when(session('sale_history_fd'), function ($query) {
                $query->where('orders.created_at', '>=', date('Y-m-d 00:00:00', strtotime(session('sale_history_fd'))));
            })
            ->when(session('sale_history_td'), function ($query) {
                $query->where('orders.created_at', '<=', date('Y-m-d 23:59:59', strtotime(session('sale_history_td'))));
            })
            ->when(session('sale_history_invoice_no'), function ($query) {
                $query->where('orders.invoice_no', 'like', '%' . session('sale_history_invoice_no') . '%');
            })
            ->orderBy(session('sale_history_field'), session('sale_history_order'))
            ->get();

        return response()->json($data);
    }
    public function showOrderDetail($id)
    {
        $data = Order::with('order_details')->join('tables', 'tables.id', '=', 'orders.table_id')
            ->join('users', 'users.id', '=', 'orders.created_by_id')
            ->select(
                'orders.total',
                'orders.net_amount',
                'orders.discount',
                'orders.invoice_no',
                'tables.name AS table_name',
                'orders.created_at',
                'orders.id',
                'orders.created_by_id',
                'orders.receive_amount',
                DB::raw('users.username AS cashier')
            )
            ->find($id);

        return view('report.order_detail', compact('data'));
    }
}
