<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Profit;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employee = User::where('role', '=', 'employee')->get();
        if ($request->ajax()) {
            return DataTables::of($employee)
            ->addColumn('pass', function($item){
                return '**********';
            })
            ->addColumn('action',function($item){
                $btn = ' <button class="btn btn-primary" data-btn="showModal">
                        <i class="bx bx-pencil"></i>
                    </button>';
                        $btn.= ' <button class="btn btn-success" data-btn="showModal">
                            <i class="bx bx-info-circle" type="solid"></i>
                        </button>';
                return $btn;
            })
                ->addIndexColumn()
                ->rawColumns(['pass','action'])
                ->make(true);
        }
        return view('admin.employee');
    }

    public function getAll()
    {
        $employee = User::where('role', '=', 'employee')->get();
        return response()->json($employee);
    }

    public function insert(Request $request)
    {
        // $input = $request->input('date')
        $id = $request->input('user_id') . '-' . $request->input('date') . '-' . $request->input('daytime');
        $check = Employee::where('date', '=', $request->input('date'))->where('user_id', '=', $request->input('user_id'))->count();
        if ($check < 1) {
            return response()->json(['error'=>$check],302);
        }
        $input = array(
            'id' => $id,
            'user_id' => $request->input('user_id'),
            'date' => $request->input('date'),
            'time' => $request->input('daytime'),
        );
        Employee::create($input);
        return response()->json(['message' => 'success']);
    }

    public function setTotalFee(Request $request)
    {
        $totalProfitSiang = Profit::where('date', '=', $request->input('date'))->where('daytime', '=', 'Siang')->first();
        $totalProfitMalam = Profit::where('date', '=', $request->input('date'))->where('daytime', '=', 'Malam')->first();

        $countScheduleSiang = Employee::where('date', '=', $request->input('date'))->where('time', '=', 'Siang')->count();
        $countScheduleMalam = Employee::where('date', '=', $request->input('date'))->where('time', '=', 'Malam')->count();
        // dd($totalProfitSiang->for_employee / $countScheduleSiang);
        // dd($feeMalam);
        if ($countScheduleSiang != 0) {

            $feeSiang = $totalProfitSiang->for_employee / $countScheduleSiang;
            Employee::where('date', '=', $request->input('date'))->where('time', '=', 'Siang')->update(['total_fee' => $feeSiang]);
        }
        if ($countScheduleMalam != 0) {

            $feeMalam = $totalProfitMalam->for_employee / $countScheduleMalam;
            Employee::where('date', '=', $request->input('date'))->where('time', '=', 'Malam')->update(['total_fee' => $feeMalam]);
        }
        return response()->json(['message' => 'success']);
    }

    public function insertEmployee(Request $request)
    {
        $field = array(
            'name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'password'=>bcrypt('12345678'),
        );
        User::create($field);
        return response()->json(['message' => 'success']);
    }

    public function getDashboard()
    {
        return view('employee.dashboard');
    }
}
