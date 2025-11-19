<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailTemp;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Table;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        $product_categories = ProductCategory::select('id', 'name')->orderBy('ordering')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('layout.cashier', compact('products', 'product_categories'));
    }

    public function product(Request $request)
    {
        $list = Product::when($request->category > 0, function ($query) use ($request) {
            $query->where('product_category_id', $request->category);
        })->when($request->search > 0, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
        })->orderBy('name')->get();

        return view('cashier.product_list', compact('list'));
    }

    public function table($id = 0)
    {
        $old_table_id = $id;
        $list = Table::select('id', 'name', 'status')->where('id', '!=', $id)->orderBy('name')->get();
        return view('cashier.table_list', compact('list', 'old_table_id'));
    }

    public function selectTable(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($request->old_table_id > 0 && $request->ids) {
                $old_table = Table::with('order_detail_temps')->find($request->old_table_id);
                Table::where('id', $request->new_table_id)->update(['status' => 1, 'discount' => $old_table->discount]);
                if ($old_table->order_detail_temps->count() == 0) {
                    $old_table->status = 2;
                    $old_table->discount = 0;
                    $old_table->total_discount = 0;
                    $old_table->grand_total = 0;
                    $old_table->total = 0;
                    $old_table->net_amount = 0;
                } else {
                    $old_table->order_detail_temps()->whereIn('id', explode(',', $request->ids))->update(['table_id' => $request->new_table_id]);
                    $grand_total = $old_table->order_detail_temps->whereNotIn('id', explode(',', $request->ids))->sum(function ($detail) {
                        return $detail->qty * $detail->unit_price;
                    });
                    $total = $old_table->order_detail_temps->whereNotIn('id', explode(',', $request->ids))->sum(function ($detail) {
                        return $detail->qty * $detail->unit_price * (1 - $detail->discount / 100);
                    });
                    $total_discount = $grand_total - $total + ($total * $old_table->discount / 100);
                    $old_table->grand_total = $grand_total;
                    $old_table->total = $total;
                    $old_table->total_discount = $total_discount;
                    $old_table->net_amount = $grand_total - $total_discount;
                }
                $old_table->save();
            }
            if ($request->new_table_id > 0)
                session()->put('table_id', $request->new_table_id);
            $data = Table::with('order_detail_temps')->find(session('table_id'));
            if ($data && $data->order_detail_temps->count() > 0) {
                $grand_total = $data->order_detail_temps->sum(function ($detail) {
                    return $detail->qty * $detail->unit_price;
                });

                $total = $data->order_detail_temps->sum(function ($detail) {
                    return $detail->qty * $detail->unit_price * (1 - $detail->discount / 100);
                });
                $total_discount = $grand_total - $total + ($total * $data->discount / 100);
                $data->grand_total = $grand_total;
                $data->total = $total;
                $data->total_discount = $total_discount;
                $data->net_amount = $grand_total - $total_discount;
                $data->save();
            }
            DB::commit();
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
            DB::rollBack();
        }
        return view('cashier.order_list', compact('data'));
    }

    public function addToOrder(Request $request)
    {
        if (!session('table_id')) {
            return 'NOTABLE';
        }
        $product = Product::with('product_category')->find($request->id);
        try {
            DB::beginTransaction();
            $order_detail = OrderDetailTemp::where('table_id', session('table_id'))->where('product_id', $product->id)->first();
            if (!$order_detail) {
                $order_detail = new OrderDetailTemp();
                $order_detail->table_id = session('table_id');
                $order_detail->product_id = $product->id;
                $order_detail->product_category_id = $product->product_category_id;
                $order_detail->qty = 1;
                $order_detail->description = $product->name;
                $order_detail->unit_price = $product->unit_price;
                $order_detail->created_by_id = $request->user()->id;
            } else {
                $order_detail->qty += 1;
            }
            $order_detail->updated_by_id = $request->user()->id;
            $order_detail->table->status = 1;
            $order_detail->push();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            abort(500, $ex->getMessage());
        }
        return $this->selectTable($request);
    }

    public function deleteOrderProduct(Request $request)
    {
        try {
            OrderDetailTemp::destroy($request->delete_id);
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
        return $this->selectTable($request);
    }

    public function updateOrderQty(Request $request)
    {
        try {
            $data = OrderDetailTemp::find($request->id);
            $data->qty = $request->qty;
            $data->save();
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
        return $this->selectTable($request);
    }

    public function updateDetailDiscount(Request $request)
    {
        try {
            $data = OrderDetailTemp::find($request->id);
            $data->discount = $request->discount;
            $data->save();
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
        return $this->selectTable($request);
    }

    public function updateDiscount(Request $request)
    {
        try {
            $table = Table::find(session('table_id'));
            $table->discount = $request->discount;
            $table->save();
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
        return $this->selectTable($request);
    }

    public function printInvoice(Request $request)
    {
        try {
            $data = Table::find($request->table_id);
            $data->invoice_no = date('YmdHi') . $request->table_id;
            $data->status = 3;
            $data->save();
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
        return view('cashier.print_invoice', compact('data'));
    }

    public function makePayment(Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = Table::find(session('table_id'));
            return view('cashier.make_payment', compact('data'));
        }
        $table = Table::find($request->table_id);

        // validation
        $rules = [
            'receive_amount' => 'required|numeric|min:' . $table->net_amount
        ];
        $validator = Validator::make($request->all(), $rules, [
            'receive_amount.required' => 'is required',
            'receive_amount.numeric' => 'must be number',
            'receive_amount.min' => 'must be at least ' . $table->net_amount,
        ]);
        if ($validator->fails())
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);

        try {
            DB::beginTransaction();
            $order = new Order();
            $order->table_id = $table->id;
            $order->invoice_no = $table->invoice_no ? $table->invoice_no : (date('YmdHi') . $request->table_id);
            $order->discount = $table->discount;
            $order->total_discount = $table->total_discount;
            $order->grand_total = $table->grand_total;
            $order->total = $table->total;
            $order->net_amount = $table->net_amount;
            $order->receive_amount = $request->receive_amount;
            $order->created_by_id = $request->user()->id;
            $order->updated_by_id = $request->user()->id;
            if ($order->save()) {
                foreach ($table->order_detail_temps as $item) {
                    $order_detail = new OrderDetail();
                    $order_detail->order_id = $order->id;
                    $order_detail->product_id = $item->product_id;
                    $order_detail->description = $item->description;
                    $order_detail->qty = $item->qty;
                    $order_detail->unit_price = $item->unit_price;
                    $order_detail->product_category_id = $item->product_category_id;
                    $order_detail->discount = $item->discount;
                    $order_detail->created_by_id = $request->user()->id;
                    $order_detail->updated_by_id = $request->user()->id;
                    $order_detail->save();
                }
                OrderDetailTemp::where('table_id', $request->table_id)->delete();
                $table->invoice_no = '';
                $table->discount = 0;
                $table->total_discount = 0;
                $table->grand_total = 0;
                $table->total = 0;
                $table->net_amount = 0;
                $table->status = 2;
                $table->save();
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'content' => view('cashier.print_receipt', compact('order'))->render()
            ]);
        } catch (Exception $ex) {
            DB::rollBack();
            abort(500, $ex->getMessage());
        }
    }
}
