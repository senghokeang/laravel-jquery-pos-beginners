<?php
//  Laravel POS With jQuery @ http://laravel-jquery-pos
namespace App\Http\Controllers;

use App\Models\BalanceAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BalanceAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        session()->put('balance_adjustment_remark', $request->get('balance_adjustment_remark', session('balance_adjustment_remark')));
        session()->put('balance_adjustment_type_id', $request->get('balance_adjustment_type_id', session('balance_adjustment_type_id', 0)));
        session()->put('balance_adjustment_fd', $request->get('balance_adjustment_fd', session('balance_adjustment_fd', date('Y-m-d'))));
        session()->put('balance_adjustment_td', $request->get('balance_adjustment_td', session('balance_adjustment_td', date('Y-m-d'))));
        session()->put('balance_adjustment_field', $request->get('balance_adjustment_field', session('balance_adjustment_field', 'created_at')));
        session()->put('balance_adjustment_order', $request->get('balance_adjustment_order', session('balance_adjustment_order', 'desc')));
        // product list
        $list = BalanceAdjustment::when(session('balance_adjustment_remark'), function ($query) {
            $query->where('remark', 'like', '%' . session('balance_adjustment_remark') . '%');
        })
            ->when(session('balance_adjustment_type_id') > 0, function ($query) {
                $query->where('type_id', '=', session('balance_adjustment_type_id'));
            })
            ->when(session('balance_adjustment_fd'), function ($query) {
                $query->where('updated_at', '>=', date('Y-m-d 00:00:00', strtotime(session('balance_adjustment_fd'))));
            })
            ->when(session('balance_adjustment_td'), function ($query) {
                $query->where('updated_at', '<=', date('Y-m-d 23:59:59', strtotime(session('balance_adjustment_td'))));
            })
            ->orderBy(session('balance_adjustment_field'), session('balance_adjustment_order'))
            ->paginate(50);

        return view('balance_adjustment.index', compact('list'));
    }

    public function form($id = 0)
    {
        if ($id == 0)
            $data = null;
        else
            $data = BalanceAdjustment::find($id);

        return view('balance_adjustment.form', compact('data'));
    }

    public function submit(Request $request)
    {
        // validation
        $rules = [
            'amount' => 'required|numeric',
            'type_id' => 'required',
            'adjusted_date' => 'required|date',
            'remark' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules, [], ['type_id' => 'type']);
        if ($validator->fails())
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);

        // save to database
        if ($request->id > 0) {
            $data = BalanceAdjustment::find($request->id);
        } else {
            $data = new BalanceAdjustment();
            $data->created_by_id = Auth::id();
        }

        $data->updated_by_id = Auth::id();
        $data->amount = $request->amount;
        $data->type_id = $request->type_id;
        $data->adjusted_date = $request->adjusted_date;
        $data->remark = $request->remark;

        $data->save();
        return response()->json([
            'success' => true,
            'redirect_url' => url('balance-adjustment')
        ]);
    }

    public function delete(Request $request)
    {
        $data = BalanceAdjustment::find($request->delete_id);
        $data->deleted_by_id = Auth::id();
        $data->delete();
        return redirect('balance-adjustment');
    }
}
