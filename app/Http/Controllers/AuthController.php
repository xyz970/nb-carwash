<?php

namespace App\Http\Controllers;
// require('./')
// use App\Http\Controllers\AdminController;7

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->back()->with('error', 'true');
        }
        // dd($input);
    }

    public function accountSetting()
    {
        return view('admin.account-setting');
    }

    public function updateSetting(Request $request)
    {
        $input = $request->only('name', 'email', 'password');
        $user = Auth::user();
        $db = User::find($user->id);
        $db->email = $input['email'];
        $db->password = bcrypt($input['password']);
        $db->name = $input['name'];
        $db->update();
        return redirect()->route('logout');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
