<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\WashType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $washType =
            $transactions = Transaction::with('washType')->get();
        if ($request->ajax()) {
            return DataTables::of($transactions)
                // ->addColumn('status', function ($item) {
                //     if ($item->time == 'true')
                //         $keterangan = '<td><span class="badge rounded-pill badge-success">Pesanan Selesai</span></td>';
                //     else
                //         $keterangan = '<td>
                //             <span class="badge rounded-pill badge-secondary">Proses <i class="fa fa-spin fa-circle-o-notch"></i></span>
                //             </td>';
                //     return $keterangan;
                // })
                ->addColumn('type', function ($item) {
                    // '.route("crew.changeStatus",$item->id).
                    if ($item->washType->type == "Mobil") {
                        $label = '<span class="badge bg-info">Mobil</span>';
                    } elseif ($item->washType->type == "Motor") {
                        $label = '<span class="badge bg-success">Motor</span>';
                    } else {
                        $label = '<span class="badge bg-secondary"> Karpet</span>';
                    }
                    return $label;
                })
                ->addColumn('action', function ($item) {
                    // '.route("crew.changeStatus",$item->id).
                    $btn = '
        <button class="btn btn-danger" data-btn="showModal" onclick="detail(`' . $item->id . '`,`' . $item->note . '`)"  data-bs-toggle="modal" data-bs-target="#detailPesanan" >
            Hapus
        </button></div>';
                    return $btn;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'type'])
                ->make(true);
        }
        return view('admin.transaction', compact('washType'));
    }

    public function detailPencucian(Request $request, $type)
    {
        $detailPencucian = WashType::where('type', '=', $type)->get();
        if ($request->ajax()) {
            return response()->json($detailPencucian);
        }
    }
    public function priceDetail(Request $request, $id)
    {
        $detailPencucian = WashType::find($id);
        if ($request->ajax()) {
            return response()->json($detailPencucian);
        }
    }

    public function insertTransaction(Request $request)
    {

        $date = Carbon::now()->format('Y-m-d');
        $time = Carbon::now()->format('H:i');
        $id = $date . '-' . Str::snake(Str::lower($request->input('name'))) . '-' . $time;
        $input = $request->only('name', 'wash_type_id', 'merk_model', 'plate_number', 'total');
        $transactions = Transaction::where('name', '=', $input['name'])->count();

        $secInput = array(
            'id' => $id,
            'date' => $date,
            'time' => $time,
        );
        $array = $input + $secInput;
        try {
            if ($transactions % 5 == 0) {
                Transaction::create($array);
                return response()->json(['bonus' => 'true'],409);
            }
            Transaction::create($array);
            return response()->json(['status' => 'success']);

            
        } catch (\Throwable $th) {
            return response()->json(['status' => 'fail', 'message' => $th->getTraceAsString()]);
        }

        /**
         * Logic 
         */
    }

    public function bonus(Request $request, $wash_type, $name)
    {

        $wash_type = WashType::where('type', '=', $wash_type)->first();
        if ($wash_type == "Motor") {
            $transactions = Transaction::where('name', '=', $name)->count();
            if ($transactions % 10 == 0) {
                return response()->json(['bonus' => 'true', 'tipe' => 'Pencucian motor 1x',]);
            }
        } elseif ($wash_type == "Mobil") {
            $transactions = Transaction::where('name', '=', $name)->count();
            if ($transactions % 10 == 0) {
                return response()->json(['bonus' => 'true', 'tipe' => 'Pencucian Mobil 1x',]);
            }
        }
    }
}
