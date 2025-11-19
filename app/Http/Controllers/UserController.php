<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        if ($request->isMethod('GET'))
            return view('user.login');

        // validation
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
            'active' => 1,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
                'redirect_url' => url('/')
            ]);
        }
        return response()->json([
            'success' => false,
            'errors' => [
                'username' => ['Incorrect username or password']
            ]
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Change password
    public function changePassword(Request $request)
    {
        if ($request->isMethod('GET'))
            return view('user.change_password');

        $rules = [
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if (!Hash::check($value, $request->user()->password)) {
                        $fail('The old password is incorrect');
                    }
                }
            ],
            'new_password' => ['required', 'confirmed'],
        ];
        //validate data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);

        // change the password
        $user = $request->user();
        $user->updated_at = now();
        $user->updated_by_id = $user->id;
        $user->password = bcrypt($request->new_password);
        $user->save();

        // redirect
        return response()->json([
            'success' => true,
            'redirect_url' => null
        ]);
    }

    public function index(Request $request)
    {
        session()->put('user_name', $request->get('user_name', session('user_name')));
        session()->put('user_role', $request->get('user_role', session('user_role')));
        session()->put('user_field', $request->get('user_field', session('user_field', 'created_at')));
        session()->put('user_order', $request->get('user_order', session('user_order', 'desc')));

        $list = User::when(session('user_name'), function ($query) {
            $query->where('username', 'like', '%' . session('user_name') . '%');
        })->when(session('user_role'), function ($query) {
            $query->where('role', '=', session('user_role'));
        })->orderBy(session('user_field'), session('user_order'))
            ->paginate(50);

        return view('user.index', compact('list'));
    }

    public function form($id = 0)
    {
        if ($id == 0)
            $data = null;
        else
            $data = User::find($id);
        return view('user.form', compact('data'));
    }

    public function submit(Request $request)
    {
        // validation
        $rules = [
            'username' => 'required|alpha_num|unique:users,username,' . $request->id,
            'role' => 'required',
            'password' => [$request->id > 0 ? 'nullable' : 'required', 'confirmed'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);

        // save to database
        if ($request->id > 0) {
            $data = User::find($request->id);
        } else {
            $data = new User();
            $data->created_by_id = Auth::id();
        }

        $data->updated_by_id = Auth::id();
        $data->username = $request->username;
        $data->role = $request->role;
        $data->active = $request->active == 'on';
        if ($request->password)
            $data->password = bcrypt($request->password);

        $data->save();
        return response()->json([
            'success' => true,
            'redirect_url' => url('user')
        ]);
    }

    public function delete(Request $request)
    {
        $data = User::find($request->delete_id);
        $data->deleted_by_id = Auth::id();
        $data->delete();
        return redirect('user');
    }
}
