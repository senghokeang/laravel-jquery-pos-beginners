<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        session()->put('product_name', $request->get('product_name', session('product_name')));
        session()->put('product_category', $request->get('product_category', session('product_category')));
        session()->put('product_field', $request->get('product_field', session('product_field', 'created_at')));
        session()->put('product_order', $request->get('product_order', session('product_order', 'desc')));

        // product list
        $list = Product::join('product_categories', 'product_categories.id', '=', 'products.product_category_id')
            ->when(session('product_name'), function ($query) {
                $query->where('products.name', 'like', '%' . session('product_name') . '%');
            })
            ->when(session('product_category'), function ($query) {
                $query->where('products.product_category_id', '=', session('product_category'));
            })
            ->select('products.id', 'products.name', 'products.image', 'products.unit_price', 'products.created_at', 'product_categories.name AS category_name')
            ->orderBy(session('product_field'), session('product_order'))
            ->paginate(50);

        // product category list
        $product_categories = ProductCategory::all(['id', 'name']);

        return view('product.index', compact('list', 'product_categories'));
    }

    public function form($id = 0)
    {
        if ($id == 0)
            $data = null;
        else
            $data = Product::find($id);
        // product category list
        $product_categories = ProductCategory::all(['id', 'name']);
        return view('product.form', compact('data', 'product_categories'));
    }

    public function submit(Request $request)
    {
        // validation
        $rules = [
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'name' => 'required|unique:products,name,' . $request->id,
            'product_category_id' => 'required',
            'unit_price' => 'required|numeric'
        ];
        $validator = Validator::make($request->all(), $rules, [], ['product_category_id' => 'product category']);
        if ($validator->fails())
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);

        // save to database
        if ($request->id > 0) {
            $data = Product::find($request->id);
        } else {
            $data = new Product();
            $data->created_by_id = Auth::id();
        }
        $data->updated_by_id = Auth::id();
        $data->name = $request->name;
        $data->product_category_id = $request->product_category_id;
        $data->unit_price = $request->unit_price;

        // delete uploaded file
        if ($request->is_deleted_image == 1 && $request->id > 0) {
            if (Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }
            $data->image = '';
        }
        // upload file
        else if ($request->hasFile('image')) {
            if ($data->image && Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }
            $data->image = Storage::disk('public')->put('product', $request->image);
        }

        $data->save();
        return response()->json([
            'success' => true,
            'redirect_url' => url('product')
        ]);
    }

    public function delete(Request $request)
    {
        $data = Product::find($request->delete_id);
        $data->deleted_by_id = Auth::id();
        // delete uploaded file
        if ($data->image && Storage::disk('public')->exists($data->image)) {
            Storage::disk('public')->delete($data->image);
        }
        $data->delete();
        return redirect('product');
    }
}
