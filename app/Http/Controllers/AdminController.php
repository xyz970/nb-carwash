<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            
        }
        return view('admin.dashboard');
    }
}
