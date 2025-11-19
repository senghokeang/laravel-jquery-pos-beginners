<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        session()->put('product_category_name', $request->get('product_category_name', session('product_category_name')));
        session()->put('product_category_field', $request->get('product_category_field', session('product_category_field', 'created_at')));
        session()->put('product_category_order', $request->get('product_category_order', session('product_category_order', 'desc')));

        $list = ProductCategory::when(session('product_category_name'), function ($query) {
            $query->where('name', 'like', '%' . session('product_category_name') . '%');
        })->orderBy(session('product_category_field'), session('product_category_order'))
            ->paginate(50);

        return view('product_category.index', compact('list'));
    }

    public function form($id = 0)
    {
        if ($id == 0)
            $data = null;
        else
            $data = ProductCategory::find($id);
        return view('product_category.form', compact('data'));
    }

    public function submit(Request $request)
    {
        // validation
        $rules = [
            'name' => 'required|unique:product_categories,name,' . $request->id
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);

        // save to database
        if ($request->id > 0) {
            $data = ProductCategory::find($request->id);
        } else {
            $data = new ProductCategory();
            $data->created_by_id = Auth::id();
        }
        $data->updated_by_id = Auth::id();
        $data->name = $request->name;
        $data->save();
        return response()->json([
            'success' => true,
            'redirect_url' => url('product-category')
        ]);
    }

    public function delete(Request $request)
    {
        $data = ProductCategory::find($request->delete_id);
        $data->deleted_by_id = Auth::id();
        $data->delete();
        return redirect('product-category');
    }
}
