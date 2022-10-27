<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Profit;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class ProfitController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with(['washType'])->get();
        if ($request->ajax()) {
            return response()->json($request->date);
            // return DataTables::of($transactions)
            // ->addIndexColumn()
            // ->make(true);
        }
        return view('admin.profit');
    }

    public function selectDate($date)
    {
        $profit = Profit::where('date', '=', $date)->count();
        // dd($profit);
        if ($profit < 2) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
    }

    public function showTable(Request $request, $date)
    {
        $profit = Profit::where('date', '=', $date)->get();
        if ($request->ajax()) {
            return DataTables::of($profit)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function insertProfit(Request $request, $date)
    {
        $yearInput = substr($date, 0, 4);
        $dayInput = substr($date, 8, 10);
        $monthInput = str_replace('-', ' ', substr($date, 4, 3));
        // $createDate = Carbon::create((int)$yearInput,(int)$monthInput,(int)$dayInput,0);
        $createDate = Carbon::createFromFormat('Y-m-d H', $date . '7', 'Asia/Jakarta');
        // $profit = Profit::where('created_at','<=',$createDate)->where('created_at','>=',$createDate->addHour(9))->sum('total');
        // $profit = Transaction::whereBetween('created_at',[$createDate,$createDate->addHour(9)])->get();
        if ($request->input('daytime') == "Siang") {
            $profit = DB::select('SELECT SUM(total) as total FROM transactions WHERE HOUR(time) BETWEEN 7 AND 16 AND date = ?', [$date]);
        } else {
            $profit = DB::select('SELECT SUM(total) as total FROM transactions WHERE HOUR(time) BETWEEN 16 AND 24 AND date = ?', [$date]);
        }
        $check = Profit::where('date', '=', $date)->where('daytime', '=', $request->input('daytime'))->first();
        $id = Str::lower($request->input('daytime')) . '-' . $date;

        $for_owner = $profit[0]->total * 0.5;
        $for_employee = $for_owner * 0.35;
        $for_cash = $for_employee * 0.15;

        $field = array(
            'id' => $id,
            'date' => $date,
            'daytime' => $request->input('daytime'),
            'total' => $profit[0]->total,
            'for_owner' => $for_owner,
            'for_employee' => $for_employee,
            'for_cash' => $for_cash,
        );
        // dd($profit);
        if ($check) {
            return response()->json(['message' => 'data telah ada'], 302);
        } else {
            Profit::create($field);
            return response()->json(['message' => 'data berhasil dimasukkan']);
        }
    }

    public function fee(Request $request,$date)
    {
        $fee = Employee::with('user')->where('date','=',$date)->get();
        if ($request->ajax()) {
            return DataTables::of($fee)
                ->addIndexColumn()
                ->make(true);
        }
    }
}
