<?php

namespace App\Http\Controllers;

use App\Exports\TransactionExport;
use App\Models\Profit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $profit = Profit::all();
        // strlen()
        if ($request->ajax()) {
            return DataTables::of($profit)
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.rekap');
    }

    public function getBetweenDate(Request $request)
    {
        $input = $request->only(['date']);
        $date = explode("-", $input['date'], 2);
        // $firstDate = Carbon::parse(trim($date[0], ' '));
        // $secondDate = Carbon::parse(trim($date[1], ' '));
        // echo $firstDate . ' '. $secondDate;
        $profit = Profit::whereBetween('date',[trim($date[0],' '),trim($date[1],' ')]);
        if ($request->ajax()) {
            return DataTables::of($profit)
                ->addIndexColumn()
                ->make(true);
        }
        
    }

    public function export(Request $request)
    {
        if (!empty($input['date'])) {
        $input = $request->only(['date']);
        $date = explode("-", $input['date'], 2);
        return Excel::download(new TransactionExport($date[0],$date[1]));
        }
        return Excel::download(new TransactionExport(),'TransaksiExport.xlsx');
    }
}
