<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    public function index(Request $request)
    {
        session()->put('table_name', $request->get('table_name', session('table_name')));
        session()->put('table_status', $request->get('table_status', session('table_status')));
        session()->put('table_field', $request->get('table_field', session('table_field', 'created_at')));
        session()->put('table_order', $request->get('table_order', session('table_order', 'desc')));

        $list = Table::when(session('table_name'), function ($query) {
            $query->where('name', 'like', '%' . session('table_name') . '%');
        })->when(session('table_status'), function ($query) {
            $query->where('status', '=', session('table_status'));
        })->orderBy(session('table_field'), session('table_order'))
            ->paginate(50);

        return view('table.index', compact('list'));
    }

    public function form($id = 0)
    {
        if ($id == 0)
            $data = null;
        else
            $data = Table::find($id);
        return view('table.form', compact('data'));
    }

    public function submit(Request $request)
    {
        // validation
        $rules = [
            'name' => 'required|unique:tables,name,' . $request->id,
            'status' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);

        // save to database
        if ($request->id > 0) {
            $data = Table::find($request->id);
        } else {
            $data = new Table();
            $data->created_by_id = Auth::id();
        }

        $data->updated_by_id = Auth::id();
        $data->name = $request->name;
        $data->status = $request->status;

        $data->save();
        return response()->json([
            'success' => true,
            'redirect_url' => url('table')
        ]);
    }

    public function delete(Request $request)
    {
        $data = Table::find($request->delete_id);
        $data->deleted_by_id = Auth::id();
        $data->delete();
        return redirect('table');
    }
}
